<?php
namespace Mci\Bundle\PatientBundle\Services;

use Guzzle\Http\Client;
use Guzzle\Http\Exception\BadResponseException;
use Guzzle\Http\Exception\RequestException;
use JMS\Serializer\Serializer;
use Guzzle\Http\Exception\CurlException;
use Mci\Bundle\PatientBundle\Utills\Utility;
use Mci\Bundle\PatientBundle\Twig\MciExtension;

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


    public function __construct(Client $client, Serializer $serializer, $endpoint,$securityContext) {

        $this->client = $client;
        $this->endpoint = $endpoint."/patients";
        $this->serializer = $serializer;
        if($securityContext->getToken()){
            $authKey = $securityContext->getToken()->getUser()->getToken();
            $this->client->setDefaultOption('headers/X-Auth-Token', $authKey);
        }
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

    public function getApprovalPatientsDetails($url, $header,$twigExtension = null){
       $result =  $this->getPatients($url,$header);
        $result['responseBody'] = $this->mappingPatientDetails($result['responseBody'],$twigExtension);
        return $result;
    }

    public function mappingPatientDetails($resultBody,$twigExtension){
        foreach($resultBody['results'] as $key => $val){

            switch($val['field_name']){
                case 'gender':
                    $resultBody['results'] [$key] = $this->mappingSingleField($val,'gender',$twigExtension);
                break;

                case 'present_address':
                    $resultBody['results'] [$key] = $this->mappingBlocksField($val,'present_address',$twigExtension);
                break;
            }
        }
        return $resultBody;
    }

    private function mappingSingleField($val,$fieldKey,$twigExtension){
        /** @var $twigExtension  MciExtension */
        if($fieldKey == 'gender'){
            $val['current_value'] = $twigExtension->genderFilter($val['current_value']);
            $field_details = $val['field_details'];
            foreach($val['field_details'] as $key => $changeValue){
                $val['field_details'][$key]['value'] = $twigExtension->genderFilter($changeValue['value']);
            }
            $val['payload'] = $field_details;
            return $val;
        }
    }

    private function mappingBlocksField($val,$fieldKey,$twigExtension){
        /** @var $twigExtension  MciExtension */;

        $current_value = $val['current_value'];
        $field_details = $val['field_details'];

        if($fieldKey == 'present_address'){
            $val['current_value']['division_id'] = $twigExtension->divisionFilter($val['current_value']['division_id']);
            $val['current_value']['district_id'] = $twigExtension->locationFilter($current_value['district_id'],$current_value['division_id']);
            $val['current_value']['upazila_id'] = $twigExtension->locationFilter($current_value['upazila_id'],$current_value['division_id'].$current_value['district_id']);

            if(isset($val['current_value']['city_corporation_id'])){
                $val['current_value']['city_corporation_id'] = $twigExtension->locationFilter($current_value['city_corporation_id'],$current_value['division_id'].$current_value['district_id'].$current_value['upazila_id']);
            }
            if(isset($val['current_value']['union_or_urban_ward_id'])){
                $val['current_value']['union_or_urban_ward_id'] = $twigExtension->locationFilter($current_value['union_or_urban_ward_id'],$current_value['division_id'].$current_value['district_id'].$current_value['upazila_id'].$current_value['city_corporation_id']);
            }
            if(isset($val['current_value']['rural_ward_id'])){
                $val['current_value']['rural_ward_id'] = $twigExtension->locationFilter($current_value['rural_ward_id'],$current_value['division_id'].$current_value['district_id'].$current_value['upazila_id'].$current_value['city_corporation_id'].$current_value['union_or_urban_ward_id']);
            }

            foreach($val['field_details'] as $key => $changeValue){
                $val['field_details'][$key]['value']['division_id'] = $twigExtension->divisionFilter($field_details[$key]['value']['division_id']);
                $val['field_details'][$key]['value']['district_id'] = $twigExtension->locationFilter($field_details[$key]['value']['district_id'],$field_details[$key]['value']['division_id']);
                $val['field_details'][$key]['value']['upazila_id'] = $twigExtension->locationFilter($field_details[$key]['value']['upazila_id'],$field_details[$key]['value']['division_id'].$field_details[$key]['value']['district_id']);

                if(isset($val['field_details'][$key]['value']['city_corporation_id'])){
                    $val['field_details'][$key]['value']['city_corporation_id'] = $twigExtension->locationFilter($field_details[$key]['value']['city_corporation_id'],$field_details[$key]['value']['division_id'].$field_details[$key]['value']['district_id'].$field_details[$key]['value']['upazila_id']);
                }
                if(isset($val['field_details'][$key]['value']['union_or_urban_ward_id'])){
                    $val['field_details'][$key]['value']['union_or_urban_ward_id'] = $twigExtension->locationFilter($field_details[$key]['value']['union_or_urban_ward_id'],$field_details[$key]['value']['division_id'].$field_details[$key]['value']['district_id'].$field_details[$key]['value']['upazila_id'].$field_details[$key]['value']['city_corporation_id']);
                }
                if(isset($val['field_details'][$key]['value']['rural_ward_id'])){
                    $val['field_details'][$key]['value']['rural_ward_id'] = $twigExtension->locationFilter($field_details[$key]['value']['rural_ward_id'],$field_details[$key]['value']['division_id'].$field_details[$key]['value']['district_id'].$field_details[$key]['value']['upazila_id'].$field_details[$key]['value']['city_corporation_id'].$field_details[$key]['value']['union_or_urban_ward_id']);
                }

            }
            $val['payload'] = $field_details;
            return $val;
        }

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
                'district_id' => '04',
                'upazila_id' => '28',
                'citycorporation_id' => '99',
                'union_or_urban_ward_id' => '28'
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

        if(isset($allCatchments[0]['city_corporation_id'])){
            $header['city_corporation_id'] = $allCatchments[0]['city_corporation_id'];
        }
        if(isset($allCatchments[0]['union_or_urban_ward_id'])){
            $header['union_or_urban_ward_id'] = $allCatchments[0]['union_or_urban_ward_id'];
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
            if(isset($catchments[4])){
                $header['union_or_urban_ward_id'] = $catchments[4];
            }

            return $header;

        }
    }

    public function pendingApproved($url,$payload,$header){
        return  $this->update($payload,$url,$header);
    }

    public function pendingReject($url,$payload,$header){
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
