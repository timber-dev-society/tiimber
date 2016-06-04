<?php

namespace Tiimber\Traits;

use Tiimber\Application;
use Tiimber\Exception;
/**
 *  Utility helper to upload files
 */
trait UploadTrait
{
  public function upload(string $field, string $name = null, string $directory = 'images'): string
  {
    $uploadDir = Application::getResourceDir() . $directory . '/';
    if (empty($_FILES[$field]['name'])) {
      throw Exception('500', $field . ' seems not to be an upload field');
    }
    $filepath = $uploadDir . $_FILES[$field]['name'];
    if (!is_null($name)) {
      $extension = pathinfo($_FILES[$identifier]['name'], PATHINFO_EXTENSION);
      if (file_exists($uploadDir . $name . $extension)) {
        $filepath = = $uploadDir . $name . '-' . substr(sha1(rand()), 0 , 4) . '.' . $extension;
      }
    }
    if (!move_uploaded_file($_FILES[$field]['tmp_name'], $filepath)){
      throw Exception('500', 'Unable to move uploaded file to ' . $filepath);
    }
    return $filepath;
  }
}
