<?php
declare(strict_types=1);

namespace Wod;

use Wod\Contracts\ExerciseInterface;
use Wod\Entity\Participant;
use Wod\ValueObjects\Exercise;
use Wod\ValueObjects\ExerciseBreak;

class Generator
{
    /**
     * @var Collection
     */
    private $participants;
    /**
     * @var Collection
     */
    private $exercises;

    /**
     * Generator constructor.
     *
     * @param Collection $participants
     * @param Collection $exercises
     */
    public function __construct(Collection $participants, Collection $exercises)
    {
        $this->participants = $participants;
        $this->exercises    = $exercises;
    }

    public function run()
    {
        $otherParticipantExercises = new Collection();

        // fill participant elements
        for ($element = 0; $element < Wod::ELEMENTS; $element++) {

            printf("\n\n%02d:00 - %02d:00", $element, $element + 1);

            /** @var Participant $participant */
            foreach ($this->participants as $participant) {

                $allowedExercises = clone $this->exercises;

                // limit the ring and pull ups to maximum of 2 participants
                $ringAndPullCount = $otherParticipantExercises->filter(function (ExerciseInterface $exercise) {
                    return $exercise->getName() == 'Rings' ||
                        $exercise->getName() == 'Pull ups';
                })->count();

                if ($ringAndPullCount >= 2) {
                    // remove those exercises from the collection
                    $allowedExercises = $allowedExercises->filter(function (ExerciseInterface $exercise) {
                        return $exercise->getName() != 'Rings' &&
                            $exercise->getName() != 'Pull ups';
                    });
                }

                // Beginner only can do one handstand
                if ($participant->isBeginner()
                    && $participant->hasDoneExercise('Handstand practice')) {
                    // remove that exercise from the list
                    $allowedExercises = $allowedExercises->filter(function (ExerciseInterface $exercise) {
                        return $exercise->getName() != 'Handstand practice';
                    });

                }

                // check latest participant exercise to see if is cardio
                // when happen remove cardio exercises
                $lastExercise = $participant->getExercise($element - 1);

                if ($lastExercise instanceof Exercise
                    && $lastExercise->isCardio()) {

                    // only allow no cardio exercises
                    $allowedExercises = $allowedExercises->filter(function (ExerciseInterface $exercise) {
                        return !$exercise instanceof Exercise || !$exercise->isCardio();
                    });
                }

                // deal with breaks

                // don't start or end with breaks
                // don't do more then the limit of breaks
                if ($element == 0
                    || $element == Wod::ELEMENTS - 1
                    || $participant->numberOfBreaks() >= Wod::MAX_BREAKS_REGULAR
                    || !$participant->isBeginner() && $element >= Wod::ELEMENTS - 2) {

                    $allowedExercises = $allowedExercises->filter(function (ExerciseInterface $exercise) {
                        return !$exercise instanceof ExerciseBreak; // remove breaks
                    });

                }

                /** @var Exercise $exercise */
                $exercise = $allowedExercises->random();

                // save the exercise made by other participant
                $otherParticipantExercises->add($exercise);

                if ($participant->getExercise($element) === null) {
                    // save the history of participant exercises
                    $participant->addExercise($exercise, $element);
                }

                echo "\n"
                    . $participant->getName()
                    . ($participant->isBeginner() ? '(beginer) ' : '')
                    . ($exercise instanceof Exercise ? ' will do ' : ' will take a ')
                    . $exercise->getName() . ($exercise->isCardio() ? '(Cardio)' : '');

            } // foreach participants

            $otherParticipantExercises->clear();

        } // for elements

        // foreach ($this->participants as $participant) {
        //
        //     echo "\n---------------\n";
        //     echo $participant->getName() . ($participant->isBeginner() ? ' (beginer) ' : '');
        //     /** @var Exercise $e */
        //     foreach ($participant->getAllExercises() as $p => $e) {
        //         echo "\n   " . ($p+1) . ": ";
        //         echo $e->getName() . ($e->isCardio() ? '(Cardio)' : '');
        //     }
        // }

    }
}
