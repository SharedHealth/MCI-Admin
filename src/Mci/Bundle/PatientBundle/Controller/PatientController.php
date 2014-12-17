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
        $responseBody = array();

        $locationService = $this->container->get('mci.location');
        $division_code = $request->get('division_id');
        $district_code = $request->get('district_id');
        $divisions = $locationService->getLocation();
        if ($division_code) {
            $districts = $locationService->getLocation($division_code);
        }

        if ($district_code && $division_code) {
            $upazilas = $locationService->getLocation($district_code.$division_code);
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

    public function updateAction(Request $request, $id){

        $postData = array_filter($request->request->get('mci_bundle_patientBundle_patients'));

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
        return $this->render('MciPatientBundle:Patient:show.html.twig',array('responseBody' => $responseBody,'hid'=>$id));
    }

    public function pendingApprovalAction($lastItemId){
        $url =  $this->container->getParameter('api_end_point').'/pendingapprovals?last_item_id='.$lastItemId;
        $response = $this->get('mci.patient')->getApprovalPatientsList($url);
        $response['last_item_id'] = $lastItemId;
        return $this->render('MciPatientBundle:Patient:pendingApproval.html.twig', $response);
    }

    public function pendingApprovalDetailsAction($hid){
        $url =  $this->container->getParameter('api_end_point').'/pendingapprovals/details?hid='.$hid;
        $response = $this->get('mci.patient')->getApprovalPatientsDetails($url);
        return $this->render('MciPatientBundle:Patient:pendingApprovalDetails.html.twig', $response);
    }

}
