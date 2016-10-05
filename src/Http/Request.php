<?php
namespace Tiimber\Http;

use Tiimber\ParameterBag;
//@deprecated;
class Request
{
  private $request;
  
  private $post;

  public function __construct($request, $data)
  {
    $post = [];
    parse_str($data, $post);
    $this->post = new ParameterBag($post);
    $this->request = $request;
  }
  
  public function __get($name)
  {
    if ($name === 'post') {
      return $this->post;
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
