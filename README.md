# Working of the day generator assignment


## Requirements
- Docker
- Or PHP 7.2 to run locally

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
$ docker run --rm --volume $PWD:/app -it wod \ 
    vendor/bin/phpunit --coverage-html build/
```

## Usage

```
Local
$ php wod.php

Or Docker
$ docker run --rm --volume $PWD:/app -it wod

```

## Testing

```
$ vendor/bin/phpunit
$ vendor/bin/phpunit --coverage-html coverage/
$ vendor/bin/phpunit --testdox

Docker
$ docker run --rm --volume $PWD:/app -it wod vendor/bin/phpunit
```
