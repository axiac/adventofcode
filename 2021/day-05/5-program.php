<?php
/**
 * Day 5: Hydrothermal Venture
 *
 * @link https://adventofcode.com/2021/day/5
 */

/**
 * Run the program as:
 *   php 5-program.php
 * or
 *   php 5-program.php 5-example
 */

// Input file
$inputfile = dirname($argv[0]).'/5-input';
if ($argc >= 2) {
  $inputfile = $argv[1];
}

// Read the input data
$inputdata = array_map(
  function ($line) {
    $line = trim($line);
    if (! preg_match('/^(\\d+),(\\d+) -> (\\d+),(\\d+)$/', $line, $matches)) {
      throw new Exception("Input line not matching the expected pattern. Line: '$line'");
    }
    return [[intval($matches[1]), intval($matches[2])], [intval($matches[3]), intval($matches[4])]];
  },
  file($inputfile)
);

$map1 = [];
$map2 = [];
foreach ($inputdata as list($start, $stop)) {
  list($x1, $y1) = $start;
  list($x2, $y2) = $stop;
  $n = ($x2 !== $x1 ? abs($x2 - $x1): abs($y2 - $y1));
  $dx = ($x2 - $x1) / $n;
  $dy = ($y2 - $y1) / $n;

  for ($i = 0, $x = $x1, $y = $y1; $i <= $n; $i ++, $x += $dx, $y += $dy) {
    // For part 1 consider only horizontal and vertical lines
    if ($x2 === $x1 || $y2 === $y1) {
      putCloud($map1, $x, $y);
    }
    // For part 2 consider all lines
    putCloud($map2, $x, $y);
  }
}

printf("part 1: %d\n", countMultiple($map1));
printf("part 2: %d\n", countMultiple($map2));



function putCloud(array &$map, int $x, int $y) {
  if (! array_key_exists($y, $map)) {
    $map[$y] = [];
  }
  $map[$y][$x] = ($map[$y][$x] ?? 0) + 1;
}

function countMultiple(array $map) {
  return array_sum(array_map(
    fn(array $row) =>
      array_sum(array_map(
        fn($item) => 1,
        array_filter(
          $row,
          fn($item) => $item >= 2
        )
      )),
    $map
  ));
}

function printMap(array $map) {
  $minY = min(array_keys($map));
  $maxY = max(array_keys($map));
  $minX = min(array_map(fn($row) => min(array_keys($row)), $map));
  $maxX = max(array_map(fn($row) => max(array_keys($row)), $map));

  for ($y = $minY; $y <= $maxY; $y ++){
    echo("$y: ");
    for ($x = $minX; $x <= $maxX; $x ++){
      echo($map[$y][$x] ?? '.');
    }
    echo("\n");
  }
}


// That's all, folks!
