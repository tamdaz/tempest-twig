# tempest-twig

Tempest Twig is a third-party package that integrates the Twig templating engine with the [Tempest framework](https://tempestphp.com). It provides full Twig `3.x` support, custom extensions for routing and debugging, and an innovative component system using HTML-like syntax.

The package includes flexible configuration for template paths, built-in Twig extensions that work with Tempest's routing and Vite pipeline, and a sophisticated component transformation system. Everything is fully tested with PHPUnit and includes GitHub Actions CI.

## Installation

First, install the Composer package:

```bash
composer require tamdaz/tempest-twig
```

## Configuration

To use Tempest Twig, create a configuration file in your `config` directory. Tempest will automatically discover and load this configuration.

```php
// config/twig.config.php
use Tamdaz\TempestTwig\TwigConfig;
use function Tempest\root_path;

return new TwigConfig(
    viewPaths: [
        root_path('templates'),
    ],
    debug: env('APP_DEBUG', false),
    strictVariables: true,
);
```

The `TwigConfig` class wraps Twig's standard options. The `viewPaths` parameter specifies which directories to search for templates. You can add multiple directories for different template locations. Other options like `debug`, `charset`, `strictVariables`, and `autoescape` follow Twig's standard behavior.

## Twig Functions

Tempest Twig automatically registers custom Twig functions that integrate with the Tempest framework. These functions are available in all templates without any additional setup.

### Routing Functions

The `route()` function generates URLs for named routes or controller methods. You can pass route parameters directly:

```twig
{{ route('route.name') }}
{{ route([ControllerClass::class, 'method']) }}
{{ route([ControllerClass::class, 'method'], param1, param2) }}
```

Use `signed_route()` to generate URLs with cryptographic signatures. Use `temporary_signed_route()` for links that expire after a duration (in seconds):

```twig
{{ signed_route('route.name') }}
{{ temporary_signed_route('route.name', 3600) }}
```

Check the current route with `is_current_route()` to highlight active navigation items:

```twig
{% if is_current_route('route.name') %}
  <span class="active">Active</span>
{% endif %}
```

The `current_path` variable provides the current request path as a string.

### Vite Functions

If your Tempest application uses Vite for asset bundling, the package provides integration functions. Use `vite_tags()` to generate script and link tags for Vite entry points:

```twig
{{ vite_tags('resources/js/app.ts', 'resources/css/app.css') }}
```

Use `vite_asset()` to get the public URL of an asset from Vite's manifest:

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

Additional utilities include `get_type()` for variable types, `env()` for environment variables with fallback, and `to_json()` for converting PHP values to JSON:

```twig
{{ get_type(value) }}
{{ env('APP_NAME', 'MyApp') }}
<script>
  const data = {{ to_json(users) }};
</script>
```

The `count()` function counts array elements, `current_url` gives the full URL, and `now` provides the current timestamp.

## Component System

Tempest Twig includes a component system using HTML-like syntax. Instead of writing Twig `include` and `embed` directives, you write component tags that look like HTML custom elements. The package transforms these tags into native Twig directives automatically.

### Self-Closing Components

Self-closing components are ideal for simple UI elements like buttons and badges. Write them as XML-style tags with attributes:

```twig
<twig:Button label="Click me" variant="primary" />
```

This transforms into a Twig `include` directive that passes attributes as variables:

```twig
{% include 'components/Button.html.twig' with { label: 'Click me', variant: 'primary' } only %}
```

Your component template at `templates/components/Button.html.twig`:

```twig
<button class="btn btn-{{ variant ?? 'primary' }}">
  {{ label }}
</button>
```

### Components with Content

Components can wrap content, similar to Vue or React components. When you add content between tags, it transforms into an `embed` directive:

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

Your component template accesses the content through Twig's block system:

```twig
<div class="alert alert-{{ type ?? 'info' }}">
  {% block content %}{% endblock %}
</div>
```

### Components with Named Slots

For complex components, define named content areas using `<twig:block>` tags. This allows different parts to accept different content:

```twig
<twig:Card title="Welcome">
  <twig:block name="header">
    <h2>{{ title }}</h2>
  </twig:block>
  
  <p>Card content goes here</p>
</twig:Card>
```

Your component template can define multiple named blocks:

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

Default content (anything not in `<twig:block>`) goes into the `content` block. Named blocks are optional, so check if they have content before rendering them.

### Attribute Binding

Components accept both static attributes and dynamic Twig expressions. Static attributes are quoted strings, while dynamic attributes use a colon prefix:

```twig
<twig:Button label="Click" variant="primary" disabled="true" />

<twig:Button
  label="{{ buttonLabel }}"
  :variant="isSecondary ? 'secondary' : 'primary'" 
  :disabled="isLoading" />
```

All attributes are available as variables in your component. The preprocessor handles nested components and escapes attribute values properly.

### Component Organization

Organize components in a dedicated `components` directory within your template's folder. This separates them from page templates and layouts:

```
templates/
├── components/
│   ├── Button.html.twig
│   ├── Card.html.twig
│   ├── Alert.html.twig
│   └── UserProfile.html.twig
├── layouts/
│   └── base.html.twig
└── pages/
    ├── home.html.twig
    └── about.html.twig
```

Always provide a default `content` block for the main content area. For optional slots, check if the block is empty before rendering to avoid extra HTML. Use PascalCase for component names to distinguish them from standard HTML tags.

## How It Works

Tempest Twig integrates several components. The `TwigInitializer` is the entry point discovered by Tempest's service container. It registers the Twig environment, sets up the `ComponentLoader` for transformation, and registers custom extensions. The `TwigViewRendererInitializer` then selects the `TwigViewRenderer` as the default renderer.

The `ComponentPreprocessor` transforms your component syntax into standard Twig directives before Twig processes the template. Component tags become `include` or `embed` directives with proper variable passing. The transformation is transparent, so you never think about the underlying Twig code.

Three extensions are automatically registered. The `DebugExtension` provides debugging utilities. The `RoutingExtension` integrates with Tempest's routing system. The `ViteExtension` handles Vite asset manifest integration.

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