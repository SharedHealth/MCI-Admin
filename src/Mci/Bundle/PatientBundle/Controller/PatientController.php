<?php

namespace Mci\Bundle\PatientBundle\Controller;

use Guzzle\Http\Exception\RequestException;
use Mci\Bundle\PatientBundle\Form\PatientType;
use Mci\Bundle\PatientBundle\FormMapper\Patient;
use Mci\Bundle\PatientBundle\FormMapper\Relation;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints\DateTime;

class PatientController extends Controller
{
    public function indexAction()
    {
        return $this->render('MciPatientBundle:Patient:index.html.twig');
    }

    function remove_utf8_bom($text)
    {
        $bom = pack('H*','EFBBBF');
        $text = preg_replace("/^$bom/", '', $text);
        return $text;
    }

    public function searchAction( Request $request)
    {
        $districts = array();
        $upazillas = array();
        $responseBody = array();

        $divisions =  $this->getJsonData('division.json');
        $districtsAll =  $this->getJsonData('district.json');
        $client = $this->get('mci_patient.client');
        $locationService = $this->container->get('mci.location');

       if($request->get('division_id')){
           $hrm_division_id = $this->getLocationId($divisions,$request->get('division_id'));
           $districts =  $locationService->getDistrict($hrm_division_id);
       }

       if($request->get('district_id')){
             $hrm_district_id = $this->getLocationId($districtsAll,$request->get('district_id'));
             $upazillas =  $locationService->getUpazilla($hrm_district_id);
       }

        $SystemAPiError = '';
        $hid = '';
        if ('GET' === $request->getMethod()) {
            try{
                $queryParam = array();
                if($request->get('hid')){
                    $hid = trim($request->get('hid'));
                    return $this->redirect($this->generateUrl('mci_patient_showpage', array('id'=>$hid)));
                }

                $queryParam = $this->getQueryParam($request, $queryParam);

                $forSeletedDropdown = array('division'=>$request->get('division_id'),'district'=>$request->get('district_id'),'upazilla'=>$request->get('upazilla_id'));

               if(!empty($queryParam)){
                   $request = $client->get($this->container->getParameter('api_end_point'), null, array('query' =>$queryParam ));
                   $response = $request->send();
                   $responseBody = json_decode($response->getBody());
                }

            } catch(RequestException $e){
                if($e instanceof \Guzzle\Http\Exception\CurlException) {
                    $SystemAPiError[] = 'Service Unvailable';
                }

                try{
                   if(method_exists($e,'getResponse')){
                        $messages =  json_decode($e->getResponse()->getBody());
                        $SystemAPiError = $this->getErrorMessages($messages);
                    }
                }catch (Exception $e) {
                    echo "Unknown Error";
                }
              }
            $searchQueryString = $this->getSearchParameterAsString($queryParam);

            return $this->render('MciPatientBundle:Patient:search.html.twig',array('responseBody' => $responseBody,'queryparam'=>$queryParam,'divisions'=>$divisions,'districts'=>(array)$districts,'upazillas'=>(array)$upazillas,'seletedDropdown'=>$forSeletedDropdown,'systemError'=>$SystemAPiError,'hid'=>$hid,'searchString'=>$searchQueryString));
        }

        return $this->render('MciPatientBundle:Patient:search.html.twig',array('divisions'=>$divisions,'districts'=>(array)$districts,'upazillas'=>(array)$upazillas,'systemError'=>$SystemAPiError,'hid'=>$hid));
    }

    public function editAction(Request $request, $id){

        $responseBody = null;
        try{
            if($id){
                $client = $this->get('mci_patient.client');
                $request = $client->get($this->container->getParameter('api_end_point').'/'.$id);
                $response = $request->send();
                $responseBody = json_decode($response->getBody());
            }
        } catch(RequestException $e){
            $e->getMessage();
        }

        if (!$responseBody) {
            throw $this->createNotFoundException('Unable to find patient');
        }

        $serializer = $this->container->get('jms_serializer');
        $object = $serializer->deserialize($response->getBody(), 'Mci\Bundle\PatientBundle\FormMapper\Patient', 'json');

        $form = $this->createForm(new PatientType($this->container), $object);

        return $this->render('MciPatientBundle:Patient:edit.html.twig', array(
            'form' => $form->createView(),
            'hid'  => $id

        ));
    }

    public function updateAction(Request $request, $id){
        if ($request->getMethod() == 'POST') {
            $nids = $_POST['relation']['nid'];
            if(!empty($nids)){
                foreach($nids as $key => $val){

                }
            }
        }
        return new Response('Update is on progress');
    }

    public function showAction($id, Request $request)
    {
        $responseBody = array();

        try{
            if($id){
                $client = $this->get('mci_patient.client');
                $request = $client->get($this->container->getParameter('api_end_point').'/'.$id);
                $response = $request->send();
                $responseBody = json_decode($response->getBody());
            }
        } catch(RequestException $e){
            $e->getMessage();
        }

        return $this->render('MciPatientBundle:Patient:show.html.twig',array('responseBody' => $responseBody,'hid'=>$id));
    }

    private function getJsonData($fileName)
    {
        $filePath =  'assets/json/'.$fileName;
        return  json_decode(file_get_contents($filePath), true);
    }

    private function getLocationId($data, $code){
        foreach($data as $value){
            if($value['code'] == $code ){
                return $value['id'];
            }
        }
    }

    /**
     * @param $messages
     * @return string
     */
    public function getErrorMessages($messages)
    {
        var_dump($messages);
        foreach ($messages->errors as $value) {

            switch ($value->code) {
                case 1006:
                    $SystemAPiError[] = "Invalid Search Parameter";
                    break;

                case 1002:
                    $SystemAPiError[] = "Invalid Pattern";
                    break;

                default:
                    $SystemAPiError[] = "Service Unavailable";
            }
        }
        return $SystemAPiError;
    }

    public function getSearchParameterAsString($queryParam){

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
        unset($queryParam['bin_brn']);
        unset($queryParam['phone_no']);
        unset($queryParam['area_code']);
        unset($queryParam['country_code']);
        unset($queryParam['extension']);
        unset($queryParam['given_name']);
        unset($queryParam['sur_name']);

        unset ($queryParam['present_address']);

        foreach($queryParam as $key => $searchItems){
            $searchParam []= $key.' - '.$searchItems;
        }
        if(!empty($searchParam)){
            return implode(' and ',$searchParam);
        }
        return false;
    }

    /**
     * @param Request $request
     * @param $queryParam
     * @return mixed
     */
    private function getQueryParam(Request $request, $queryParam)
    {
        $district = null;

        if ($request->get('nid')) {
            $queryParam['nid'] = trim($request->get('nid'));
        }

        if ($request->get('uid')) {
            $queryParam['uid'] = trim($request->get('uid'));
        }

        if ($request->get('brn')) {
            $queryParam['bin_brn'] = trim($request->get('brn'));
        }

        if ($request->get('given_name')) {
            $queryParam['given_name'] = trim($request->get('given_name'));
        }

        if ($request->get('sur_name')) {
            $queryParam['sur_name'] = trim($request->get('sur_name'));
        }

        if ($request->get('dob')) {
            $queryParam['date_of_birth'] = trim($request->get('dob'));
        }
        if ($request->get('phone_no')) {
            $queryParam['phone_no'] = trim($request->get('phone_no'));
        }
        if ($request->get('area_code')) {
            $queryParam['area_code'] = trim($request->get('area_code'));
        }
        if ($request->get('country_code')) {
            $queryParam['country_code'] = trim($request->get('country_code'));
        }
        if ($request->get('extension')) {
            $queryParam['extension'] = trim($request->get('extension'));
        }

        if ($request->get('district_id')) {
            $district = str_pad($request->get('district_id'), 2, '0', STR_PAD_LEFT);
        }

        $location = $request->get('division_id') . $district . $request->get('upazilla_id');

        if ($request->get('district_id')) {
            $queryParam['present_address'] = $location;

        }

        return $queryParam;
    }

}
