<?php

namespace App\DataFixtures;

use App\Entity\Campus;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CampusFixtures extends Fixture
{
    private $campusesData = ['Nantes', 'Niort', 'En ligne', 'Quimper', 'Rennes'];

    public function load(ObjectManager $manager): void
    {


        foreach ($this->campusesData as $campusName) {
            $campus = new Campus();
            $campus->setName($campusName);
            $manager->persist($campus);
            $this->addReference($campusName, $campus);
        }

        $manager->flush();
    }
}
