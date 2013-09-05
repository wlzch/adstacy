<?php

namespace Adstacy\AppBundle\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use JMS\Serializer\SerializationContext;
use Adstacy\AppBundle\Model\Contact;
use Adstacy\AppBundle\Form\Type\ContactType;
use JMS\SecurityExtraBundle\Annotation\Secure;

class AppController extends Controller
{
    public function indexAction()
    {
        $request = $this->getRequest();
        $maxAd = $this->getParameter('max_ads_per_page');
        $ads = $this->getRepository('AdstacyAppBundle:Ad')->findAdsSinceId($request->query->get('id'), $maxAd);

        return $this->render('AdstacyAppBundle:App:index.html.twig', array(
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

        return $this->render('AdstacyAppBundle:App:stream.html.twig', array(
            'paginator' => $paginator
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

    public function contactUsAction()
    {
        $contact = new Contact();
        $form = $this->createForm(new ContactType(), $contact);
        $form->handleRequest($this->getRequest());

        if ($form->isValid()) {
            $message = \Swift_Message::newInstance()
                ->setSubject($contact->getSubject())
                ->setFrom($contact->getEmail())
                ->setTo($this->getParameter('adstacy.mail.support'))
                ->setBody($contact->getContent())
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
