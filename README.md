# SentryPHPManager

## Description

SentryPHPManager is a PHP library that allows you to integrate Sentry with your PHP application as
plug-and-play as originally is, with benefits.

>It aims to be a simple and easy to use library that makes the work of
set the things up in a single line of code at any time of the runtime.
> 
Very useful for debugging and testing purposes, change the environment param of the
communication whenever you think you need to.

E.g.:

    Sentry::run();
    Sentry::in('production');
    Sentry::debug(); // sets the environment param to 'debug'
