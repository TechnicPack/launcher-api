# TechnicPack Launcher Api

[![Latest Stable Version](https://poser.pugx.org/technicpack/launcher-api/v/stable)](https://packagist.org/packages/technicpack/launcher-api) [![Total Downloads](https://poser.pugx.org/technicpack/launcher-api/downloads)](https://packagist.org/packages/technicpack/launcher-api) [![CircleCI](https://circleci.com/gh/TechnicPack/launcher-api.svg?style=shield)](https://circleci.com/gh/TechnicPack/launcher-api) [![Coverage Status](https://coveralls.io/repos/github/TechnicPack/launcher-api/badge.svg?branch=master)](https://coveralls.io/github/TechnicPack/launcher-api?branch=master) [![StyleCI](https://styleci.io/repos/7548986/shield)](https://styleci.io/repos/127381550) [![License](https://poser.pugx.org/technicpack/launcher-api/license)](https://github.com/TechnicPack/launcher-api/blob/master/LICENSE.md)


This package provides the endpoints necessary for the TechnicPack Launcher to
load a modpack, its builds and the mods related with a build.

## Getting Started

These instructions will get you a copy of the package up and running on your local machine for development and testing purposes. Its important to note that this package does very little on its own. Its intended to be included in a larger project such as [TechnicPack Solder](https://github.com/technicpack/solder). See deployment for notes on how to deploy the package into another project.

### Prerequisites

Most of the requirements are handled through composer but you will still need to have a system with PHP 7.1+ and composer installed.

### Setup

The package takes advantage of a framework for Laravel package development called Orchestral Testbench so there is little to do beyond cloning the repo and installing dependencies.

```bash
$ git clone https://github.com/technicpack/launcher-Api
$ cd launcher-api
$ composer install
```

## Running the tests

Its important that the project maintain very high test coverage to ensure that changes to the code don't break any expected behavior from the API. This API is called on nearly every time a user runs the TechnicPack Launcher, its an invisible part of what makes Technic work, and we want to keep it invisible to the day-to-day user.

### PHPUnit Feature and Unit tests

A majority of the testing is done in feature tests which test the expected output of API endpoints under various conditions and with various inputs. You can run the full suite of unit and feature tests with PHPUnit.

```bash
$ vendor/bin/phpunit
```

### Code style tests

Code style is also very important to us, a consistent code style makes the project easier to maintain and the pre-defined rules for how code should look lets everyone focus on function over form. Any push or PR will be checked by StyleCI before being merged. In order to reduce the number of commits, a local config and tool are included to allow you to run a fixer on your code before pushing it up to github.

```bash
$ vendor/bin/php-cs-fixer fix -v
```

## Versioning

We use [SemVer](http://semver.org/) for versioning. For the versions available, see the [tags on this repository](https://github.com/technicpack/launcher-api/tags).

## Contributing

Please read [CONTRIBUTING.md](https://github.com/technicpack/launcher-api/CONTRIBUTING.md) for details on our code of conduct, and the process for submitting pull requests to us.


## Deployment

Deploying this package into a project is not trivial; the package is flexible but opinionated about the general underlying data structure. The instructions here will not be exhaustive, but should get you started towards integrating the Launcher API into your project.

### Installation

Require this package with composer.

```bash
$ composer require technicpack/launcher-api
```

Laravel 5.5 uses Package Auto-Discovery, so doesn't require you to manually add the ServiceProvider.

If you are using Laravel < 5.5, you also need to add the service provider to your `config/app.php` providers array:

```php
TechnicPack\LauncherApi\Providers\LauncherApiServiceProvider::class,
```

The package includes some database migrations, so make sure you run those migrations with `php artisan migrate`.

### Configuration

The defaults are set in config/launcher-api.php. Copy this file to your own config directory to modify the values. You can publish the config using this command:

```bash
$ php artisan vendor:publish --tag=launcher-api-config
```

The configuration file is pretty well documented but make sure you read through everything and align it with your application. Sane defaults are set for all options, but these won't guarantee that the application works without any tweaking.

### Implementation

As much of the code as possible has been encapsulated into the package; but there are some changes required to your application to hook up all the pieces. We've made those changes as minimal as possible end grouped things into traits and interfaces for ease of deployment.

**Linking Modpacks to Clients**

The `HasClients` trait provides several methods to assist you in managing private modpacks exposed to known clients. The trait defines a clients relation to the `TechnicPack\LauncherApi\Client` model allows you to iterate over all of the modpacks's clients:

```php
foreach ($modpack->clients as $client) {
    echo $client->token;
}
```

The trait also provides several other helper methods that are useful when working with clients:

```php
if ($modpack->hasClients()) {
    // This modpack has at least one client...
}

if ($modpack->knowsClient($client)) {
    // The modpack knows the given client (the client is attached)...
}

// Get modpacks for which the given client is known...
$modpacks = Modpack::scopeForClient($client)->get();

// Get modpacks for which the given client token is known...
$modpacks = Modpack::scopeForClientToken($token)->get();

// Attach the client to the modpack...
$modpack->attachClient($client);

// Remove the client from the modpack...
$modpack->dettachClient($client);
```

**Describe relationships and scopes**

This package assumes that the Modpack, Build and Mod models expose information about how they're related, and how to limit queries to specific kinds of results. The easiest way to provide that information is to implement the Modpack and Build interfaces on your models (there is no interface for Mod since the only information we need comes from the `$build->mods()` relationship).

```php
use TechnicPack\LauncherApi\Build as PlatformBuild;
use TechnicPack\LauncherApi\Modpack as PlatformModpack;

class Modpack implements PlatformModpack
{
    public function builds()
    {
        // Return a hasMany or belongsToMany relationship of your builds model
        // eg: return $this->hasMany(Build::class);
    }

    public function scopePublic(Builder $query)
    {
        // Provide a scope for modpacks which should be public
        // eg: return $query->where('private', false);
    }

    public function scopePrivate(Builder $query)
    {
        // Provide a scope for modpacks which should be private
        // eg: return $query->where('private', true);
    }
}

class Build implements PlatformBuild
{
    public function mods()
    {
        // Return a hasMany or belongsToMany relationship of your Mods model
        // eg: return $this->hasMany(Mod::class);
    }

    public function scopePublic(Builder $query)
    {
        // Provide a scope for builds which should be public
        // eg: return $query->where('private', false);
    }

    public function scopePrivate(Builder $query)
    {
        // Provide a scope for builds which should be private
        // eg: return $query->where('private', true);
    }
}
```

*Frontend*

This package ships with a JSON API that you may use to allow your users to create clients and keys. However, it can be time consuming to code a frontend to interact with these APIs. So, we've also included pre-built Vue components you may use as an starting point for your own implementation.

_note that the components do not include ANY template, you must implement your own html as an inline-template_

To publish the Launcher API Vue components, use the vendor:publish Artisan command:

```bash
$ php artisan vendor:publish --tag=launcher-api-components
```

The published components will be placed in your `resources/assets/js/vendor/launcher-api` directory. Once the components have been published, you should register them in your `resources/assets/js/app.js` file:

```js
require('./vendor/launcher-api/bootstrap');
```

After registering the components, make sure to run npm run dev to recompile your assets. Once you have recompiled your assets, you may drop the components into one of your application's templates to get started creating clients and keys.

```html
<launcher-api-clients inline-template>
    <launcher-api-create-client inline-template> ... </launcher-api-create-client>
    <launcher-api-list-clients :clients="clients" inline-template> ... </launcher-api-list-clients>
</launcher-api-clients>

<launcher-api-keys inline-template>
    <launcher-api-create-key inline-template> ... </launcher-api-create-key>
    <launcher-api-list-keys :keys="keys" inline-template> ... </launcher-api-list-key>
</launcher-api-keys>
```

## Authors

* **Kyle Klaus** - *Initial work* - [Indemnity83](https://github.com/indemnity83)

See also the list of [contributors](https://github.com/technicpack/launcher-api/contributors) who participated in this project.

## License

This project is licensed under the MIT License - see the [LICENSE.md](LICENSE.md) file for details
