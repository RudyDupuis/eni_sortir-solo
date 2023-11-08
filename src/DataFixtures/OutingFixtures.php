<?php

namespace App\DataFixtures;

use App\Entity\Outing;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class OutingFixtures extends Fixture implements DependentFixtureInterface
{
    private $userFixtures;
    private $campusFixtures;

    public function __construct(CampusFixtures $campusFixtures, UserFixtures $userFixtures)
    {
        $this->campusFixtures = $campusFixtures;
        $this->userFixtures = $userFixtures;
    }

    public function getDependencies()
    {
        return [
            UserFixtures::class,
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
            $userReferences[] = $this->userFixtures->getReference('user' . $i);
        }

        $outingNames = [
            'Partie de foot',
            'Révision en groupe',
            'Marathon de lecture',
            'Camping sous les étoiles',
            'Journée de ski à la montagne',
            'Journée à la plage ensoleillée',
            'Plongée sous-marine passionnante',
            'Compétition de Rubik\'s Cube',
            'Match de basket entre amis',
            'Promenade à vélo dans la nature',
            'Session de skateboard dans le parc',
            'Excursion en jet ski sur le lac',
            'Course de vélo autour de la ville',
            'Randonnée en kayak sur la rivière',
            'Pêche relaxante au bord du lac',
            'Séance de yoga en plein air',
            'Tournoi de pétanque amical',
            'Atelier de peinture créative',
            'Balade à cheval à la campagne',
            'Vol en parapente avec vue panoramique'
        ];

        $outingDescriptions = [
            'Une partie de foot amicale entre étudiants. Profitez d\'un match de football amical avec vos camarades de classe.',
            'Groupe de révision pour se préparer aux examens. Rejoignez notre groupe de révision pour réussir vos examens ensemble.',
            'Lecture intensive lors d\'un marathon de lecture. Plongez dans un marathon de lecture passionnant.',
            'Camping sous les étoiles avec des amis. Passez une nuit sous les étoiles en camping avec vos amis.',
            'Journée de ski dans les montagnes enneigées. Découvrez la beauté des montagnes en skiant toute la journée.',
            'Une journée à la plage pour se détendre et s\'amuser. Profitez du soleil et de la plage pour une journée de détente.',
            'Plongée sous-marine pour explorer le monde sous-marin. Explorez les fonds marins lors d\'une plongée passionnante.',
            'Compétition amicale de Rubik\'s Cube. Testez vos compétences en résolvant le Rubik\'s Cube.',
            'Match de basket entre amis et camarades de classe. Un match de basket convivial entre camarades de classe et amis.',
            'Promenade à vélo dans la nature pour profiter du paysage. Découvrez la nature lors d\'une balade à vélo pittoresque.',
            'Session de skateboard dans le parc de la ville. Amusez-vous en skateboard dans le parc de la ville.',
            'Excursion en jet ski sur un lac pittoresque. Partez pour une excursion palpitante en jet ski sur un lac magnifique.',
            'Course de vélo à travers la ville et les rues. Faites une course de vélo à travers la ville et les rues animées.',
            'Randonnée en kayak sur une rivière paisible. Profitez d\'une randonnée en kayak sur une rivière paisible.',
            'Pêche relaxante au bord du lac en pleine nature. Détendez-vous en pêchant au bord d\'un lac en pleine nature.',
            'Séance de yoga en plein air pour se détendre. Détendez-vous avec une séance de yoga en plein air.',
            'Tournoi amical de pétanque entre étudiants. Participez à un tournoi amical de pétanque avec vos camarades de classe.',
            'Atelier de peinture créative pour s\'exprimer. Exprimez votre créativité lors d\'un atelier de peinture.',
            'Balade à cheval à travers la campagne pittoresque. Profitez d\'une balade à cheval à travers une campagne pittoresque.',
            'Vol en parapente avec une vue panoramique incroyable. Découvrez une vue panoramique incroyable lors d\'un vol en parapente.'
        ];


        for ($i = 0; $i < 20; $i++) {
            $outing = new Outing();
            $outing->setName($outingNames[$i]);
            $outing->setRegistrationDeadline($faker->dateTimeBetween('-1 week', '+2 month'));
            $outing->setOutingDate($faker->dateTimeBetween($outing->getRegistrationDeadline(), '+5 month'));
            $outing->setNumberPlaces($faker->numberBetween(5, 20));
            $outing->setDuration($faker->numberBetween(30, 500));
            $outing->setDescription($outingDescriptions[$i]);
            $outing->setCampus($faker->randomElement($campusReferences));
            $outing->setOutingImage('fakeimage' . ($i + 1) . '.jpg');
            $outing->setNamePlace($faker->city());
            $outing->setStreet($faker->streetAddress());
            $outing->setPostalCode($faker->numberBetween(1000, 99999));
            $outing->setCity($faker->city());
            $outing->setAuthor($faker->randomElement($userReferences));

            $participants = $faker->randomElements($userReferences, $faker->numberBetween(1, count($userReferences)));

            foreach ($participants as $participant) {
                $outing->addRegistrant($participant);
            }

            $manager->persist($outing);
        }

        $manager->flush();
    }
}
