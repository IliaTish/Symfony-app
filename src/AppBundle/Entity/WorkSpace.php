<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * WorkSpace
 *
 * @ORM\Table(name="work_space")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\WorkSpaceRepository")
 */
class WorkSpace
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="userId", type="string")
     */
    private $userId;


    /**
     * @var string
     *
     * @ORM\Column(name="workspaceName", type="string")
     */
    private $workspaceName;

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    public function setWorkSpaceName($workspacename){
        $this->workspaceName = $workspacename;
        return $this;
    }

    public function getWorkSpaceName(){
        return $this->workspaceName;
    }


    /**
     * Set userId
     *
     * @param integer $userId
     *
     * @return WorkSpace
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * Get userId
     *
     * @return int
     */
    public function getUserId()
    {
        return $this->userId;
    }
}

