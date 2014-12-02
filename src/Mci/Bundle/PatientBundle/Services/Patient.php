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
            $queryParam['present_address'] = $queryParam['division_id'] . $district . $queryParam['upazilla_id'];
            unset($queryParam['division_id']);
            unset($queryParam['district_id']);
            unset($queryParam['upazilla_id']);
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
        $responseBody = null;
        $SystemAPiError = null;
        try{
            if($id){
                $request = $this->client->get($this->endpoint.'/'.$id);
                $response = $request->send();
                $responseBody = $response->getBody();
            }
        }catch(RequestException $e){

            if($e instanceof CurlException) {
                $SystemAPiError[] = 'Service Unvailable';
            }
        }

        return  array('responseBody' => $responseBody,'systemError'=>$SystemAPiError);
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


}
