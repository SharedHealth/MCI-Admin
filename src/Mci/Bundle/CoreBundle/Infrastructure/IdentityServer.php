<?php

namespace Mci\Bundle\CoreBundle\Infrastructure;

use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\Request;

class IdentityServer extends ContainerAware
{

    private $baseUrl;
    /**
     * @var
     */
    private $domain;
    /**
     * @var
     */
    private $port;

    public function __construct($domain, $port){

        $this->domain = $domain;
        $this->port = $port;
    }

    private function buildBaseUrl($domain, $port)
    {
        $requestContext = $this->getRequest();

        if( $domain == null) {
            $domain = rtrim($requestContext->getHost());
        }

        if($port != null) {
            $port = ":" . $port;
        }

        return $requestContext->getScheme(). '://' . $domain . $port;
    }

    /**
     * @return string
     */
    public function getBaseUrl()
    {
        if($this->baseUrl == null) {
            $this->baseUrl = $this->buildBaseUrl($this->domain, $this->port);
        }

        return $this->baseUrl;
    }


    /**
     * @return Request
     */
    private function getRequest()
    {
        return $this->container->get('request');
    }

}