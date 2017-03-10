<?php

namespace Tiimber\Interfaces;

use Tiimber\Http\Request;

interface EventInterface
{
  public function onGet(Request $request, array $args);

  public function onPost(Request $request, array $args);

  public function onCall(Request $request, array $args);
}