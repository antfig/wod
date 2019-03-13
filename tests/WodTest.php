<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use Wod\Collection;
use Wod\Entity\Participant;
use Wod\Exception\RingsAndPullUpsLimitException;
use Wod\ValueObjects\Exercise;
use Wod\ValueObjects\ExerciseBreak;
use Wod\Wod;

class WodTest extends TestCase
{
    /**
     * @var Wod
     */
    private $wod;

    /**
     * @var Collection
     */
    private $exercises;

    protected function setUp(): void
    {
        // Load Exercises Elements
        $this->exercises = (new Collection())
            ->add(new Exercise('Jumping jacks', true))
            ->add(new Exercise('Push ups', false))
            ->add(new Exercise('Front squats', false))
            ->add(new Exercise('Back squats', false))
            ->add(new Exercise('Pull ups', false))
            ->add(new Exercise('Rings', false))
            ->add(new Exercise('Short sprints', true))
            ->add(new Exercise('Handstand practice', false))
            ->add(new Exercise('Jumping rope', true))
            ->add(new ExerciseBreak());

        $this->wod = new Wod($this->exercises);
    }

    public function testWodHasExercises()
    {
        $this->assertEquals($this->exercises, $this->wod->getExercises());
    }

    public function testCanAddParticipantExercises()
    {
        $antonio = new Participant('Antonio', true);
        $ronaldo = new Participant('Ronaldo', false);
        $messi = new Participant('Messi', false);

        $pullUpsExercise = new Exercise('Pull ups', false);
        $ringsExercise = new Exercise('Rings', false);
        $pushUps = new Exercise('Push ups', false);

        $this->wod->setParticipantExercise($antonio, $pullUpsExercise, 0);
        $this->wod->setParticipantExercise($ronaldo, $ringsExercise, 0);
        $this->wod->setParticipantExercise($messi, $pushUps, 0);

        $this->assertEquals(3, $this->wod->getParticipants()->count());
        $this->assertEquals($pullUpsExercise, $antonio->getExercise(0), "Wod should update participant exercises");
        $this->assertEquals($ringsExercise, $ronaldo->getExercise(0), "Wod should update participant exercises");
        $this->assertEquals($pushUps, $messi->getExercise(0), "Wod should update participant exercises");
    }

    public function testIndividualParticipantsAreAddedOnlyOnce()
    {
        $participant = new Participant('Antonio', true);
        $participant2 = new Participant('Ronaldo', false);
        $exercise = new Exercise('Pull ups', false);

        $this->wod->setParticipantExercise($participant, $exercise, 0);
        $this->wod->setParticipantExercise($participant, clone $exercise, 1);

        $this->wod->setParticipantExercise($participant2, clone $exercise, 1);

        // should only be one participant in the list
        $this->assertEquals(2, $this->wod->getParticipants()->count());
        $this->assertEquals($exercise, $participant->getExercise(0), "Wod should update participant exercises");
        $this->assertEquals($exercise, $participant->getExercise(1), "Wod should update participant exercises");
    }

    public function testOnlyTwoParticipantsCanDoRingsAndPullUpsAtSameTime()
    {
        $antonio = new Participant('Antonio', true);
        $ronaldo = new Participant('Ronaldo', false);
        $messi = new Participant('Messi', false);

        $pullUpsExercise = new Exercise('Pull ups', false);
        $ringsExercise = new Exercise('Rings', false);

        $this->wod->setParticipantExercise($antonio, $pullUpsExercise, 0);
        $this->wod->setParticipantExercise($ronaldo, $ringsExercise, 0);

        $this->expectException(RingsAndPullUpsLimitException::class);

        $this->wod->setParticipantExercise($messi, clone $pullUpsExercise, 0);

    }
}
