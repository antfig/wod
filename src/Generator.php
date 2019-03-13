<?php
declare(strict_types=1);

namespace Wod;

use Wod\Contracts\ExerciseInterface;
use Wod\Entity\Participant;
use Wod\ValueObjects\Exercise;
use Wod\ValueObjects\ExerciseBreak;

final class Generator
{
    /**
     * @var Collection
     */
    private $participants;

    /**
     * @var Wod
     */
    private $wod;

    /**
     * Generator constructor.
     *
     * @param Collection $participants
     * @param Collection $exercises
     */
    public function __construct(Collection $participants, Collection $exercises)
    {
        $this->participants = $participants;
        $this->wod = new Wod($exercises);
    }

    /**
     * @return Wod
     * @throws Exception\BeginnerCanOnlyDoOneHandstandPracticeException
     * @throws Exception\CardioExercisesCanNotFollowEachOtherException
     * @throws Exception\ParticipantBreaksLimitException
     * @throws Exception\ParticipantCanNotStartOrEndWithBreakException
     * @throws Exception\RingsAndPullUpsLimitException
     */
    public function run(): Wod
    {
        $otherParticipantExercises = new Collection();

        // fill participant elements
        for ($element = 0; $element < Wod::ELEMENTS; $element++) {

            /** @var Participant $participant */
            foreach ($this->participants as $participant) {

                // reset allowed Exercises
                $allowedExercises = clone $this->wod->getExercises();

                // limit the ring and pull ups to maximum of 2 participants
                $allowedExercises = $this->limitRingsAndPullUps($allowedExercises, $otherParticipantExercises);

                // Beginner can only do one handstand
                $allowedExercises = $this->limitBeginnerHandstand($participant, $allowedExercises);

                // check latest participant exercise to see if is cardio
                // when happen remove cardio exercises
                $allowedExercises = $this->limitCardioExercises($participant, $element, $allowedExercises);

                // Breaks
                // - don't start or end with breaks
                // - don't do more then the limit of breaks
                $allowedExercises = $this->limitStartOrEndWithBreaks($element, $participant, $allowedExercises);

                /** @var Exercise $exercise */
                $exercise = $allowedExercises->random();

                // save the exercise made by other participant
                $otherParticipantExercises->add($exercise);

                // check for null to ignore when already have a break
                if ($participant->getExercise($element) === null) {
                    $this->wod->setParticipantExercise($participant, $exercise, $element);
                }

            } // foreach participants

            $otherParticipantExercises->clear();

        } // for elements

        return $this->wod;
    }

    /**
     * @param Collection $allowedExercises
     * @param Collection $otherParticipantExercises
     *
     * @return Collection
     */
    private function limitRingsAndPullUps(
        Collection $allowedExercises,
        Collection $otherParticipantExercises
    ): Collection {
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

        return $allowedExercises;
    }

    /**
     * @param Participant $participant
     * @param Collection $allowedExercises
     *
     * @return Collection
     */
    private function limitBeginnerHandstand(Participant $participant, Collection $allowedExercises): Collection
    {
        if ($participant->isBeginner()
            && $participant->hasDoneExercise('Handstand practice')) {
            // remove that exercise from the list
            $allowedExercises = $allowedExercises->filter(function (ExerciseInterface $exercise) {
                return $exercise->getName() != 'Handstand practice';
            });

        }
        return $allowedExercises;
    }

    /**
     * @param Participant $participant
     * @param int $element
     * @param Collection $allowedExercises
     *
     * @return Collection
     */
    private function limitCardioExercises(
        Participant $participant,
        int $element,
        Collection $allowedExercises
    ): Collection {

        $lastExercise = $participant->getExercise($element - 1);

        if ($lastExercise instanceof Exercise
            && $lastExercise->isCardio()) {

            // only allow no cardio exercises and breaks
            $allowedExercises = $allowedExercises->filter(function (ExerciseInterface $exercise) {
                return !$exercise instanceof Exercise || !$exercise->isCardio();
            });
        }
        return $allowedExercises;
    }

    /**
     * @param int $element
     * @param Participant $participant
     * @param Collection $allowedExercises
     *
     * @return Collection
     */
    private function limitStartOrEndWithBreaks(
        int $element,
        Participant $participant,
        Collection $allowedExercises
    ): Collection {

        if ($element == 0
            || $element == Wod::ELEMENTS - 1
            || $participant->numberOfBreaks() >= Wod::MAX_BREAKS_REGULAR
            || !$participant->isBeginner() && $element >= Wod::ELEMENTS - 2) {

            $allowedExercises = $allowedExercises->filter(function (ExerciseInterface $exercise) {
                return !$exercise instanceof ExerciseBreak; // remove breaks
            });

        }
        return $allowedExercises;
    }
}
