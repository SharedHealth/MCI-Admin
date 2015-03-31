<?php

namespace Mci\Bundle\PatientBundle\Services;

use Guzzle\Http\Client;
use Mci\Bundle\CoreBundle\Service\CacheAwareService;
use Mci\Bundle\UserBundle\Security\User;
use Symfony\Component\Security\Core\SecurityContext;

class Location extends CacheAwareService
{
    /**
     * Client
     */
    private $client;

    private $endpoint = "locations";

    public function __construct(Client $client, SecurityContext $securityContext)
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
     * @return \Guzzle\Http\EntityBodyInterface|mixed|null|string
     */
    public function getChildLocations($parent = null)
    {
        $key = empty($parent) ? $parent : '0';

        if(false === $locations = $this->getCache()->fetch($key)) {
            $locations = $this->ensureCaching($parent);
        }

        return $locations;

    }

    /**
     * @param int $parent
     * @return \Guzzle\Http\EntityBodyInterface|mixed|null|string
     */
    private function ensureCaching($parent = null)
    {
        $locations = $this->getChildLocationsFromApi($parent);

        if ($locations == null) {
            return null;
        }

        $key = empty($parent) ? $parent : '0';

        $this->getCache()->save($key, $locations, 3600);

        return $locations;
    }

    public function prepareFormData($data){
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
     * @param SecurityContext $securityContext
     * @return User|null
     */
    private function getUser(SecurityContext $securityContext)
    {
        if ($securityContext && $securityContext->getToken()) {
            $user = $securityContext->getToken()->getUser();

            if ($user instanceof User) {
                return $user;
            }
        }
    }

    /**
     * @param $parent
     * @return null
     */
    private function getChildLocationsFromApi($parent)
    {
        $queryParam = array('parent' => $parent);

        try {
            $request = $this->client->get($this->endpoint, null, array('query' => $queryParam));
            $response = $request->send();
            $responseBody = json_decode($response->getBody());
            return $responseBody->results;
        } catch (\Exception $e) {
            return null;

        }
    }
}


