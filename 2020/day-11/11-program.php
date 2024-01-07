<?php
/**
 * Day 11: Seating System
 *
 * @link https://adventofcode.com/2020/day/11
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
$input = array_map('str_split', explode("\n", trim(file_get_contents($inputfile))));


// Part 1: count occupied neighbour seats
$current = $input;
do {
  list($moves, $next) = occupy($current, 4, 'countNeighbours');
  $current = $next;
} while ($moves);

$occupied = countOccupied($current);
echo("part 1: $occupied\n");


// Part 2: count occupied visible seats
$current = $input;
do {
  list($moves, $next) = occupy($current, 5, 'countVisible');
  $current = $next;
} while ($moves);

$occupied = countOccupied($current);
echo("part 2: $occupied\n");




function occupy(array $seats, $nb, callable $fnCount) {
  $moves = false;
  $next = [];

  $nbRows = count($seats);
  for ($r = 0; $r < $nbRows; $r ++) {
    $nextR = [];

    $nbCols = count($seats[$r]);
    for ($c = 0; $c < $nbCols; $c ++) {
      $s = $seats[$r][$c];

      // Floor do not change
      if ($s === '.') {
        $nextR[$c] = $s;
        continue;
      }

      // Count the surrounding occupied chairs
      $occupied = $fnCount($seats, $r, $c);

      // If a seat is empty (L) and there are no occupied seats adjacent to it, the seat becomes occupied.
      if ($s === 'L' && $occupied === 0) {
        $nextR[$c] = '#';
        $moves = true;
        continue;
      }

      // If a seat is occupied (#) and four or more seats adjacent to it are also occupied, the seat becomes empty.
      if ($s === '#' && $occupied >= $nb) {
        $nextR[$c] = 'L';
        $moves = true;
        continue;
      }

      // Otherwise, the seat's state does not change.
      $nextR[$c] = $s;
    }

    $next[$r] = $nextR;
  }

  return [ $moves, $next ];
}

function countNeighbours(array $seats, $r, $c) {
  $nbRows = count($seats);
  $nbCols = count($seats[$r]);
  $nbOccupied = 0;

  foreach ([
    [-1, -1], [-1, 0], [-1, +1],
    [ 0, -1],          [ 0, +1],
    [+1, -1], [+1, 0], [+1, +1],
  ] as list($dr, $dc)) {
    $rx = $r + $dr;
    $cx = $c + $dc;
    if ((0 <= $rx && $rx < $nbRows) && (0 <= $cx && $cx < $nbCols)) {
      if ($seats[$rx][$cx] === '#') {
        $nbOccupied ++;
      }
    }
  }

  return $nbOccupied;
}

function countVisible(array $seats, $r, $c) {
  $nbRows = count($seats);
  $nbCols = count($seats[$r]);
  $nbOccupied = 0;

  foreach ([
    [-1, -1], [-1, 0], [-1, +1],
    [ 0, -1],          [ 0, +1],
    [+1, -1], [+1, 0], [+1, +1],
  ] as list($dr, $dc)) {
    $rx = $r + $dr;
    $cx = $c + $dc;
    $found = false;

    while (! $found && (0 <= $rx && $rx < $nbRows) && (0 <= $cx && $cx < $nbCols)) {
      switch ($seats[$rx][$cx]) {
        case '#':
          $nbOccupied ++;
          $found = true;
          break;
        case 'L':
          $found = true;
          break;
        default:
          $found = false;
          break;
      }

      $rx += $dr;
      $cx += $dc;
    }
  }

  return $nbOccupied;
}

function countOccupied(array $seats) {
  return array_sum(
    array_map(
      function(array $row) { return array_count_values($row)['#'] ?? 0; },
      $seats
    )
  );
}

function printSeats(array $seats) {
  foreach ($seats as $row) {
    echo(implode('', $row)."\n");
  }
  echo("\n");
}


// That's all, folks!
