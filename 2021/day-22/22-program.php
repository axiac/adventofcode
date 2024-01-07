<?php
/**
 * Day 22: Reactor Reboot
 *
 * @link https://adventofcode.com/2021/day/22
 */

/**
 * Run the program as:
 *   php 22-program.php
 * or
 *   php 22-program.php 22-example-1
 */

// Input file
$inputfile = dirname($argv[0]).'/22-input';
if ($argc >= 2) {
  $inputfile = $argv[1];
}

// Read the input data
$inputdata = array_map('trim', file($inputfile));

// Parse the input data
$steps = array_map(
  function ($line) {
    if (! preg_match('/^(on|off) x=(-?\d+)\.\.(-?\d+),y=(-?\d+)\.\.(-?\d+),z=(-?\d+)\.\.(-?\d+)$/', $line, $m)) {
      throw new Exception("The line '$line' is not matching the expected pattern");
    }
    return [
      'action' => $m[1],
      'x' => [intval($m[2]), intval($m[3])],
      'y' => [intval($m[4]), intval($m[5])],
      'z' => [intval($m[6]), intval($m[7])],
    ];
  },
  $inputdata
);


// Part 1
$reactor = [];
// Run the reboot steps
foreach ($steps as $step) {
  $value = $step['action'] === 'on' ? 1 : 0;
  for ($x = max($step['x'][0], -50); $x <= min(50, $step['x'][1]); $x ++) {
    for ($y = max($step['y'][0], -50); $y <= min(50, $step['y'][1]); $y ++) {
      for ($z = max($step['z'][0], -50); $z <= min(50, $step['z'][1]); $z ++) {
        $reactor[$x][$y][$z] = $value;
      }
    }
  }
}

// Count the cubes turned on
$count = 0;
for ($x = -50; $x <= 50; $x ++) {
  for ($y = -50; $y <= 50; $y ++) {
    for ($z = -50; $z <= 50; $z ++) {
      $count += $reactor[$x][$y][$z] ?? 0;
    }
  }
}
printf("part 1: %d\n", $count);


// Part 2

//
//
class Point {
  public function __construct(public int $x, public int $y, public int $z) {}
  public function __toString(): string { return "($this->x,$this->y,$this->z)"; }
}

class Cube {
  public function __construct(private Point $p1, private Point $p2) { }
  public function __toString(): string { return "[$this->p1,$this->p2]"; }

  public function intersects(Cube $other): bool {
    return ($this->p1->x - $other->p2->x) * ($this->p2->x - $other->p1->x) <= 0
      &&   ($this->p1->y - $other->p2->y) * ($this->p2->y - $other->p1->y) <= 0
      &&   ($this->p1->z - $other->p2->z) * ($this->p2->z - $other->p1->z) <= 0
    ;
  }

  public function getIntersection(Cube $other): Cube {
    return new Cube(
      new Point(max($this->p1->x, $other->p1->x), max($this->p1->y, $other->p1->y), max($this->p1->z, $other->p1->z)),
      new Point(min($this->p2->x, $other->p2->x), min($this->p2->y, $other->p2->y), min($this->p2->z, $other->p2->z))
    );
  }

  public function getVolume(): int {
    return ($this->p2->x - $this->p1->x + 1) * ($this->p2->y - $this->p1->y + 1) * ($this->p2->z - $this->p1->z + 1);
  }
}

//
$reactor = [];
foreach ($steps as $i => $step) {
  $value = $step['action'] === 'on' ? +1 : -1;
  $current = new Cube(
    new Point($step['x'][0], $step['y'][0], $step['z'][0]),
    new Point($step['x'][1], $step['y'][1], $step['z'][1])
  );

  // Start with a new, empty reactor
  $next = [];
  // Check the current cuboid against each cuboid that already exists in the reactor
  foreach ($reactor as $c) {
    $cube = $c['cube'];
    $times  = $c['times'];

    // Put the original cuboid in the new reactor
    $next[] = $c;
    // If the current cuboid intersects the original then count the intersection and compute how many times it is covered
    if ($current->intersects($cube)) {
      $inter = $current->getIntersection($cube);
      $next[] = ['cube' => $inter, 'times' => $times + $value];
    }
  }

  // Put the current cuboid in the reactor only if it is an 'on' cuboid
  if ($value > 0) {
    $next[] = ['cube' => $current, 'times' => 1];
  }

  $reactor = $next;
}
// Count the values
$count = 0;
foreach ($reactor as $c) {
  $value = $c['times'];
  if ($value % 2 !== 0) {
    $count += $c['cube']->getVolume();
  } else {
    $count -= $c['cube']->getVolume();
  }
}
printf("part 2: %d\n", $count);


// That's all, folks!
