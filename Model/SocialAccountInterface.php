<?php

namespace PE\Bundle\OAuth2ClientBundle\Model;

interface SocialAccountInterface
{
    /**
     * @return string
     */
    public function getID();

    /**
     * @param string $id
     * @return self
     */
    public function setID($id);

    /**
     * @return string
     */
    public function getSocialID();

    /**
     * @param string $socialID
     *
     * @return self
     */
    public function setSocialID($socialID);

    /**
     * @return string
     */
    public function getSocialType();

    /**
     * @param string $socialType
     *
     * @return self
     */
    public function setSocialType($socialType);

    /**
     * @return string
     */
    public function getUserClass();

    /**
     * @param string $userClass
     *
     * @return self
     */
    public function setUserClass($userClass);

    /**
     * @return string
     */
    public function getUserID();

    /**
     * @param string $userID
     *
     * @return self
     */
    public function setUserID($userID);
}