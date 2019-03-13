<?php
declare(strict_types=1);

namespace Wod\ValueObjects;

use Wod\Contracts\ExerciseInterface;

final class ExerciseBreak implements ExerciseInterface
{
    /**
     * @return string
     */
    public function getName(): string
    {
        return "Break";
    }

    /**
     * @return bool
     */
    public function isCardio(): bool
    {
        return false;
    }
}
