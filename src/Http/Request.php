<?php
namespace Tiimber\Http;

use Tiimber\ParameterBag;
use Tiimber\Http\Session;
use Tiimber\Traits\LoggerTrait;

class Request
{
  private $request;

  private $session;
  
  private $post;

  private $tiimberid;

  public function __construct($request, $tiimberid, $data = '')
  {
    $post = [];
    parse_str($data, $post);
    $this->post = new ParameterBag($post);
    $this->request = $request;
    $this->tiimberid = $tiimberid;
    $this->session = Session::load($this->tiimberid);
  }

  public function storeSession()
  {
    Session::store($this->tiimberid, $this->session);
  }
  
  public function __get($name)
  {
    if ($name === 'post') {
      return $this->post;
    } elseif ($name === 'session') {
      return $this->session;
    }
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
