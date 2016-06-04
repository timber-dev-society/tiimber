<?php
namespace Tiimber\Traits;

use Tiimber\Helpers\UrlHelper;
/**
 *  Utility helper to upload files
 */
trait UploadTrait
{
  public function redirect($location, array $args = [])
  {
    if (false === stripos($location, '/')) {
      $url = new UrlHelper();

      $url->setArguments(array_merge(['url' => $location], $args));
      $location = $url->render();
    }

    header('Location: ' . $location);
  }
}
