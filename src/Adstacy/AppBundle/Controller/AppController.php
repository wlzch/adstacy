<?php

namespace Adstacy\AppBundle\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use JMS\Serializer\SerializationContext;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Adstacy\AppBundle\Model\Contact;
use Adstacy\AppBundle\Entity\User;
use Adstacy\AppBundle\Form\Type\ContactType;
use Adstacy\UserBundle\Form\Type\RegistrationFormType;
use Adstacy\AppBundle\Helper\Twitter;

class AppController extends Controller
{
    public function indexAction()
    {
        if (!$this->get('security.context')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
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
        $limit = $this->getParameter('max_ads_per_page');
        $ads = $this->getRepository('AdstacyAppBundle:Ad')->findAdsSinceId($request->query->get('id'), $limit);

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
        $id = $this->getRequest()->query->get('id');
        $limit = $this->getParameter('max_ads_per_page');
        $ads = $this->getRepository('AdstacyAppBundle:Ad')->findUserStream($user, $id, $limit);

        $suggestionsPaginator = array();
        $users = array();
        if ($user->getFollowingsCount() <= 0) {
            $users = $this->getRecommendations($user);
        }

        return $this->render('AdstacyAppBundle:App:stream.html.twig', array(
            'ads' => $ads,
            'users' => $users,
            'user' => $user
        ));
    }

    /**
     * Show trending ads
     */
    public function trendingAction()
    {
        $since = date('Y-m-d', strtotime('-7 day', time())); // 7 days ago
        $ads = $this->getRepository('AdstacyAppBundle:Ad')->findTrendingPromotes();

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

    /**
     * @Secure(roles="ROLE_USER")
     */
    public function whoToFollowAction()
    {
        $users = $this->getRecommendations($this->getUser());

        return $this->render('AdstacyAppBundle:App:who_to_follow.html.twig', array(
            'users' => $users
        ));
    }

    private function getRecommendations(User $user)
    {
        $redis = $this->get('snc_redis.default');
        $request = $this->getRequest();
        $page = $request->query->get('page') ?: 1;
        $max = $this->getParameter('max_who_to_follow');
        $start = ($page - 1) * $max;
        $end = $start + ($max - 1);

        $ids = $redis->zrevrange('recommendation:'.$user->getUsername(), $start, $end);
        $users = array();
        if (count($ids) > 0) {
            $users = $this->getRepository('AdstacyAppBundle:User')->findById($ids);
        }

        return $users;
    }

    /**
     * @Secure(roles="ROLE_USER")
     */
    public function whoToFollowFacebookAction()
    {
        $facebook = $this->get('facebook');
        $user = $this->getUser();
        $request = $this->getRequest();
        $repo = $this->getRepository('AdstacyAppBundle:User');

        $loginUrl = $facebook->getLoginUrl(array(
            'redirect_uri' => $this->generateUrl('adstacy_app_who_to_follow_facebook', array(), true),
            'scope' => 'read_friendlists'
        ));

        if ($request->query->has('code')) {
            $accessToken = $facebook->getAccessToken();
            $user->setFacebookAccessToken($accessToken);
            $em = $this->getManager();
            $em->persist($user);
            $em->flush();
        }

        if ($user->getFacebookAccessToken()) {
            try {
                $facebookAPI = $this->get('adstacy.oauth.facebook_api');
                $facebookIds = $facebookAPI->getFriends($user->getFacebookAccessToken());

                $users = $repo->findByFacebookId($facebookIds);
                $followings = $user->getFollowings()->toArray();

                $usersToSuggest = array();
                foreach ($users as $user) {
                    if (!in_array($user, $followings, true)) {
                        $usersToSuggest[] = $user;
                    }
                }

                return $this->render('AdstacyAppBundle:App:who_to_follow.html.twig', array(
                    'users' => $usersToSuggest
                ));
            } catch (\FacebookApiException $exception) {
                return $this->redirect($loginUrl);
            }
        }

        return $this->redirect($loginUrl);
    }

    /**
     * @Secure(roles="ROLE_USER")
     */
    public function whoToFollowTwitterAction()
    {
        $user = $this->getUser();
        $twitterAPI = $this->get('adstacy.oauth.twitter_api');
        $followingIds = $twitterAPI->getFollowings($user->getTwitterId());
        $users = $this->getRepository('AdstacyAppBundle:User')->findByTwitterId($followingIds);

        $usersToSuggest = array();
        $followings = $user->getFollowings()->toArray();
        foreach ($users as $user) {
            if (!in_array($user, $followings, true)) {
                $usersToSuggest[] = $user;
            }
        }

        return $this->render('AdstacyAppBundle:App:who_to_follow.html.twig', array(
            'users' => $usersToSuggest
        ));
    }
}
