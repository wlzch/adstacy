<?php

namespace Adstacy\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller as BaseController;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;

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

    /**
     * Get doctrine paginator
     *
     * @param QueryBuilder|Query
     * @param integer $maxPerPage
     * @param integer $currentPage
     *
     * @return Pagerfanta
     */
    public function getDoctrinePaginator($query, $maxPerPage, $currentPage = null)
    {
        $adapter = new DoctrineORMAdapter($query);
        $paginator = new Pagerfanta($adapter);
        $paginator->setMaxPerPage($maxPerPage);
        if ($currentPage) {
            $paginator->setCurrentPage($currentPage);
        } else {
            $paginator->setCurrentPage($this->getRequest()->query->get('page') ?: 1);
        }

        return $paginator;
    }

    /**
     * Translate a message
     *
     * @param string keyword|word
     * @param array replacements
     *
     * @return string translated message
     */
    public function translate($keyword, $replacements = array())
    {
        return $this->get('translator')->trans($keyword, $replacements);
    }
}
