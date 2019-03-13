<?php

namespace Tests\Entity;

use PHPUnit\Framework\TestCase;
use Wod\Collection;
use Wod\Entity\Participant;
use Wod\Exception\BeginnerCanOnlyDoOneHandstandPracticeException;
use Wod\Exception\CardioExercisesCanNotFollowEachOtherException;
use Wod\Exception\ParticipantBreaksLimitException;
use Wod\Exception\ParticipantCanNotStartOrEndWithBreakException;
use Wod\ValueObjects\Exercise;
use Wod\ValueObjects\ExerciseBreak;
use Wod\Wod;

class ParticipantTest extends TestCase
{
    public function testParticipantHasNameAndCanBeBeginner()
    {
        $beginnerParticipant = new Participant("Antonio", true);

        $this->assertEquals("Antonio", $beginnerParticipant->getName());
        $this->assertTrue($beginnerParticipant->isBeginner());

        $participant = new Participant("Peter", false);
        $this->assertEquals("Peter", $participant->getName());
        $this->assertFalse($participant->isBeginner());
    }

    public function testCanAddExerciseInSelectedPosition()
    {
        $participant = new Participant("Antonio", true);

        $exercise = new Exercise('Push ups', false);
        $position = 4;

        $participant->addExercise($exercise, $position);

        $this->assertEquals($exercise, $participant->getExercise($position));
    }

    public function testCanAddBreakInSelectedPosition()
    {
        $participant = new Participant("Antonio", true);
        $position = 4;
        $break = new ExerciseBreak();

        $participant->addExercise($break, $position);

        $this->assertEquals($break, $participant->getExercise($position));
    }

    public function testWhenIsNotBeginnerBreaksOccupyTwoElements()
    {
        $position = 1;
        $participant = new Participant("Antonio", false);

        $this->assertNull($participant->getExercise($position));
        $this->assertNull($participant->getExercise($position + 1));

        $participant->addExercise(new ExerciseBreak(), $position);
        $this->assertInstanceOf(ExerciseBreak::class, $participant->getExercise($position));
        $this->assertInstanceOf(ExerciseBreak::class, $participant->getExercise($position + 1));
    }

    public function testCanCheckIfAlreadyDoneSomeExerciseByName()
    {
        $participant = new Participant("Antonio", true);

        $this->assertFalse($participant->hasDoneExercise('Push ups'));

        $participant->addExercise(new Exercise('Push ups', false), 1);
        $this->assertTrue($participant->hasDoneExercise('Push ups'));
    }

    public function testCanGetAllExercises()
    {
        $participant = new Participant("Antonio", false);

        $exercise1 = new Exercise('Push ups', false);
        $exercise2 = new Exercise('Front squats', false);

        $participant->addExercise($exercise1, 1);
        $participant->addExercise($exercise2, 2);

        $allExercises = $participant->getAllExercises();

        $this->assertInstanceOf(Collection::class, $allExercises);
        $this->assertEquals(2, $allExercises->count());
        $this->assertEquals($exercise1, $allExercises->get(1));
        $this->assertEquals($exercise2, $allExercises->get(2));
    }

    public function testCanGetNumberOfBreaks()
    {
        $beginner = new Participant("Antonio", true);

        $beginner->addExercise(new ExerciseBreak(), 2);
        $beginner->addExercise(new ExerciseBreak(), 4);
        $beginner->addExercise(new ExerciseBreak(), 10);

        $this->assertEquals(3, $beginner->numberOfBreaks());

        $notBeginner = new Participant("Ronaldo", false);

        $notBeginner->addExercise(new ExerciseBreak(), 1);
        $notBeginner->addExercise(new ExerciseBreak(), 4);
        $this->assertEquals(4, $notBeginner->numberOfBreaks());
    }

    public function testCanNotAddBreaksAtBeginningOfExercises()
    {
        $participant = new Participant("Antonio", true);

        $this->expectException(ParticipantCanNotStartOrEndWithBreakException::class);

        $participant->addExercise(new ExerciseBreak(), 0);
    }

    public function testCanNotAddBreaksAtEndOfExercises()
    {
        $participant = new Participant("Antonio", true);

        $this->expectException(ParticipantCanNotStartOrEndWithBreakException::class);

        $participant->addExercise(new ExerciseBreak(), Wod::ELEMENTS);
    }

    public function testBeginnersCanDoMaximumOfFourBreaks()
    {
        $participant = new Participant("Antonio", true);

        $participant->addExercise(new ExerciseBreak(), 3);
        $participant->addExercise(new ExerciseBreak(), 6);
        $participant->addExercise(new ExerciseBreak(), 10);
        $participant->addExercise(new ExerciseBreak(), 15);

        $this->expectException(ParticipantBreaksLimitException::class);

        $participant->addExercise(new ExerciseBreak(), 20);
    }

    public function testNotBeginnersCanDoMaximumOfTwoBreaks()
    {
        $participant = new Participant("Antonio", false);

        $participant->addExercise(new ExerciseBreak(), 3);
        $participant->addExercise(new ExerciseBreak(), 6);

        $this->expectException(ParticipantBreaksLimitException::class);

        $participant->addExercise(new ExerciseBreak(), 20);
    }

    public function testBeginnerCanOnlyDoOneHandstandExercise()
    {
        $participant = new Participant("Antonio", true);

        $participant->addExercise(new Exercise('Handstand practice'), 3);

        $this->expectException(BeginnerCanOnlyDoOneHandstandPracticeException::class);

        $participant->addExercise(new Exercise('Handstand practice'), 3);
    }

    public function testCardioExercisesCanNotFollowBeforeOtherCardio()
    {
        $participant = new Participant("Antonio", false);
        $cardio = new Exercise('Jumping rope', true);
        $otherCardio = new Exercise('short sprints', true);

        $participant->addExercise($cardio, 3);

        $this->expectException(CardioExercisesCanNotFollowEachOtherException::class);

        $participant->addExercise($otherCardio, 2);
    }

    public function testCardioExercisesCanNotFollowAfterOtherCardio()
    {
        $participant = new Participant("Antonio", false);
        $cardio = new Exercise('Jumping rope', true);
        $otherCardio = new Exercise('short sprints', true);

        $participant->addExercise($cardio, 3);

        $this->expectException(CardioExercisesCanNotFollowEachOtherException::class);

        $participant->addExercise($otherCardio, 4);
    }

}
