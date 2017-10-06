<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use Buzz\Message\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class ApiController extends Controller
{
    public function blockUserAction(Request $request)
    {
        $id = $request->get('id');
        $currentID = $request->get('currentID');
        $userManager =  $this->get('fos_user.user_manager');
        $currentUser = $userManager->findUserBy(array('id'=>$currentID));

        if($currentUser == null){
          return;
        }
        if($currentUser->getLocked()){
            return;
        }
        $user = $userManager->findUserBy(array('id'=>$id));
        if($user == null){
            return new JsonResponse(array("result"=>true));
        }
        $user->setLock(true);
        $userManager->updateUser($user);

        $online_service = $this->container->get("online_service");
        $online_service->setLastUpdate();

        return new JsonResponse(array("result"=>true));
    }


    public function blockSelectedAction(Request $request){
        $jsonData = $request->get("selected");
        $currentID = $request->get("id");
        $parsedData = json_decode($jsonData);

        $userManager = $this->get('fos_user.user_manager');

        $currentUser = $userManager->findUserBy(array('id'=>$currentID));


        if($currentUser == null){
            return;
        }
        if($currentUser->getLocked()){
            return;
        }

        $online_service = $this->container->get("online_service");
        $online_service->setLastUpdate();


        foreach($parsedData as $id){
            $user = $userManager->findUserBy(array('id'=>$id));
            if($user != null){
                if(!$user->getLocked()){
                    $user->setLock(true);
                    $userManager->updateUser($user);
                }
            }
        }
        return new JsonResponse(array("result"=>true));
    }


    public function deleteSelectedAction(Request $request){
        $jsonData = $request->get("selected");
        $currentID = $request->get("id");
        $parsedData = json_decode($jsonData);

        $userManager = $this->get('fos_user.user_manager');

        $currentUser = $userManager->findUserBy(array('id'=>$currentID));


        if($currentUser == null){
            return;
        }
        if($currentUser->getLocked()){
            return;
        }

        $online_service = $this->container->get("online_service");
        $online_service->setLastUpdate();


        foreach($parsedData as $id){
            $user = $userManager->findUserBy(array('id'=>$id));
            if($user != null){
                $userManager->deleteUser($user);
            }
        }
        return new JsonResponse(array("result"=>true));


    }

    public function deleteUserAction(Request $request){
        $id = $request->get('id');
        $currentID = $request->get('currentID');
        $userManager = $this->get('fos_user.user_manager');
        $currentUser = $userManager->findUserBy(array('id'=>$currentID));

        if($currentUser == null)
        {
            return;
        }
        if($currentUser->getLocked()){
            return;
        }
        $user = $userManager->findUserBy(array('id'=>$id));
        if($user == null){
            return new JsonResponse(array("result"=>true));
        }

        $online_service = $this->container->get("online_service");
        $online_service->setLastUpdate();

        $userManager->deleteUser($user);


        return new JsonResponse(array("result"=>true));
    }

}
