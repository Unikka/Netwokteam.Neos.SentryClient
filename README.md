Unikka.Legacy.Neos.SentryClient
=============================

This is a Sentry client package for the legacy Neos CMS versions (https://www.neos.io).
It's based on the Sentry package of the Network-Team.

Have a look at https://sentry.io for more information about Sentry.

Installation:
-------------

    $ composer require unikka/legacy-neos-sentryclient

Configuration:
--------------

Add the following to your `Settings.yaml` and replace the `dsn` setting with your project DSN (API Keys in your Sentry project):

    Networkteam:
      SentryClient:
        # The Sentry DSN
        dsn: 'http://secret_key@your-sentry-server.com/project-id'

You can implement the `\Unikka\Neos\SentryClient\User\UserContextServiceInterface` to pass your own user context 
information to the logging. If you do not have the TYPO3.Party Package and don't want to implement your own 
`UserContextService` you need to set the `\Unikka\Neos\SentryClient\User\DummyUserContext` in the Objects.yaml like

    Unikka\Neos\SentryClient\User\UserContextServiceInterface:
      className: Unikka\Neos\SentryClient\User\DummyUserContext

This will prevent any collection of user information except information that is available via the Flow SecurityContext.

Usage:
------

Sentry will log all exceptions that have the rendering option `logException` enabled. This can be enabled or disabled
by status code or exception class according to the Flow configuration.

Targetgroup:
------------

Sometimes you have to work on legacy versions of an system and you would like to use sentry
reporting also for the legacy Software. While upgrading the software tools like sentry are really helpful.

The package is build for older Neos versions like neos 2.3. The networkteam client in a older version sadly did not
work anymore. So this package uses the latest sentry API for the legacy neos.

Development:
------------

This package is managed on GitHub. Feel free to get in touch at https://github.com/Unikka/Unikka.Legacy.Neos.SentryClient.
This package is based on the Netwokteam.Neos.SentryClient.

License:
--------

See the [LICENSE](LICENSE.md) file for license rights and limitations (MIT).
