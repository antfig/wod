<?php

namespace Wod;

use Wod\Entity\Participant;
use Wod\ValueObjects\Exercise;

class Output
{
    /**
     * @var Wod
     */
    private $wod;

    /**
     * @param Wod $wod
     */
    public function __construct(Wod $wod)
    {
        $this->wod = $wod;
    }

    /**
     * Factory
     *
     * @param Wod $wod
     * @return self
     */
    public static function fromWod(Wod $wod): self
    {
        return new self($wod);
    }

    /**
     * Print By elements (time)
     */
    public function printByElement(): void
    {
        $participants = [];
        /** @var Participant $participant */
        foreach ($this->wod->getParticipants() as $participant) {
            $participants[] = $participant->getName() . ($participant->isBeginner() ? ' (beginner)' : '');
        }

        echo "Starting the workout with ";
        echo implode(', ', $participants);

        for ($element = 0; $element < Wod::ELEMENTS; $element++) {

            printf("\n\n%02d:00 - %02d:00", $element, $element + 1);

            /** @var Participant $participant */
            foreach ($this->wod->getParticipants() as $participant) {

                $participantExercise = $participant->getExercise($element);
                echo " | "
                    . $participant->getName()
                    . ($participant->isBeginner() ? ' (beginner)' : '')
                    . ($participantExercise instanceof Exercise ? ' will do ' : ' will take a ')
                    . $participantExercise->getName();
            }
        }
    }

    /**
     * Print exercises by participant
     */
    public function printByParticipant(): void
    {
        //Print by participant
        foreach ($this->wod->getParticipants() as $participant) {
            echo "\n---------------\n";
            echo $participant->getName() . ($participant->isBeginner() ? ' (beginenr) ' : '');
            /** @var Exercise $e */
            foreach ($participant->getAllExercises() as $p => $e) {
                printf("\n%02d:00 - %02d:00 ", $p, $p + 1);
                echo $e->getName() . ($e->isCardio() ? ' (Cardio)' : '');
            }
        }
    }
}
