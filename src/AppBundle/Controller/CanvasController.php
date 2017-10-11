<?php

namespace AppBundle\Controller;

use AppBundle\Entity\WorkSpace;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CanvasController extends Controller
{
    /**
     * @Route("/workspace/{id}", name="workspace_show")
     */
    public function showCanvasAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $workspace = $em->createQuery("SELECT w.id, w.workspaceName FROM AppBundle\Entity\WorkSpace w WHERE w.id = ".$id)->getResult();
        if($workspace) {
            $user = $this->getUser();
            return $this->render('AppBundle:Canvas:show_canvas.html.twig', array(
                'user' => $user, "workspace_id" => $id
            ));
        }
        else
            {
            throw $this->createNotFoundException("Sorry, we can't found this page");
        }
    }

}
