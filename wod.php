<?php

use Wod\Collection;
use Wod\Entity\Participant;
use Wod\Generator;
use Wod\ValueObjects\Exercise;
use Wod\ValueObjects\ExerciseBreak;
use Wod\Wod;

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
$wod = $generator->run();

// Print by Element (time)
for ($element = 0; $element < Wod::ELEMENTS; $element++) {

    printf("\n\n%02d:00 - %02d:00", $element, $element + 1);

    /** @var Participant $participant */
    foreach ($wod->getParticipants() as $participant) {

        $participantExercise = $participant->getExercise($element);
        echo " | "
            . $participant->getName()
            . ($participant->isBeginner() ? ' (beginer)' : '')
            . ($participantExercise instanceof Exercise ? ' will do ' : ' will take a ')
            . $participantExercise->getName();
    }
}


//Print by participant
foreach ($wod->getParticipants() as $participant)
{
        echo "\n---------------\n";
        echo $participant->getName() . ($participant->isBeginner() ? ' (beginer) ' : '');
        /** @var Exercise $e */
        foreach ($participant->getAllExercises() as $p => $e) {
            printf("\n%02d:00 - %02d:00 ", $p, $p + 1);
            echo $e->getName() . ($e->isCardio() ? ' (Cardio)' : '');
        }
}

echo "\n\nEnd of the program";
