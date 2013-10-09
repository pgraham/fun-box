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

### announceWait

Output a countdown to the command line.

    announceWait($waitTime, $message = 'Commencing');

 -  @param **waitTime** _integer_ Number of seconds to count down before
continuing.
 -  @param **message** _string_ What will happen when the countdown is complete.

### passwordPrompt

Prompt the user for a password from the command line.

    passwordPrompt($prompt = 'password: ');

 -  @param **prompt** _string_ The prompt for the user.
