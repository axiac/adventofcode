<?php
/**
 * Day 25: Sea Cucumber
 *
 * @link https://adventofcode.com/2021/day/25
 */

/**
 * Run the program as:
 *   php 25-program.php
 * or
 *   php 25-program.php 25-example
 */

// Input file
$inputfile = dirname($argv[0]).'/25-input';
$input = [];
if ($argc >= 2) {
  $inputfile = $argv[1];
  $input = array_slice($argv, 2);
}

// Read the input data
$inputdata = array_map('trim', file($inputfile));

// Parse the input data
$map = array_map('str_split', $inputdata);

$sig = draw($map);
$step = 0;
do {
  $oldSig = $sig;
  $map = moveCucumbers($map);
  $sig = draw($map);
  $step ++;
} while ($sig !== $oldSig);


// Part 1
printf("part 1: %d\n", $step);


function moveCucumbers(array $map): array {
  $nbRows = count($map);
  $nbCols = count($map[0]);

  // Move the east-facing cucumbers first
  $next = array_fill(0, $nbRows, array_fill(0, $nbCols, '.'));
  for ($r = 0; $r < $nbRows; $r ++) {
    for ($c = 0; $c < $nbCols; $c ++) {
      switch ($map[$r][$c]) {
        case 'v':
          // Not moving at this step; copy to destination
          $next[$r][$c] = 'v';
          break;
        case '>':
          $dc = ($c + 1) % $nbCols;
          if ($map[$r][$dc] === '.') {
            $next[$r][$dc] = '>';       // move to the empty cell
          } else {
            $next[$r][$c] = '>';        // cannot move, the cell is not empty
          }
          break;
        default:
          break;
      }
    }
  }
  $map = $next;

  // Move the south-facing cucumbers
  $next = array_fill(0, $nbRows, array_fill(0, $nbCols, '.'));
  for ($r = 0; $r < $nbRows; $r ++) {
    for ($c = 0; $c < $nbCols; $c ++) {
      switch ($map[$r][$c]) {
        case '>':
          // Not moving at this step; copy to destination
          $next[$r][$c] = '>';
          break;
        case 'v':
          $dr = ($r + 1) % $nbRows;
          if ($map[$dr][$c] === '.') {
            $next[$dr][$c] = 'v';       // move to the empty cell
          } else {
            $next[$r][$c] = 'v';        // cannot move, the cell is not empty
          }
          break;
        default:
          break;
      }
    }
  }

  return $next;
}

function draw(array $map): string {
  return implode("\n", array_map(fn($row) => implode('', $row), $map))."\n";
}


// That's all, folks!
