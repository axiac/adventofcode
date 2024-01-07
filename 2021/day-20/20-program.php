<?php
/**
 * Day 20: Trench Map
 *
 * @link https://adventofcode.com/2021/day/20
 */

/**
 * Run the program as:
 *   php 20-program.php
 * or
 *   php 20-program.php 20-example
 */

// Input file
$inputfile = dirname($argv[0]).'/20-input';
if ($argc >= 2) {
  $inputfile = $argv[1];
}

// Read the input data
$inputdata = array_map('trim', file($inputfile));

// Parse the input data
$algorithm = array_shift($inputdata);
// empty line
array_shift($inputdata);
// input image
include '../../lib/Map.php';
$inputImage = new Map();
foreach ($inputdata as $y => $line) {
  foreach (str_split($line) as $x => $pixel) {
    $inputImage->setAt($y, $x, $pixel === '#' ? 1 : 0);
  }
}


// Part 1
$image = $inputImage;
//$image->expand(10, 10, 10, 10);
$algo = fn($value) => $algorithm[$value] === '#' ? 1 : 0;
for ($i = 0; $i < 2; $i ++) {
  $image = enhanceImage($image, $algo);
}
$count = 0;
$image->run(function($value) use (&$count) { $count += $value; });

printf("part 1: %d\n", $count);

// Part 2
for (; $i < 50; $i ++) {
  $image = enhanceImage($image, $algo);
}
$count = 0;
$image->run(function($value) use (&$count) { $count += $value; });
printf("part 2: %d\n", $count);


function enhanceImage(Map $input, Closure $algorithm): Map {
  // All pixels outside the visible area have the same colour
  $default = $input->getAt(PHP_INT_MAX, PHP_INT_MAX);
  $output = new Map($algorithm(511 * $default));

  for ($y = $input->getMinY() - 1; $y <= $input->getMaxY() + 1; $y ++) {
    for ($x = $input->getMinX() - 1; $x <= $input->getMaxX() + 1; $x ++) {
      $value = computePixelValue($input, $y, $x);
      $output->setAt($y, $x, $algorithm($value));
    }
  }
  return $output;
}

function computePixelValue(Map $input, int $y, int $x): int {
  $value = 0;
  for ($sy = $y - 1; $sy <= $y + 1; $sy ++) {
    for ($sx = $x - 1; $sx <= $x + 1; $sx ++) {
      $value = 2 * $value + $input->getAt($sy, $sx);
    }
  }
  return $value;
}


// That's all, folks!
