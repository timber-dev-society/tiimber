<?php
namespace Tiimber;

use Tiimber\Action;

use Tiimber\Exceptions\ViewException;
use Tiimber\Interfaces\RenderableInterface;

abstract class View
{
  private $props;

  private $state;

  public function __construct(array $props = null)
  {
    $this->props = $props === null ? [] : $props;
    $this->state = [];
  }

  final public function getData(): array
  {
    return array_merge($this->state, $this->props);
  }

  final protected function setState(array $state)
  {
    $this->state = array_merge($this->state, $state);
  }

  public function raise(int $code, string $message = null)
  {
    throw new ViewException($message, $code);
  }

  public function propsToState(array $props, array $ownProps): array
  {
    return $ownProps;
  }
  
  public function render(): string
  {
    return '';
  }

  final public function __GET(string $key): array
  {
    if ($key !== 'state' && $key !== 'props') {
      return null;
    }
    return $this->{$key};
  }
}
