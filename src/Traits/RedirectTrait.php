<?php
namespace Tiimber\Traits;

use Tiimber\Helpers\UrlHelper;
use Tiimber\Memory;

use const Tiimber\Consts\{Scopes\HTTP, Http\HEADER, Http\CODE};
use Tiimber\Traits\LoggerTrait;
/**
 *  Utility helper to upload files
 */
trait RedirectTrait
{
  use LoggerTrait;

  public function redirect($location, array $args = [])
  {
    if (false === stripos($location, '/')) {
      $url = new UrlHelper();

      $url->setArguments(array_merge(['url' => $location], $args));
      $location = $url->render();
    }

    $this->log('info', 'redirecting');
    Memory::get(HTTP)->set(HEADER, ['Location' => $location]);
    Memory::get(HTTP)->set(CODE, 302);
    Memory::events()->emit('response::end', ['content' => '']);
    Memory::events()->emit('stop::rendering');
  }
}
