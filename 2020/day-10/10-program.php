<?php
/**
 * Day 10: Adapter Array
 *
 * @link https://adventofcode.com/2020/day/10
 */

/**
 * Run the program as:
 *   php 10-program.php
 * or
 *   php 10-program.php 10-example-1
 */

// Input file
$inputfile = dirname($argv[0]).'/10-input';
if ($argc >= 2) {
  $inputfile = $argv[1];
}

// Read the input data
$input = array_map('intval', explode("\n", trim(file_get_contents($inputfile))));


// Sort the input values ascending
sort($input, SORT_NUMERIC);
// Create an array that contains the outlet at start
$first = $input;
array_unshift($first, 0);
// Create a new array that contains the built-in adapter at the end
$next = $input;
$next[] = end($input) + 3;

// all
$all = $input;
array_unshift($all, 0);
array_push($all, end($input) + 3);

// Compute the differences
$differences = array_map(
  fn($one, $two) => $two - $one,
  $first,
  $next
);

$values = array_count_values($differences);
printf("part 1: %d\n", $values[1] * $values[3]);


// Find the the sequences of differences of `1`
$sequences = [];
$prev = $differences[0];
$seq = $prev;
$len = 1;
for ($i = 1; $i < count($differences); $i ++) {
  $curr = $differences[$i];
  if ($curr != $seq) {
    $sequences[] = [$seq, $len, $i - $len];
    $seq = $curr;
    $len = 0;
  }

  $len ++;
  $prev = $curr;
}


// The sequences of `1` can be combined to use `2` or `3`
//
// # of 1s | # of combinations
//     1   | 1      (1)
//     2   | 2      (1,1) and (2)
//     3   | 4      (1,1,1), (1,2), (2,1) and (3)
//     4   | 7      (1,1,1,1), (1,1,2), (1,2,1), (2,1,1), (2,2), (1,3), (3,1)

$combinations = [
  1 => 1,
  2 => 2,
  3 => 4,
  4 => 7,
];

$count = 1;
foreach ($sequences as list($seq, $len, $start)) {
  if ($seq === 1 && $len !== 1) {
    $count *= $combinations[$len];
  }
}
echo("part 2: {$count}\n");


// That's all, folks!
