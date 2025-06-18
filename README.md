# Silverstripe Vitesse

Support for Vite in Silverstripe, built atop [Laravel’s Vite components](https://laravel.com/docs/12.x/vite).

## Setup

```bash
composer require bigfork/silverstripe-vite
```

Setup the `laravel-vite-plugin` in your `vite.config.js`:

```js
import {defineConfig} from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
  plugins: [
    laravel({
      input: [
        'src/scss/style.scss',
        'src/scss/editor.scss',
        'src/js/app.js',
      ],
      refresh: [
        'templates/**/*.ss',
      ],
    }),
  ],
  // ... etc
})
```

## Usage

Most of the [Laravel Vite documentation](https://laravel.com/docs/12.x/vite) applies, though with an adjusted syntax for Silverstripe templates vs Blade.

Include scripts/stylesheets using the `<% vite %>` tag, for example:
```html
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <% base_tag %>
    {$MetaTags}

    <% vite 'src/scss/style.scss', 'src/js/app.js' %>
</head>
```

For React projects, you can also include `<% viteReactRefresh %>`. Note that this must be included before any calls to `<% vite %>`

Paths for assets processed via Vite can be output using `$viteAsset`:
```html
<img src="{$viteAsset('src/images/logo.svg')}" alt="Logo" width="100" height="100" />
```

File contents for assets processed via Vite can be output using `$viteContent`:
```html
<div class="my-inline-svg">
    {$viteContent('src/images/image.svg')}
</div>
```

## Configuration

If you’re using different build directories, or want to take advantage of [other features](https://laravel.com/docs/12.x/vite), you can adjust configuration via YAML or directly in PHP:

YAML config:
```yml
SilverStripe\Core\Injector\Injector:
  Bigfork\Vitesse\Vite:
    calls:
      buildDirectory: [ 'useBuildDirectory', ['dist'] ]
```

PHP method calls:
```php
use Bigfork\Vitesse\Vite;
use SilverStripe\CMS\Controllers\ContentController;

class PageController extends ContentController
{
    protected function init()
    {
        parent::init();
        Vite::inst()->useScriptTagAttributes(['data-foo' => 'bar']);
    }
}
```
