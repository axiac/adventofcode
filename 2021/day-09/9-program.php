<?php
/**
 * Day 9: Smoke Basin
 *
 * @link https://adventofcode.com/2021/day/9
 */

/**
 * Run the program as:
 *   php 9-program.php
 * or
 *   php 9-program.php 9-example-1
 */

// Input file
$inputfile = dirname($argv[0]).'/9-input';
if ($argc >= 2) {
  $inputfile = $argv[1];
}

// Read the input data
$inputdata = array_map(fn($line) => trim($line), file($inputfile));

$map = array_merge(
  [array_pad([], strlen($inputdata[0]) + 2, 99)],
  array_map(
    fn($line) => array_merge([99], array_map('intval', str_split($line)), [99]),
    $inputdata
  ),
  [array_pad([], strlen($inputdata[0]) + 2, 99)]
);


// Part 1
$sum = 0;
$lows = [];
for ($r = 1; $r < count($map) - 1; $r ++) {
  for ($c = 1; $c < count($map[$r]) - 1; $c ++) {
    $h = $map[$r][$c];
    if ($h < $map[$r-1][$c] && $h < $map[$r+1][$c] && $h < $map[$r][$c-1] && $h < $map[$r][$c+1]) {
      $sum += $h + 1;
      $lows[] = [$r, $c];
    }
  }
}
printf("part 1: %d\n", $sum);

// Part 2
$basins = [];
foreach ($lows as list($r, $c)) {
  $b = [];
  $p = [[$r, $c]];
  while (count($p) > 0) {
    list($r, $c) = array_shift($p);     // get first unvisited
    if ($map[$r][$c] >= 9) {
      // has already been visited
      continue;
    }
    $b[] = [$r, $c];                    // add to basin
    $map[$r][$c] = 999;                 // mark as visited

    // check neighbours
    foreach ([[-1,0],[0,+1],[+1,0],[0,-1]] as list($dr, $dc)) {
      if ($map[$r+$dr][$c+$dc] < 9) {
        $p[] = [$r+$dr, $c+$dc];         // add to the list to be visited
      }
    }
  }
  $basins[] = $b;
}

$sizes = array_map(fn(array $b) => count($b), $basins);
sort($sizes);
$product = array_product(array_slice($sizes, -3));

printf("part 2: %d\n", $product);


// That's all, folks!
