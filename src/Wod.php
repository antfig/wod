<?php
declare(strict_types=1);

namespace Wod;

use Wod\Contracts\ExerciseInterface;
use Wod\Entity\Participant;
use Wod\Exception\RingsAndPullUpsLimitException;

/**
 * The class will work as aggregator for all participant and there exercises for wod
 */
final class Wod
{
    /**
     * Number of elements of the program
     */
    const ELEMENTS = 30;

    /**
     * Limit of breaks for each type of participant
     */
    const MAX_BREAKS_BEGINNER = 4;
    const MAX_BREAKS_REGULAR = 4;// ony get 2 breaks but since each one occupy to elements count as 4

    /**
     * @var Collection
     */
    private $exercises;

    /**
     * @var Collection
     */
    private $participants;

    /**
     * Store all exercises made on each element
     *
     * @var Collection
     */
    private $elements;

    public function __construct(Collection $exercises)
    {
        $this->exercises = $exercises;
        $this->participants = new Collection();
        $this->elements = new Collection();
    }

    /**
     * @return Collection
     */
    public function getExercises(): Collection
    {
        return $this->exercises;
    }

    /**
     * @return Collection
     */
    public function getParticipants(): Collection
    {
        return $this->participants;
    }

    /**
     * @return Collection
     */
    public function getElements(): Collection
    {
        return $this->elements;
    }

    /**
     * @param Participant $participant
     * @param ExerciseInterface $exercise
     * @param int $position
     *
     * @throws Exception\BeginnerCanOnlyDoOneHandstandPracticeException
     * @throws Exception\CardioExercisesCanNotFollowEachOtherException
     * @throws Exception\ParticipantBreaksLimitException
     * @throws Exception\ParticipantCanNotStartOrEndWithBreakException
     * @throws Exception\RingsAndPullUpsLimitException
     */
    public function setParticipantExercise(Participant $participant, ExerciseInterface $exercise, int $position): void
    {
        $this->ensureRingsAndPullUpsLimit($exercise, $position);

        $participant->addExercise($exercise, $position);

        // only add new participants to the list
        if (!$this->participants->hasValue($participant)) {
            $this->participants->add($participant);
        }

        // store other participants positions exercises
        $this->addElementExercise($exercise, $position);
    }

    /**
     * @param ExerciseInterface $exercise
     * @param int $position
     *
     * @throws Exception\RingsAndPullUpsLimitException
     */
    private function ensureRingsAndPullUpsLimit(ExerciseInterface $exercise, int $position): void
    {
        if ($exercise->getName() !== 'Rings'
            && $exercise->getName() !== 'Pull ups') {
            return;
        }

        $positionExercises = $this->elements->get($position);

        // position don't have any exercise yet
        if ($positionExercises == null) {
            return;
        }

        $total = $positionExercises->filter(function (ExerciseInterface $exercise) {
            return $exercise->getName() == 'Rings' ||
                $exercise->getName() == 'Pull ups';
        })->count();

        if ($total >= 2) {
            throw new RingsAndPullUpsLimitException();
        }
    }

    /**
     * @param ExerciseInterface $exercise
     * @param int $position
     */
    private function addElementExercise(ExerciseInterface $exercise, int $position): void
    {
        $element = $this->elements->get($position);

        if ($element === null) {
            $element = new Collection();
            $this->elements->set($position, $element);
        }

        $element->add($exercise);
    }
}
