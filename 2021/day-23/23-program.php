<?php
/**
 * Day 23: Amphipod
 *
 * @link https://adventofcode.com/2021/day/23
 */

/**
 * Run the program as:
 *   php 23-program.php
 * or
 *   php 23-program.php 23-example-1
 */

// Input data
$example = false;
if ($argc >= 2) {
  $example = true;
}


// The game definition
if ($example) {
  // The initial positions (example)
  $start[1] = [12 => 'B', 13 => 'C', 14 => 'B', 15 => 'D', 16 => 'A', 17 => 'D', 18 => 'C', 19 => 'A'];
  $start[2] = [12 => 'B', 13 => 'C', 14 => 'B', 15 => 'D', 16 => 'D', 17 => 'C', 18 => 'B', 19 => 'A', 20 => 'D', 21 => 'B', 22 => 'A', 23 => 'C', 24 => 'A', 25 => 'D', 26 => 'C', 27 => 'A'];
} else {
  // The initial positions (input)
  $start[1] = [12 => 'D', 13 => 'B', 14 => 'D', 15 => 'A', 16 => 'C', 17 => 'C', 18 => 'A', 19 => 'B'];
  $start[2] = [12 => 'D', 13 => 'B', 14 => 'D', 15 => 'A', 16 => 'D', 17 => 'C', 18 => 'B', 19 => 'A', 20 => 'D', 21 => 'B', 22 => 'A', 23 => 'C', 24 => 'C', 25 => 'C', 26 => 'A', 27 => 'B'];
}


require './Amphipods.php';

// A*
function resolve(Position $pos): Position {
  $visited = [];
  $pending = [$pos->sig() => $pos];

  while (count($pending)) {
    // Get the best candidate
    uasort($pending, fn(Position $a, Position $b) => $a->compareWith($b));
    $pos = array_shift($pending);

    // Mark it as visited
    $visited[$pos->sig()] = true;
    // printf("\r[%s]; energy: %5d; distance: %5d; visited: %5d; pending: %5d", $pos->sig(), $pos->getEnergy(), $pos->getDistance(), count($visited), count($pending));

    // Check if it solves the problem
    if ($pos->isSolution()) {
      $solution = $pos;
      break;
    }

    // Generate new positions by moving the amphipods
    $next = $pos->generateNext();
    foreach ($next as $p) {
      $sig = $p->sig();
      if (! array_key_exists($sig, $visited)) {
        $pending[$sig] = $p;
      }
    }
  }

  return $pos;
}


// Part 1
$pos = resolve(new Position($start[1], new Board1()));
printf("part 1: %d\n", $pos->getEnergy());

// Part 2
$pos = resolve(new Position($start[2], new Board2()));
printf("part 2: %d\n", $pos->getEnergy());


// That's all, folks!
