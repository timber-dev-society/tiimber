<?php
namespace KissPHP;

class Utility
{
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

  public static function cropText($text, $maxSize)
  {
    return strlen($text) > $maxSize ? substr($text, 0, $maxSize) . '...' : $text;
  }

  public static function t($text)
  {
    if ($text === 'dashboard') {
      return 'Tableau de bord';
    }
  }
}
