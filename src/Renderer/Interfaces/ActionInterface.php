<?php

namespace Tiimber\Interfaces;

use Tiimber\Http\{Request, Response};

interface ActionInterface
{
  public function onGet(Request $req, Response $res);

  public function onPost(Request $req, Response $res);

  public function onCall(Request $req, Response $res);
}