<?php

namespace Mci\Bundle\PatientBundle\Controller;

use Guzzle\Http\Exception\BadResponseException;
use Guzzle\Http\Exception\CurlException;
use Guzzle\Http\Exception\RequestException;
use Mci\Bundle\PatientBundle\Form\PatientType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Mci\Bundle\PatientBundle\Utills\Utility;


class PatientController extends Controller
{
    public function indexAction()
    {
        return $this->render('MciPatientBundle:Patient:index.html.twig');
    }

    public function searchAction(Request $request)
    {
        if ($request->get('hid')) {
            return $this->redirect($this->generateUrl('mci_patient_showpage', array('id' => trim($request->get('hid')))));
        }

        $districts = array();
        $upazilas = array();
        $citycorporations = array();
        $unions = array();
        $wards = array();
        $responseBody = array();

        $locationService = $this->container->get('mci.location');
        $division_code = $request->get('division_id');
        $district_code = $request->get('district_id');
        $upazila_code = $request->get('upazila_id');
        $citycorporation_code = $request->get('citycorporation_id');
        $union_code = $request->get('union_id');
        $ward_code = $request->get('ward_id');

        $divisions = $locationService->getLocation();
        if ($division_code) {
            $districts = $locationService->getLocation($division_code);
        }

        if ($district_code && $division_code) {
            $upazilas = $locationService->getLocation($division_code.$district_code);
        }
        if ($district_code && $division_code && $upazila_code  ) {
            $citycorporations = $locationService->getLocation($division_code.$district_code.$upazila_code);
        }
        if ( $division_code && $district_code && $upazila_code && $citycorporation_code  && $union_code ) {
            $unions = $locationService->getLocation($division_code.$district_code.$upazila_code.$citycorporation_code);
        }
        if ( $division_code && $district_code && $upazila_code && $citycorporation_code && $union_code && $ward_code ) {
            $wards = $locationService->getLocation($division_code.$district_code.$upazila_code.$citycorporation_code.$union_code);
        }

        $SystemAPiError = '';
        try {
            $queryParam = $request->query->all();
            $responseBody = $this->get('mci.patient')->findPatientByRequestQueryParameter($queryParam);
        } catch (CurlException $e) {
            $SystemAPiError[] = 'Service Unavailable';
        } catch (BadResponseException $e) {
            $messages = json_decode($e->getResponse()->getBody());
            $SystemAPiError = Utility::getErrorMessages($messages);
        } catch (RequestException $e) {
            $SystemAPiError[] = 'Something went wrong';
        }

        return $this->render('MciPatientBundle:Patient:search.html.twig', array(
                'responseBody' => $responseBody,
                'queryparam' => $queryParam,
                'divisions' => $divisions,
                'districts' => (array)$districts,
                'upazilas' => (array)$upazilas,
                'citycorporations' => (array)$citycorporations,
                'unions' => (array)$unions,
                'wards' => (array)$wards,
                'systemError' => $SystemAPiError,
                'searchString' => $this->get('mci.patient')->getSearchParameterAsString($queryParam)
            ));
    }

    public function editAction( $id){

        $patient = $this->get('mci.patient')->getPatientById($id);

        if($patient['systemError']){
            throw $this->createNotFoundException('Service Unavailable');
        }

        if (!$patient['responseBody']) {
            throw $this->createNotFoundException('Unable to find patient');
        }

        $object = $this->get('mci.patient')->getFormMappingObject(json_encode($patient['responseBody']));

        $form = $this->createForm(new PatientType($this->container,$object), $object);

        return $this->render('MciPatientBundle:Patient:edit.html.twig', array(
            'form' => $form->createView(),
            'hid'  => $id

        ));
    }

    private function filterAcceptZero($var){
        return !($var == '' || $var == null);
    }

    public function updateAction(Request $request, $id){

        $postData = array_filter($request->request->get('mci_bundle_patientBundle_patients'), array($this,'filterAcceptZero'));

        if(!empty($postData['relation'])){
            $relations = Utility::filterRelations($postData['relation']);
            $postData['relations'] = $relations;
        }
        if(!empty($postData['present_address'])){
            $presentAddress = Utility::filterAddress($postData['present_address']);
            $postData['present_address'] = $presentAddress;
        }
        if(!empty($postData['permanent_address']['division_id']) && !empty($postData['permanent_address']['address_line']) && !empty($postData['permanent_address']['district_id']) && !empty($postData['permanent_address']['upazila_id'])){
            $permanentAddress = Utility::filterAddress($postData['permanent_address']);
            $postData['permanent_address'] = $permanentAddress;
        }else{
            unset($postData['permanent_address']);
        }

        $phoneNumber = Utility::filterPhoneNumber($postData['phone_number']);
        if ($phoneNumber) {
            $postData['phone_number'] = $phoneNumber;
        } else {
            unset($postData['phone_number']);
        }

        $primaryPhoneNumber = Utility::filterPhoneNumber($postData['primary_contact_number']);

        if ($primaryPhoneNumber) {
            $postData['primary_contact_number'] = $primaryPhoneNumber;
        } else {
            unset($postData['primary_contact_number']);
        }

        $postData = Utility::unsetUnessaryData($postData);
        $errors = $this->get('mci.patient')->updatePatientById($id, $postData);
        $patient = $this->get('mci.patient')->getPatientById($id);
        $object = $this->get('mci.patient')->getFormMappingObject(json_encode($patient['responseBody']));
        $form = $this->createForm(new PatientType($this->container, $object), $object);

        return $this->render('MciPatientBundle:Patient:edit.html.twig', array(
            'form' => $form->createView(),
            'hid' => $id,
            'errors' => $errors
        ));

    }

    public function removeRelationAction($id){

        if($id){
                 $relationId = $this->get('request')->request->get('realtionId');
                 $relationtype = $this->get('request')->request->get('relationType');
                 $maritalStatus = $this->get('request')->request->get('maritalStatus');

                 $postData = array('relations'=>array(array('id'=>$relationId,'type'=>$relationtype)));

                if(!empty($maritalStatus)){
                     $postData['marital_status'] = $maritalStatus;
                 }
                 $errors = $this->get('mci.patient')->updatePatientById($id, $postData);
                 if($errors){
                     return new Response(json_encode($postData));
                 }else{
                     return new Response("ok");
                 }
        }

    }

    public function showAction($id)
    {
        $response = $this->get('mci.patient')->getPatientById($id);
        $responseBody = $response['responseBody'];
        $systemError = $response['systemError'];
        $catchment = array();
        if(!empty($responseBody['present_address'])){
            $catchment = array(
                'division_id' => $responseBody['present_address']['division_id'],
                'district_id' => $responseBody['present_address']['district_id'],
                'upazila_id' => $responseBody['present_address']['upazila_id']
            );
        }

        if(isset($responseBody['present_address']['city_corporation_id'])){
            $catchment['city_corporation_id'] = $responseBody['present_address']['city_corporation_id'];
        }

        if(isset($responseBody['present_address']['union_or_urban_ward_id'])){
            $catchment['union_or_urban_ward_id'] = $responseBody['present_address']['union_or_urban_ward_id'];
        }
        if(isset($responseBody['present_address']['rural_ward_id'])){
            $catchment['rural_ward_id'] = $responseBody['present_address']['rural_ward_id'];
        }
        $twigExtension = $this->get('mci.twig.mci_extension');

        if($catchment){
            $catchmentCode = implode($catchment);
            $pendingApprovalUrl=  $this->container->getParameter('api_end_point')."/catchments/$catchmentCode/approvals/".$id;
            $pendingApprovalDetails = $this->get('mci.patient')->getApprovalPatientsDetails($pendingApprovalUrl,$twigExtension);
        }
        $appovalDetails = !empty($pendingApprovalDetails['responseBody']['results'])?$pendingApprovalDetails['responseBody']['results']:'';
        return $this->render('MciPatientBundle:Patient:show.html.twig',array('responseBody' => $responseBody,'hid'=>$id,'systemError'=>$systemError,'approvalDetails'=>$appovalDetails));
    }

    public function pendingApprovalNextAction($after,Request $request){
        $catchment = $request->get('catchment');
        $url =  $this->container->getParameter('api_end_point')."/catchments/$catchment/approvals?after=".$after;
        $response = $this->get('mci.patient')->getApprovalPatientsList($url);

        return $this->render('MciPatientBundle:Patient:pendingApproval.html.twig', $response);
    }

    public function pendingApprovalPreviousAction($before, Request $request){
        $catchment = $request->get('catchment');
        $url =  $this->container->getParameter('api_end_point')."/catchments/$catchment/approvals?before=".$before;
        $response = $this->get('mci.patient')->getApprovalPatientsList($url);
        return $this->render('MciPatientBundle:Patient:pendingApproval.html.twig', $response);
    }

    public function pendingApprovalDetailsAction($hid, Request $request){
        $catchment = $request->get('catchment');
        $url =  $this->container->getParameter('api_end_point')."/catchments/$catchment/approvals/".$hid;

        $twigExtension = $this->get('mci.twig.mci_extension');
        $response = $this->get('mci.patient')->getApprovalPatientsDetails($url,$twigExtension);
        $patient = $this->get('mci.patient')->getPatientById($hid);
        $response['patient'] = $patient;
        return $this->render('MciPatientBundle:Patient:pendingApprovalDetails.html.twig', $response);
    }

    public function pendingApprovalAcceptAction(Request $request, $hid){
        $value = $request->query->get('payload');
        $fieldName = $request->query->get('field_name');
        $payload = array($fieldName => json_decode($value));
        $catchment = $request->get('catchment');
        $url = $this->container->getParameter('api_end_point')."/catchments/$catchment/approvals/".$hid;

        $this->get('mci.patient')->pendingApproved($url,$payload);
        if($request->isXmlHttpRequest()){
            return new Response("ok");
        }else{
            return $this->redirect($this->generateUrl('mci_patient_approval_details', array('hid' => $hid,'catchment'=>$catchment)));
        }
    }

    public function pendingApprovalRejectAction(Request $request, $hid){
        $fieldName = $request->query->get('field_name');
        $value = $request->query->get('payload');
        $catchment = $request->get('catchment');
        $payload = array($fieldName => json_decode($value));
        $url = $this->container->getParameter('api_end_point')."/catchments/$catchment/approvals/".$hid;
        $this->get('mci.patient')->pendingReject($url,$payload);
        return $this->redirect($this->generateUrl('mci_patient_approval_details', array('hid' => $hid,'catchment'=>$catchment)));
    }

}
