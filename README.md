# Working of the day generator assignment
[![Codacy Badge](https://api.codacy.com/project/badge/Grade/fb993d5a26864a89b16ddf62f421f780)](https://www.codacy.com?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=antfig/wod&amp;utm_campaign=Badge_Grade)
[![Maintainability](https://api.codeclimate.com/v1/badges/58929e097b9cfe71dc80/maintainability)](https://codeclimate.com/github/antfig/wod/maintainability)
[![Test Coverage](https://api.codeclimate.com/v1/badges/58929e097b9cfe71dc80/test_coverage)](https://codeclimate.com/github/antfig/wod/test_coverage)

## Business rules
- During the program participants get two breaks. Beginners get four breaks of 1
  minute instead of two
- The program shouldn’t begin or end with breaks
- Beginners should do a maximum of 1 handstand practise during the WOD
- Jumping jacks, jumping rope and short sprints are cardio exercises and should not
  follow after each other
- The gym has limited space for the rings and pullups, a maximum of 2 participants
  may do either one of these exercises (ring + pull up combined max 2)  

## Requirements
- Docker
- Or PHP 7.3 to run locally

## Install
- Clone this repo and enter it

- Build docker image (For docker usage)
```
$ docker build -t wod .
```

- Install dependencies
```
Local
$ composer install

Or with docker
$ docker run --rm --volume $(pwd):/app -it wod composer install
```

## Usage

```
Local
$ php wod.php

Or Docker
$ docker run --rm --volume $(pwd):/app -it wod

```

## Testing

```
$ vendor/bin/phpunit
$ vendor/bin/phpunit --coverage-html coverage/
$ vendor/bin/phpunit --testdox

Docker
$ docker run --rm --volume $(pwd):/app -it wod vendor/bin/phpunit
```

## Static Analysis (phpstan)

```bash
vendor/bin/phpstan analyse
```

## Example output

```
Starting the workout with Camille, Michael, Tom (beginner), Tim, Erik, Lars, Mathijs (beginner)
00:00 - 01:00 | Camille will do Push ups | Michael will do Jumping rope | Tom (beginer) will do Short sprints | Tim will do Back squats | Erik will do Pull ups | Lars will do Jumping jacks | Mathijs (beginer) will do Rings
01:00 - 02:00 | Camille will do Push ups | Michael will do Back squats | Tom (beginer) will take a Break | Tim will do Pull ups | Erik will take a Break | Lars will do Pull ups | Mathijs (beginer) will do Back squats
02:00 - 03:00 | Camille will do Jumping jacks | Michael will do Handstand practice | Tom (beginer) will do Short sprints | Tim will do Pull ups | Erik will take a Break | Lars will do Jumping rope | Mathijs (beginer) will do Back squats
03:00 - 04:00 | Camille will do Push ups | Michael will do Front squats | Tom (beginer) will do Push ups | Tim will do Short sprints | Erik will do Front squats | Lars will do Push ups | Mathijs (beginer) will do Pull ups
04:00 - 05:00 | Camille will do Jumping jacks | Michael will do Pull ups | Tom (beginer) will do Rings | Tim will do Front squats | Erik will do Handstand practice | Lars will do Handstand practice | Mathijs (beginer) will do Jumping rope
...
```
