<?php

namespace PE\Bundle\OAuth2ClientBundle\Doctrine;

use PE\Bundle\OAuth2ClientBundle\Model\SocialAccountInterface;
use PE\Bundle\OAuth2ClientBundle\Repository\SocialAccountRepositoryInterface;

class SocialAccountRepository extends AbstractRepository implements SocialAccountRepositoryInterface
{
    /**
     * @inheritDoc
     */
    public function findAccountsByUser($userClass, $userID)
    {
        return $this->getRepository()->findBy(['userClass' => $userClass, 'userID' => $userID]);
    }

    /**
     * @inheritDoc
     */
    public function findAccountByID($id)
    {
        return $this->getRepository()->find($id);
    }

    /**
     * @inheritDoc
     */
    public function findAccountBySocial($socialType, $socialID)
    {
        return $this->getRepository()->findOneBy(['socialType' => $socialType, 'socialID' => $socialID]);
    }

    /**
     * @inheritDoc
     */
    public function createAccount()
    {
        $class = $this->getClass();
        return new $class;
    }

    /**
     * @inheritDoc
     */
    public function updateAccount(SocialAccountInterface $account, $flush = true)
    {
        $manager = $this->getManager();
        $manager->persist($account);

        if ($flush) {
            $manager->flush();
        }
    }

    /**
     * @inheritDoc
     */
    public function removeAccount(SocialAccountInterface $account, $flush = true)
    {
        $manager = $this->getManager();
        $manager->remove($account);

        if ($flush) {
            $manager->flush();
        }
    }

    /**
     * @inheritDoc
     */
    public function flush()
    {
        $this->getManager()->flush();
    }
}