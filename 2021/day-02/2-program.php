<?php
/**
 * Day 2: Dive!
 *
 * @link https://adventofcode.com/2021/day/2
 */

/**
 * Run the program as:
 *   php 2-program.php
 * or
 *   php 2-program.php 2-example
 */

// Input file
$inputfile = dirname($argv[0]).'/2-input';
if ($argc >= 2) {
  $inputfile = $argv[1];
}

// Read the input data
$course = array_map(
  function($line) { return explode(' ', $line); },
  file($inputfile)
);



// Part 1
$position = 0;
$depth = 0;
foreach ($course as $leg) {
  $distance = intval($leg[1]);
  switch ($leg[0]) {
    case 'forward': $position += $distance; break;
    case 'up': $depth -= $distance; break;
    case 'down': $depth += $distance; break;
    default: throw new Exception("Unknown command '${leg[0]}");
  }
}
printf("part 1: %d\n", $position * $depth);


// Part 2
$position = 0;
$depth = 0;
$aim = 0;
foreach ($course as $leg) {
  $command = $leg[0];
  $distance = intval($leg[1]);
  switch ($command) {
    case 'forward': $position += $distance; $depth += $aim * $distance; break;
    case 'up': $aim -= $distance; break;
    case 'down': $aim += $distance; break;
    default: throw new Exception("Unknown command '${command}");
  }
}
printf("part 1: %d\n", $position * $depth);

// That's all, folks!
