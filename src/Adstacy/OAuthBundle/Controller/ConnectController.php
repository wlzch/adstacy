<?php

namespace Adstacy\OAuthBundle\Controller;

use HWI\Bundle\OAuthBundle\Controller\ConnectController as BaseController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Exception\AccountStatusException;

/**
 * Extends the base controller so that user doesnt have to confirm to connect account
 */
class ConnectController extends BaseController
{
    public function connectAction(Request $request)
    {
        $response = parent::connectAction($request);
        if ($response instanceof RedirectResponse) {
            $response->headers->setCookie(new Cookie('logged_in', 'true'));
        }

        return $response;
    }
    public function connectServiceAction(Request $request, $service)
    {
        $connect = $this->container->getParameter('hwi_oauth.connect');
        if (!$connect) {
            throw new NotFoundHttpException();
        }

        $hasUser = $this->container->get('security.context')->isGranted('IS_AUTHENTICATED_REMEMBERED');
        if (!$hasUser) {
            throw new AccessDeniedException('Cannot connect an account.');
        }

        // Get the data from the resource owner
        $resourceOwner = $this->getResourceOwnerByName($service);

        $session = $request->getSession();
        $key = $request->query->get('key', time());

        if ($resourceOwner->handles($request)) {
            $accessToken = $resourceOwner->getAccessToken(
                $request,
                $this->generate('hwi_oauth_connect_service', array('service' => $service), true)
            );

            // save in session
            $session->set('_hwi_oauth.connect_confirmation.'.$key, $accessToken);
        } else {
            $accessToken = $session->get('_hwi_oauth.connect_confirmation.'.$key);
        }

        $userInformation = $resourceOwner->getUserInformation($accessToken);

        $user = $this->container->get('security.context')->getToken()->getUser();

        $this->container->get('hwi_oauth.account.connector')->connect($user, $userInformation);
        $router = $this->container->get('router');
        $request->getSession()->getFlashBag()->add('success', 'You have successfully connected with your '.$service.' account');

        $response = new RedirectResponse($router->generate('fos_user_profile_edit'));
        $response->headers->setCookie(new Cookie('logged_in', 'true'));

        return $response;
    }
}
