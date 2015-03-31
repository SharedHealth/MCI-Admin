<?php

namespace Mci\Bundle\PatientBundle\Services;

use Guzzle\Http\Client;
use Guzzle\Http\Exception\BadResponseException;
use Guzzle\Http\Exception\CurlException;
use Guzzle\Http\Exception\RequestException;
use Mci\Bundle\CoreBundle\Service\CacheAwareService;
use Mci\Bundle\PatientBundle\Utills\Utility;
use Mci\Bundle\UserBundle\Security\User;
use Symfony\Component\Security\Core\SecurityContext;

class Location extends CacheAwareService
{
    /**
     * Client
     */
    private $client;

    private $endpoint = "locations";

    public function __construct(Client $client, $securityContext)
    {
        $this->client = $client;
        $user = $this->getUser($securityContext);

        if (null !== $user) {
            $this->client->setDefaultOption('headers/X-Auth-Token', $user->getToken());
            $this->client->setDefaultOption('headers/client_id', $user->getId());
            $this->client->setDefaultOption('headers/From', $user->getEmail());
        }
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
        if(!is_array($data)) {
            return array();
        }

        $newArray = array();
        foreach($data as $val){
            $newArray[$val->code] = ucfirst(strtolower($val->name));
        }

        return $newArray;
    }

    /**
     * @return null|User
     */
    private function getUser($securityContext)
    {
        if ($securityContext && $securityContext->getToken()) {
            $user = $securityContext->getToken()->getUser();

            if ($user instanceof User) {
                return $user;
            }
        }
    }
}


