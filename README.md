PHPRelease
==========
[![Build Status](https://travis-ci.org/c9s/PHPRelease.png?branch=master)](https://travis-ci.org/c9s/PHPRelease)

The simplest way to define your release process.

Features
---------

- Automatically version bumping for Composer, Onion, PHPDoc or Class constant.
- Support version parsing from PHPDoc or class const.
- Git tagging, pushing.
- Simplest config.

Install
-------

```sh
$ curl -O https://raw.github.com/c9s/PHPRelease/master/phprelease
$ chmod +x phprelease
$ mv phprelease /usr/bin
```

Usage
-----

Create phprelease.ini config file by a simple command:


```sh
$ phprelease init
```

The above command creates a `phprelease.ini` config file, you can also edit it
by yourself:

```ini
Steps = PHPUnit, BumpVersion, GitTag, GitPush, GitPushTags
```

The release steps may contains script files, you can simply insert the script
path and phprelease will run it for you. the return code 0 means we are going
to the next step.

```ini
Steps = BumpVersion, scripts/compile, GitTag

```

Then, to release your package, simply type:

```sh
$ phprelease
```

Skip Specific Step
--------------------------

```sh
$ phprelease --skip BumpVersion
```

Version From
------------

If you defined your version string in your PHP source file or class const, 
to bump version from php source file, you can simply define a `VersionFrom` option:


```ini
; to read version from php class file or from phpdoc "@VERSION ..."
VersionFrom = src/PHPRelease/Console.php
```


Task Options
------------

Each task has its own options, run help command, you should see the options from these tasks:

    $ phprelease help
    PHPRelease - The Fast PHP Release Manager

    Usage
        phprelease [options] [command] [argument1 argument2...]

    Options
           -v, --verbose   Print verbose message.
             -d, --debug   Print debug message.
             -q, --quiet   Be quiet.
              -h, --help   help
               --version   show version
                   --dry   dryrun mode.
            --bump-major   bump major (X) version.
            --bump-minor   bump minor (Y) version.
            --bump-patch   bump patch (Z) version, this is the default.
       --remote <value>+   git remote names for pushing.


So to bump the major verion, simply pass the flag:

    phprelease --bump-major

You can also test your release steps in dry-run mode:

    phprelease --dryrun


Built-In Tasks
--------------

    BumpVersion
    GitCommit
    GitPush
    GitPushTags
    GitTag
    PHPUnit


Hacking
-------

1. For this project.

2. Get composer, and run:

    composer install --dev

3. Hack!


