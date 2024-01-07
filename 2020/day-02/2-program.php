<?php
/**
 * Day 2: Password Philosophy
 *
 * @link https://adventofcode.com/2020/day/2
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
$lines = file($inputfile);
$pieces = array_map(
  function($line) {
    sscanf($line, "%d-%d %c: %s\n", $min, $max, $char, $input);
    return [$min, $max, $char, $input];
  },
  $lines
);

$count1 = count(
  array_filter(
    $pieces,
    function($pieces) {
      list($min, $max, $char, $input) = $pieces;
      $letters = array_count_values(str_split($input));
      $count = @$letters[$char];
      return $min <= $count && $count <= $max;
    }
  )
);
echo("part 1: $count1\n");

$count2 = count(
  array_filter(
    $pieces,
    function($pieces) {
      list($pos1, $pos2, $char, $input) = $pieces;
      $match1 = ($char === $input[$pos1 - 1]);
      $match2 = ($char === $input[$pos2 - 1]);
      return $match1 ^ $match2;
    }
  )
);
echo("part 2: $count2\n");


// That's all, folks!
