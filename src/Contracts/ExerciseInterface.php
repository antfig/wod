<?php
declare(strict_types=1);

namespace Wod\Contracts;

interface ExerciseInterface
{
    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @return bool
     */
    public function isCardio(): bool;

}
