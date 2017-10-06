<?php
namespace AppBundle\Service;
use Psr\Container\ContainerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;


class OnlineService
{
    private $tokenStrorage;
    private $container;
    public function __construct(TokenStorageInterface $tokenStorage, ContainerInterface $container)
    {
        $this->tokenStrorage = $tokenStorage;
        $this->container = $container;
    }

    public function setLastUpdate(){
        $date = new \DateTime();
        $user = $this->tokenStrorage->getToken()->getUser();
        $user->setLastUpdate($date);
        $manager = $this->container->get("fos_user.user_manager");
        $manager->updateUser($user);
    }
}