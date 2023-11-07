<?php

namespace App\DataFixtures;

use App\Entity\Outing;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

/**
 * @DependsOn({"UserFixtures", "CampusFixtures"})
 */
class OutingFixtures extends Fixture
{
    private $userFixtures;
    private $campusFixtures;

    public function __construct(UserFixtures $userFixtures, CampusFixtures $campusFixtures)
    {
        $this->userFixtures = $userFixtures;
        $this->campusFixtures = $campusFixtures;
    }
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        $campusReferences = [
            $this->campusFixtures->getReference('Nantes'),
            $this->campusFixtures->getReference('Niort'),
            $this->campusFixtures->getReference('En ligne'),
            $this->campusFixtures->getReference('Quimper'),
            $this->campusFixtures->getReference('Rennes'),
        ];

        $userReferences = [
            $this->userFixtures->getReference('user0'),
            $this->userFixtures->getReference('user1'),
            $this->userFixtures->getReference('user2'),
            $this->userFixtures->getReference('user3'),
            $this->userFixtures->getReference('user4'),
        ];

        for ($i = 0; $i < 20; $i++) {
            $outing = new Outing();
            $outing->setName($faker->sentence(3));
            $outing->setRegistrationDeadline($faker->dateTimeBetween('now', '+12 month'));
            $outing->setOutingDate($faker->dateTimeBetween($outing->getRegistrationDeadline(), '+12 month'));
            $outing->setNumberPlaces($faker->numberBetween(5, 20));
            $outing->setDuration($faker->numberBetween(30, 500));
            $outing->setDescription($faker->text(255));
            $outing->setCampus($faker->randomElement($campusReferences));
            $outing->setOutingImage('fakeimage' . $i + 1 . '.jpg');
            $outing->setNamePlace($faker->city);
            $outing->setStreet($faker->streetAddress);
            $outing->setPostalCode($faker->numberBetween(1000, 99999));
            $outing->setCity($faker->city);

            $participants = $faker->randomElements($userReferences, $faker->numberBetween(1, count($userReferences)));

            foreach ($participants as $participant) {
                $outing->addRegistrant($participant);
            }

            $manager->persist($outing);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            UserFixtures::class,
            CampusFixtures::class,
        ];
    }
}
