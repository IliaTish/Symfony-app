<?php

namespace AppBundle\Controller;

use Abraham\TwitterOAuth\TwitterOAuthException;
use AppBundle\Entity\User;
use Buzz\Message\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Abraham\TwitterOAuth\TwitterOAuth;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

class TwitterController extends Controller
{

    protected $consumer_key = "yw7bfIWEg5pCqXb2JdRmYG6wY";
    protected $consumer_secret = "ramLf23kxko50ggJ91EiRoz3iPtr2YbN99tLwGVhMeXvaKD6iT";
    /**
     * @Route("/twitter_auth")
     */
    public function loginAction(\Symfony\Component\HttpFoundation\Request $request)
    {
        $session = $this->get('session');

        if($session->get("_security_main"))
        {
            $route = $this->container->get('router')->generate('main');
            return new RedirectResponse($route);
        }

        try {
            $request_token = [];
            $request_token['oauth_token'] = $session->get("oauth_token");
            $request_token['oauth_token_secret'] = $session->get('oauth_token_secret');
            $connection = new TwitterOAuth($this->consumer_key, $this->consumer_secret, $request->get("oauth_token"), $request_token['oauth_token_secret']);
            $access_token = $connection->oauth("oauth/access_token", ["oauth_verifier" => $request->get('oauth_verifier')]);
            $connection = new TwitterOAuth($this->consumer_key, $this->consumer_secret, $access_token['oauth_token'], $access_token['oauth_token_secret']);
            $content = $connection->get("account/verify_credentials");

            $jsonContent = json_encode($content);
            $content = json_decode($jsonContent, true);

            $userManager = $this->get('fos_user.user_manager');
            $user = $userManager->findUserBy(array('twitterID' => $content['id']));

            $session->remove("oauth_token");
            $session->remove("oauth_token_secret");

            if (!$user) {
                $user = new User();
                $user->setEmail($content['id']."twitter@email.com");
                $user->setPassword("none");
                $user->setEnabled(true);
                $user->setTwitterID($content['id']);
                $user->setPhotoSrc($content['profile_image_url']);
                $user->setUsername($content['name']);
            }

            $token = new UsernamePasswordToken($user, null, 'main', $user->getRoles());
            $this->get("security.token_storage")->setToken($token);
            $event = new InteractiveLoginEvent($request, $token);
            $this->get('event_dispatcher')->dispatch('security.interactive_login', $event);
            return $this->redirectToRoute("main");
        }catch (TwitterOAuthException $exception){
            return $this->redirectToRoute("main");
        }
    }

}
