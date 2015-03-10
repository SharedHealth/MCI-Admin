<?php

namespace Mci\Bundle\UserBundle\Service;

use Guzzle\Http\Client;
use Guzzle\Http\Message\RequestInterface;
use JMS\Serializer\Serializer;

class SsoClient
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

        $userResponse = $this->handleRequestToServer(
            $this->client->get('token/' . $accessToken)
        );

        if ($userResponse == null) {
            return null;
        }

        return $this->serializer->deserialize($userResponse, 'Mci\Bundle\UserBundle\Security\User', 'json');
    }

    /**
     * @param Serializer $serializer
     */
    public function setSerializer($serializer)
    {
        $this->serializer = $serializer;
    }
}