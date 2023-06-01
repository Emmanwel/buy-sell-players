<?php

namespace App\DataFixtures;

use App\Entity\Team;
use App\Entity\Player;
use App\DataFixtures\TeamFixtures;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class PlayerFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {

        // Retrieve the reference to the "Team" entity created in TeamFixtures
        $team = $this->getReference('team');

        $player1 = new Player();
        $player1->setPlayerName('Ronaldinho');
        $player1->setTeam($team);

        $manager->persist($player1);
        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            TeamFixtures::class,
        ];
    }
}
