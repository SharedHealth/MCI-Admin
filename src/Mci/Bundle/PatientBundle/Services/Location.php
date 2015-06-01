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
        $key = !empty($parent) ? $parent : '0';

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

        $key = !empty($parent) ? $parent : '0';

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

    public function getParentlocation($request)
    {
        $districts = array();
        $upazilas = array();
        $citycorporations = array();
        $unions = array();
        $wards = array();
        $responseBody = array();

        $division_code = $request->get('division_id');
        $district_code = $request->get('district_id');
        $upazila_code = $request->get('upazila_id');
        $citycorporation_code = $request->get('citycorporation_id');
        $union_code = $request->get('union_id');
        $ward_code = $request->get('ward_id');

        $divisions = $this->getChildLocations();

        if ($division_code) {
            $districts = $this->getChildLocations($division_code);
        }

        if ($district_code && $division_code) {
            $upazilas = $this->getChildLocations($division_code.$district_code);
        }
        if ($district_code && $division_code && $upazila_code  ) {
            $citycorporations = $this->getChildLocations($division_code.$district_code.$upazila_code);
        }
        if ( $division_code && $district_code && $upazila_code && $citycorporation_code  && $union_code ) {
            $unions = $this->getChildLocations($division_code.$district_code.$upazila_code.$citycorporation_code);
        }
        if ( $division_code && $district_code && $upazila_code && $citycorporation_code && $union_code && $ward_code ) {
            $wards = $this->getChildLocations($division_code.$district_code.$upazila_code.$citycorporation_code.$union_code);
        }

        return array( $divisions,$districts,$upazilas,$citycorporations, $unions,$wards);
    }
}


