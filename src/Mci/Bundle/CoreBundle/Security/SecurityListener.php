<?php


namespace Mci\Bundle\CoreBundle\Security;

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

	public function __construct($baseUrl, $port)
    {
	    $this->loginUrl = $baseUrl.':'.$port.'/loginForm?redirectTo=%s';
		$this->loginUrl = sprintf($this->loginUrl, $this->getCurrentUrl());
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
		$event->setResponse(new RedirectResponse($this->loginUrl));

		return $event;
	}

	/**
	 * @return string
	 */
	private function getCurrentUrl()
	{
		$requestContext = $this->router->getContext();

		$route = $this->router->match($requestContext->getPathInfo());

		return $this->router->generate($route['_route'], $requestContext->getParameters(), Router::ABSOLUTE_URL);
	}
}