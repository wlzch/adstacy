<?php

namespace Adstacy\AppBundle\Twig;

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Form extension
 */
class FormExtension extends \Twig_Extension
{
    private $container;
    
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getFunctions()
    {
        return array(
            'form_login' => new \Twig_Function_Method($this, 'renderLoginForm', array('is_safe' => array('html'))),
        );
    }

    /**
     * Render login form
     */
    public function renderLoginForm()
    {
        $csrfToken = $this->container->has('form.csrf_provider')
            ? $this->container->get('form.csrf_provider')->generateCsrfToken('authenticate')
            : null;

        return $this->container->get('templating')->render('AdstacyUserBundle:Includes:login.html.twig', array(
            'csrf_token' => $csrfToken 
        ));
    }

    public function getName()
    {
        return 'adstacy_form_extension';
    }
}
