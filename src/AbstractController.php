<?php
namespace Tiimber;

use Tiimber\Utility;

abstract class AbstractController
{
  protected $request;

  protected $renderer;

  public $tpl;

  public function __construct($request, $renderer)
  {
    $this->request = $request;
    $this->renderer = $renderer;
  }

  public function render(Array $arguments = null)
  {
    return $this->renderer->render($this->tpl, $arguments);
  }

  public function beforeAction($action)
  {
  }

  public function redirect($location)
  {
    header('Location: ' . $location);
  }

  protected function uploadFile($post, $identifier, $directory = 'images')
  {
    $post->{$identifier . '_slug'} = empty($post->{$identifier . '_slug'}) ? Utility::slugify($post->{$identifier . '_title'}) : $post->{$identifier . '_slug'};
    $message = '';
    $target = dirname(__DIR__) . '/Resources/' . $directory . '/';

    // traitement de l'upload
    if (!empty($_FILES[$identifier]['name'])) {
      $extension = pathinfo($_FILES[$identifier]['name'], PATHINFO_EXTENSION);
      $img_name = $post->{$identifier . '_slug'} . '.' . $extension;

      $finalName = $img_name;

      if (file_exists($target . $finalName)) {
        $rand = substr(sha1(rand()), 0 , 4);
        $finalName = $post->{$identifier . '_slug'} . '-' . $rand . '.' . $extension;
      }
      $post->{$identifier} = $finalName;

      return move_uploaded_file($_FILES[$identifier]['tmp_name'], $target . $finalName);
    }
    return false;
  }
}