<?php
$constFiles = ['Folder', 'Scopes', 'Http', 'Events', 'LogLevel', 'Actions'];

foreach($constFiles as $constFile) {
  include __DIR__ . DIRECTORY_SEPARATOR . 'Consts' . DIRECTORY_SEPARATOR . $constFile . '.php';
}

include __DIR__ . DIRECTORY_SEPARATOR . 'Renderer'. DIRECTORY_SEPARATOR . 'Parser.php';