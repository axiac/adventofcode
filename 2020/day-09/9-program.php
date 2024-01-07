<?php
/**
 * Day 9: Encoding Error
 *
 * @link https://adventofcode.com/2020/day/9
 */

/**
 * Run the program as:
 *   php 9-program.php
 * or
 *   php 9-program.php 5 9-example
 */

// Input file
$inputfile = dirname($argv[0]).'/9-input';
$size = 25;            // length of the preamble
if ($argc >= 3) {
  $size = intval($argv[1]);
  $inputfile = $argv[2];
}

// Read the input data
$lines = explode("\n", trim(file_get_contents($inputfile)));


try {
  // Part 1
  $invalid = part_1($lines, $size);
  echo("part 1: $invalid\n");
  // Part 2
  $weakness = part_2($lines, $invalid);
  echo("part 2: $weakness\n");
} catch (Exception $e) {
  fprintf(STDERR, "%s\n", $e->getMessage());
  exit(1);
}


// What is the first number in the list that is not the sum of two of the $size numbers before it?
function part_1(array $lines, $size) {
  for ($i = $size; $i < count($lines); $i ++) {
    $number = intval($lines[$i]);
    if (! is_valid($number, array_slice($lines, $i - $size, $size))) {
      // echo("part 1: {$number}\n");
      // exit(0);
      return $number;
    }
  }
  // echo("all numbers are valid\n");
  // exit(1);
  throw new Exception('all numbers are valid');
}

// Add the smallest and the largest number from the contiguous range of input values
// whose sum is equal to the invalid number
function part_2(array $lines, $invalid) {
  $count = count($lines);
  for ($i = 0; $i < $count; $i ++) {
    for ($j = 0; $j < $i; $j ++) {
      $range = array_slice($lines, $j, $i - $j);
      if (array_sum($range) === $invalid) {
        return min($range) + max($range);
      }
    }
  }
  throw new Exception('range not found');
}


function is_valid($number, array $array) {
  $count = count($array);

  for ($i = 0; $i < $count; $i ++) {
    for ($j = 0; $j < $i; $j ++) {
      if ($array[$i] + $array[$j] === $number) {
        return true;
      }
    }
  }
  return false;
}


// That's all, folks!
