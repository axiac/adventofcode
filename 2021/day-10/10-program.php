<?php
/**
 * Day 10: Syntax Scoring
 *
 * @link https://adventofcode.com/2021/day/10
 */

/**
 * Run the program as:
 *   php 10-program.php
 * or
 *   php 10-program.php 10-example
 */

// Input file
$inputfile = dirname($argv[0]).'/10-input';
if ($argc >= 2) {
  $inputfile = $argv[1];
}

// Read the input data
$inputdata = array_map(fn($line) => trim($line), file($inputfile));

$openChars = [
  '(' => [')', 1],
  '[' => [']', 2],
  '{' => ['}', 3],
  '<' => ['>', 4],
];

$closeChars = [
  ')' => ['(', 3],
  ']' => ['[', 57],
  '}' => ['{', 1197],
  '>' => ['<', 25137],
];

// Part 1
$score = 0;
$incomplete = [];
foreach ($inputdata as $line) {
  $stack = [];
  foreach (str_split($line) as $char) {
    if (array_key_exists($char, $openChars)) {
      array_push($stack, $char);
      continue;
    }
    if (array_key_exists($char, $closeChars)) {
      $close = $closeChars[$char];
      $pair = array_pop($stack);
      if ($pair == $close[0]) {
        continue;
      }

      $score += $close[1];
      continue 2;
    }
    throw new Exception("Unexpected character '$char' in '$line'");
  }
  if (count($stack) === 0) {
    throw new Exception("Correct line found: '$line'");
  }

  // The line is incomplete
  $incomplete[] = $stack;
}

printf("part 1: %d\n", $score);


// Part 2
if (count($incomplete) % 2 === 0) {
  throw new Exception("The number of incomplete lines must be odd.");
}

$scores = [];
foreach ($incomplete as $stack) {
  $s = 0;
  foreach (array_reverse($stack) as $char) {
    $s = 5 * $s + $openChars[$char][1];
  }
  $scores[] = $s;
}
sort($scores);
$score = $scores[(count($scores) - 1) / 2];

printf("part 2: %d\n", $score);


// That's all, folks!
