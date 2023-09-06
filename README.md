# Peasy Router
Peasy router is an **easy peasy** and opinionated router for PHP.
This is to be used with very simple projects. Nothing complicated.

## Getting Started

```php
// public/index.php
require_once __DIR__ . '/../vendor/autoload.php';

use ArtKoder\Peasy\Http\Router;

// You need to pass the namespace of your controllers
$router = new Router('ArtKoder\\Peasy\\Controllers\\');

$router->handleRequest();
```

## Controllers


