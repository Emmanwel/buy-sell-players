<?php

namespace App\DataFixtures;


use App\DataFixtures\TeamFixtures;
use App\DataFixtures\PlayerFixtures;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {

        $teamFixtures = new TeamFixtures();
        $teamFixtures->load($manager);

        // Create a reference to the "Team" entity, which can be retrieved in PlayerFixtures
        $this->addReference('team', $teamFixtures);

        $playerFixtures = new PlayerFixtures();
        $playerFixtures->load($manager);
    }
}
