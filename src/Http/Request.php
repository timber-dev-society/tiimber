<?php
namespace Tiimber\Http;

use Tiimber\Bags\ImmutableBag;
use Tiimber\Http\{Cookie, Session};
use React\Http\Request as ReactRequest;

class Request
{
  private $request;

  private $session;

  private $cookie;  
  
  private $post;

  private $args;

  private $locked = false;

  public function __construct(ReactRequest $request, Session $session, Cookie $cookie)
  {
    $this->request = $request;
    $this->session = $session;
    $this->cookie = $cookie;
  }

  public function setData(array $data): Request
  {
    if (!$this->locked) {
      $this->post = new ImmutableBag($data);
    }
    return $this;
  }

  public function setArgs(array $args): Request
  {
    if (!$this->locked) {
      $this->args = new ImmutableBag($args);
    }
    return $this->lock();
  }

  private function lock(): Request
  {
    $this->locked = true;
    return $this;
  }
  
  public function __get($name)
  {
    if ($name === 'post') {
      return $this->post;
    } elseif ($name === 'session') {
      return $this->session;
    } elseif ($name === 'cookie') {
      return $this->cookie;
    } elseif ($name === 'args') {
      return $this->args;
    } else {
      return $this->request->{ucfirst($name)}();
    }
  }

  public function clone($args)
  {
    $request = new self($this->request, $this->session, $this->cookie);
    if ($this->post !== null) {
      $request->setData($this->post->toArray());
    }
    if ($this->args instanceof ImmutableBag) {
      $request->setArgs(array_merge($this->args->toArray(), $args));
    } else {
      $request->setArgs($args);
    }
      
    return $request;
  }

  public function getPost(): ImmutableBag
  {
    return $this->post;
  }

  public function getArgs(): ImmutableBag
  {
    return $this->args;
  }

  public function getSession(): Session
  {
    return $this->session;
  }

  public function getCookie(): Cookie
  {
    return $this->cookie;
  }
  
  public function getMethod()
  {
    return $this->request->getMethod();
  }

  public function getPath()
  {
    return $this->request->getPath();
  }

  public function getQuery()
  {
    return $this->request->getQuery();
  }
  
  public function getHttpVersion()
  {
    return $this->request->getHttpVersion();
  }

  public function getHeaders()
  {
    return $this->request->getHeaders();
  }

  public function expectsContinue()
  {
    return $this->request->expectsContinue();
  }

  public function isReadable()
  {
    return $this->request->isReadable();
  }

  public function pause()
  {
    $this->request->pause();
  }

  public function resume()
  {
    $this->request->resume();
  }

  public function close()
  {
    $this->request->close();
  }

  public function pipe(WritableStreamInterface $dest, array $options = array())
  {
    return $this->request->pipe($dest, $options);
  }
}
