<?php

namespace Mci\Bundle\PatientBundle\Controller;

use Guzzle\Http\Exception\RequestException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Request;

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
        $divisions =  $client->getLocations('api/1.0/locations/divisions/list');

        if ('POST' === $request->getMethod()) {

            try{
                $queryParam = array();

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

                $client = $this->get('mci_patient.client');
                $request = $client->get($this->container->getParameter('api_end_point'), null, array('query' =>$queryParam ));
                $response = $request->send();
                $responseBody = json_decode($response->getBody());
            } catch(RequestException $e){
               echo $e->getMessage();
            }
            return $this->render('MciPatientBundle:Patient:search.html.twig',array('responseBody' => $responseBody,'queryparam'=>$queryParam,'divisions'=>json_decode($this->remove_utf8_bom($divisions))));
        }

        return $this->render('MciPatientBundle:Patient:search.html.twig',array('divisions'=>json_decode($this->remove_utf8_bom($divisions))));
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
}
