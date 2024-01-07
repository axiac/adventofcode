<?php
/**
 * Day 7: The Treachery of Whales
 *
 * @link https://adventofcode.com/2021/day/7
 */

/**
 * Run the program as:
 *   php 7-program.php
 * or
 *   php 7-program.php 7-example
 */

// Input file
$inputfile = dirname($argv[0]).'/7-input';
if ($argc >= 2) {
  $inputfile = $argv[1];
}

// Read the input data
$inputdata = array_map(fn($value) => intval($value), explode(',', file_get_contents($inputfile)));

// Part 1
$minX = min($inputdata);
$maxX = max($inputdata);

$consumption = array_map(
  fn($pos) => array_sum(array_map(
    fn($idx) => abs($pos - $inputdata[$idx]),
    array_keys($inputdata)
  )),
  range($minX, $maxX)
);
asort($consumption);

printf("part 1: %d\n", array_values($consumption)[0]);

// Part 2
$consumption = array_map(
  fn($pos) => array_sum(array_map(
    fn($idx) => abs($pos - $inputdata[$idx]) * (abs($pos - $inputdata[$idx]) + 1) / 2,
    array_keys($inputdata)
  )),
  range($minX, $maxX)
);
asort($consumption);

printf("part 2: %d\n", array_values($consumption)[0]);


// That's all, folks!
