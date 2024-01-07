<?php
/**
 * Day 25: Combo Breaker
 *
 * @link https://adventofcode.com/2020/day/25
 */

/**
 * Run the program as:
 *   php 25-program.php
 * or
 *   php 25-program.php 25-example
 */

// Input file
$inputfile = dirname($argv[0]).'/25-input';
if ($argc >= 2) {
  $inputfile = $argv[1];
}

// Read the input data
$input = explode("\n", trim(file_get_contents($inputfile)));


$publicCard = intval($input[0]);
$publicDoor = intval($input[1]);

const DIVIDER = 20201227;

$loopsCard = findLoopSize(7, $publicCard);
$loopsDoor = findLoopSize(7, $publicDoor);

printf("card: %d\n", $loopsCard);
printf("door: %d\n", $loopsDoor);

printf("encryption key: %d\n", transform($publicDoor, $loopsCard));
printf("encryption key: %d\n", transform($publicCard, $loopsDoor));



function findLoopSize($subject, $publicKey) {
  $value = 1;

  $i = 0;
  do {
    $value = ($value * $subject) % DIVIDER;
    $i ++;
    //printf("%3d: %d\n", $i, $value);
  } while ($value !== $publicKey);

  return $i;
}

function transform($subject, $loopSize) {
  $value = 1;

  for ($i = 0; $i < $loopSize; $i ++) {
    $value = ($value * $subject) % DIVIDER;
  }

  return $value;
}




// That's all, folks!
