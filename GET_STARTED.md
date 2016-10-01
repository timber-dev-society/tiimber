- [Tiimber](#tiimber)
  - [Requierement](#requierement)
  - [Installing Tiimber](#installing-tiimber)
  - [Project creation](#project-creation)
  - [Hello world](#hello-word)

# Tiimber

The project of this file is for purpose to create a very little blog to see how organize the code and know how work tiimber.

## Requierement

For use Tiimber, you need intall PHP 7 and [composer](https://getcomposer.org/download/)

## Intalling Tiimber

> create composer.json

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

then type the following command to install dependencies.

```bash
./composer.phar install
```

## Project creation

In Tiimber, you need to create an Application class where you put your entry point and for create a Tiimber App, you need to use Tiimber ApplicationTrait.

> create Blog/Application.php

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

Then we need to create a index.php and call your Application.

> create index.php

```php
<?php
require __DIR__ . '/vendor/autoload.php';
(new Blog\Application())->start();
```

## Hello world

To create an hello world we need 3 components a route, a layout and a view.

### Route creation :

> create config/routes.json

```json
{
  "hello": {
    "route": "/hello"
  }
}

```

### Layout creation :

A minimal Layout is composed by one constant.

The const TPL is your template. A Layout template expose outlets and the way to declare an outlet is by encapsulating it into `{{{ }}}`.

In the following Layout we expose only one outlet `content`.

> create Blog\Layouts\DefaultLayout.php

```php
<?php
namespace Blog\Layouts;

use Tiimber\Layout;

class DefaultLayout extends Layout
{
  const TPL = '{{{content}}}';
}
```

### View creation :

A minimal view it's composed of two constants.

The EVENTS constant is array who the key represent the event to listen and outlet location.

The TPL constant is your view template.

To create the *HelloWorldView*, we need to print *Hello world* into `content` outlet declared into the `DefaultLayout` when a `request` is received in `hello` route defined into *config/routes.json* file.

And to do that create your view like this.

> create Blog\Views\HelloWorldView.php

```php
<?php
namespace Blog\Views;

use Tiimber\View;

class HelloWorldView extends View
{
  const EVENTS = [
    'request::hello' => 'content'
  ];

  const TPL = 'Hello world.';
}
```

### Now you can test it :

Start a php server

```bash
php -S localhost:1337 index.php
```

And try it in your navigator by calling the URL http://localhost:1337/hello .
