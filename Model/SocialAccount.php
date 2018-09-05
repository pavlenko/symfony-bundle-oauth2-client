<?php

namespace PE\Bundle\OAuth2ClientBundle\Model;

class SocialAccount implements SocialAccountInterface
{
    /**
     * @var string
     */
    protected $id;

    /**
     * @var string
     */
    protected $socialID;

    /**
     * @var string
     */
    protected $socialType;

    /**
     * @var string
     */
    protected $userClass;

    /**
     * @var string
     */
    protected $userID;

    /**
     * @inheritDoc
     */
    public function getID()
    {
        return $this->id;
    }

    /**
     * @inheritDoc
     */
    public function setID($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getSocialID()
    {
        return $this->socialID;
    }

    /**
     * @inheritDoc
     */
    public function setSocialID($socialID)
    {
        $this->socialID = $socialID;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getSocialType()
    {
        return $this->socialType;
    }

    /**
     * @inheritDoc
     */
    public function setSocialType($socialType)
    {
        $this->socialType = $socialType;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getUserClass()
    {
        return $this->userClass;
    }

    /**
     * @inheritDoc
     */
    public function setUserClass($userClass)
    {
        $this->userClass = $userClass;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getUserID()
    {
        return $this->userID;
    }

    /**
     * @inheritDoc
     */
    public function setUserID($userID)
    {
        $this->userID = $userID;
        return $this;
    }
}