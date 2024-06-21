<?php

namespace App\Tests\Entity;

use App\Entity\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function testEmail(): void
    {
        $user = new User();
        $user->setEmail('test@example.com');
        $this->assertEquals('test@example.com', $user->getEmail());
    }

    public function testRoles(): void
    {
        $user = new User();
        $user->setRoles(['ROLE_USER']);
        $this->assertTrue(in_array('ROLE_USER', $user->getRoles()));
    }
}
