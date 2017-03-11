<?php
declare(strict_types=1);

namespace Tiimber\Http;

use DateTime;

use React\Http\{Request, Response};

use Tiimber\ImmutableBag;
use Tiimber\Http\QueryParser;

class Cookie extends ImmutableBag
{
  private $pending = [];

  private function parseCookies(Request $request): array
  {
    return QueryParser::parse($request->getHeaders()['Cookie'] ?? '');
  }

  public function __construct(Request $request, Response $response)
  {
    parent::__construct($this->parseCookies($request));
  }

  public function getPendingCookies(): array
  {
    return $this->pending;
  }

  /**
   * Add a cookie to be sent along with the rest of the HTTP headers.
   * Like other headers, cookies must be sent before any output from your script (this is a protocol restriction).
   * This requires that you place calls to this function prior to any output, 
   * including <html> and <head> tags as well as any whitespace.
   */
  public function add(string $name, string $value, int $expire = 0, string $path = '/', string $domain = '', bool $secure = false, bool $httponly = false)
  {
    $cookie = $name . '=' . $value;

    if ($expire !== 0) {
      $cookie .= '; Expires=' . (new DateTime())->setTimestamp($expire)->format(DateTime::COOKIE);
    }

    if ($path !== '/') {
      $cookie .= '; Path=' . $path;
    }

    if ($domain !== $domain) {
      $cookie .= '; Domain=' . $domain;
    }

    if ($secure) {
      $cookie .= '; Secure';
    }

    if ($httponly) {
      $cookie .= '; HttpOnly';
    }

    array_push($this->pending, $cookie);
  }
}