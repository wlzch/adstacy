<?php

namespace Adstacy\AppBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use JMS\SecurityExtraBundle\Annotation\Secure;

class AdminController extends Controller
{
    /**
     * @Secure(roles="ROLE_SUPER_ADMIN")
     */
    public function blockAdAction($id)
    {
        $ad = $this->getRepository('AdstacyAppBundle:Ad')->find($id);
        if (!$ad) {
            throw $this->createNotFoundException();
        }
        $ad->setActive(false);
        $this->get('adstacy.notification.manager')->save($this->getUser(), $ad->getUser(), $ad, false, 'block_ad');
        $em = $this->getManager();
        $em->persist($ad);
        $em->flush();

        return $this->redirect($this->generateUrl('admin_adstacy_app_ad_list'));
    }

    /**
     * @Secure(roles="ROLE_SUPER_ADMIN")
     */
    public function unblockAdAction($id)
    {
        $ad = $this->getRepository('AdstacyAppBundle:Ad')->find($id);
        if (!$ad) {
            throw new $this->createNotFoundException();
        }
        $ad->setActive(true);
        $this->get('adstacy.notification.manager')->save($this->getUser(), $ad->getUser(), $ad, false, 'unblock_ad');
        $em = $this->getManager();
        $em->persist($ad);
        $em->flush();

        return $this->redirect($this->generateUrl('admin_adstacy_app_ad_list'));
    }
}
