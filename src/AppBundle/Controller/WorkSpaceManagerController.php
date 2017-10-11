<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class WorkSpaceManagerController extends Controller
{
    /**
     * @Route("/spacemanager",name="show_manager")
     */
    public function showManagerAction()
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();


        $myWorkSpaces = $em->createQuery("SELECT w.id, w.workspaceName FROM AppBundle\Entity\WorkSpace w WHERE w.userId = ".$user->getId())->getResult();
        return $this->render('AppBundle:WorkSpaceManager:show_manager.html.twig', array(
            "workspaces"=>$myWorkSpaces,"user"=>$user
        ));
    }

}
