<?php

namespace App\Tests\Repository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;


class UserRepositoryTest extends KernelTestCase
{

    public function testCount(){
        self::bootkernel();

        $users=self::$container->get(UserRepository::class)->count([]);
        $this->assertEquals(10, $users);
    }
}