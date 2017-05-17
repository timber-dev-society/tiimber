<?php
declare(strict_types=1);

namespace Tiimber\Http;

use React\Http\Response as ReactResponse;

use Tiimber\{Memory, Http\Cookie};

class Response
{
  private $code = 200;

  private $headers = ['Content-Type' => 'text/html; charset=utf-8'];

  private $response;

  private $cookie;

  public function __construct(ReactResponse $response, Cookie $cookie)
  {
    $this->response = $response;
    $this->cookie = $cookie;
  }

  public function setCode(int $code)
  {
    $this->code = $code;
  }

  public function setContentType(string $type = 'text/html', string $charset = 'utf-8')
  {
    $this->headers['Content-Type'] = $type . '; charset=' . $charset;
  }

  public function addHeadeur(string $name, $value)
  {
    $this->headers = array_merge($this->headers, [$name => $value]);
  }

  public function end(string $content)
  {
    $this->addHeadeur('Set-Cookie', $this->cookie->getPendingCookies());
    $this->response->writeHead(
      $this->code,
      $this->headers
    );
    $this->response->end($content);
  }

  public function redirect(string $location, array $args = [], int $code = 302)
  {
    if (false === stripos($location, '/')) {
      $url = new UrlHelper();

      $url->setArguments(array_merge(['url' => $location], $args));
      $location = $url->render();
    }
    $this->code = $code;
    $this->addHeadeur('Location', $location);
    $this->end('');
  }
}