<?php
/**
 * Day {day}: {title}
 *
 * @link https://adventofcode.com/{year}/day/{day}
 */

/**
 * Run the program as:
 *   php {day}-program.php
 * or
 *   php {day}-program.php {day}-example
 */

// Input file
$inputfile = dirname($argv[0]).'/{day}-input';
if ($argc >= 2) {
  $inputfile = $argv[1];
}

// Read the input data
$inputdata = array_map('trim', file($inputfile));

// Parse the input data




// Part 1
$part1 = 0;
printf("part 1: %d\n", $part1);

// Part 2
$part2 = 0;
printf("part 2: %d\n", $part2);
