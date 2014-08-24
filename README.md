
# Attentra - Attendance tracking monitoring

Caution : WIP

This project allows to monitor the working hours by employees, equipment rentals ...
A second project (soon available) will permit to use a Raspberry Pi as a time clock, using RFID chips.

## Features

* Resources crud
* REST API
* Working hours calendar per resource
* Data monitoring per resource and per period
* Data exports
* Personnal dashboard as resource

## TODO (almost all)

* Migrations
* REST API
* Calendar
* Data monitoring per resource
* Data exports
* Personnal dashboard
* Exports

* Paginate the Time Inputs
* Use NelmioApiDocBundle to generate the API doc

* Add true users management : each user can access to one or many resources (consumers), many groups of resources (team leaders), or to all resources (admins)

## Usage

    git clone git@github.com:mRoca/attentra.git
    composer install
    app/console doctrine:database:create
    app/console doctrine:schema:update --force

