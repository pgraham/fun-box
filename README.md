# Fun Box

A set of useful utility functions.

## Install

Install via [Composer](http:://getcomposer.org):

    {
        "require": {
            "zeptech/fun-box": "dev-master"
        }
    }

## Initialization

In order to use functions Fun Box must be initialized because php doesn't
provide function autoloading. Once initialized, functions are included using the
`ensureFn` function.

    <?php
    // ... Initialize composer
    FunBox::init();
    ensureFn('passwordPrompt');

    // ...
    if ($interactive) {
        $pw = passwordPrompt();
    }

## Functions

List of functions provided by Fun Box

### announceWait($waitTime, $message = 'Commencing');

Output a countdown to the command line.

**@param** _waitTime_ {integer} Number of seconds to count down before
continuing.

**@param** _message_ {string} What will happen when the countdown is complete.

### passwordPrompt($prompt = 'password: ');

Prompt the user for a password from the command line.

**@param** _prompt_ {string} The prompt for the user.
