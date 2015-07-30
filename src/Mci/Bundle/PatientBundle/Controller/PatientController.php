<?php

namespace Mci\Bundle\PatientBundle\Controller;

use Guzzle\Http\Exception\BadResponseException;
use Guzzle\Http\Exception\CurlException;
use Guzzle\Http\Exception\RequestException;
use Mci\Bundle\PatientBundle\Form\PatientType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Mci\Bundle\PatientBundle\Utills\Utility;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
Use Symfony\Component\Form\Extension\Csrf\CsrfProvider\SessionCsrfProvider;


class PatientController extends Controller
{
    public function indexAction()
    {
        return $this->render('MciPatientBundle:Patient:index.html.twig');
    }

    /**
     * @Secure(roles="ROLE_MCI_ADMIN")
     */
    public function searchAction(Request $request)
    {
        if ($request->get('hid')) {
            return $this->redirect($this->generateUrl('mci_patient_showpage', array('id' => trim($request->get('hid')))));
        }

        list($divisions,$districts,$upazilas,$citycorporations,$unions,$wards) = $this->get('mci.location')->getParentlocation($request);

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

    /**
     * @Secure(roles="ROLE_MCI_ADMIN")
     */
    public function editAction( $id)
    {

        $patient = $this->get('mci.patient')->getPatientById($id);
        $location = $this->get('mci.location');
        $masterData = $this->get('mci.master_data');
        $this->throwingException($patient);
        $object = $this->get('mci.patient')->getFormMappingObject(json_encode($patient['responseBody']));

        $form = $this->createForm(new PatientType($location,$masterData,$object), $object);

        return $this->render('MciPatientBundle:Patient:edit.html.twig', array(
            'form' => $form->createView(),
            'hid'  => $id

        ));
    }

    private function filterAcceptZero($var){
        return !($var == '' || $var == null);
    }

    /**
     * @Secure(roles="ROLE_MCI_ADMIN")
     */
    public function updateAction(Request $request, $id)
    {

        $postData = array_filter($request->request->get('mci_bundle_patientBundle_patients'), array($this,'filterAcceptZero'));

        if(!empty($postData['relation'])){
            $relations = Utility::filterRelations($postData['relation']);
            $postData['relations'] = $relations;
        }
        if(!empty($postData['present_address'])){
            $presentAddress = Utility::filterAddress($postData['present_address']);
            $postData['present_address'] = $presentAddress;
        }

        if($postData['permanent_address']['country_code']== ""){
            $postData['permanent_address']['country_code'] = '050';
        }

        if(($postData['permanent_address']['country_code'] == '050') && !empty($postData['permanent_address']['division_id']) && !empty($postData['permanent_address']['address_line']) && !empty($postData['permanent_address']['district_id']) && !empty($postData['permanent_address']['upazila_id'])){
            $permanentAddress = Utility::filterAddress($postData['permanent_address']);
            $postData['permanent_address'] = $permanentAddress;
        }elseif($postData['permanent_address']['country_code']!="" && $postData['permanent_address']['country_code'] != '050' && !empty($postData['permanent_address']['address_line']) ){
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
        $postData = Utility::ConvertDateISOFormat($postData);
        $errors = $this->get('mci.patient')->updatePatientById($id, $postData);
        $patient = $this->get('mci.patient')->getPatientById($id);
        $object = $this->get('mci.patient')->getFormMappingObject(json_encode($patient['responseBody']));
        $location = $this->get('mci.location');
        $masterData = $this->get('mci.master_data');
        $form = $this->createForm(new PatientType($location,$masterData,$object), $object);

        return $this->render('MciPatientBundle:Patient:edit.html.twig', array(
            'form' => $form->createView(),
            'hid' => $id,
            'errors' => $errors
        ));

    }

    /**
     * @Secure(roles="ROLE_MCI_ADMIN")
     */
    public function removeRelationAction($id)
    {

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

    /**
     * @Secure(roles="ROLE_MCI_ADMIN")
     */
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

        if($catchment){
            $catchmentCode = implode($catchment);
            $pendingApprovalUrl=  "$catchmentCode/approvals/".$id;
            $pendingApprovalDetails = $this->get('mci.patient')->getApprovalPatientsDetails($pendingApprovalUrl);
        }
        $appovalDetails = !empty($pendingApprovalDetails['responseBody']['results'])?$pendingApprovalDetails['responseBody']['results']:'';

        if( isset($responseBody['active']) && false === $responseBody['active']){
            return $this->render('MciPatientBundle:Patient:show-inactive.html.twig',array('responseBody' => $responseBody));
        }
        return $this->render('MciPatientBundle:Patient:show.html.twig',array('responseBody' => $responseBody,'hid'=>$id,'systemError'=>$systemError,'approvalDetails'=>$appovalDetails));
    }

    /**
     * @Secure(roles="ROLE_MCI_APPROVER")
     */
    public function pendingApprovalAction($dir, $marker, Request $request)
    {
        $catchment = $request->get('catchment');

        if(!empty($catchment)) {
            $this->handleCatchmentRestriction($catchment);
        }

        $patientModel = $this->get('mci.patient');
        $response = $patientModel->getApprovalListByCatchment($catchment, array($dir => $marker));
        $response['catchments'] = $patientModel->getAllCatchment();
        $response['catchment'] = $catchment;

        return $this->render('MciPatientBundle:Patient:pendingApproval.html.twig', $response);
    }

    public function pendingApprovalDetailsAction($hid, Request $request){
        $catchment = $request->get('catchment');

        $this->handleCatchmentRestriction($catchment);

        $url =  "$catchment/approvals/".$hid;
        $response = $this->get('mci.patient')->getApprovalPatientsDetails($url);
        $patient = $this->get('mci.patient')->getPatientById($hid);
        $response['patient'] = $patient;
        return $this->render('MciPatientBundle:Patient:pendingApprovalDetails.html.twig', $response);
    }

    public function pendingApprovalAcceptAction(Request $request, $hid){
        $catchment = $request->get('catchment');
        $catchment_array = str_split($catchment,2);
        $this->handleCatchmentRestriction($catchment);

        $value = $request->query->get('payload');
        $fieldName = $request->query->get('field_name');
        $payload = array($fieldName => json_decode($value));

        $url = "$catchment/approvals/".$hid;

        $approved = $this->get('mci.patient')->pendingApproved($url,$payload);

        if(!$this->noAccessToCatchment($catchment) && $fieldName == 'present_address'){
            if(is_array($approved) && empty($approved) && ($payload['present_address']->division_id != $catchment_array[0])){
                $this->get('session')->getFlashBag()->add('approval', 'Your request is accepted!');
                if($request->isXmlHttpRequest()){
                    return new Response($catchment);
                }else{
                    return $this->redirect($this->generateUrl('mci_patient_pending_approval', array('catchment'=>$catchment)));
                }
            }
        }

        if($request->isXmlHttpRequest()){
            return new Response("ok");
        }else{
           return $this->redirect($this->generateUrl('mci_patient_approval_details', array('hid' => $hid,'catchment'=>$catchment)));
        }
    }

    public function pendingApprovalRejectAction(Request $request, $hid){
        $catchment = $request->get('catchment');

        $this->handleCatchmentRestriction($catchment);

        $fieldName = $request->query->get('field_name');
        $value = $request->query->get('payload');
        $payload = array($fieldName => json_decode($value));
        $url = "$catchment/approvals/".$hid;
        $this->get('mci.patient')->pendingReject($url,$payload);

        return $this->redirect($this->generateUrl('mci_patient_approval_details', array('hid' => $hid,'catchment'=>$catchment)));
    }

    /**
     * @Secure(roles="ROLE_MCI_ADMIN")
     */
    public function auditLogAction(Request $request, $hid){
            $responses = $this->get('mci.patient')->getPatientAuditLogDetails($hid);
            return $this->render('MciPatientBundle:Patient:auditLog.html.twig', $responses);
        }

    private function noAccessToCatchment($catchment)
    {
        return false === in_array($catchment, $this->getUser()->getCatchments());
    }

    /**
     * @param $catchment
     */
    private function handleCatchmentRestriction($catchment)
    {
        if ($this->noAccessToCatchment($catchment)) {
            throw new AccessDeniedHttpException('Insufficient access privilege');
        }
    }

    /**
     * @param $dir
     * @param $marker
     * @param Request $request
     * @return Response
     */
    public function deduplicationAction($dir, $marker, Request $request)
    {
        $catchment = $request->get('catchment');
        if (!empty($catchment)) {
            $this->handleCatchmentRestriction($catchment);
        }

        $patientModel = $this->get('mci.patient');
        $response = $patientModel->getDedupListByCatchment($catchment, array($dir => $marker));
        if(!empty($response['systemError'])){
            throw new Exception("Service unavailable", 500);
        }

        $response['catchments'] = $patientModel->getAllCatchment();
        $response['catchment'] = $catchment;


        return $this->render('MciPatientBundle:Patient:deDuplication.html.twig', $response);
    }

    /**
     * @param Request $request
     * @param $hid
     * @return Response
     */
    public function deduplicationDetailsAction(Request $request, $hid1,$hid2)
    {
        $patientModel = $this->get('mci.patient');
        $catchment = $request->get('catchment');
        $reasonText = $request->get('reason');
        $originalPatient = $patientModel->getPatientById($hid1);
        $deDupPatient = $patientModel->getPatientById($hid2);
        $this->throwingException($deDupPatient);
        $this->throwingException($originalPatient);
        $reason = explode('-',$reasonText);
        $csrf = $this->get('form.csrf_provider');
        $csrfToken = $csrf->generateCsrfToken('dedup');

        return $this->render('MciPatientBundle:Patient:deDuplicationDetails.html.twig', array('original'=>$originalPatient,'duplicate'=>$deDupPatient,'csrfToken'=>$csrfToken,'catchment'=>$catchment,'reason'=>$reason));
    }

    /**
     * @param Request $request
     */
    public function dedupDistinctAction(Request $request){
        $csrf = $this->get('form.csrf_provider');

        if($request->isMethod("POST")){
            $csrfToken = $request->get('csrfToken');
            $hidOne = $request->get('hidOne');
            $hidTwo = $request->get('hidTwo');

            if($csrf->isCsrfTokenValid('dedup',$csrfToken)){
                $error = $this->get('mci.patient')->dedupRetain($hidOne,$hidTwo);
                if(empty($error)){
                    $this->get('session')->getFlashBag()->set('dedupFlash','Records has been retained successfully');
                    return new JsonResponse("OK");
                }else{
                    return new JsonResponse($error);
                }
            }
        }
    }

    /**
     * @param Request $request
     */
    public function dedupMergeAction(Request $request){

         $csrf = $this->get('form.csrf_provider');

        if($request->isMethod("POST") && isset($_POST['merge_to'] )){
            $csrfToken = $request->get('csrfToken');
            $_POST['present_address'] = json_decode($request->get('present_address'));

            if($request->get('permanent_address')){
                $_POST['permanent_address'] = json_decode($request->get('permanent_address'));
            }
            if($request->get('relations')){
                $_POST['relations'] = json_decode($request->get('relations'));
            }
            if($request->get('status')){
                $_POST['status'] = json_decode($request->get('status'));
            }

            if($request->get('phone_number')){
                $_POST['phone_number']  = json_decode($request->get('phone_number'));
            }
            if($request->get('primary_contact_number')){
                $_POST['primary_contact_number'] = json_decode($request->get('primary_contact_number'));
            }

            if($csrf->isCsrfTokenValid('dedup',$csrfToken)){

                $error = $this->get('mci.patient')->dedupMerge($_POST);

                if(empty($error)){

                    $this->get('session')->getFlashBag()->set('dedupFlash','Records have been merged successfully');
                }else{
                    $this->get('session')->getFlashBag()->set('dedupFlash','The field (s) is marked for not updatable so record can not be merged');

                }

                return $this->redirect($this->generateUrl('mci_patient_deduplication',array('catchment'=>$request->get('catchment'))));
            }
        }
        return $this->redirect($this->generateUrl('mci_patient_deduplication'));
    }

    /**
     * @param $response
     */
    private function throwingException($response)
    {
        if (empty($response['responseBody'])) {
            throw new NotFoundHttpException("Patient not found");
        }

        if (!empty($response['systemError'])) {
            throw new Exception("Service unavailable", 500);
        }
    }


}
