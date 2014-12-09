<?php
namespace Mci\Bundle\PatientBundle\Services;

use Guzzle\Http\Exception\RequestException;
use Symfony\Component\DependencyInjection\Container;
use Guzzle\Http\Exception\CurlException;
use Mci\Bundle\PatientBundle\Utills\Utility;

class Patient
{
    /**
     * @var Client
     */
    private $container;
    private $endpoint;
    private $client;

    public function __construct(Container $container, $endpoint) {

        $this->client = $container->get('mci_patient.client');
        $this->endpoint = $endpoint;
        $this->container = $container;
    }

    public function findPatientByRequestQueryParameter($query)
    {
        $queryParam = $this->getQueryParam($query);

        if (empty($queryParam)) {
            return array();
        }

        $request = $this->client->get($this->endpoint, null, array('query' => $queryParam));
        $response = $request->send();

        return json_decode($response->getBody());
    }

    /**
     * @param $query
     * @return array
     */
    private function getQueryParam($query)
    {
        $queryParam = array_filter(array_map('trim', $query));

        $district = null;

        if (isset($queryParam['district_id']) && !empty($queryParam['district_id'])) {
            $district = str_pad($queryParam['district_id'], 2, '0', STR_PAD_LEFT);
            $queryParam['present_address'] = $queryParam['division_id'] . $district . $queryParam['upazila_id'];
            unset($queryParam['division_id']);
            unset($queryParam['district_id']);
            unset($queryParam['upazila_id']);
        }

        return $queryParam;
    }

     public function getSearchParameterAsString($query){

        $queryParam = array_filter(array_map('trim', $query));

        $searchParam = array();
        $name = '';
        $name .=  isset($queryParam['given_name']) ? $queryParam['given_name']:'';
        $name .= isset($queryParam['sur_name']) ? ' '.$queryParam['sur_name']:'';

        if($name){
            $queryParam['name'] = $name;
        }
        $phone_number =  '';

        $phone_number .= isset($queryParam['country_code']) ? $queryParam['country_code'] : '';
        $phone_number .= isset($queryParam['area_code']) ? ' ' .$queryParam['area_code'] : '';
        $phone_number .= isset($queryParam['phone_no']) ? ' '.$queryParam['phone_no'] : '';
        $phone_number .= isset($queryParam['extension']) ? ' '.$queryParam['extension'] : '';

        if($phone_number){
            $queryParam['phone No'] = $phone_number;
        }

        if(isset ($queryParam['bin_brn'])){
            $queryParam['brn'] = $queryParam['bin_brn'];
        }


        $allowed = array('nid','brn','uid','name','phone No');

        $queryParam = array_intersect_key($queryParam, array_flip($allowed));

        foreach($queryParam as $key => $searchItems){
            $searchParam []= $key.' - '.$searchItems;
        }

        if(!empty($searchParam)){
            return implode(' and ',$searchParam);
        }

        return false;
    }


       public  function getPatientById($id){

           $url = $this->endpoint.'/'.$id;
           return $this->getPatients($url);


    }

    /**
     * @param $responseBody
     * @return mixed
     */
    public function getFormMappingObject($responseBody)
    {
        $serializer = $this->container->get('jms_serializer');
        $object = $serializer->deserialize($responseBody, 'Mci\Bundle\PatientBundle\FormMapper\Patient', 'json');
        return $object;
    }

    public function updatePatientById($id, $postData){
        if(isset( $postData['present_address']['upazila_id'])){
            $postData['present_address']['upazilla_id'] = $postData['present_address']['upazila_id'];
        }
        if(isset($postData['permanent_address']['upazila_id'])){
            $postData['permanent_address']['upazilla_id'] = $postData['permanent_address']['upazila_id'];
        }
        unset($postData['present_address']['upazila_id']);
        unset($postData['permanent_address']['upazila_id']);

        $SystemAPiError = array();
        $url = $this->endpoint.'/'.$id;
        try{
            $request = $this->client->put($url,array(
                'content-type' => 'application/json'
            ),array());
            $request->setBody(json_encode($postData,JSON_UNESCAPED_UNICODE));
            $request->send();
        }
        catch(RequestException $e){
            if($e instanceof CurlException) {
                $SystemAPiError[] = 'Service Unvailable';
            }
            if(method_exists($e,'getResponse')){

                $messages =  json_decode($e->getResponse()->getBody());
                if($messages){
                    $SystemAPiError = Utility::getErrorMessages($messages);
                }
             }
            if(empty($SystemAPiError)){
                $SystemAPiError[]= "Unknown Error";
            }
        }
        return $SystemAPiError;

    }

    public function getApprovarLocation(){
       return array(
           'division_id' => '10',
           'district_id' => '04',
           'upazila_id' => '09'
       );
    }

    public function getApprovalPatientsList($url){
        $header = $this->getApprovarLocation();
        return $this->getPatients($url,$header);
    }

    public function getApprovalPatientsDetails($url){
        return $this->getPatients($url);
    }

    public function getPatients($url, $header = null){
        $responseBody = array();
        $SystemAPiError = array();
        try{
            $request = $this->client->get($url,$header);
            $response = $request->send();
            $responseBody = json_decode($response->getBody(), true);
            $responseBody['present_address']['upazila_id'] = $responseBody['present_address']['upazilla_id'];
            if(isset($responseBody['permanent_address']['upazilla_id'])){
                $responseBody['permanent_address']['upazila_id'] = $responseBody['permanent_address']['upazilla_id'];
            }
        }catch (CurlException $e) {
            $SystemAPiError[] = 'Service Unavailable';
        } catch (BadResponseException $e) {
            $messages = json_decode($e->getResponse()->getBody());
            $SystemAPiError = Utility::getErrorMessages($messages);
        } catch (RequestException $e) {
            $SystemAPiError[] = 'Something went wrong';
        }

        return  array('responseBody' => $responseBody,'systemError'=>$SystemAPiError);
    }
}
