<?php

namespace Mci\Bundle\PatientBundle\Controller;

use Guzzle\Http\Exception\RequestException;
use Mci\Bundle\PatientBundle\Form\PatientType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Request;
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

        $results = array();
        $responseBody = array();
        $client = $this->get('mci.location');
        $divisions =  $this->getJsonData('division.json');
        $districts =  $this->getJsonData('district.json');
        $upazilas =  $this->getJsonData('upazilla-single.json');

        if ('GET' === $request->getMethod()) {
            try{
                $queryParam = array();
                $district = null;

                if($request->get('hid')){
                    $hid = trim($request->get('hid'));
                    return $this->redirect($this->generateUrl('mci_patient_showpage', array('id'=>$hid)));
                }

                if($request->get('nid')){
                    $queryParam['nid'] = trim($request->get('nid'));
                }

                if($request->get('uid')){
                    $queryParam['uid'] = trim($request->get('uid'));
                }

                if($request->get('brn')){
                    $queryParam['bin_brn'] = trim($request->get('brn'));
                }

                if($request->get('given_name')){
                    $queryParam['given_name'] = trim($request->get('given_name'));
                }

                if($request->get('sur_name')){
                    $queryParam['sur_name'] = trim($request->get('sur_name'));
                }

                if($request->get('dob')){
                    $queryParam['date_of_birth'] = trim($request->get('dob'));
                }
                if($request->get('district_id')){
                   $district =  str_pad($request->get('district_id'),2,'0',STR_PAD_LEFT);
                }
                $location = $request->get('division_id').$district.$request->get('upazilla_id');

                if($request->get('district_id')){
                    $queryParam['present_address'] = $location;
                }

                $forSeletedDropdown = array('division'=>$request->get('division_id'),'district'=>$request->get('district_id'),'upazilla'=>$request->get('upazilla_id'));

                $client = $this->get('mci_patient.client');
                $request = $client->get($this->container->getParameter('api_end_point'), null, array('query' =>$queryParam ));
                $response = $request->send();
                $responseBody = json_decode($response->getBody());
            } catch(RequestException $e){
                $e->getMessage();
            }
            return $this->render('MciPatientBundle:Patient:search.html.twig',array('responseBody' => $responseBody,'queryparam'=>$queryParam,'divisions'=>$divisions,'districts'=>$districts,'upazilas'=>$upazilas,'seletedDropdown'=>$forSeletedDropdown));
        }

        return $this->render('MciPatientBundle:Patient:search.html.twig',array('divisions'=>$divisions,'districts'=>$districts,'upazillas'=>$upazilas));
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
        $form = $this->createForm(new PatientType(),$data = array('test'));

        return $this->render('MciPatientBundle:Patient:edit.html.twig', array(
            'form' => $form->createView()

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

        return $this->render('MciPatientBundle:Patient:show.html.twig',array('responseBody' => $responseBody));
    }

    private function getJsonData($fileName)
    {
        $filePath =  'assets/json/'.$fileName;
        return  json_decode(file_get_contents($filePath), true);
    }

}
