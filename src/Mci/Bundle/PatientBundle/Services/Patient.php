<?php

namespace Mci\Bundle\PatientBundle\Services;

use Guzzle\Service\Client;

class Patient
{
    /**
     * @var Client
     */
    private $client;
    private $endpoint;

    public function __construct(Client $client, $endpoint) {

        $this->client = $client;
        $this->endpoint = $endpoint;
    }

    public function findPatientByRequestQueryParameter($query)
    {
        $queryParam = $this->getQueryParam($query);

        if (empty($queryParam)) {
            return array();
        }

        $request = $this->client->get($this->endpoint, null, array('query' => $queryParam));
        $response = $request->send();

        return json_decode($response->getBody());
    }

    /**
     * @param $query
     * @return array
     */
    private function getQueryParam($query)
    {
        $queryParam = array_filter(array_map('trim', $query));

        $district = null;

        if (isset($queryParam['district_id']) && !empty($queryParam['district_id'])) {
            $district = str_pad($queryParam['district_id'], 2, '0', STR_PAD_LEFT);
            $queryParam['present_address'] = $queryParam['division_id'] . $district . $queryParam['upazilla_id'];
            unset($queryParam['division_id']);
            unset($queryParam['district_id']);
            unset($queryParam['upazilla_id']);
        }

        return $queryParam;
    }
}