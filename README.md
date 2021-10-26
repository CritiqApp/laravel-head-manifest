## Laravel Head Manifest

### Installation

**Step 1:** Add Laravel Head Manifest to your laravel project

```bash
composer require critiq/laravel-head-manifest
```

**Step 2:** Add `LaravelHeadManifestServiceProvider` to your app's configured providers

In `app/config/app.php`:

```diff
 'providers' => [

        ... // Other providers,

        App\Providers\RouteServiceProvider::class,
        + Critiq\LaravelHeadManifest\LaravelHeadManifestServiceProvider::class,

```

**Step 3:** Create your manifest file

In your `public` folder, create `head-manifest.json` with the following base structure:

```json
{
    "defaultTitle": "Welcome to Laravel Head Manifest",
    "globalMeta": [],
    "defaultMeta": [],
    "paths": {
        "test/1": {
            "title": "Test1 Title",
            "meta": [
                {
                    "name": "Description",
                    "property": "description",
                    "content": "This is a description"
                }
            ]
        },
        "test/2": {
            "title": "Test2 Title",
            "meta": []
        },
    }
    
}
```

**Step 4:** In your blade file(s), add the `$metadata` variable

```diff

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        +{!! $metadata !!}

        ... // Rest of blade template
```

## Usage



### Define your path structures

#### Basic path structure
In the manifest's `paths` values, you may define keys representing a path and an associated object containing the associated path's title and meta objects (more on meta objects later).
```json
{
    ... // Rest of manifest
    "paths": {
        "/": {
            "title": "Home",
            "meta": [],
        }
    }
}
```

If you do not specify a `title` in the path object, it will fall back to your manifest's `defaultTitle` value.

#### Metadata structure
The metadata structure is a dictionary of attributes which directly translates to the attributes of an HTML `meta` tag

For instance:
```json
... // Rest of manifest
"meta": [
    { "name": "description", "content": "This is a sample meta description"}
],

```
Will convert to:
```html
<meta name="description" content="This is a sample meta description" />
```

#### Global Metadata
Specifying the root `globalMeta` list of metadata objects will always be added. This is useful if you need metadata to exist on every page.

#### Default Metadata
Specifying the root `defaultMeta` list of metadata objects will only be added if the request path isn't matched, or if a path's `meta` field isn't specified. If you want to omit the default metadata from a path, pass an empty array as the `meta` value.

#### Path variables
Laravel Head Manifest supports variable paths. While defining your path key, prefix a path section with `:` followed by the name of the variable.

Example:
```json
{
    ... // Rest of manifest
    "paths": {
        "/page/:pageName": {
            "title": "You're visiting: :pageName",
        }
    }
}
```

If you were to navigate to `/page/cats`, this would match the `/page/:pageName` path and set `pageName` variable to "cats". You may reference this variable in any string within the path object.

#### Match all paths with prefix
If you would like to support matching all nested paths from a root path, your may append your path name with a `*`.

Example:
```json
{
    ... // Rest of manifest
    "paths": {
        "/admin/*": {
            "title": "Secret admin dashboard",
        }
    }
}
```
Visiting paths such as `/admin`, `/admin/banusers` and `/admin/purge/everything` will all match the defined `/admin/*` path.

### Get resolved path data from the server
Laravel Head Manifest includes a simple controller which may be used to resolve a path and return the head data for a specified path (without having to navigate to said path). This is in the form of the `LaravelHeadManifestController`. While there are many ways to use this controller, due to how Laravel's default `RouteServiceProvider` is structured, we recommend the following:

**Step 1:** In `app/Http/Controllers`, create a `HeadManifestController.php` file and define the controller with:

```php
<?php

namespace App\Http\Controllers;

use Critiq\LaravelHeadManifest\LaravelHeadManifestController;

class HeadManifestController extends LaravelHeadManifestController { }
```

**Step 2**: In your `web.php`, create a GET route to your new controller
```php
Route::get('head-manifest', 'HeadManifestController@index');
```

**Note:** Depending on your project structure, this may not work right away. I recommend reading up on how Laravel's routing system works (notably namespaces in the `RouteServiceProvider`). It's not essential to create your own `HeadManifestController`, you may instead route to the `LaravelHeadManifestController` class directly.

## Why this project?
SPA apps don't always work well with our robot friends (Google crawlers, Facebook/Slack/Discord/etc unfurling, and many, many more). The goal of this project is to provide a way to elegantly define your head data for use in SPA contexts without sacrifing the benefits of page-specific metadata.