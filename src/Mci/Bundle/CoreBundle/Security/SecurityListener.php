<?php


namespace Mci\Bundle\CoreBundle\Security;

use Mci\Bundle\CoreBundle\Infrastructure\IdentityServer;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Routing\Router;

class SecurityListener
{

	/**
	 * @var SecurityContext
	 */
	private $securityContext;

	/**
	 * @var Router
	 */
	private $router;
	/**
	 * @var IdentityServer
	 */
	private $identityServer;

	private $loginUrl;

	public function __construct(IdentityServer $identityServer)
    {
		$this->identityServer = $identityServer;
	}



    public function onKernelRequest(GetResponseEvent $event)
    {
		$token = $this->securityContext->getToken();

		if(null === $token) {
			return null;
		}


	    if (!$this->isUserAuthenticated() || $token->getUser()->getUserName() == null) {
			return $this->redirectToLoginPage($event);
	    }

		return $event;
	}


	/**
	 * @return bool
	 */
	protected function isUserAuthenticated() {
		return $this->securityContext->isGranted( 'IS_AUTHENTICATED_FULLY' ) || $this->securityContext->isGranted( 'IS_AUTHENTICATED_REMEMBERED' );
	}


	/**
	 * @param SecurityContext $securityContext
	 */
	public function setSecurityContext($securityContext)
	{
		$this->securityContext = $securityContext;
	}

	/**
	 * @param Router $router
	 */
	public function setRouter($router)
	{
		$this->router = $router;
	}

	private function redirectToLoginPage(GetResponseEvent $event)
	{
		$event->setResponse(new RedirectResponse($this->getLoginUrl()));

		return $event;
	}

	/**
	 * @return mixed
	 */
	public function getLoginUrl()
	{
		if($this->loginUrl == null) {
			$this->loginUrl = $this->identityServer->getWebBaseUrl().'/loginForm?redirectTo=%s';
			$this->loginUrl = sprintf($this->loginUrl, $this->getCurrentUrl());
		}
		return $this->loginUrl;
	}

	/**
	 * @return string
	 */
	private function getCurrentUrl()
	{
		$requestContext = $this->router->getContext();

		$route = $this->router->match($requestContext->getPathInfo());

        $routerName = $route['_route'];
        $route['_route'];

        $parameters = $requestContext->getParameters();
        unset($parameters['_locale']);
        unset($route['_route']);
        unset($route['_controller']);

        $parameters = array_merge($route, $parameters);

        return $this->router->generate($routerName, $parameters, Router::ABSOLUTE_URL);
	}
}