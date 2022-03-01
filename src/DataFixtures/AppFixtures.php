<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = \Faker\Factory::create('fr_FR');
        for ($i = 0; $i < 10; $i++) {
            $user = new User();
            $user->setNom($faker->firstName());
            $user->setPrenom($faker->lastName());
            $user->setEmail($faker->email());
            $user->setPassword($faker->password());
            $user->setRole(['admin']);

            $manager->persist($user);
        }
        $manager->flush();
    }
}
