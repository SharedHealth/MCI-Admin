<?php
namespace Mci\Bundle\PatientBundle\Services;

use Guzzle\Http\Client;
use Guzzle\Http\Exception\BadResponseException;
use Guzzle\Http\Exception\RequestException;
use JMS\Serializer\Serializer;
use Guzzle\Http\Exception\CurlException;
use Mci\Bundle\PatientBundle\Utills\Utility;
use Mci\Bundle\PatientBundle\Twig\MciExtension;
use Mci\Bundle\UserBundle\Security\User;

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

    private $endpoint = "patients";

    private $securityContext;

    private $twigExtension;

    private $catchmentUrl="catchments/";


    public function __construct(Client $client, Serializer $serializer, $securityContext, $twigExtension) {

        $this->client = $client;
        $this->serializer = $serializer;
        $this->securityContext = $securityContext;

        $user = $this->getUser();

        if (null !== $user) {
            $this->client->setDefaultOption('headers/X-Auth-Token', $user->getToken());
            $this->client->setDefaultOption('headers/client_id', $user->getId());
            $this->client->setDefaultOption('headers/From', $user->getEmail());
        }

        $this->twigExtension = $twigExtension;
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
           return $this->getPatientsResponse($url);
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

        return $this->update($postData, $url);

    }

    public function getApprovalListByCatchment($catchment, $param) {
        if(empty($catchment)) {
            return array();
        }

        return $this->getPatientsResponse($this->catchmentUrl . "$catchment/approvals", $param);
    }

    public function getApprovalPatientsList($url){
        $url = $url = $this->catchmentUrl.$url;
       $response =  $this->getPatientsResponse($url);
       $response['catchments'] = $this->getAllCatchment();
       return $response;
    }

    public function getApprovalPatientsDetails($url){
        $url = $this->catchmentUrl.$url;
        $result =  $this->getPatientsResponse($url);
        if(!empty($result['responseBody'])){
            $result['responseBody'] = $this->mappingPatientDetails($result['responseBody']);
        }
        return $result;
    }

    public function mappingPatientDetails($resultBody){

        foreach($resultBody['results'] as $key => $val){

            switch($val['field_name']){
                case 'gender':
                    $resultBody['results'] [$key] = $this->mappingSingleField($val,'gender');
                break;
                case 'status':
                    $resultBody['results'] [$key] = $this->mappingSingleField($val,'status');
                    break;
                case 'occupation':
                    $resultBody['results'] [$key] = $this->mappingSingleField($val,'occupation');
                    break;
                case 'blood_group':
                    $resultBody['results'] [$key] = $this->mappingSingleField($val,'blood_group');
                    break;
                case 'disability':
                    $resultBody['results'] [$key] = $this->mappingSingleField($val,'disability');
                    break;
                case 'edu_level':
                    $resultBody['results'] [$key] = $this->mappingSingleField($val,'edu_level');
                    break;
                case 'marital_status':
                    $resultBody['results'] [$key] = $this->mappingSingleField($val,'marital_status');
                    break;
                case 'religion':
                    $resultBody['results'] [$key] = $this->mappingSingleField($val,'religion');
                    break;
                case 'present_address':
                    $resultBody['results'] [$key] = $this->mappingBlocksField($val,'present_address');
                break;

                case 'permanent_address':
                    $resultBody['results'] [$key] = $this->mappingBlocksField($val,'permanent_address');
                    break;
            }
        }
        return $resultBody;
    }

    private function mappingSingleField($val,$fieldKey){
        /** @var $twigExtension  MciExtension */
        $twigExtension = $this->twigExtension;
        if($fieldKey == 'gender'){
            $val['current_value'] = $twigExtension->genderFilter($val['current_value']);
            $field_details = $val['field_details'];
            foreach($val['field_details'] as $key => $changeValue){
                $val['field_details'][$key]['value'] = $twigExtension->genderFilter($changeValue['value']);
            }
            $val['payload'] = $field_details;
            return $val;
        }

        if($fieldKey == 'status'){
            $val['current_value'] = $twigExtension->livingStatusFilter($val['current_value']);
            $field_details = $val['field_details'];
            foreach($val['field_details'] as $key => $changeValue){
                $val['field_details'][$key]['value'] = $twigExtension->livingStatusFilter($changeValue['value']);
            }
            $val['payload'] = $field_details;
            return $val;
        }

        if($fieldKey == 'occupation'){
            $val['current_value'] = $twigExtension->occupationFilter($val['current_value']);
            $field_details = $val['field_details'];
            foreach($val['field_details'] as $key => $changeValue){
                $val['field_details'][$key]['value'] = $twigExtension->occupationFilter($changeValue['value']);
            }
            $val['payload'] = $field_details;
            return $val;
        }

        if($fieldKey == 'blood_group'){
            $val['current_value'] = $twigExtension->bloodGroupFilter($val['current_value']);
            $field_details = $val['field_details'];
            foreach($val['field_details'] as $key => $changeValue){
                $val['field_details'][$key]['value'] = $twigExtension->bloodGroupFilter($changeValue['value']);
            }
            $val['payload'] = $field_details;
            return $val;
        }

        if($fieldKey == 'disability'){
            $val['current_value'] = $twigExtension->disabilityFilter($val['current_value']);
            $field_details = $val['field_details'];
            foreach($val['field_details'] as $key => $changeValue){
                $val['field_details'][$key]['value'] = $twigExtension->disabilityFilter($changeValue['value']);
            }
            $val['payload'] = $field_details;
            return $val;
        }
        if($fieldKey == 'edu_level'){
            $val['current_value'] = $twigExtension->eduLevelFilter($val['current_value']);
            $field_details = $val['field_details'];
            foreach($val['field_details'] as $key => $changeValue){
                $val['field_details'][$key]['value'] = $twigExtension->eduLevelFilter($changeValue['value']);
            }
            $val['payload'] = $field_details;
            return $val;
        }
        if($fieldKey == 'marital_status'){
            $val['current_value'] = $twigExtension->maritalStatusFilter($val['current_value']);
            $field_details = $val['field_details'];
            foreach($val['field_details'] as $key => $changeValue){
                $val['field_details'][$key]['value'] = $twigExtension->maritalStatusFilter($changeValue['value']);
            }
            $val['payload'] = $field_details;
            return $val;
        }

        if($fieldKey == 'religion'){
            $val['current_value'] = $twigExtension->religionFilter($val['current_value']);
            $field_details = $val['field_details'];
            foreach($val['field_details'] as $key => $changeValue){
                $val['field_details'][$key]['value'] = $twigExtension->religionFilter($changeValue['value']);
            }
            $val['payload'] = $field_details;
            return $val;
        }
    }

    private function mappingBlocksField($val,$fieldKey){
        /** @var $twigExtension  MciExtension */;
        $twigExtension = $this->twigExtension;
        $current_value = $val['current_value'];
        $field_details = $val['field_details'];

        if($fieldKey == 'present_address' || $fieldKey == 'permanent_address'){
            $val['current_value']['division_id'] = $twigExtension->divisionFilter($val['current_value']['division_id']);
            if(isset($val['current_value']['country_code'])){
                $val['current_value']['country_code'] = $twigExtension->countryCodeFilter($val['current_value']['country_code']);
            }
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
                if(isset($val['field_details'][$key]['value']['division_id'])){
                    $val['field_details'][$key]['value']['division_id'] = $twigExtension->divisionFilter($field_details[$key]['value']['division_id']);
                }
                if(isset($val['field_details'][$key]['value']['district_id'])){
                    $val['field_details'][$key]['value']['district_id'] = $twigExtension->locationFilter($field_details[$key]['value']['district_id'],$field_details[$key]['value']['division_id']);
                }
                if(isset($val['field_details'][$key]['value']['upazila_id'])){
                    $val['field_details'][$key]['value']['upazila_id'] = $twigExtension->locationFilter($field_details[$key]['value']['upazila_id'],$field_details[$key]['value']['division_id'].$field_details[$key]['value']['district_id']);
                }

                if(isset($val['field_details'][$key]['value']['city_corporation_id'])){
                    $val['field_details'][$key]['value']['city_corporation_id'] = $twigExtension->locationFilter($field_details[$key]['value']['city_corporation_id'],$field_details[$key]['value']['division_id'].$field_details[$key]['value']['district_id'].$field_details[$key]['value']['upazila_id']);
                }
                if(isset($val['field_details'][$key]['value']['union_or_urban_ward_id'])){
                    $val['field_details'][$key]['value']['union_or_urban_ward_id'] = $twigExtension->locationFilter($field_details[$key]['value']['union_or_urban_ward_id'],$field_details[$key]['value']['division_id'].$field_details[$key]['value']['district_id'].$field_details[$key]['value']['upazila_id'].$field_details[$key]['value']['city_corporation_id']);
                }
                if(isset($val['field_details'][$key]['value']['rural_ward_id'])){
                    $val['field_details'][$key]['value']['rural_ward_id'] = $twigExtension->locationFilter($field_details[$key]['value']['rural_ward_id'],$field_details[$key]['value']['division_id'].$field_details[$key]['value']['district_id'].$field_details[$key]['value']['upazila_id'].$field_details[$key]['value']['city_corporation_id'].$field_details[$key]['value']['union_or_urban_ward_id']);
                }
                if(isset($val['field_details'][$key]['value']['country_code'])){
                    $val['field_details'][$key]['value']['country_code'] = $twigExtension->countryCodeFilter($val['field_details'][$key]['value']['country_code']);
                }

            }
            $val['payload'] = $field_details;
            return $val;
        }

    }

    public function getPatientsResponse($url, $parameter = array()){
        $responseBody = array();
        $SystemAPiError = array();
        try {
            $request = $this->client->get($url, null, array('query' => $parameter));
            $response = $request->send();
            $responseBody = json_decode($response->getBody(), true);

        } catch (CurlException $e) {
            $SystemAPiError[] = 'Service Unavailable';
        } catch (BadResponseException $e) {
            $messages = json_decode($e->getResponse()->getBody());
            $SystemAPiError = Utility::getErrorMessages($messages);
        } catch (RequestException $e) {
            $SystemAPiError[] = 'Something went wrong';
        }

        return  array('responseBody' => $responseBody,'systemError'=>$SystemAPiError);
    }

    public function getAllCatchment(){
        $catchments = $this->getUser()->getCatchments();

        $allLocation = array();

        foreach($catchments as $key => $catchment ){

            $location_splits =  str_split($catchment, 2);

            foreach($location_splits as $geoCodeKey=>$geoCode){
                if($geoCodeKey == 0){
                    $allLocation[$key]['division_id'] = $geoCode;
                }
                if($geoCodeKey == 1){
                    $allLocation[$key]['district_id'] = $geoCode;
                }
                if($geoCodeKey == 2){
                    $allLocation[$key]['upazila_id'] = $geoCode;
                }
                if($geoCodeKey == 3){
                    $allLocation[$key]['city_corporation_id'] = $geoCode;
                }
                if($geoCodeKey == 4){
                    $allLocation[$key]['union_or_urban_ward_id'] = $geoCode;
                }
                if($geoCodeKey == 5){
                    $allLocation[$key]['rural_ward_id'] = $geoCode;
                }
            }
        }

       return $allLocation;

    }


    public function pendingApproved($url,$payload){
        $url = $this->catchmentUrl.$url;
        return  $this->update($payload,$url);
    }

    public function pendingReject($url,$payload){
        $url = $this->catchmentUrl.$url;
        return  $this->delete($payload,$url);
    }

    public function delete($postData,$url){

        $SystemAPiError = array();
        try {
            $request = $this->client->delete(
                $url
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
    protected function update($postData,$url)
    {
        $SystemAPiError = array();
        try {
            $request = $this->client->put(
                $url
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

    public function getPatientAuditLogDetails($hid){
        $url = "audit/patients/" . $hid;
        $response =  $this->getPatientsResponse($url);
        $responseBody = $this->processAuditLogDetails($response['responseBody']['updates']);
        return array('responseBody' => $responseBody,'createdBy'=>$response['responseBody']['created_by'],'createdAt'=>$response['responseBody']['created_at']);

    }

    public function processAuditLogDetails($responseBody){
        /** @var $twigExtension  MciExtension */;
        $twigExtension = $this->twigExtension;
       foreach($responseBody as $key => $val){
            foreach($val['change_set'] as  $field_name => $fieldDetails){
               if($field_name == 'gender'){
                   $responseBody[$key]['change_set'][$field_name]['new_value'] = $twigExtension->genderFilter($fieldDetails['new_value']);
                   $responseBody[$key]['change_set'][$field_name]['old_value'] = $twigExtension->genderFilter($fieldDetails['old_value']);
               }
                if($field_name == 'religion'){
                   $responseBody[$key]['change_set'][$field_name]['new_value'] = $twigExtension->religionFilter($fieldDetails['new_value']);
                   $responseBody[$key]['change_set'][$field_name]['old_value'] = $twigExtension->religionFilter($fieldDetails['old_value']);
               }
                if($field_name == 'occupation'){
                    $responseBody[$key]['change_set'][$field_name]['new_value'] = $twigExtension->occupationFilter($fieldDetails['new_value']);
                    $responseBody[$key]['change_set'][$field_name]['old_value'] = $twigExtension->occupationFilter($fieldDetails['old_value']);
                }

                if($field_name == 'edu_level'){
                    $responseBody[$key]['change_set'][$field_name]['new_value'] = $twigExtension->eduLevelFilter($fieldDetails['new_value']);
                    $responseBody[$key]['change_set'][$field_name]['old_value'] = $twigExtension->eduLevelFilter($fieldDetails['old_value']);
                }
                if($field_name == 'status'){
                    $responseBody[$key]['change_set'][$field_name]['new_value'] = $twigExtension->livingStatusFilter($fieldDetails['new_value']);
                    $responseBody[$key]['change_set'][$field_name]['old_value'] = $twigExtension->livingStatusFilter($fieldDetails['old_value']);
                }
                if($field_name == 'status'){
                    $responseBody[$key]['change_set'][$field_name]['new_value'] = $twigExtension->livingStatusFilter($fieldDetails['new_value']);
                    $responseBody[$key]['change_set'][$field_name]['old_value'] = $twigExtension->livingStatusFilter($fieldDetails['old_value']);
                }
                if($field_name == 'disability'){
                    $responseBody[$key]['change_set'][$field_name]['new_value'] = $twigExtension->disabilityFilter($fieldDetails['new_value']);
                    $responseBody[$key]['change_set'][$field_name]['old_value'] = $twigExtension->disabilityFilter($fieldDetails['old_value']);
                }
                if($field_name == 'blood_group'){
                    $responseBody[$key]['change_set'][$field_name]['new_value'] = $twigExtension->bloodGroupFilter($fieldDetails['new_value']);
                    $responseBody[$key]['change_set'][$field_name]['old_value'] = $twigExtension->bloodGroupFilter($fieldDetails['old_value']);
                }
                if($field_name == 'marital_status'){
                    $responseBody[$key]['change_set'][$field_name]['new_value'] = $twigExtension->maritalStatusFilter($fieldDetails['new_value']);
                    $responseBody[$key]['change_set'][$field_name]['old_value'] = $twigExtension->maritalStatusFilter($fieldDetails['old_value']);
                }

                if($field_name == 'present_address' || $field_name == 'permanent_address' ){
                    if(isset($fieldDetails['old_value']['division_id'])){
                        $responseBody[$key]['change_set'][$field_name]['old_value']['division_id'] = $twigExtension->divisionFilter($fieldDetails['old_value']['division_id']);
                    }
                    if(isset($fieldDetails['old_value']['district_id'])){
                        $responseBody[$key]['change_set'][$field_name]['old_value']['district_id'] = $twigExtension->locationFilter($fieldDetails['old_value']['district_id'],$fieldDetails['old_value']['division_id']);
                    }
                    if(isset($fieldDetails['old_value']['upazila_id'])){
                        $responseBody[$key]['change_set'][$field_name]['old_value']['upazila_id'] = $twigExtension->locationFilter($fieldDetails['old_value']['upazila_id'],$fieldDetails['old_value']['division_id'].$fieldDetails['old_value']['district_id']);
                    }

                    if(isset($responseBody[$key]['change_set'][$field_name]['old_value']['city_corporation_id'])){
                        $responseBody[$key]['change_set'][$field_name]['old_value']['city_corporation_id'] = $twigExtension->locationFilter($fieldDetails['old_value']['city_corporation_id'],$fieldDetails['old_value']['division_id'].$fieldDetails['old_value']['district_id'].$fieldDetails['old_value']['upazila_id']);
                    }
                    if(isset($responseBody[$key]['change_set'][$field_name]['old_value']['union_or_urban_ward_id'])){
                        $responseBody[$key]['change_set'][$field_name]['old_value']['union_or_urban_ward_id'] = $twigExtension->locationFilter($fieldDetails['old_value']['union_or_urban_ward_id'],$fieldDetails['old_value']['division_id'].$fieldDetails['old_value']['district_id'].$fieldDetails['old_value']['upazila_id'].$fieldDetails['old_value']['city_corporation_id']);
                    }
                    if(isset($responseBody[$key]['change_set'][$field_name]['old_value']['rural_ward_id'])){
                        $responseBody[$key]['change_set'][$field_name]['old_value']['rural_ward_id'] = $twigExtension->locationFilter($fieldDetails['old_value']['rural_ward_id'],$fieldDetails['old_value']['division_id'].$fieldDetails['old_value']['district_id'].$fieldDetails['old_value']['upazila_id'].$fieldDetails['old_value']['city_corporation_id'].$fieldDetails['old_value']['union_or_urban_ward_id']);
                    }

                    if(isset($responseBody[$key]['change_set'][$field_name]['old_value']['country_code'])){
                        $responseBody[$key]['change_set'][$field_name]['old_value']['country_code'] = $twigExtension->countryCodeFilter($responseBody[$key]['change_set'][$field_name]['old_value']['country_code']);
                    }
                    if(isset($fieldDetails['new_value']['division_id'])){
                        $responseBody[$key]['change_set'][$field_name]['new_value']['division_id'] = $twigExtension->divisionFilter($fieldDetails['new_value']['division_id']);
                    }
                    if(isset($fieldDetails['new_value']['district_id'])){
                        $responseBody[$key]['change_set'][$field_name]['new_value']['district_id'] = $twigExtension->locationFilter($fieldDetails['new_value']['district_id'],$fieldDetails['new_value']['division_id']);
                    }
                    if(isset($fieldDetails['new_value']['upazila_id'])){
                        $responseBody[$key]['change_set'][$field_name]['new_value']['upazila_id'] = $twigExtension->locationFilter($fieldDetails['new_value']['upazila_id'],$fieldDetails['new_value']['division_id'].$fieldDetails['new_value']['district_id']);
                    }

                    if(isset($responseBody[$key]['change_set'][$field_name]['new_value']['city_corporation_id'])){
                        $responseBody[$key]['change_set'][$field_name]['new_value']['city_corporation_id'] = $twigExtension->locationFilter($fieldDetails['new_value']['city_corporation_id'],$fieldDetails['new_value']['division_id'].$fieldDetails['new_value']['district_id'].$fieldDetails['new_value']['upazila_id']);
                    }
                    if(isset($responseBody[$key]['change_set'][$field_name]['new_value']['union_or_urban_ward_id'])){
                        $responseBody[$key]['change_set'][$field_name]['new_value']['union_or_urban_ward_id'] = $twigExtension->locationFilter($fieldDetails['new_value']['union_or_urban_ward_id'],$fieldDetails['new_value']['division_id'].$fieldDetails['new_value']['district_id'].$fieldDetails['new_value']['upazila_id'].$fieldDetails['new_value']['city_corporation_id']);
                    }
                    if(isset($responseBody[$key]['change_set'][$field_name]['new_value']['rural_ward_id'])){
                        $responseBody[$key]['change_set'][$field_name]['new_value']['rural_ward_id'] = $twigExtension->locationFilter($fieldDetails['new_value']['rural_ward_id'],$fieldDetails['new_value']['division_id'].$fieldDetails['new_value']['district_id'].$fieldDetails['new_value']['upazila_id'].$fieldDetails['new_value']['city_corporation_id'].$fieldDetails['new_value']['union_or_urban_ward_id']);
                    }
                    if(isset($responseBody[$key]['change_set'][$field_name]['new_value']['country_code'])){
                        $responseBody[$key]['change_set'][$field_name]['new_value']['country_code'] = $twigExtension->countryCodeFilter($responseBody[$key]['change_set'][$field_name]['new_value']['country_code']);
                    }

                }
            }
       }
        return $responseBody;
    }

    /**
     * @return null|User
     */
    private function getUser()
    {
        if ($this->securityContext && $this->securityContext->getToken()) {
            $user = $this->securityContext->getToken()->getUser();

            if ($user instanceof User) {
                return $user;
            }
        }
    }
}
