<?php
/**
 * Day 3: Toboggan Trajectory
 *
 * @link https://adventofcode.com/2020/day/3
 */

/**
 * Run the program as:
 *   php 3-program.php
 * or
 *   php 3-program.php 3-example
 */

// Input file
$inputfile = dirname($argv[0]).'/3-input';
if ($argc >= 2) {
  $inputfile = $argv[1];
}

// Read the input data
$track = array_map('trim', file($inputfile));

function slope(array $track, int $dx, int $dy): int {
  $x = 0;
  $y = 0;
  $trees = 0;

  while ($y < count($track)) {
    $trees += ($track[$y][$x] === '#' ? 1 : 0);
    $x = ($x + $dx) % strlen($track[$y]);
    $y += $dy;
  }

  return $trees;
}

echo('part 1: '.slope($track, 3, 1)."\n");

$product = 1;
foreach ([[1, 1], [3, 1], [5, 1], [7, 1], [1, 2]] as list($dx, $dy)) {
  $product *= slope($track, $dx, $dy);
}

echo("part 2: {$product}\n");


// That's all, folks!
