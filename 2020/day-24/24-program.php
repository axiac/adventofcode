<?php
/**
 * Day 24: Lobby Layout
 *
 * @link https://adventofcode.com/2020/day/24
 */

/**
 * Run the program as:
 *   php 24-program.php
 * or
 *   php 24-program.php 24-example
 */

// Input file
$inputfile = dirname($argv[0]).'/24-input';
if ($argc >= 2) {
  $inputfile = $argv[1];
}

// Read the input data
$input = explode("\n", trim(file_get_contents($inputfile)));

$tiles = [];

// Place the tiles
foreach ($input as $line) {
  $x = 0;
  $y = 0;

  for ($index = 0; $index < strlen($line); $index ++) {
    $dir = $line[$index];
    switch ($dir) {
      case 'e': $x ++; break;
      case 'w': $x --; break;
      case 'n':
      case 's':
        $index ++;
        $dir .= $line[$index];
        switch ($dir) {
          case 'ne': $x ++; $y --; break;
          case 'nw': $y --; break;
          case 'se': $y ++; break;
          case 'sw': $x --; $y ++; break;
          default: fprintf(STDERR, "Unknown direction '$dir'"); exit(1);
        }
        break;
      default:
        fprintf(STDERR, "Unknown direction '$dir'"); exit(1);
    }
  }

  if (! isset($tiles[$y])) {
    $tiles[$y] = [];
  }
  if (! isset($tiles[$y][$x])) {
    $tiles[$y][$x] = 1;                         // black
  } else {
    $tiles[$y][$x] = 1 - $tiles[$y][$x];        // flip
  }
}

printf("part 1: %d\n", howMany($tiles));


// Flip the tiles
$next = $tiles;
for ($i = 0; $i < 100; $i ++) {
  $next = flip($next);
}

$part2 = howMany($next);
printf("part 2: %d\n", $part2);


function howMany(array $tiles) {
  return array_sum(array_map('array_sum', $tiles));
}


function flip(array $tiles) {
  $minX = min(array_map(function(array $row) { return min(array_keys($row)); }, $tiles));
  $maxX = max(array_map(function(array $row) { return max(array_keys($row)); }, $tiles));
  $minY = min(array_keys($tiles));
  $maxY = max(array_keys($tiles));

  $next = [];

  for ($y = $minY - 1; $y <= $maxY + 1; $y ++) {
    for ($x = $minX - 1; $x <= $maxX + 1; $x ++) {
      $nb = getBlackNeighbours($tiles, $y, $x);
      $black = false;
      if (isset($tiles[$y][$x]) && $tiles[$y][$x] === 1) {
        // black
        if ($nb === 1 || $nb === 2) {
          // remain black
          $black = true;
        }
      } else {
        // white
        if ($nb === 2) {
          // turn to black
          $black = true;
        }
      }

      if ($black) {
        if (! isset($next[$y])) {
          $next[$y] = [];
        }
        $next[$y][$x] = 1;
      }
    }
  }

  return $next;
}


function getBlackNeighbours(array $tiles, $r, $c) {
  $nbNeighbours = 0;
  // e, se, sw, w, nw, ne
  foreach ([[1, 0], [0, 1], [-1, 1], [-1, 0], [0, -1], [1, -1]] as list($dr, $dc)) {
    if (isset($tiles[$r + $dr][$c + $dc])) {
      $nbNeighbours += $tiles[$r + $dr][$c + $dc];
    }
  }

  return $nbNeighbours;
}


// That's all, folks!
