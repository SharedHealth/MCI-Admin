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
        $baseUrl =  $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath();
        $results = array();
        $districts = array();
        $upazillas = array();
        $responseBody = array();
        $client = $this->get('mci.location');
        $divisions =  $this->getJsonData('division.json');
        $districtsAll =  $this->getJsonData('district.json');

       if($request->get('division_id')){
             $hrm_division_id = $this->getLocationId($divisions,$request->get('division_id'));
             $districtUrl = $this->generateUrl('mci_location_district', array('id'=>$hrm_division_id));
             $districts = json_decode(file_get_contents($baseUrl.$districtUrl));
       }

       if($request->get('district_id')){
             $hrm_district_id = $this->getLocationId($districtsAll,$request->get('district_id'));
             $upazillaUrl = $this->generateUrl('mci_location_upazilla', array('id'=> $hrm_district_id));
             $upazillas = json_decode(file_get_contents($baseUrl.$upazillaUrl));
       }


        $upazilas =  $this->getJsonData('upazilla-single.json');

        $SystemAPiError = '';

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

               if(!empty($queryParam)){
                   $client = $this->get('mci_patient.client');
                   $request = $client->get($this->container->getParameter('api_end_point'), null, array('query' =>$queryParam ));
                   $response = $request->send();
                   $responseBody = json_decode($response->getBody());

                }

            } catch(RequestException $e){
                  $messages =  json_decode($e->getResponse()->getBody());

                $SystemAPiError = $this->getErrorMessages($messages);

            }
            return $this->render('MciPatientBundle:Patient:search.html.twig',array('responseBody' => $responseBody,'queryparam'=>$queryParam,'divisions'=>$divisions,'districts'=>(array)$districts,'upazillas'=>(array)$upazillas,'seletedDropdown'=>$forSeletedDropdown,'systemError'=>$SystemAPiError));
        }

        return $this->render('MciPatientBundle:Patient:search.html.twig',array('divisions'=>$divisions,'districts'=>(array)$districts,'upazillas'=>(array)$upazillas,'systemError'=>$SystemAPiError));
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

}
