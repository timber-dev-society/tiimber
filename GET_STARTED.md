# How to use Tiimber

The project of this file is for purpose to create a very little blog to see how organize the code and know how work tiimber.

## Requierement

For use Tiimber, you need intall PHP 7 and composer

## Intalling Tiimber

`in composer.json`

```json
{
  "require": {
    "ndufreche/tiimber": "dev-master"
  },
  "autoload": {
    "psr-4": {
      "Blog\\": "Blog/"
    }
  }
}
```

then install deps

```bash
composer install
```

## Create the project

In Tiimber, you need to create an Application class where you put your entry point and for that you need to use Tiimber ApplicationTrait.

`in Blog/Application.php`

```php
<?php
namespace Blog;

use Tiimber\Traits\ApplicationTrait as Tiimber;

class Application
{
  use Tiimber;

  public function start()
  {
    // define your application Root folder
    $this->setRoot(dirname(__DIR__));
    // Start Tiimber
    $this->chop();
  }
}

```

Then we need to create your index.php and call your Application.

`in index.php`

```php
<?php
require __DIR__ . '/vendor/autoload.php';
(new Blog\Application())->start();
```

## Hello world

For create an hello world we need 3 component a route, a layout and a view.

Route creation :

`in config/routes.json`

```json
{
  "hello": {
    "route": "/"
  }
}

```

Strict minimal Layout creation :

`in Blog\Layouts\DefaultLayout.php`

```php
<?php
namespace Blog\Layouts;

use Tiimber\Layout;

class DefaultLayout extends Layout
{
  const TPL = '{{{content}}}';
}
```

And View creation :

`in Blog\Views\HelloWorldView.php`

```php
<?php
namespace Blog\Views;

use Tiimber\View;

class IndexView extends View
{
  const EVENTS = [
    'request::index' => 'content'
  ];

  const TPL = <<<EOF
<p>hello {{planet}}.</p>
EOF;

  public function render()
  {
    return ['planet' => 'earth'];
  }
}
```