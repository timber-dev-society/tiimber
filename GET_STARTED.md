- [Tiimber](#tiimber)
  - [Requierement](#requierement)
  - [Installing Tiimber](#installing-tiimber)
  - [Project creation](#project-creation)
  - [Hello world](#hello-word)
  - [Templating a Layout](#templating-a-layout)
  - [Render errors](#render-errors)
  - [Dynamize your views](#dynamize-your-views)
  - [Views and Actions](#views-and-actions)

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

use Tiimber\Traits\{ApplicationTrait as Tiimber, ServerTrait as Server};

class Application
{
  use Tiimber, Server;
  
  private function prepare()
  {
    $this->setRoot(dirname(__DIR__));
    $this->setCacheFolder(dirname(__DIR__) . '/cache');
    $this->setHost('localhost');
    $this->setPort(1337);
  }

  public function start()
  {
    $this->prepare();
    $this->chop();
    $this->runHttpServer($this->runApp());
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

Start the php server

```bash
php index.php
```

And try it in your navigator by calling the URL http://localhost:1337/hello .

## Templating a Layout

Currently our layout is pretty simple then we need to upgrade it to create more outlets. We want to add a navigation and a footer to our Layout.

> in Blog\Layouts\DefaultLayout.php

```php
<?php
// ...
class DefaultLayout extends Layout
{
  const TPL = <<<HTML
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>My blog</title>
  </head>
  <body>
    <header>
      {{{navigation}}}
    </header>
    <div>
      {{{content}}}
    </div>
    <footer>
      {{{footer}}}
    </footer>
  </body>
</html>
HTML;
}
```

Now we have created 2 more outlets then we need to render it.

### Navigation view creation

> create Blog\Views\NavigationView.php

```php
<?php
namespace Blog\Views;

use Tiimber\View;

class NavigationView extends View
{
  const EVENTS = [
    'request::hello' => 'navigation'
  ];

  const TPL = <<<HTML
<ul>
  <li><a href="/"><h1>My Blog</h1></a></li>
  <li><a href="/hello">Hello</a></li>
</ul>
HTML;
}

```

### Footer view creation

> create Blog\Views\FooterView.php

```php
<?php
namespace Blog\Views;

use Tiimber\View;

class FooterView extends View
{
  const EVENTS = [
    'request::hello' => 'footer'
  ];

  const TPL = 'Power by Tiimber';
}

```


### Now you can test it :

Restart the php server

```bash
php index.php
```

If you go to http://localhost:1337/hello there is no problem and evrething work fine but if you click on the link `My Blog` you have a empty page.

Then we need to upgrade our `NaviagtionView` and `FooterView` to catch more globally the request event. For that we need to use wildcard represented by `*`.

> in Blog\Views\NavigationView.php

```php
<?php
// ...
class NavigationView extends View
{
  const EVENTS = [
    'request::*' => 'navigation'
  ];
// ...
}

```

> in Blog\Views\FooterView.php

```php
<?php
// ...
class FooterView extends View
{
  const EVENTS = [
    'request::*' => 'footer'
  ];
// ...
}

```

Now restart the php server

```bash
php index.php
```

And if you go to http://localhost:1337/ , you have to see your navigation and footer.

## Render errors

Currently we haven't define a home page, then when we go to http://localhost:1337/ we need to an error 404 not Found.

For that we need to create a new view.

> create Blog\Views\Errors\NotFoundView.php

```php
<?php
namespace Blog\Views\Errors;

use Tiimber\View;

class NotFoundView extends View
{
  const EVENTS = [
    'error::404' => 'content'
  ];

  const TPL = 'Error 404 page not found';
}

```

Now if you restart your server and go to the home page, you have to see the error.

You can create a server error to who listen `error::500` event.


## Dynamize your views

Your tiimber view can be more dynamic, the goal of this tuto is to create a simple blog, then we need to the capacity to create and read posts.

Tiimber don't have ORM it let you the choice and the implementation.

For this tuto we use a really simple ORM called [RedBeanPHP](http://www.redbeanphp.com/index.php).

### Update your dependencies :

> in composer.json

```json
{
  "require": {
    "ndufreche/tiimber": "dev-master",
    "gabordemooij/redbean": "dev-master"
  },
// ...
}
```

and now install it.

```bash
./composer.phar update
```

### Setup the database :

> in Blog/Application.php

```php
<?php
// ...

use RedBeanPHP\R;

class Application
{
  // ...
  private function prepare()
  {
    R::setup('mysql:host=localhost;dbname=blog', 'user', 'password');
    // ...
  }
  // ...
}

```

### Now create a Home page :

> in config/routes.json

```json
{
  "home": {
    "route": "/"
  },
  // ...
}

```

> create Blog/Views/Articles/IndexView.php

```php
<?php
namespace Blog\Views\Articles;

use Tiimber\View;

class IndexView extends View
{
  const EVENTS = [
    'request::home' => 'content'
  ];

  const TPL = <<<HTML
<ul>
  {{#articles}}
    <li>
      <a href="/article/{{id}}">
        <h2>{{title}}</h2>
        <p>
          {{content}}
        </p>
      </a>
    </li>
  {{/articles}}
</ul>
{{^articles}}
  <p>No article available yet.</p>
{{/articles}}
HTML;

  public function render()
  {
    $articles = R::findAll('article','ORDER BY id DESC LIMIT 10');
    return ['articles' => array_values($articles)];
  }
}

```

In the Article\IndexView we use a new function called render. This method has only one goal, return all the variable need in the template.

If you relaunch the server and visit the home page you see `No article available yet.`.

### Create a article page :

> in config/routes.json

```json
{
  // ...
  "article::show": {
    "route": "/{id}",
    "params": {
      "id": "\d+"
    }
  },
  // ...
}

```

Our article route need to be dynamic for that pass dynamical params by defining beetwen `{ }` and in the section params you can add the regex to validate the params.

> create Blog/Views/Articles/ShowView.php

```php
<?php
namespace Blog\Views\Articles;

use Tiimber\View;

use RedBeanPHP\R;

class ShowView extends View
{

  const EVENTS = [
    'request::article::show' => 'content'
  ];

  const TPL = <<<HTML
<h2>{{article.title}}</h2>
<p>{{article.content}}</p>
HTML;

  private $article;

  public function onGet($request, $args)
  {
    $this->article = R::load('article', (integer)$args['id']);
  }

  public function render()
  {
    return ['article' => $this->article];
  }
}

```

In this view we use a new method `onGet` who received two parameters the request and the url arguments. The args variable is an array and contain the `id` defined into the route `article::show`.

With that id we can load our article.

But we stilln't have article in our database then we need a way to contribute it


## Views and Actions

### Form creation :

The first stape of this part is create a form.

Then for that we need a new route and view.

> in config/routes.json

```json
{
  // ...
  "article::manage": {
    "route": "/article/{id}",
    "params": {
      "id": ".+"
    }
  },
  // ...
}

```

> create Blog/Views/Articles/ManageView.php

```php
<?php
namespace Blog\Views\Articles;

use Tiimber\View;

use RedBeanPHP\R;

class ManageView extends View
{

  const EVENTS = [
    'request::article::manage' => 'content'
  ];

  const TPL = <<<HTML
<form method="post">
    <input type="hidden" name="id" value="{{article.id}}">
    <p><input type="text" name="title" placeholder="Title" value="{{article.title}}"></p>
    <p><textarea name="content">{{article.content}}</textarea></p>
    <p><button type="submit">Submit</button></p>
  </form>
HTML;

  private $article;

  public function onGet($request, $args)
  {
    $this->article = R::load('article', (integer)$args['id']);
  }

  public function render()
  {
    return ['article' => $this->article];
  }
}

```

> create Blog/Views/Articles/ShowView.php

```php
<?php
// ...
class ShowView extends View
{
  // ...
  const TPL = <<<HTML
<h2>{{article.title}}</h2>
<p>{{article.content}}</p>
<p><a href="{{article.id}}">edit</a></p>
HTML;
  // ...
}

```

> in Blog\Views\NavigationView.php

```php
<?php
// ...
class NavigationView extends View
{
  // ...
  const TPL = <<<HTML
<ul>
  <li><a href="/"><h1>My Blog</h1></a></li>
  <li><a href="/hello">Hello</a></li>
  <li><a href="/article/new">New article</a></li>
</ul>
HTML;
}

```

### Introducing Actions :

An Action in tiimber is work like a View with the difference they have nothing to display. Then they dont have to specify an outlet where to be displayed or TPL constant or a render method.

In this case we go to use an Action to save our article.

> create Blog/Actions/Articles/SaveAction.php

```php
<?php
namespace Blog\Actions\Articles;

use Tiimber\{Action, Traits\RedirectTrait};

use RedBeanPHP\R;

class SaveAction extends Action
{
  use RedirectTrait;

  const EVENTS = [
    'request::article::manage'
  ];
  
  private function prepare($id)
  {
    if ($id !== 'new') {
      return R::load('article', $id);
    } else {
      return R::dispense('article');
    }
  }
  
  public function onPost($request, $args)
  {
    $article = $this->prepare($args['id']);

    $article->title = $request->post->get('title');
    $article->content = $request->post->get('content');

    $id = R::store($this->article);
    $this->redirect('/'.$id);
  }

}

```

In this action create a create or update an article then when the save is done we redirect to the article. To redirect you need to use the redirect trait becaue if the traditionnal headers() is not enough and you can't stop the server.

You can see we use the method `onPost`. By this way we don't pass into the onGet on the view. Is really important to specify `method="post"` in your form to access at the goods methods into your view an action.

> Tips: a view and a action have access to the methods onGet and onPost;
