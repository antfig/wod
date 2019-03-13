<?php
declare(strict_types=1);

namespace Wod\Entity;

use Wod\Collection;
use Wod\Contracts\ExerciseInterface;
use Wod\Exception\BeginnerCanOnlyDoOneHandstandPracticeException;
use Wod\Exception\CardioExercisesCanNotFollowEachOtherException;
use Wod\Exception\ParticipantBreaksLimitException;
use Wod\Exception\ParticipantCanNotStartOrEndWithBreakException;
use Wod\ValueObjects\ExerciseBreak;
use Wod\Wod;

class Participant
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var bool
     */
    private $beginner;

    /**
     * @var Collection
     */
    private $exercises;

    public function __construct(string $name, bool $beginner)
    {
        $this->name = $name;
        $this->beginner = $beginner;
        $this->exercises = new Collection();
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return bool
     */
    public function isBeginner(): bool
    {
        return $this->beginner;
    }

    /**
     * @param ExerciseInterface $element
     * @param int $position starting in 0
     *
     * @throws BeginnerCanOnlyDoOneHandstandPracticeException
     * @throws ParticipantBreaksLimitException
     * @throws ParticipantCanNotStartOrEndWithBreakException
     * @throws CardioExercisesCanNotFollowEachOtherException
     */
    public function addExercise(ExerciseInterface $element, int $position): void
    {
        $this->ensureNoBreaksAtBeginningOrEnd($element, $position);

        $this->ensureLimitOfBreaks($element);

        $this->ensureMaximumOfOneHandstandPractice($element);

        $this->ensureCardioExercisesNotFollowEachOther($element, $position);

        $this->exercises->set($position, $element);

        // fill 2 elements with break when isn't beginner
        if (!$this->isBeginner() && $element instanceof ExerciseBreak) {
            $this->exercises->set($position + 1, $element);
        }
    }

    /**
     * @param string $exercise
     *
     * @return bool
     */
    public function hasDoneExercise(string $exercise): bool
    {
        return $this->exercises->filter(function (ExerciseInterface $finishedExercise) use ($exercise) {
                return $finishedExercise->getName() == $exercise;
            })->count() > 0;
    }

    /**
     * @param int $position
     *
     * @return ExerciseInterface|null return null when don't exist exercise for the position
     */
    public function getExercise(int $position): ?ExerciseInterface
    {
        return $this->exercises->get($position);
    }

    /**
     * @return Collection
     */
    public function getAllExercises(): Collection
    {
        return $this->exercises;
    }

    /**
     * Number of breaks made
     *
     * @return int
     */
    public function numberOfBreaks(): int
    {
        return $this->exercises->filter(function (ExerciseInterface $exercise) {
            return $exercise instanceof ExerciseBreak;
        })->count();
    }

    /**
     * @return int
     */
    public function getMaxOfBreaks(): int
    {
        return $this->isBeginner() ? Wod::MAX_BREAKS_BEGINNER : Wod::MAX_BREAKS_REGULAR;
    }

    /**
     * @return int
     */
    private function getNumberOfMaxElements(): int
    {
        return Wod::ELEMENTS;
    }

    /**
     * @param ExerciseInterface $element
     * @param int $position
     *
     * @throws ParticipantCanNotStartOrEndWithBreakException
     */
    private function ensureNoBreaksAtBeginningOrEnd(ExerciseInterface $element, int $position)
    {
        if ($element instanceof ExerciseBreak
            && ($position === 0 || $position >= $this->getNumberOfMaxElements() - 1)) {
            throw new ParticipantCanNotStartOrEndWithBreakException();
        }
    }

    /**
     * @param ExerciseInterface $element
     *
     * @throws ParticipantBreaksLimitException
     */
    private function ensureLimitOfBreaks(ExerciseInterface $element)
    {
        if ($element instanceof ExerciseBreak
            && $this->numberOfBreaks() === $this->getMaxOfBreaks()) {

            throw new ParticipantBreaksLimitException("Participant reach limit of breaks: " . $this->getMaxOfBreaks());
        }
    }

    /**
     * @param ExerciseInterface $element
     * @throws BeginnerCanOnlyDoOneHandstandPracticeException
     */
    private function ensureMaximumOfOneHandstandPractice(ExerciseInterface $element)
    {
        $handstandPractice = 'Handstand practice';
        if ($element->getName() === $handstandPractice
            && $this->isBeginner()
            && $this->hasDoneExercise($handstandPractice)) {

            throw new BeginnerCanOnlyDoOneHandstandPracticeException();
        }
    }

    /**
     * @param ExerciseInterface $element
     * @param int $position
     *
     * @throws CardioExercisesCanNotFollowEachOtherException
     */
    private function ensureCardioExercisesNotFollowEachOther(ExerciseInterface $element, int $position)
    {
        if (!$element->isCardio()) {
            return;
        }

        $elementBefore = $this->getExercise($position - 1);

        if ($elementBefore !== null
            && $elementBefore->isCardio()) {
            throw new CardioExercisesCanNotFollowEachOtherException();
        }

        $elementAfter = $this->getExercise($position + 1);
        if ($elementAfter !== null
            && $elementAfter->isCardio()) {
            throw new CardioExercisesCanNotFollowEachOtherException();
        }
    }
}
