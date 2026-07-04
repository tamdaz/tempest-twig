# tempest-twig

Tempest Twig is a third-party package that integrates the Twig templating engine with the [Tempest framework](https://tempestphp.com). It provides full Twig `3.x` support, custom extensions for routing and debugging, and an innovative component system using HTML-like syntax.

The package includes flexible configuration for template paths, built-in Twig extensions that work with Tempest's routing and Vite pipeline, and a sophisticated component transformation system.

## Installation

First, install the Composer package:

```bash
composer require tamdaz/tempest-twig
```

## Configuration

### View configuration

For the moment, Tempest still renders views with its own default renderer. To render views with Twig, create _(or edit)_ your view configuration and set `rendererClass` explicitly via `php tempest make:config view`:

```diff
+use Tamdaz\TempestTwig\Twig\TwigViewRenderer;
use Tempest\View\ViewConfig;

return new ViewConfig(
+    rendererClass: TwigViewRenderer::class,
);
```

This keeps the renderer choice explicit: installing Tempest Twig never overrides an already configured renderer.

### Twig Options

Twig itself is configured through a file in the `config` directory, automatically discovered and loaded by Tempest.

```php
// config/twig.config.php
use Tempest\View\Renderers\TwigConfig;
use function Tempest\env;
use function Tempest\root_path;

return new TwigConfig(
    viewPaths: [
        root_path('templates'),
    ],
    debug: env('APP_DEBUG', false),
    strictVariables: true,
);
```

The `TwigConfig` class wraps Twig's standard options. The `viewPaths` parameter specifies which directories to search for templates, and multiple directories can be added for different template locations. Other options like `debug`, `charset`, `strictVariables`, and `autoescape` follow Twig's standard behavior.

## Twig Functions

Tempest Twig automatically registers custom Twig functions that integrate with the Tempest framework. These functions are available in all templates without any additional setup.

### Routing Functions

The `route()` function generates URLs for named routes or controller methods, accepting route parameters directly:

```twig
{{ route('route.name') }}
{{ route(['App\\Controllers\\ControllerClass', 'method']) }}
{{ route(['App\\Controllers\\ControllerClass', 'method'], param1, param2) }}
```

The `signed_route()` function generates URLs with cryptographic signatures, while `temporary_signed_route()` produces links that expire after a duration (in seconds):

```twig
{{ signed_route('route.name') }}
{{ temporary_signed_route('route.name', 3600) }}
```

The `is_current_route()` function checks the current route, useful for highlighting active navigation items:

```twig
{% if is_current_route('route.name') %}
  <span class="active">Active</span>
{% endif %}
```

The `current_path()` function returns the current request path as a string.

### Vite Functions

For Tempest applications using Vite for asset bundling, the package provides integration functions. The `vite_tags()` function generates script and link tags for Vite entry points:

```twig
{{ vite_tags('resources/js/app.ts', 'resources/css/app.css') }}
```

The `vite_asset()` function returns the public URL of an asset from Vite's manifest:

```twig
<img src="{{ vite_asset('resources/images/logo.png') }}" />
```

### Debug Functions

Tempest Twig includes debugging and utility functions. The `dump()` function inspects variables during development. The `class()` function returns an object's class name, and `is_empty()` checks if a variable is empty:

```twig
{{ dump(user, post) }}
{{ class(user) }}
{% if is_empty(posts) %}No posts{% endif %}
```

Additional utilities include `get_type()` for variable types, `env()` for environment variables with fallback, `root_path()` for absolute paths scoped to the project root, and `to_json()` for converting PHP values to JSON:

```twig
{{ get_type(value) }}
{{ env('APP_NAME', 'MyApp') }}
{{ root_path('storage', 'app.log') }}
<script>
  const data = {{ to_json(users) }};
</script>
```

The `count()` function counts array elements, `current_url()` gives the full URL, and `now()` provides the current timestamp.

## Component System

Tempest Twig includes a component system using HTML-like syntax. Instead of writing Twig `include` and `embed` directives, component tags that look like HTML custom elements can be written directly. The package transforms these tags into native Twig directives automatically.

### Self-Closing Components

Self-closing components are ideal for simple UI elements like buttons and badges, written as XML-style tags with attributes:

```twig
<twig:Button label="Click me" variant="primary" />
```

This transforms into a Twig `include` directive that passes attributes as variables:

```twig
{% include 'components/Button.html.twig' with { label: 'Click me', variant: 'primary' } only %}
```

The component template at `templates/components/Button.html.twig`:

```twig
<button class="btn btn-{{ variant ?? 'primary' }}">
  {{ label }}
</button>
```

### Components with Content

Components can wrap content, similar to Vue or React components. Content added between tags transforms into an `embed` directive:

```twig
<twig:Alert type="warning">
  This is a warning message
</twig:Alert>
```

This becomes:

```twig
{% embed 'components/Alert.html.twig' with { type: 'warning' } %}
  {% block content %}This is a warning message{% endblock %}
{% endembed %}
```

The component template accesses the content through Twig's block system:

```twig
<div class="alert alert-{{ type ?? 'info' }}">
  {% block content %}{% endblock %}
</div>
```

### Components with Named Slots

Complex components can define named content areas using `<twig:block>` tags, allowing different parts to accept different content:

```twig
<twig:Card title="Welcome">
  <twig:block name="header">
    <h2>{{ title }}</h2>
  </twig:block>
  
  <p>Card content goes here</p>
</twig:Card>
```

The component template can define multiple named blocks:

```twig
<div class="card">
  {% if block('header') is not empty %}
    <div class="card-header">
      {% block header %}{% endblock %}
    </div>
  {% endif %}
  
  <div class="card-body">
    {% block content %}{% endblock %}
  </div>
</div>
```

Default content (anything not in `<twig:block>`) goes into the `content` block. Named blocks are optional, and checking whether they have content before rendering avoids empty markup.

### Attribute Binding

Components accept both static attributes and dynamic Twig expressions. Static attributes are quoted strings, while dynamic attributes use a colon prefix:

```twig
<twig:Button label="Click" variant="primary" disabled="true" />

<twig:Button
  :label="buttonLabel"
  :variant="isSecondary ? 'secondary' : 'primary'"
  :disabled="isLoading" />
```

All attributes are available as variables inside the component. The preprocessor handles nested components and escapes attribute values properly.

## Testing and Development

The package includes a test suite covering component attribute parsing, template transformation, and loader functionality. Run tests with:

```bash
composer test
```

All tests use PHPUnit and follow the standard test directory structure. The package includes GitHub Actions workflows for continuous integration and compatibility checks.

## License

This package is licensed under the MIT license. See `LICENSE` for details.

## Support and Contributing

If you encounter any issues or have suggestions for improvements, you can open an issue or a PR on GitHub. Contributions are welcome!