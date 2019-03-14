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
$participants->add(new Participant('Camille', Participant::NOT_BEGINNER))
    ->add(new Participant('Michael', Participant::NOT_BEGINNER))
    ->add(new Participant('Tom', Participant::IS_BEGINNER))
    ->add(new Participant('Tim', Participant::NOT_BEGINNER))
    ->add(new Participant('Erik', Participant::NOT_BEGINNER))
    ->add(new Participant('Lars', Participant::NOT_BEGINNER))
    ->add(new Participant('Mathijs', Participant::IS_BEGINNER));

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

// Generate random wod for participants
$wod = (new Generator($participants, $exercises))->run();

\Wod\Output::fromWod($wod)->printByElement();
// \Wod\Output::fromWod($wod)->printByParticipant();

echo "\n\nEnd of the program\n";
