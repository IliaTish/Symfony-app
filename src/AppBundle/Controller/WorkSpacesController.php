<?php

namespace AppBundle\Controller;

use AppBundle\Entity\WorkSpace;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class WorkSpacesController extends Controller
{
    /**
     * @Route("/workspacelist/{userid}")
     */
    public function showListAction($userid)
    {
        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery("SELECT w.id, w.workspaceName FROM AppBundle\Entity\WorkSpace w WHERE w.userId = ".$userid);
        $workspaces = $query->getResult();
        return $this->render('AppBundle:WorkSpaces:show_list.html.twig', array(
            "workspaces"=>$workspaces, "userid"=>$userid
        ));
    }

}
