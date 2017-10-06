<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use FOS\UserBundle\Model\UserInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class MainController extends Controller
{
    /**
     * @Route("/",name="main")
     */
    public function showListAction(Request $request){
        $paginator = $this->get('knp_paginator');
        $doctrine = $this->getDoctrine()->getManager();

        //$users = $doctrine->getRepository(User::class)->findAll();
        $user = $this->getUser();

        $users = $doctrine->createQuery("SELECT u FROM AppBundle\Entity\User u WHERE u.id != ".$user->getId())->getResult();
        $online_service = $this->container->get("online_service");
        $online_service->setLastUpdate();

        $date_now = new \DateTime();

        $result = $paginator->paginate($users,$request->query->getInt('page',1),$request->query->getInt('limit',6));

        return $this->render('AppBundle:Main:main.html.twig',array('paginator'=>$result,"users"=>$users,"currentUser"=>$user,"now"=>$date_now));
    }

}
