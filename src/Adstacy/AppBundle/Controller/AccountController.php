<?php

namespace Adstacy\AppBundle\Controller;

use JMS\SecurityExtraBundle\Annotation\Secure;

class AccountController extends Controller
{
    /**
     * Disconnect social account
     *
     * @param string $service
     */
    public function disconnectAction($service)
    {
        $user = $this->getUser();
        $getter = 'get'.ucfirst($service).'Id';
        if ($user->$getter()) {
            $setter = 'set'.ucfirst($service);
            $setter_id = $setter.'Id';
            $setter_token = $setter.'AccessToken';
            $setter_username = $setter.'Username';
            $setter_real_name = $setter.'RealName';
            $user->$setter_id(null);
            $user->$setter_token(null);
            $user->$setter_username(null);
            $user->$setter_real_name(null);
            $user->removeRole('ROLE_'.strtoupper($service));
            $em = $this->getManager();
            $em->persist($user);
            $em->flush();

            $this->addFlash('success', $this->translate('flash.account.disconnect.success', array('%service%' => $service)));
        } else {
            $this->addFlash('error', $this->translate('flash.account.disconnect.error', array('%service%' => $service)));
        }

        return $this->redirect($this->generateUrl('fos_user_profile_edit'));
    }
}
