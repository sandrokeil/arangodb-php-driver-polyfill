# Polyfill for ArangoDB PHP extension

[![Gitter](https://badges.gitter.im/Join%20Chat.svg)](https://gitter.im/prooph/improoph)

Polyfill for [ArangoDB PHP extension](https://github.com/sandrokeil/arangodb-php-driver)

## Requirements

- PHP >= 7.1
- ArangoDB server version >= 3.2

## Tests
If you want to run the unit tests locally you need [Docker](https://docs.docker.com/engine/installation/ "Install Docker") 
and [Docker Compose](https://docs.docker.com/compose/install/ "Install Docker Compose").

Install dependencies with:

```
$ docker run --rm -i -v $(pwd):/app prooph/composer:7.1 update -o
```

Start containers with
```
$ docker-compose up -d --no-recreate
```

Execute tests with

```
$ docker-compose run --rm php vendor/bin/phpunit
```

