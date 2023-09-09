# Peasy Router
Peasy Router is an **easy peasy** and opinionated router for PHP.
It is meant to be used with small projects that need a very basic
ruting functionality. Nothing complicated.

## Installation

### Using composer

```shell
composer require artkoder/peasy-router
```

## Configure your .htaccess file

Add the following code to your `.htaccess` inside a public folder
at the same level as your `index.php`. 

```
<IfModule mod_rewrite.c>
    # Redirect Trailing Slashes If Not A Folder...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    # Send Requests To Front Controller...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>

```

## Setting `index.php`

```php
// public/index.php
require_once __DIR__ . '/../vendor/autoload.php';

use ArtKoder\Peasy\Http\Router;

// You need to pass the path to your routes directory
$router = new Router('/path/to/routes/directory');

$router->handleRequest();
```

## Routes

You can define your routes inside a directory. You can use either a text file with extension `.routes` or a normal
`.php` file. Peasy router will scan all the route files inside the directory recursivelly. You can use either of
the file formats or combine both if you so desire.

### Defining your routes using a `.routes` file

Here is a sample `web.routes` file:

```yaml
name: budget-report
path: /budget/{start}/{end}
method: get
controller: ArtKoder\Peasy\Controllers\Budget::report

name: budget-index
path: /budget
method: get
controller: ArtKoder\Peasy\Controllers\Budget::index
```

### Defining your routes using a `.php` file

The previous routes can alternatively be defined in a `.php` file as follows:

```php
// web.php
return [
    [
        'name' => 'budget-report',
        'path' => '/budget/{start}/{end}',
        'method' => 'get',
        'controller' => 'ArtKoder\Peasy\Controllers\Budget::report'
    ],
    [
        'name' => 'budget-index',
        'path' => '/budget',
        'method' => 'get',
        'controller' => 'ArtKoder\Peasy\Controllers\Budget::index'
    ]
];

```

## Controllers

Once you have your route definitions, you can create the controllers for your paths.

Here is an example using the previous route definitions:

```php
// src/Controllers/Budget.php

namespace ArtKoder\Peasy\Controllers;

class Budget 
{
    public static function report($start, $end)
    {
        echo "start: $start\n";
        echo "end: $end\n";
    }

    public static function index()
    {
        echo "Budget index\n";
    }
}
```

### Query parameters

Peasy router maps all query parameters as arguments to the controllers function.
So, if you request the following path: `https://peasy.test/budget/2023-09-01/2023-12-31?var=value`,
the controller will expect to get a variable `$var`, so you need to declare it.
To avoid errors when a query parameter is not present, you can make variables optional.

Here is an example:


```php
// src/Controllers/Budget.php

namespace ArtKoder\Peasy\Controllers;

class Budget 
{
    public static function report($start, $end, $var = '')
    {
        echo "start: $start\n";
        echo "end: $end\n";
        echo "var: $var\n";
    }

    public static function index()
    {
        echo "Budget index\n";
    }
}
```
