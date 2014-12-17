<?php

namespace Mci\Bundle\PatientBundle\Services;

use Guzzle\Http\Client;

class Location
{
    /**
     * Client
     */
    private $client;

    public function __construct(Client $client, $endpoint)
    {
        $this->client = $client;
       // $this->endpoint = $endpoint;
        $this->endpoint = "http://localhost/sample.json";
    }

    public function getLocation($parent = null)
    {
        $queryParam = array('level' => $parent);
        $request = $this->client->get($this->endpoint, null, array('query' => $queryParam));
        $response = $request->send();
        $responseBody = json_decode($response->getBody());
        return $responseBody->results;
    }

    public function prepairFormData($data){
        foreach($data as $val){
            $newArray[$val->code] = $val->name;
        }
        return $newArray;
    }
}


