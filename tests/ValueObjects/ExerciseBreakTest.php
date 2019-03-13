<?php

namespace Tests\ValueObjects;

use PHPUnit\Framework\TestCase;
use Wod\Contracts\ExerciseInterface;
use Wod\ValueObjects\ExerciseBreak;

class ExerciseBreakTest extends TestCase
{
    public function testBreakHasNameAndIsNotCardio()
    {
        $break = new ExerciseBreak();
        $this->assertEquals('Break', $break->getName());
        $this->assertFalse($break->isCardio());
    }

    public function testExerciseBreakImplementExerciseInterface()
    {
        $exercise = new ExerciseBreak();
        $this->assertInstanceOf(ExerciseInterface::class, $exercise);
    }
}
