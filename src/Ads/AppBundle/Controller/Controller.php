<?php

namespace Ads\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller as BaseController;

class Controller extends BaseController
{
    /**
     * Get doctrine manager
     *
     * @return EntityManager
     */
    public function getManager()
    {
        return $this->getDoctrine()->getManager();
    }

    /**
     * Get repository
     *
     * @param string repository name (BundleName:RepoName)
     *
     * @return EntityRepository
     */
    public function getRepository($repo)
    {
        return $this->getManager()->getRepository($repo);
    }

    /**
     * Get session
     *
     * @return Session
     */
    public function getSession()
    {
        return $this->getRequest()->getSession();
    }

    /**
     * Get container parameter
     *
     * @param string parameter key
     *
     * @return string parameter value
     */
    public function getParameter($key)
    {
        return $this->container->getParameter($key);
    }

    /**
     * Add flash
     *
     * @param string flash key
     * @param string flash value
     */
    public function addFlash($key, $value)
    {
        $this->getSession()->getFlashBag()->add($key, $value);
    }
}
