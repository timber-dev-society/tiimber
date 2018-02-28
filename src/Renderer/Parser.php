<?php
namespace Tiimber\Renderer\Parser;

use const Tiimber\Consts\Actions\RENDER;
use Tiimber\Renderer\Base\View;

function convertParams(int $i, array $params, array $values, array $acc): array
{
  if ($i > count($params) || empty($params[$i])) return $acc;

  list($key, $val) = explode('=', $params[$i]);

  if ($acc[$key] = json_decode($val) !== null) {
    return convertParams($i + 1, $params, $values, $acc);
  } else {
    $param = str_replace(['{', '}'], '', $val);
    $acc[$key] = ($values[str_replace(['{', '}'], '', $val)] ?? null);
    return convertParams($i + 1, $params, $values, $acc);
  }
}

function generateTpl(int $i, array $matches, View $view, string $tpl, callable $cb): string
{
  if (($i + 1) > count($matches[0]) || count($matches[0]) === 0) return $tpl;

  $params = convertParams(0, explode(' ', trim($matches[2][$i])), $view->getData(), []);

  $cb(
    $view->{$matches[1][$i]}($params),
    $matches[0][$i],
    $params
  );

  return generateTpl(
    $i + 1,
    $matches,
    $view,
    $tpl,
    $cb
  );
}
