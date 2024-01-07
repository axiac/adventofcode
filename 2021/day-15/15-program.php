<?php
/**
 * Day 15: Chiton
 *
 * @link https://adventofcode.com/2021/day/15
 */

/**
 * Run the program as:
 *   php 15-program.php
 * or
 *   php 15-program.php 15-example
 */

// Input file
$inputfile = dirname($argv[0]).'/15-input';
if ($argc >= 2) {
  $inputfile = $argv[1];
}

// Read the input data
$inputdata = array_map('trim', file($inputfile));

$cave1 = array_map(fn($line) => array_map('intval', str_split($line)), $inputdata);

include '../../lib/Map.php';


// Part 1
$totalRisk = aStar(makeCave($cave1, 1));
printf("part 1: %d\n", $totalRisk);


// Part 2
$totalRisk = aStar(makeCave($cave1, 5));
printf("part 2: %d\n", $totalRisk);


function makeCave(array $tile, int $count): array {
  $nbRows = count($tile);
  $nbCols = count($tile[0]);
  // Multiply the tile to get the cave
  $cave = [];
  for ($cy = 0; $cy < $count; $cy ++) {
    for ($cx = 0; $cx < $count; $cx ++) {
      for ($y = 0; $y < $nbRows; $y ++) {
        for ($x = 0; $x < $nbCols; $x ++) {
          $risk = $tile[$y][$x] + ($cy+$cx);
          $cave[$cy*$nbRows+$y][$cx*$nbCols+$x] = $risk <= 9 ? $risk : $risk - 9;
        }
      }
    }
  }

  // Border the cave with walls (cells with infinite risk, never to be entered)
  $nbRows = count($cave);
  $nbCols = count($cave[0]);
  $wallRow = array_fill(-1, $nbCols + 2, PHP_INT_MAX);

  return array_replace(
    array_fill(-1, $nbRows + 2, $wallRow),
    array_map(fn(array $line) => array_replace($wallRow, $line), $cave)
  );
}

function aStar(array $risks): int {
  $nbRows = count($risks) - 2;
  $nbCols = count($risks[0]) - 2;

  // The start cell is the first candidate
  $candidates = [
    '0:0' => ['y' => 0, 'x' => 0, 'r' => 0, 'q' => ($nbRows - 1) + ($nbCols - 1), 'p' => ['0,0']],
  ];

  // A* algorithm
  $found = false;
  $totalRisk = 0;
  $visited = new Map();
  $i = 1;
  while (! $found) {
    uasort($candidates, fn($a, $b) => $a['r'] === $b['r'] ? $a['q'] - $b['q'] : $a['r'] - $b['r']);
    $c = array_shift($candidates);
    $visited->setAt($c['y'], $c['x'], 1);
    if ($c['y'] == $nbRows - 1 && $c['x'] == $nbCols - 1) {
      $found = true;
      $totalRisk = $c['r'];
      $winner = $c;
    }
    $candidates = addCandidates($candidates, $c, $risks, $visited);
  }

  return $totalRisk;
}


function addCandidates(array $candidates, array $c, array $risks, Map $visited): array {
  foreach ([[-1, 0], [0, +1], [+1, 0], [0, -1]] as list($dy, $dx)) {
    $y = $c['y'] + $dy;
    $x = $c['x'] + $dx;
    if ($risks[$y][$x] === PHP_INT_MAX) {
      continue;                // wall
    }
    if ($visited->getAt($y, $x) === 1) {
      continue;                // visited
    }
    $key = "$y:$x";
    $risk = $c['r'] + $risks[$y][$x];
    if (! array_key_exists($key, $candidates) || $risk < $candidates[$key]['r']) {
      $candidates[$key] = ['y' => $y, 'x' => $x, 'r' => $risk, 'q' => $c['q'] - ($dy + $dx), 'p' => array_merge($c['p'], ["$y,$x"])];
    }
  }

  return $candidates;
}


// That's all, folks!
