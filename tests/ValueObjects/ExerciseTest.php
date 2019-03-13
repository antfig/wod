<?php

namespace Tests\ValueObjects;

use PHPUnit\Framework\TestCase;
use Wod\Contracts\ExerciseInterface;
use Wod\ValueObjects\Exercise;

class ExerciseTest extends TestCase
{
    public function testExerciseHasNameAndCanBeCardio()
    {
        $cardioExercise = new Exercise('Foo bar', true);

        $this->assertEquals('Foo bar', $cardioExercise->getName());
        $this->assertTrue($cardioExercise->isCardio());

        $notCardio = new Exercise('Baz', false);

        $this->assertFalse($notCardio->isCardio());
    }

    public function testExerciseImplementExerciseInterface()
    {
        $exercise = new Exercise('Foo', false);
        $this->assertInstanceOf(ExerciseInterface::class, $exercise);
    }
}
