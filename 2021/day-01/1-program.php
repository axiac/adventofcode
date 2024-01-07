<?php
/**
 * Day 1: Sonar Sweep
 *
 * @link https://adventofcode.com/2021/day/1
 */

/**
 * Run the program as:
 *   php 1-program.php
 * or
 *   php 1-program.php 1-example
 */

// Input file
$inputfile = dirname($argv[0]).'/1-input';
if ($argc >= 2) {
  $inputfile = $argv[1];
}

// Read the input data
$depths = array_map('intval', file($inputfile));

// Part 1
$increases = array_map(
  fn($i) => $depths[$i] > $depths[$i - 1] ? 1 : 0,
  range(1, count($depths) - 1)
);
printf("part 1: %d\n", array_sum($increases));

// Part 2
// Count the sums by 3-measurement sliding windows
$windows = array_map(
  fn($i) => array_sum(array_slice($depths, $i, 3)),
  range(0, count($depths) - 3)
);
$increases = array_map(
  fn($i) => $windows[$i] > $windows[$i - 1] ? 1 : 0,
  range(1, count($windows) - 1)
);
printf("part 2: %d\n", array_sum($increases));


// That's all, folks!
