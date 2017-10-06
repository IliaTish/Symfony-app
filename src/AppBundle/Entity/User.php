<?php


namespace AppBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\UserInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="users")
 */
class User extends BaseUser implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $locked = false;


    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $vkID = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $twitterID = null;


    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $facebookID = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $bDate = null;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $lastUpdate = null;
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $phothSrc = null;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $sex = null;

    public function getLastUpdate(){
        return $this->lastUpdate;
    }

    public function setLastUpdate($lastUpdate){
        $this->lastUpdate = $lastUpdate;
        return $this;
    }

    public function setSex($sex){
        $this->sex = $sex;
        return $this;
    }

    public function getSex(){
        return $this->sex;
    }

    public function setPhotoSrc($photoSrc){
        $this->phothSrc = $photoSrc;
        return $this;
    }


    public function getPhotoSrc(){
        return $this->phothSrc;
    }


    public function setbDate($bDate){
        $this->bDate = $bDate;
        return $this;
    }

    public function getbDate(){
        return $this->bDate;
    }

    public function setFaceBookID($facebookID){
        $this->facebookID = $facebookID;
        return $this;
    }

    /**
     * @return $facebookID
     */
    public function getFaceBookID()
    {
        return $this->facebookID;
    }


    public function getTwitterID(){
        return $this->twitterID;
    }

    public function setTwitterID($twitterID){
        $this->twitterID = $twitterID;
        return $this;
    }


    public function setVkID($vkID){
        $this->vkID = $vkID;
        return $this;
    }

    public function getVkID(){
        return $this->vkID;
    }





    /**
     * set lock
     *
     * @param boolean $locked
     *
     * @return User
     */
    public function setLock($locked){
        $this->locked = $locked;
        return $this;
    }

    /**
     * get locked
     *
     * @return boolean
     */
    public function getLocked(){
        return $this->locked;
    }

    public function isAccountNonLocked()
    {
        return !$this->locked;
    }

    public function __construct()
    {
        parent::__construct();
        // your own logic
    }
}