<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Debug\Exception\ContextErrorException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

class FaceBookController extends Controller
{
    /**
     * @Route("/facebook_auth")
     */
    public function loginAction(Request $request)
    {
        $session = $this->get("session");

        if($session->get("_security_main"))
        {
            $route = $this->container->get('router')->generate('main');
            return new RedirectResponse($route);
        }

        if(isset($_GET['code'])){

            $client_id = $this->getParameter("facebook_id");
            $redirect_uri = $this->getParameter("facebook_callback");
            $client_secret = $this->getParameter("facebook_secret_key");
            try {
                $params = array('client_id' => $client_id, 'redirect_uri' => $redirect_uri, 'client_secret' => $client_secret, 'code' => $_GET['code']);
                $url = 'https://graph.facebook.com/oauth/access_token';
                $tokenInfo = null;
                $tokenInfo = json_decode(file_get_contents($url . '?' . trim(urldecode(trim(http_build_query($params))))), true);
                if ($tokenInfo != null && isset($tokenInfo['access_token'])) {
                    $params = array('access_token' => $tokenInfo['access_token']);
                    $userInfo = json_decode(file_get_contents('https://graph.facebook.com/me' . '?' . trim(urldecode(trim(http_build_query($params))))), true);
                    $userManager = $this->get('fos_user.user_manager');


                    $user = $userManager->findUserBy(array('facebookID' => $userInfo['id']));

                    if (!$user) {
                        $user = new User();
                        $user->setPassword("none");
                        $user->setEmail($userInfo['id']."@facebook.com");
                        $user->setUsername($userInfo['name']);
                        $user->setEnabled(true);
                        $user->setFaceBookID($userInfo['id']);
                    }
                    $token = new UsernamePasswordToken($user, null, 'main', $user->getRoles());
                    $this->get("security.token_storage")->setToken($token);
                    $event = new InteractiveLoginEvent($request, $token);
                    $this->get('event_dispatcher')->dispatch('security.interactive_login', $event);


                    return $this->redirectToRoute("main");
                } else {
                    return $this->redirectToRoute("main");
                }
            }catch (\ErrorException $exception){
                $this->redirectToRoute("main");
            }
        }
        else{
            return $this->redirectToRoute("main");
        }
    }

}
