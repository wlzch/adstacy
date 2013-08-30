<?php

namespace Adstacy\AppBundle\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use JMS\Serializer\SerializationContext;
use Adstacy\AppBundle\Model\Contact;
use Adstacy\AppBundle\Form\Type\ContactType;

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

    public function contactAction()
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
             
            $this->addFlash('success', 'Your suggestions have already been emailed to our support team. We will response it soon. Thank you.');

            return $this->redirect($this->generateUrl('homepage'));
        }

        return $this->render('AdstacyAppBundle:App:contact.html.twig', array(
            'form' => $form->createView()
        ));
    }
}
