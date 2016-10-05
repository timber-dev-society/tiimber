<?php
namespace Tiimber\Traits;

use Tiimber\Helpers\UrlHelper;
use Tiimber\Memory;

use const Tiimber\Consts\{Scopes\HTTP, Http\HEADER};
/**
 *  Utility helper to upload files
 */
trait RedirectTrait
{
  public function redirect($location, array $args = [])
  {
    if (false === stripos($location, '/')) {
      $url = new UrlHelper();

      $url->setArguments(array_merge(['url' => $location], $args));
      $location = $url->render();
    }

    Memory::get(HTTP)->set(HEADER, ['Location' => $location]);
  }
}
