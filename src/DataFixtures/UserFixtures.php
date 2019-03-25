<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
    /**
     * @var UserPasswordEncoderInterface $encoder
     */
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user
            ->setRoles(['ROLE_API'])
            ->setUsername('superUser')
            ->setApiToken('superTokenDeTest')
            ->setPassword($this->encoder->encodePassword($user, 'superpassword'))
        ;

        $manager->persist($user);
        $manager->flush();
    }
}
