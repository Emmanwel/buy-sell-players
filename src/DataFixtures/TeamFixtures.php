<?php

namespace App\DataFixtures;

use App\Entity\Team;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class TeamFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {


        $team = new Team();
        $team->setTeamName('Team Kubwa');
        $team->setTeamLogo('https://cdn.pixabay.com/photo/2020/07/02/19/36/marvel-5364165_960_720.jpg');
        $team->setLeague('Premier League');
        $team->setReleaseYear(2000);

        $manager->persist($team);

        $manager->flush();
    }
}
