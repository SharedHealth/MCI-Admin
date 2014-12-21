<?php

namespace Mci\Bundle\PatientBundle\Services;

use Guzzle\Http\Client;
use Guzzle\Http\Exception\BadResponseException;
use Guzzle\Http\Exception\CurlException;
use Guzzle\Http\Exception\RequestException;
use Mci\Bundle\PatientBundle\Utills\Utility;

class Location
{
    /**
     * Client
     */
    private $client;

    public function __construct(Client $client, $endpoint)
    {
        $this->client = $client;
        $this->endpoint = $endpoint."/locations";
    }

    /**
     * @param null $parent
     */
    public function getLocation($parent = null)
    {
        $queryParam = array('parent' => $parent);
        $SystemAPiError = array();

        try{
            $request = $this->client->get($this->endpoint, null, array('query' => $queryParam));
            $response = $request->send();
            $responseBody = json_decode($response->getBody());
            return $responseBody->results;
        }catch (CurlException $e) {
            $SystemAPiError[] = 'Service Unavailable';
        } catch (BadResponseException $e) {
            $messages = json_decode($e->getResponse()->getBody());
            $SystemAPiError = Utility::getErrorMessages($messages);
        } catch (RequestException $e) {
            $SystemAPiError[] = 'Something went wrong';
        }

    }

    public function prepairFormData($data){
        $newArray = array();
        foreach($data as $val){
            $newArray[$val->code] = $val->name;
        }
        return $newArray;
    }
}


