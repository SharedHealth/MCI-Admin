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

    private function buildWebBaseUrl($domain, $port)
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

    private function buildApiBaseUrl($port)
    {
        if($port != null) {
            $port = ":" . $port;
        }

        return $this->getRequest()->getScheme(). '://' . "localhost" . $port;

    }

    /**
     * @return string
     */
    public function getWebBaseUrl()
    {
        if($this->baseUrl == null) {
            $this->baseUrl = $this->buildWebBaseUrl($this->domain, $this->port);
        }

        return $this->baseUrl;
    }

    /**
     * @return string
     */
    public function getApiBaseUrl()
    {
        if($this->domain == null) {
            return $this->buildApiBaseUrl($this->port);
        }

        return $this->getWebBaseUrl();
    }


    /**
     * @return Request
     */
    private function getRequest()
    {
        return $this->container->get('request');
    }

}