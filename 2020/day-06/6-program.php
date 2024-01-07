<?php
/**
 * Day 6: Custom Customs
 *
 * @link https://adventofcode.com/2020/day/6
 */

/**
 * Run the program as:
 *   php 6-program.php
 * or
 *   php 6-program.php 6-example-1
 */

// Input file
$inputfile = dirname($argv[0]).'/6-input';
if ($argc >= 2) {
  $inputfile = $argv[1];
}


// Read the input data
$lines = explode("\n", trim(file_get_contents($inputfile))."\n");

$totalAny = 0;
$totalEvery = 0;

$groupAnswers = array_combine(range('a', 'z'), array_fill(0, 26, 0));
$groupSize = 0;
foreach ($lines as $line) {
  if (! strlen($line) && $groupSize !== 0) {
    $anyone = count(array_filter($groupAnswers));
    $totalAny += $anyone;
    $every = count(array_filter($groupAnswers, fn($value) => $value == $groupSize));
    $totalEvery += $every;

    $groupAnswers = array_combine(range('a', 'z'), array_fill(0, 26, 0));
    $groupSize = 0;
    continue;
  }

  foreach (str_split($line) as $char) {
    if ('a' <= $char && $char <= 'z') {
      $groupAnswers[$char] ++;
    }
  }
  $groupSize ++;
}

echo("part 1: $totalAny\n");
echo("part 2: $totalEvery\n");


// That's all, folks!
