<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use Wod\Collection;
use Wod\Entity\Participant;
use Wod\Generator;
use Wod\ValueObjects\Exercise;
use Wod\ValueObjects\ExerciseBreak;
use Wod\Wod;

class GeneratorTest extends TestCase
{

    public function testCanGenerateWod()
    {
        $participants = new Collection();
        $participants->add(new Participant('Camille', Participant::NOT_BEGINNER))
            ->add(new Participant('Michael', Participant::NOT_BEGINNER))
            ->add(new Participant('Tom', Participant::IS_BEGINNER))
            ->add(new Participant('Tim', Participant::NOT_BEGINNER))
            ->add(new Participant('Erik', Participant::NOT_BEGINNER))
            ->add(new Participant('Lars', Participant::NOT_BEGINNER))
            ->add(new Participant('Mathijs', Participant::IS_BEGINNER));

        // Load Exercises Elements
        $exercises = new Collection();
        $exercises->add(new Exercise('Jumping jacks', true))
            ->add(new Exercise('Push ups', false))
            ->add(new Exercise('Front squats', false))
            ->add(new Exercise('Back squats', false))
            ->add(new Exercise('Pull ups', false))
            ->add(new Exercise('Rings', false))
            ->add(new Exercise('Short sprints', true))
            ->add(new Exercise('Handstand practice', false))
            ->add(new Exercise('Jumping rope', true))
            ->add(new ExerciseBreak());

        $generator = new Generator($participants, $exercises);
        $wod = $generator->run();

        $this->assertEquals(7, $wod->getParticipants()->count(), "Should exist 7 participants in wod");

        // all elements should have exercises
        for ($element = 0; $element < Wod::ELEMENTS; $element++) {
            /** @var Collection $positionExercises */
            $positionExercises = $wod->getElements()->get($element);
            $this->assertInstanceOf(Collection::class, $positionExercises);
        }
    }
}
