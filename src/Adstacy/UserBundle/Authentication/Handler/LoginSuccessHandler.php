<?php

namespace Adstacy\UserBundle\Authentication\Handler;

use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Router;
use Symfony\Component\HttpFoundation\Cookie;

class LoginSuccessHandler implements AuthenticationSuccessHandlerInterface
{
	
	protected $router;
	
	public function __construct(Router $router)
	{
		$this->router = $router;
	}
	
	public function onAuthenticationSuccess(Request $request, TokenInterface $token)
	{
        $response = new RedirectResponse($this->router->generate('homepage'));
        $response->headers->setCookie(new Cookie('logged_in', 'true'));

        return $response;
	}
	
}
