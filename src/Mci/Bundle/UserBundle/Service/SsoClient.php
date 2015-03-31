<?php

namespace Mci\Bundle\UserBundle\Service;

use Guzzle\Http\Client;
use Guzzle\Http\Message\RequestInterface;
use JMS\Serializer\Serializer;
use Mci\Bundle\CoreBundle\Service\CacheAwareService;

class SsoClient extends CacheAwareService
{

    /**
     * @var Serializer
     */

    private $serializer;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function getUserByEmailAndPassword($email, $password)
    {
        return $this->getUserFromToken(
            $this->getAccessTokenFor($email, $password)
        );
    }

    public function getAccessTokenFor($email, $password)
    {
        if ($email == null || $password == null) {
            return null;
        }

        $post_data = array(
            'email'    => $email,
            'password' => $password
        );

        $request = $this->client->post('signin', array(), $post_data);

        $response = $this->handleRequestToServer($request);

        $response = json_decode($response);

        if (null == $response || !isset($response->access_token)) {
            return null;
        }

        return $response->access_token;
    }

    public function handleRequestToServer(RequestInterface $request)
    {
        try {
            $response = $request->send();

            return $response->getBody();
        } catch (\Exception $e) {
            return null;
        }
    }

    public function getUserFromToken($accessToken)
    {
        if ($accessToken == null) {
            return null;
        }

        if(false === $userResponse = $this->getCache()->fetch($accessToken)) {
            $userResponse = $this->ensureCaching($accessToken);
        }

        return $userResponse;
    }

    /**
     * @param Serializer $serializer
     */
    public function setSerializer($serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * @param $accessToken
     * @return \Guzzle\Http\EntityBodyInterface|mixed|null|string
     */
    private function ensureCaching($accessToken)
    {
        $userResponse = $this->handleRequestToServer(
            $this->client->get('token/' . $accessToken)
        );

        if ($userResponse == null) {
            return null;
        }

        $userResponse = $this->serializer->deserialize($userResponse, 'Mci\Bundle\UserBundle\Security\User', 'json');
        $this->getCache()->save($accessToken, $userResponse, 60);

        return $userResponse;
    }
}