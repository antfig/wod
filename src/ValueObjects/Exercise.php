<?php
declare(strict_types=1);

namespace Wod\ValueObjects;

use Wod\Contracts\ExerciseInterface;

class Exercise implements ExerciseInterface
{
    /**
     * @var string
     */
    private $name;
    /**
     * @var bool
     */
    private $cardio;

    /**
     * Exercise constructor.
     *
     * @param string $name
     * @param bool $cardio
     */
    public function __construct(string $name, bool $cardio = false)
    {
        $this->name = $name;
        $this->cardio = $cardio;
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
    public function isCardio(): bool
    {
        return $this->cardio;
    }
}
