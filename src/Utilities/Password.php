<?php
namespace Tiimber\Utilities;

use Tiimber\Config;
use Tiimber\ParameterBag;

class Text
{
  /**
   * Slugify text by removing non ASCII carac
   *
   * @param $text String to slugify
   * @return Sting
   */
  public static function bcrypt(string $password): string
  {
    return password_hash(
      $password,
      PASSWORD_BCRYPT,
      ['salt' => Config::get('security', new ParameterBag())->get('salt', null)]
    );
  }
}
