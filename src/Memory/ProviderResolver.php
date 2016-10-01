<?php

namespace Tiimber\Memory;

use Tiimber\Memory\Session;
use Tiimber\Memory\Sql;

class ProviderResolver
{
  private $providers;

  public function __construct()
  {
    $providers['session'] = new Session();
    $providers['sql'] = new Sql();
  }

  public function resolve(string $provider)
  {
    if (array_key_exists($provider, $this->providers)) {
      return $this->providers[$provider];
    }
  }
}
