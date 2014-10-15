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

    public function searchAction( Request $request)
    {
        $results = array();
        $responseBody = array();

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

                $client = $this->get('mci_patient.client');
                $request = $client->get($this->container->getParameter('api_end_point'), null, array('query' =>$queryParam ));
                $response = $request->send();
                $responseBody = json_decode($response->getBody());
            } catch(RequestException $e){
               echo $e->getMessage();
            }
            return $this->render('MciPatientBundle:Patient:search.html.twig',array('responseBody' => $responseBody,'queryparam'=>$queryParam));
        }

        return $this->render('MciPatientBundle:Patient:search.html.twig');
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
