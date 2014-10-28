<?php

namespace Mci\Bundle\PatientBundle\Services;



class Location {
   public $container;

   public function __construct($servicecontainer){
       $this->container = $servicecontainer;
   }

   public function getLocations($url = ''){
        try{
            $client = $this->container->get('mci_patient.client');
            $client->setDefaultOption('headers',
                array(
                    'X-Auth-Token' => $this->container->getParameter('location_auth_key'),
                    'Content-Type'=>'application/json'
                )
            );

            $request = $client->get($this->container->getParameter('location_api_end_point').'/'.$url);
            $response = $request->send();

            return $response->getBody();

        } catch(RequestException $e){
            echo  $e->getMessage();
        }
    }

}
