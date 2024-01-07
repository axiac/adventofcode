<?php
/**
 * Day 1: Report Repair
 *
 * @link https://adventofcode.com/2020/day/1
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

$numbers = array_map('intval', file($inputfile));

$two = [];
$three = [];

for ($i = 0; $i < count($numbers); $i ++) {
  $x = $numbers[$i];
  for ($j = 0; $j < $i; $j ++) {
    $y = $numbers[$j];
    if ($x + $y === 2020) {
      $two[] = [$x, $y];
    }

    for ($k = 0; $k < $j; $k ++) {
      $z = $numbers[$k];
      if ($x + $y + $z === 2020) {
        $three[] = [$x, $y, $z];
      }
    }
  }
}

$part1 = implode(' ', array_map(fn($t) => array_product($t), $two));
$part2 = implode(' ', array_map(fn($t) => array_product($t), $three));

echo("part 1: {$part1}\n");
echo("part 2: {$part2}\n");


// That's all, folks!
