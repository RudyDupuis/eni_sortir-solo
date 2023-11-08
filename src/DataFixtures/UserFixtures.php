<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture implements DependentFixtureInterface
{
    private $userPasswordHasher;
    private $campusFixtures;

    public function __construct(UserPasswordHasherInterface $userPasswordHasher, CampusFixtures $campusFixtures)
    {
        $this->userPasswordHasher = $userPasswordHasher;
        $this->campusFixtures = $campusFixtures;
    }

    public function getDependencies()
    {
        return [
            CampusFixtures::class,
        ];
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        for ($i = 0; $i < 5; $i++) {
            $campusReferences[] = $this->campusFixtures->getReference('campus' . $i);
        }

        for ($i = 0; $i < 5; $i++) {
            $user = new User();
            $user->setFirstName($faker->firstName);
            $user->setLastName($faker->firstName);
            $user->setEmail($faker->email);
            $user->setPassword(
                $this->userPasswordHasher->hashPassword(
                    $user,
                    '12345'
                )
            );
            $user->setRoles(['ROLE_USER']);
            $user->setCampus($faker->randomElement($campusReferences));
            $user->setProfilePicture('fakepp' . $i + 1 . '.jpg');

            $manager->persist($user);
            $this->addReference(('user' . $i), $user);
        }

        $manager->flush();
    }
}
