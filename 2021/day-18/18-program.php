<?php
/**
 * Day 18: Snailfish
 *
 * @link https://adventofcode.com/2021/day/18
 */

/**
 * Run the program as:
 *   php 18-program.php
 * or
 *   php 18-program.php 18-example
 */

// Input file
$inputfile = dirname($argv[0]).'/18-input';
if ($argc >= 2) {
  $inputfile = $argv[1];
}

// Read the input data
$inputdata = array_map('trim', file($inputfile));

$allNumbers = array_map(
  fn($line) => readNumber($line),
  $inputdata
);


// Part 1
$sum = $allNumbers[0];
for ($i = 1; $i < count($allNumbers); $i ++) {
  $next = $allNumbers[$i];
  $sum = addNumbers($sum, $next);
  //echo("after addition: "); displayNumber($sum);
  $sum = reduceNumber($sum);
  //echo("i=$i; sum=");displayNumber($sum);
}

$magnitude = computeMagnitude($sum);
printf("part 1: %d\n", $magnitude);


// Part 2
$maxMagnitude = 0;
for ($i = 0; $i < count($allNumbers); $i ++) {
  for ($j = 0; $j < count($allNumbers); $j ++) {
    if ($j !== $i) {
      $sum = reduceNumber(addNumbers($allNumbers[$i], $allNumbers[$j]));
      $magnitude = computeMagnitude($sum);
      $maxMagnitude = max($magnitude, $maxMagnitude);
    }
  }
}

printf("part 2: %d\n", $maxMagnitude);



function readNumber(string $input): array {
  return json_decode($input, true);
}

function addNumbers(array $left, array $right): array {
  return [$left, $right];
}

function reduceNumber(array $number): array {
  do {
    $reduced = false;
    // explode?
    $next = explode_number($number);
    if ($next !== $number) {
      // echo("after explode: "); displayNumber($next);
      $number = $next;
      $reduced = true;
      continue;
    }

    // split?
    $next = split_number($number);
    if ($next !== $number) {
      // echo("after split: "); displayNumber($next);
      $number = $next;
      $reduced = true;
      continue;
    }
  } while ($reduced);

  return $number;
}

function displayNumber(array $number): void {
  echo(json_encode($number)."\n");
}

function explode_number(array $number): array {
  $where = find_path_to_explode($number, []);
  if (!$where) {
    return $number;
  }

  return explode_node($number, $where);
}

function find_path_to_explode(array $number, array $path): ?array {
  if (is_array($number[0])) {
    $where = find_path_to_explode($number[0], [...$path, 0]);
    if ($where) {
      return $where;
    }
  }
  if (count($path) >= 4 && is_int($number[0]) && is_int($number[1])) {
    return ['left' => $number[0], 'right' => $number[1], 'path' => $path];
  }
  if (is_array($number[1])) {
    $where = find_path_to_explode($number[1], [...$path, 1]);
    if ($where) {
      return $where;
    }
  }
  return null;
}

function increment_left(array $number, array $path, int $value): array {
  // When the path is full of `0` then there is nothing to increment
  if (array_sum($path) === 0) {
    return $number;
  }

  // Find the last `1` (right) in the path
  for ($i = count($path) - 1; $i >= 0; $i --) {
    if ($path[$i] === 1) {
      break;
    }
  }
  $path = array_slice($path, 0, $i);
  // Turn it into a `0` (go left)
  $path[] = 0;

  // Walk the path
  $node = &$number;
  while (count($path)) {
    $idx = array_shift($path);
    $node = &$node[$idx];
  }
  // Continue on the right node while it exist
  while (is_array($node)) {
    $node = &$node[1];
  }
  $node += $value;
  return $number;
}

function increment_right(array $number, array $path, int $value): array {
  // When the path is full of `1` then there is nothing to increment
  if (array_sum($path) === count($path)) {
    return $number;
  }

  // Find the last `0` (left) in the path
  for ($i = count($path) - 1; $i >= 0; $i --) {
    if ($path[$i] === 0) {
      break;
    }
  }
  $path = array_slice($path, 0, $i);
  // Go right
  $path[] = 1;

  // Walk the path
  $node = &$number;
  while (count($path)) {
    $idx = array_shift($path);
    $node = &$node[$idx];
  }
  // Continue on the left node while it exist
  while (is_array($node)) {
    $node = &$node[0];
  }
  $node += $value;
  return $number;
}

function explode_node(array $number, array $where): array {
  $path = $where['path'];
  $number = increment_left($number, $path, $where['left']);
  $number = increment_right($number, $path, $where['right']);

  // Walk the path
  $node = &$number;
  while (count($path)) {
    $idx = array_shift($path);
    $node = &$node[$idx];
  }
  // Replace the pair with `0`
  $node = 0;

  return $number;
}

function split_number(array $number): array {
  $where = find_path_to_split($number, []);
  if (! $where) {
    return $number;
  }

  return split_node($number, $where['path']);
}

function find_path_to_split(array|int $number, array $path): ?array {
  if (is_array($number)) {
    $where = find_path_to_split($number[0], [...$path, 0]);
    if ($where) {
      return $where;
    }
  }
  if (is_int($number) && $number >= 10) {
    return ['value' => $number, 'path' => $path];
  }
  if (is_array($number)) {
    $where = find_path_to_split($number[1], [...$path, 1]);
    if ($where) {
      return $where;
    }
  }
  return null;
}

function split_node(array $number, array $path): array {
  // Walk the path
  $node = &$number;
  while (count($path)) {
    $idx = array_shift($path);
    $node = &$node[$idx];
  }
  // Replace the node with a pair
  $left = ($node - $node % 2) / 2;
  $right = $node - $left;

  $node = [$left, $right];

  return $number;
}

function computeMagnitude(array | int $number): int {
  if (is_int($number)) {
    return $number;
  }

  return 3 * computeMagnitude($number[0]) + 2 * computeMagnitude($number[1]);
}


// That's all, folks!
