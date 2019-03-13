<?php

use Wod\Collection;
use Wod\Entity\Participant;
use Wod\Generator;
use Wod\ValueObjects\Exercise;
use Wod\ValueObjects\ExerciseBreak;

// Register the autoloader
require_once __DIR__ . '/vendor/autoload.php';

// Load Participants
$participants = new Collection();
$participants->add(new Participant('Camille', false))
    ->add(new Participant('Michael', false))
    ->add(new Participant('Tom', true))
    ->add(new Participant('Tim', false))
    ->add(new Participant('Erik', false))
    ->add(new Participant('Lars', false))
    ->add(new Participant('Mathijs', true));

// Load Exercises Elements
$exercises = new Collection();
$exercises->add(new Exercise('Jumping jacks', true))
    ->add(new Exercise('Push ups', false))
    ->add(new Exercise('Front squats', false))
    ->add(new Exercise('Back squats', false))
    ->add(new Exercise('Pull ups', false))
    ->add(new Exercise('Rings', false))
    ->add(new Exercise('Short sprints', true))
    ->add(new Exercise('Handstand practice', false))
    ->add(new Exercise('Jumping rope', true))
    ->add(new ExerciseBreak());

$generator = new Generator($participants, $exercises);
$generator->run();

echo "\n\nEnd of the program";
