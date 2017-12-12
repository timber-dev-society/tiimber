<?php
namespace Tiimber;

use Tiimber\{Action, Bags\ImmutableBag, Bags\ParameterBag};

use Tiimber\Exceptions\ViewException;
use Tiimber\Interfaces\RenderableInterface;

abstract class View
{
  private $props;

  private $state;

  public function __construct(array $props = null)
  {
    $this->props = new ImmutableBag($props) ?? new ImmutableBag();
    $this->state = new ParameterBag();
  }

  final public function getData(): array
  {
    return array_merge(
      $this->state->toArray(),
      $this->props->toArray()
    );
  }

  final protected function setState(array $state)
  {
    $this->state = $this->state->merge($state);
  }

  public function raise(int $code, string $message = null)
  {
    throw new ViewException($message, $code);
  }

  public function propsToState(array $props, array $ownProps): array
  {
    return array_merge($props, $ownProps);
  }

  public function render(): string
  {
    return '';
  }

  final public function __GET(string $key)
  {
    if ($key !== 'state' && $key !== 'props') {
      return null;
    }
    return $this->{$key};
  }
}
