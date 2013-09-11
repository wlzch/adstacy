<?php

namespace Adstacy\AppBundle\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use JMS\Serializer\SerializationContext;
use Adstacy\AppBundle\Model\Contact;
use Adstacy\AppBundle\Form\Type\ContactType;
use Adstacy\UserBundle\Form\Type\RegistrationFormType;
use JMS\SecurityExtraBundle\Annotation\Secure;

class AppController extends Controller
{
    public function indexAction()
    {
        if (!$this->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY')) {
            /** @var $formFactory \FOS\UserBundle\Form\Factory\FactoryInterface */
            $formFactory = $this->container->get('fos_user.registration.form.factory');
            $form = $formFactory->createForm();

            return $this->render('AdstacyAppBundle:App:landing.html.twig', array(
                'form' => $form->createView()
            ));
        }

        return $this->streamAction();
    }

    public function exploreAction()
    {
        $request = $this->getRequest();
        $maxAd = $this->getParameter('max_ads_per_page');
        $ads = $this->getRepository('AdstacyAppBundle:Ad')->findAdsSinceId($request->query->get('id'), $maxAd);

        return $this->render('AdstacyAppBundle:App:explore.html.twig', array(
            'ads' => $ads
        ));
    }

    /**
     * @Secure(roles="ROLE_USER")
     *
     * Show all ads created by loggedin user, ads promoted by loggedin user, ads by loggedin user's followings and ads promoted by loggedin user's followings
     */
    public function streamAction()
    {
        $user = $this->getUser();
        $query = $this->getRepository('AdstacyAppBundle:Ad')->findUserStreamQuery($user);
        $paginator = $this->getDoctrinePaginator($query, $this->getParameter('max_ads_per_page'));
        $suggestionsPaginator = array();
        if ($user->getFollowingsCount() <= 0) {
            $suggestQuery = $this->getRepository('AdstacyAppBundle:User')->suggestUserQuery($user);
            $suggestionsPaginator = $this->getDoctrinePaginator($suggestQuery, 10);
        }

        return $this->render('AdstacyAppBundle:App:stream.html.twig', array(
            'paginator' => $paginator,
            'suggestionsPaginator' => $suggestionsPaginator,
            'user' => $user
        ));
    }

    /**
     * Show trending ads
     */
    public function trendingAction()
    {
        $since = date('Y-m-d', strtotime('-7 day', time())); // 7 days ago
        $ads = $this->getRepository('AdstacyAppBundle:Ad')->findTrendingSince($since);

        return $this->render('AdstacyAppBundle:App:trending.html.twig', array(
            'ads' => $ads
        ));
    }

    /**
     * Show tags
     */
    public function tagsAction()
    {
        return $this->render('AdstacyAppBundle:App:tags.html.twig');
    }

    public function contactUsAction()
    {
        $contact = new Contact();
        $form = $this->createForm(new ContactType(), $contact);
        $form->handleRequest($this->getRequest());

        if ($form->isValid()) {
            // temporary hack
            $message = \Swift_Message::newInstance()
                ->setSubject($contact->getSubject())
                ->setFrom($this->getParameter('adstacy.mail.support'))
                ->setTo($this->getParameter('adstacy.mail.support'))
                ->setBody($contact->getEmail().$contact->getContent())
            ;
            $this->get('mailer')->send($message);

            $this->addFlash('success', $this->translate('flash.contact.success'));

            return $this->redirect($this->generateUrl('homepage'));
        }

        return $this->render('AdstacyAppBundle:App:contact_us.html.twig', array(
            'form' => $form->createView()
        ));
    }
}
