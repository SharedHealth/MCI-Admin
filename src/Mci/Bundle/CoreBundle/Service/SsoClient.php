<?php


namespace Mci\Bundle\CoreBundle\Service;
use Guzzle\Http\Client;
use JMS\Serializer\Serializer;

class SsoClient
{
    /**
     * @var Client
     */
    private $client;

    /**
     * @var Serializer
     */

    private $serializer;


    private $endPoint;


    public function __construct(Client $client, Serializer $serializer, $endpoint,$port) {

        $this->client = $client;
        $this->serializer = $serializer;
        $this->endPoint = $endpoint.':'.$port.'/userInfo';
    }

    public function getUserFromToken($session)
    {
        $userResponse = $this->handleRequestToServer($this->endPoint . '/' . $session);

        if($userResponse == null) {
            return null;
        }

        $user = $this->serializer->deserialize($userResponse, 'Mci\Bundle\CoreBundle\Security\User', 'json');
        $user->setToken($session);

        return $user;
    }

    public function handleRequestToServer($url) {
        $request = $this->client->get($url);

        try{
            $response = $request->send();
            return $response->getBody();
        }catch (\Exception $e) {
            return null;
        }
    }
}