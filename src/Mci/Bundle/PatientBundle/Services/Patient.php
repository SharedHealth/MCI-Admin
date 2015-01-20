<?php
namespace Mci\Bundle\PatientBundle\Services;

use Guzzle\Http\Client;
use Guzzle\Http\Exception\BadResponseException;
use Guzzle\Http\Exception\RequestException;
use JMS\Serializer\Serializer;
use Guzzle\Http\Exception\CurlException;
use Mci\Bundle\PatientBundle\Utills\Utility;

class Patient
{
    /**
     * @var Client
     */
    private $client;

    /**
     * @var Serializer
     */

    private $serializer;

    private $endpoint;


    public function __construct(Client $client, Serializer $serializer, $endpoint) {

        $this->client = $client;
        $this->endpoint = $endpoint."/patients";
        $this->serializer = $serializer;
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
            $searchQueryParam = $queryParam['division_id'] . $district . $queryParam['upazila_id'];
            if(isset($queryParam['citycorporation_id'])){
                $searchQueryParam .= $queryParam['citycorporation_id'];
            }

            if(isset($queryParam['union_id'])){
                $searchQueryParam .= $queryParam['union_id'];
            }

            if(isset($queryParam['ward_id'])){
                $searchQueryParam .= $queryParam['ward_id'];
            }
            $queryParam['present_address'] = $searchQueryParam;
            unset($queryParam['division_id']);
            unset($queryParam['district_id']);
            unset($queryParam['upazila_id']);

            unset($queryParam['citycorporation_id']);
            unset($queryParam['union_id']);
            unset($queryParam['ward_id']);
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
        $object = $this->serializer->deserialize($responseBody, 'Mci\Bundle\PatientBundle\FormMapper\Patient', 'json');
        return $object;
    }

    public function updatePatientById($id, $postData){
        $url = $this->endpoint.'/'.$id;

        return $this->update($postData, $url,$header = array('content-type' => 'application/json'));

    }


    public function getApprovalPatientsList($url,$header){
        return $this->getPatients($url,$header);
    }

    public function getApprovalPatientsDetails($url){
       $result =  $this->getPatients($url);
        return $result;
    }

    public function getPatients($url, $header = null){
        $responseBody = array();
        $SystemAPiError = array();
        try{
            $request = $this->client->get($url,$header);
            $response = $request->send();
            $responseBody = json_decode($response->getBody(), true);

        }catch (CurlException $e) {
            $SystemAPiError[] = 'Service Unavailable';
        } catch (BadResponseException $e) {
            $messages = json_decode($e->getResponse()->getBody());
            $SystemAPiError = Utility::getErrorMessages($messages);
        } catch (RequestException $e) {
            $SystemAPiError[] = 'Something went wrong';
        }

        return  array('responseBody' => $responseBody,'systemError'=>$SystemAPiError,'catchments'=> $this->getAllCatchment());
    }

    public function getAllCatchment(){

        return array(
            array(
            'division_id' => '10',
            'district_id' => '04',
            'upazila_id' => '09'
            ),
            array(
                'division_id' => '10',
                'district_id' => '05',
                'upazila_id' => '09'
            ),
            array(
                'division_id' => '10',
                'district_id' => '06',
                'upazila_id' => '09'
            ),
            array(
                'division_id' => '10',
                'district_id' => '07',
                'upazila_id' => '09'
            )
        );
    }

    public function getApprovarDefaultLocation(){
        $allCatchments =  $this->getAllCatchment();
        $header = array(
            'content-type' => 'application/json',
            'division_id' => $allCatchments[0]['division_id'],
            'district_id' => $allCatchments[0]['district_id'],
        );

        if(isset($allCatchments[0]['upazila_id'])){
            $header['upazila_id'] = $allCatchments[0]['upazila_id'];
        }

        return $header;
    }

    /**
     * @param $catchments
     * @return array
     */
    public function getHeader($catchments)
    {
        if (!isset($catchments)) {
            $header = $this->getApprovarDefaultLocation();
            return $header;
        } else {
            $header = array(
                'content-type' => 'application/json',
                'division_id' => $catchments[0],
                'district_id' => $catchments[1],
            );
            if(isset($catchments[2])){
                $header['upazila_id'] = $catchments[2];
            }
            if(isset($catchments[3])){
                $header['city_corporation_id'] = $catchments[3];
            }

            return $header;

        }
    }

    public function pendingApproved($url,$payload){
        $header = $this->getApprovarLocation();
        return  $this->update($payload,$url,$header);
    }

    public function pendingReject($url,$payload){
        $header = $this->getApprovarLocation();
        return  $this->delete($payload,$url,$header);
    }

    public function delete($postData,$url,$header = null){

        $SystemAPiError = array();
        try {
            $request = $this->client->delete(
                $url,
                $header
            );
            $request->setBody(json_encode($postData, JSON_UNESCAPED_UNICODE));
            $request->send();
        } catch (RequestException $e) {
            if ($e instanceof CurlException) {
                $SystemAPiError[] = 'Service Unvailable';
            }
            if (method_exists($e, 'getResponse')) {
                $messages = json_decode($e->getResponse()->getBody());
                if ($messages) {
                    $SystemAPiError = Utility::getErrorMessages($messages);
                }
            }
            if (empty($SystemAPiError)) {
                $SystemAPiError[] = "Unknown Error";
            }
        }

        return $SystemAPiError;
    }


    /**
     * @param $postData
     * @param $url
     * @return array|string
     */
    protected function update($postData,$url,$header = null)
    {
        $SystemAPiError = array();
        try {
            $request = $this->client->put(
                $url,
                $header
            );
            $request->setBody(json_encode($postData, JSON_UNESCAPED_UNICODE));
            $request->send();
        } catch (RequestException $e) {
            if ($e instanceof CurlException) {
                $SystemAPiError[] = 'Service Unvailable';
            }
            if (method_exists($e, 'getResponse')) {
                $messages = json_decode($e->getResponse()->getBody());
                if ($messages) {
                    $SystemAPiError = Utility::getErrorMessages($messages);
                }
            }
            if (empty($SystemAPiError)) {
                $SystemAPiError[] = "Unknown Error";
            }
        }

        return $SystemAPiError;
    }
}
