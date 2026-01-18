<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\Skill;
use PHPUnit\Framework\TestCase;

final class SkillTest extends TestCase
{
    public function testGettersAndSetters(): void
    {
        $skill = new Skill();
        $name = 'PHP';
        $percentage = 90;
        $position = 1;

        $skill->setName($name);
        $skill->setPercentage($percentage);
        $skill->setPosition($position);

        $this->assertSame($name, $skill->getName());
        $this->assertSame($percentage, $skill->getPercentage());
        $this->assertSame($position, $skill->getPosition());
    }
}
