
# Attentra - Attendance tracking monitoring

Caution : WIP

This project allows to monitor the working hours by employees, equipment rentals ...
Another project (https://github.com/mRoca/attentra-worker) allows to use a Raspberry Pi as a time clock, using RFID chips.

## Features

* Resources crud
* REST API
* Working hours calendar per resource
* Data monitoring per resource and per period
* Data exports
* Personnal dashboard as resource

## TODO

* Migrations
* Event creation from calendar
* Data exports
* Personnal dashboard
* Authentication layer
* Users management : each user can access to one or many resources (consumers), many groups of resources (team leaders), or to all resources (admins)
* Improve the API documentation generated markdown
* Adapt to see spent time per hour

## Usage

### From command line

    git clone git@github.com:mRoca/attentra.git
    composer install
    app/console doctrine:database:create
    app/console doctrine:schema:update --force

### From files

* Put the project files in the concerned directory
* Go to /postdeploy/run.php to install all resources

## API Documentation

For the HTML version, with sandbox (NelmioApiDoc) : clone, install the project and go to :

    /api/doc

For the generated markdown version, see

    src/Attentra/ApiBundle/Resources/doc

[Read the documentation](https://github.com/mRoca/attentra/blob/master/src/Attentra/ApiBundle/Resources/doc/index.md)

# Contributing

## Running the Tests

Install the [Composer](http://getcomposer.org/) `dev` dependencies:

    php composer.phar install --dev

Then, run the test suite using
[PHPUnit](https://github.com/sebastianbergmann/phpunit/):

    phpunit -c app/

## Generate the API documentation

    app/console api:doc:dump --format=markdown > src/Attentra/ApiBundle/Resources/doc/index.md
