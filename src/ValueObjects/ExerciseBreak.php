<?php
declare(strict_types=1);

namespace Wod\ValueObjects;

use Wod\Contracts\ExerciseInterface;

final class ExerciseBreak implements ExerciseInterface
{
    public function getName(): string
    {
        return "Break";
    }

    public function isCardio(): bool
    {
        return false;
    }
}
