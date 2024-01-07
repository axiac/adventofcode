<?php

class Point implements JsonSerializable {
  public function __construct(public int $x, public int $y, public int $z) {}

  public function getSqDistanceFrom(Point $o): int {
    return ($o->x - $this->x) ** 2 + ($o->y - $this->y) ** 2 + ($o->z - $this->z) ** 2;
  }

  public function dotProduct(int $x, int $y, int $z): int {
    return $x * $this->x + $y * $this->y + $z * $this->z;
  }

  public function add(Point $p): Point {
    return new Point($this->x + $p->x, $this->y + $p->y, $this->z + $p->z);
  }

  public function subtract(Point $p): Point {
    return new Point($this->x - $p->x, $this->y - $p->y, $this->z - $p->z);
  }

  public function rotate(Rotation $r): Point {
    return $r->applyTo($this);
  }

  public function equals(Point $p): bool {
    return $p->x === $this->x && $p->y === $this->y && $p->z === $this->z;
  }

  public function manhattanDistance(Point $p): int {
    return abs($this->x - $p->x) + abs($this->y - $p->y) + abs($this->z - $p->z);
  }

  public function jsonSerialize(): array {
    return [$this->x, $this->y, $this->z];
  }

  public function __toString(): string {
    return json_encode([$this->x, $this->y, $this->z]);
  }
}

class Rotation implements JsonSerializable {
  public function __construct(private array $x, private array $y, private array $z) {}

  public function applyTo(Point $p): Point {
    return new Point(
      $p->dotProduct($this->x[0], $this->x[1], $this->x[2]),
      $p->dotProduct($this->y[0], $this->y[1], $this->y[2]),
      $p->dotProduct($this->z[0], $this->z[1], $this->z[2]),
    );
  }

  public function inverse(): Rotation {
    return new Rotation(
      [$this->x[0], $this->y[0], $this->z[0]],
      [$this->x[1], $this->y[1], $this->z[1]],
      [$this->x[2], $this->y[2], $this->z[2]]
    );
  }

  public function multiply(Rotation $other): Rotation {
    $x = new Point(...$this->x);
    $y = new Point(...$this->y);
    $z = new Point(...$this->z);
    $t = $other->inverse();

    return new Rotation(
      [$x->dotProduct(...$t->x), $x->dotProduct(...$t->y), $x->dotProduct(...$t->z)],
      [$y->dotProduct(...$t->x), $y->dotProduct(...$t->y), $y->dotProduct(...$t->z)],
      [$z->dotProduct(...$t->x), $z->dotProduct(...$t->y), $z->dotProduct(...$t->z)]
    );
  }

  public function jsonSerialize(): array {
    return [$this->x, $this->y, $this->z];
  }

  public function __toString(): string {
    return json_encode($this);
  }
}

class Pair implements JsonSerializable {
  public function __construct(public Point $left, public Point $right) {}

  public function jsonSerialize(): array {
    return [$this->left, $this->right];
  }

  public function __toString(): string {
    return "($this->left, $this->right)";
  }
}

// Scanner position and rotation relative to another scanner
class Orientation {
  public function __construct(public Point $position, public Rotation $rotation) {}

  public function __toString(): string {
    return json_encode($this);
  }
}


// That's all, folks!
