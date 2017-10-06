<?php
namespace AppBundle\Service;


use Abraham\TwitterOAuth\TwitterOAuth;
use Abraham\TwitterOAuth\TwitterOAuthException;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class OAuthService
{

    private $session;
    private $container;

    public function __construct(SessionInterface $session, ContainerInterface $container)
    {
        $this->session = $session;
        $this->container = $container;
    }

    public function generateVkUrl(){
        $client_id = $this->container->getParameter("vk_secret_id");
        $redirect_uri = $this->container->getParameter("vk_callback");

        $params = array('client_id'=>$client_id,'redirect_uri'=>$redirect_uri,'response_type'=>'code');

        return $this->container->getParameter("vk_oauth_url")."?".urldecode(http_build_query($params));
    }

    public function generateFacebookUrl(){
        $facebook_client_id = $this->container->getParameter("facebook_id");
        $redirect_uri = $this->container->getParameter("facebook_callback");

        $params = array('client_id'=>$facebook_client_id,'redirect_uri'=>$redirect_uri,'response_type'=>'code');

        return $this->container->getParameter("facebook_oauth_url")."?".urldecode(http_build_query($params));
    }

    public function generateTwitterUrl(){
        $twitter_consumer_key = $this->container->getParameter("twitter_consumer_key");
        $twitter_consumer_secret = $this->container->getParameter("twitter_consumer_secret");
        $callback_uri = $this->container->getParameter("twitter_callback");

        try {
            $twitteroauth = new TwitterOAuth($twitter_consumer_key, $twitter_consumer_secret);


            $request_token = $twitteroauth->oauth(
                'oauth/request_token', [
                    'oauth_callback' => $callback_uri
                ]
            );

            $this->session->set('oauth_token', $request_token['oauth_token']);
            $this->session->set('oauth_token_secret', $request_token['oauth_token_secret']);

            $this->session->save();
            $twitterUrl = $twitteroauth->url(
                'oauth/authorize', [
                    'oauth_token' => $request_token['oauth_token']
                ]
            );
            return $twitterUrl;
        }catch (TwitterOAuthException $exception){
            return "/";
        }
    }
}