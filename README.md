# W.I.P.

# Larapress Profiles
A package to provide common Models for a Larapress Web Application.

## Dependencies
* [Larapress CRUD](../../../press-crud)
* [Larapress Reports](../../../press-reports)

## Install
1. ```composer require ```

## Config
1. Run ```php artisan vendor:publish --tag=larapress-profiles```

## Usage
* This package provides following models:
    1. Domain
    1. Form & Form-Entrie
    1. Email, Phone, Address, Device/Client
    1. Settings, Activity-Log

* Add ``IProfileUser`` and ``BaseProfileUser`` to your ``User`` class

## Development/Contribution Guid
* See guid in [Larapress CRUD](../../../press-crud)
