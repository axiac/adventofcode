<?php
/**
 * Day 11: Dumbo Octopus
 *
 * @link https://adventofcode.com/2021/day/11
 */

/**
 * Run the program as:
 *   php 11-program.php
 * or
 *   php 11-program.php 11-example
 */

// Input file
$inputfile = dirname($argv[0]).'/11-input';
if ($argc >= 2) {
  $inputfile = $argv[1];
}

// Read the input data
$inputdata = array_map(fn($line) => trim($line), file($inputfile));

$nbRows = count($inputdata);
$nbCols = strlen($inputdata[0]);
$levels = array_merge(
  [array_fill(0, $nbCols + 2, 0)],
  array_map(
    fn($line) => array_merge([0], array_map('intval', str_split($line)), [0]),
    $inputdata
  ),
  [array_fill(0, $nbCols + 2, 0)]
);

function runOneStep(array $prev, int &$totalFlashes) {
  $neighbours = [[-1, -1], [-1, 0], [-1, +1],  [0, -1], [0, +1],  [+1, -1], [+1, 0], [+1, +1]];

  $nbRows = count($prev) - 2;
  $nbCols = count($prev[0]) - 2;
  $next = array_fill(0, $nbRows + 2, array_fill(0, $nbCols + 2, 0));

  // first, the energy level of each octopus increases by 1.
  for ($y = 1; $y <= $nbRows; $y ++) {
    for ($x = 1; $x <= $nbCols; $x ++) {
      $next[$y][$x] = $prev[$y][$x] + 1;
    }
  }

  do {
    $newFlashes = false;
    // Then, any octopus with an energy level greater than 9 flashes
    $prev = $next;
    for ($y = 1; $y <= $nbRows; $y ++) {
      for ($x = 1; $x <= $nbCols; $x ++) {
        if ($prev[$y][$x] > 9) {
          $totalFlashes ++;
          $next[$y][$x] = 0;
          foreach($neighbours as list($dy, $dx)) {
            if ($next[$y + $dy][$x + $dx] > 0) {
              $next[$y + $dy][$x + $dx] = $next[$y + $dy][$x + $dx] + 1;
            }
          }
          $newFlashes = true;
        }
      }
    }

  } while ($newFlashes);

  return $next;
}

function draw(array $levels) {
  return implode("\n", array_map(
    fn($row) => implode(array_map(
      fn($item) => $item >= 10 ? "[$item]" : $item,
      array_slice($row, 1, count($row) - 2)
    )),
    array_slice($levels, 1, count($levels) - 2)
  ))."\n";
}

// Part 1
$next = $levels;
$nbFlashes = 0;
for ($i = 1; $i <= 100; $i ++) {
  $next = runOneStep($next, $nbFlashes);
}

printf("part 1: %d\n", $nbFlashes);

// Part 2
$allEmpty = draw(array_fill(0, $nbRows + 2, array_fill(0, $nbCols + 2, 0)));
while (true) {
  $next = runOneStep($next, $nbFlashes);
  if (draw($next) === $allEmpty) {
    $allFlashLevel = $i;
    break;
  }
  $i ++;
}

// Part 2
printf("part 2: %d\n", $allFlashLevel);


// That's all, folks!
