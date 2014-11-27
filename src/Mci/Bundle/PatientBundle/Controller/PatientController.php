<?php

namespace Mci\Bundle\PatientBundle\Controller;

use Guzzle\Http\Exception\BadResponseException;
use Guzzle\Http\Exception\CurlException;
use Guzzle\Http\Exception\RequestException;
use Mci\Bundle\PatientBundle\Form\PatientType;
use Mci\Bundle\PatientBundle\FormMapper\Patient;
use Mci\Bundle\PatientBundle\FormMapper\Relation;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints\DateTime;
use Mci\Bundle\PatientBundle\Lib\Utility;


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
        $upazillas = array();
        $responseBody = array();

        $divisions = Utility::getJsonData('division.json');
        $districtsAll = Utility::getJsonData('district.json');
        $client = $this->get('mci_patient.client');
        $locationService = $this->container->get('mci.location');

        if ($request->get('division_id')) {
            $hrm_division_id = $locationService->getLocationId($divisions, $request->get('division_id'));
            $districts = $locationService->getDistrict($hrm_division_id);
        }

        if ($request->get('district_id')) {
            $hrm_district_id = $locationService->getLocationId($districtsAll, $request->get('district_id'));
            $upazillas = $locationService->getUpazilla($hrm_district_id);
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
                'upazillas' => (array)$upazillas,
                'systemError' => $SystemAPiError,
                'searchString' => $this->get('mci.patient')->getSearchParameterAsString($queryParam)
            ));
    }

    public function editAction(Request $request, $id){

        $patient = $this->get('mci.patient')->getPatientById($id);
        if($patient['systemError']){
            throw $this->createNotFoundException('Service Unavailable');
        }

        if (!json_decode($patient['responseBody'])) {
            throw $this->createNotFoundException('Unable to find patient');
        }

        $object = $this->get('mci.patient')->getFormMappingObject($patient['responseBody']);

        $form = $this->createForm(new PatientType($this->container,$object), $object);

        return $this->render('MciPatientBundle:Patient:edit.html.twig', array(
            'form' => $form->createView(),
            'hid'  => $id

        ));
    }

    public function updateAction(Request $request, $id){

        $postData = array_filter($request->request->get('mci_bundle_patientBundle_patients'));
        $postData = Utility::filterRelations($postData);
        $postData = Utility::filterPhoneNumber($postData);
        $postData = Utility::filterAddress($postData);
        $postData = Utility::unsetUnessaryData($postData);
        $errors = $this->get('mci.patient')->updatePatientById($id, $postData);
        $patient = $this->get('mci.patient')->getPatientById($id);
        $object = $this->get('mci.patient')->getFormMappingObject($patient['responseBody']);
        $form = $this->createForm(new PatientType($this->container, $object), $object);

        return $this->render('MciPatientBundle:Patient:edit.html.twig', array(
            'form' => $form->createView(),
            'hid' => $id,
            'errors' => $errors
        ));

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


}
