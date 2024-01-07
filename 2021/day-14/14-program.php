<?php
/**
 * Day 14: Extended Polymerization
 *
 * @link https://adventofcode.com/2021/day/14
 */

/**
 * Run the program as:
 *   php 14-program.php
 * or
 *   php 14-program.php 14-example
 */

// Input file
$inputfile = dirname($argv[0]).'/14-input';
if ($argc >= 2) {
  $inputfile = $argv[1];
}

// Read the input data
$inputdata = array_map('trim', file($inputfile));

// Parse the input data
$template = array_shift($inputdata);
array_shift($inputdata);

$rules = [];
foreach ($inputdata as $line) {
  if (! preg_match('/^(\w+) -> (\w+)$/', $line, $matches)) {
    throw new Exception("The line '$line' does not match the pattern");
  }

  $rules[$matches[1]] = $matches[2];
}


// Split the input in pairs
$polymer = [];
for ($i = 0; $i < strlen($template) - 1; $i ++) {
  $pair = substr($template, $i, 2);
  $polymer[$pair] = ($polymer[$pair] ?? 0) + 1;
}
// Count each element once
$elements = array_count_values(str_split($template));


// Part 1
for ($i = 1; $i <= 10; $i ++) {
  $polymer = process($polymer, $rules, $elements);
}
asort($elements);
printf("part 1: %d\n", end($elements) - reset($elements));


// Part 2
for (; $i <= 40; $i ++) {
  $polymer = process($polymer, $rules, $elements);
}
asort($elements);
printf("part 2: %d\n", end($elements) - reset($elements));



function process(array $polymer, array $rules, array &$elements): array {
  $next = [];

  foreach ($polymer as $pair => $count) {
    $insert = $rules[$pair];
    $next = count_item($next, "{$pair[0]}{$insert}", $count);
    $next = count_item($next, "{$insert}{$pair[1]}", $count);
    $elements = count_item($elements, $insert, $count);
  }

  return $next;
}

function count_item(array $next, string $item, int $count) {
  $next[$item] = ($next[$item] ?? 0) + $count;
  return $next;
}


// That's all, folks!
