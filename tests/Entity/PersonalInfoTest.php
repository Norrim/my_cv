<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\PersonalInfo;
use PHPUnit\Framework\TestCase;

final class PersonalInfoTest extends TestCase
{
    public function testGettersAndSetters(): void
    {
        $info = new PersonalInfo();
        $name = 'Doe';
        $firstname = 'John';
        $title = 'Senior Developer';
        $phoneNumber = '0123456789';
        $localisation = 'Paris, France';
        $email = 'john@example.com';
        $about = 'I am a passionate developer.';

        $info->setName($name);
        $info->setFirstname($firstname);
        $info->setTitle($title);
        $info->setPhoneNumber($phoneNumber);
        $info->setLocalisation($localisation);
        $info->setEmail($email);
        $info->setAbout($about);

        $this->assertSame($name, $info->getName());
        $this->assertSame($firstname, $info->getFirstname());
        $this->assertSame($title, $info->getTitle());
        $this->assertSame($phoneNumber, $info->getPhoneNumber());
        $this->assertSame($localisation, $info->getLocalisation());
        $this->assertSame($email, $info->getEmail());
        $this->assertSame($about, $info->getAbout());
    }
}
