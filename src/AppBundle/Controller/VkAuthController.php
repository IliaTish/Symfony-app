<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Entity\VkUser;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

class VkAuthController extends Controller
{


    /**
     * @Route("/vk_auth", name="vk_auth")
     */
    public function loginAction(Request $request)
    {
        $session = $this->get('session');

        if($session->get("_security_main")) {
            $route = $this->container->get('router')->generate('main');
            return new RedirectResponse($route);
        }


        if(isset($_GET['code'])){

            $url = "http://oauth.vk.com/access_token";

            $vkAuthId = $this->getParameter("vk_secret_id");
            $vkAuthSecureKey = $this->getParameter("vk_secret_key");
            $redirect_url = $this->getParameter("vk_callback");

            $params = array('client_id'=> $vkAuthId, 'redirect_uri'=> $redirect_url, 'code'=> $_GET['code'], 'client_secret' => $vkAuthSecureKey);
            $link = $url.'?'.urldecode(http_build_query($params));

            $token = json_decode(file_get_contents($link,true),true);
            if(isset($token['access_token'])){
                $params = array('uids' => $token['user_id'],'fields'=> 'uid,first_name,last_name,screen_name,sex,bdate,photo_big','access_token'=>$token['access_token']);
                $userInfo = json_decode(file_get_contents('https://api.vk.com/method/users.get' . '?' . urldecode(http_build_query($params))), true);


                $userManager =  $this->get('fos_user.user_manager');
                $user = $userManager->findUserBy(array('vkID'=>$userInfo['response'][0]['uid']));


                if(!$user){
                    $user = new User();
                    $user->setPassword("none");
                    $user->setEmail($userInfo['response'][0]['uid']."@vk.com");
                    $user->setUsername($userInfo['response'][0]['first_name'] . " " . $userInfo['response'][0]['last_name']);
                    $user->setEnabled(true);
                    $user->setVkID($userInfo['response'][0]['uid']);
                    $user->setbDate($userInfo['response'][0]['bdate']);
                    $user->setPhotoSrc($userInfo['response'][0]['photo_big']);
                    $user->setSex($userInfo['response'][0]['sex']);
                }


                $token = new UsernamePasswordToken($user,null,'main',$user->getRoles());
                $this->get("security.token_storage")->setToken($token);
                $event = new InteractiveLoginEvent($request,$token);
                $this->get('event_dispatcher')->dispatch('security.interactive_login', $event);


                return $this->redirectToRoute("main");
            }
        }
        else{
            return $this->redirectToRoute("main");
        }
    }

}
