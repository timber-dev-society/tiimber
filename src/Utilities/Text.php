<?php
namespace Tiimber\Utilities;

class Text
{
  /**
   * Slugify text by removing non ASCII carac
   *
   * @param $text String to slugify
   * @return Sting
   */
  public static function slugify($text)
  {
    $text = preg_replace('~[^\\pL\d]+~u', '-', $text);
    $text = trim($text, '-');
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    $text = strtolower($text);
    $text = preg_replace('~[^-\w]+~', '', $text);
    if (empty($text)) {
      return 'n-a';
    }

    return $text;
  }

  /**
   * Crop text at the max size of your choice
   *
   * @param $text String to crop
   * @param $maxSize Integer the max size of the string
   * @param $preserveWord Boolean the max size of the string
   * @return String
   */
  public static function crop($text, $maxSize, $preserveWords = true)
  {
    if ($preserveWords) {
      $words = explode(' ', $text);
      $count = 0;
      foreach($words as $word) {
        $count += strlen($word) + 1;

        if ($count >= ($maxSize + 1)) {
          break;
        }
      }

      $maxSize = $count + 2;
    }

    return strlen($text) > ($maxSize)
      ? substr($text, 0, $maxSize - 3) . '...'
      : $text;
  }
}
