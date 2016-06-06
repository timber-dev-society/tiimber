<?php

namespace Tiimber\Memory;

use Tiimber\Memory\Session;

class ProviderResolver
{
  private $providers;

  public function __construct()
  {
    $providers['session'] = new Session;
  }

  public function resolve(string $provider)
  {
    if (array_key_exists($provider, $this->providers)) {
      return $this->providers[$provider];
    }
  }
}
