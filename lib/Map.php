<?php


class Map {
  private int $minY = PHP_INT_MAX;
  private int $maxY = PHP_INT_MIN;
  private int $minX = PHP_INT_MAX;
  private int $maxX = PHP_INT_MIN;

  private array $values = [];
  private $defaultValue;           // The value to be returned by `getAt()` when the cell has not been set

  private string $emptyCell;       // How to draw an empty cell
  protected $drawCell;

  public function __construct($default = 0, string $emptyCell = '.') {
    $this->defaultValue = $default;
    $this->emptyCell = $emptyCell;

    $this->drawCell = function($value, int $y, int $x) { echo($value !== $this->defaultValue ? $value : $this->emptyCell); };
  }

  public function getMinX(): int { return $this->minX; }
  public function getMaxX(): int { return $this->maxX; }
  public function getMinY(): int { return $this->minY; }
  public function getMaxY(): int { return $this->maxY; }

  public function getWidth(): int { return $this->maxX - $this->minX + 1; }
  public function getHeight(): int { return $this->maxY - $this->minY + 1; }

  public function expand(int $left, int $top, int $right, int $bottom) {
    $this->minX = min($this->minX - $left, $this->maxX + $right);
    $this->maxX = max($this->minX - $left, $this->maxX + $right);
    $this->minY = min($this->minY - $top, $this->maxY + $bottom);
    $this->maxY = max($this->minY - $top, $this->maxY + $bottom);
  }

  public function expandTo(int $left, int $top, int $right, int $bottom) {
    $this->minX = min($this->minX, $left);
    $this->maxX = max($this->maxX, $right);
    $this->minY = min($this->minY, $top);
    $this->maxY = max($this->maxY, $bottom);
  }

  public function setAt(int $y, int $x, $value) {
    if (! array_key_exists($y, $this->values)) {
      $this->values[$y] = [];
    }
    $this->values[$y][$x] = $value;

    $this->minY = min($this->minY, $y);
    $this->maxY = max($this->maxY, $y);
    $this->minX = min($this->minX, $x);
    $this->maxX = max($this->maxX, $x);
  }

  public function getAt(int $y, int $x) {
    return ($this->values[$y] ?? [])[$x] ?? $this->defaultValue;
  }

  public function draw(callable $drawCell = null) {
    $draw = $drawCell ?: $this->drawCell;

    for ($y = $this->minY; $y <= $this->maxY; $y ++) {
      for ($x = $this->minX; $x <= $this->maxX; $x ++) {
        $draw($this->getAt($y, $x), $y, $x);
      }
      echo("\n");
    }
  }

  public function run(callable $cb) {
    for ($y = $this->minY; $y <= $this->maxY; $y ++) {
      for ($x = $this->minX; $x <= $this->maxX; $x ++) {
        $cb($this->getAt($y, $x), $y, $x);
      }
    }
  }

  public function __toString() {
    $string = '';
    for ($y = $this->minY; $y <= $this->maxY; $y++) {
      for ($x = $this->minX; $x <= $this->maxX; $x++) {
        $value = $this->getAt($y, $x);
        $string .= $value !== $this->defaultValue ? $value : $this->emptyCell;
      }
      $string .= "\n";
    }

    return $string;
  }
}


// That's all, folks!
