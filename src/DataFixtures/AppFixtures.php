<?php

namespace App\DataFixtures;

use App\Entity\CategoryService;
use App\Entity\Contact;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = \Faker\Factory::create('fr_FR');
        for ($i = 0; $i < 3; $i++) {
            $cat = new CategoryService();
            $cat->setDesignation($faker->text($maxNbChars = 30));
            $manager->persist($cat);
        }
        $manager->flush();
    }
}
