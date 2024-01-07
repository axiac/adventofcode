<?php
/**
 * Day 17: Conway Cubes
 *
 * @link https://adventofcode.com/2020/day/17
 */

/**
 * Run the program as:
 *   php 17-program.php
 * or
 *   php 17-program.php 17-example-1
 */

// Input file
$inputfile = dirname($argv[0]).'/17-input';
if ($argc >= 2) {
  $inputfile = $argv[1];
}

// Read the input data
$input = explode("\n", trim(file_get_contents($inputfile)));



$matrix = [array_map('str_split', $input)];

$width = count($matrix[0][0]);
$depth = count($matrix[0]);
$height = count($matrix);

$matrix4d = [$matrix];
$fourth = count($matrix4d);


for ($cycle = 1; $cycle <= 6; $cycle ++) {
  // 3D
  $new = [];
  $totalActive = 0;

  for ($h = -$cycle; $h < $height + $cycle; $h ++) {
    for ($d = -$cycle; $d < $depth + $cycle; $d ++) {
      for ($w = -$cycle; $w < $width + $cycle; $w ++) {
        $active = countActive($matrix, $h, $d, $w);

        if (isActive($matrix, $h, $d, $w)) {
          if (in_array($active, [2, 3])) {
            $new = setActive($new, $h, $d, $w);
            $totalActive ++;
          }
        } else {
          if ($active === 3) {
            $new = setActive($new, $h, $d, $w);
            $totalActive ++;
          }
        }
      }
    }
  }
  $matrix = $new;

  // 4D
  $new4d = [];
  $totalActive4d = 0;
  for ($q = -$cycle; $q < $fourth + $cycle; $q ++) {
    for ($h = -$cycle; $h < $height + $cycle; $h ++) {
      for ($d = -$cycle; $d < $depth + $cycle; $d ++) {
        for ($w = -$cycle; $w < $width + $cycle; $w ++) {
          $active4d = countActive4d($matrix4d, $q, $h, $d, $w);

          if (isActive4d($matrix4d, $q, $h, $d, $w)) {
            if (in_array($active4d, [2, 3])) {
              $new4d = setActive4d($new4d, $q, $h, $d, $w);
              $totalActive4d ++;
            }
          } else {
            if ($active4d === 3) {
              $new4d = setActive4d($new4d, $q, $h, $d, $w);
              $totalActive4d ++;
            }
          }
        }
      }
    }
  }
  $matrix4d = $new4d;
}


echo("part 1: $totalActive\n");
echo("part 2: $totalActive4d\n");

// Part 1: 3D
function countActive(array $matrix, $h, $d, $w) {
  $active = isActive($matrix, $h, $d, $w) ? -1 : 0;

  for ($z = $h - 1; $z <= $h + 1; $z ++) {
    for ($y = $d - 1; $y <= $d + 1; $y ++) {
      for ($x = $w - 1; $x <= $w + 1; $x ++) {
        $active += isActive($matrix, $z, $y, $x) ? +1 : 0;
      }
    }
  }

//  echo("countActive($h, $d, $w) -> $active\n");
  return $active;
}

function isActive(array $matrix, $h, $d, $w) {
  return isset($matrix[$h][$d][$w]) && $matrix[$h][$d][$w] === '#';
}

function setActive(array $matrix, $h, $d, $w) {
//  echo("setActive($h, $d, $w)\n");
  $matrix[$h][$d][$w] = '#';
  return $matrix;
}

function setInactive(array $matrix, $h, $d, $w) {
//  echo("setInactive($h, $d, $w)\n");
  $matrix[$h][$d][$w] = '.';
  return $matrix;
}


// Part 2: 3D
function countActive4d(array $matrix, $q, $h, $d, $w) {
  $active = isActive4d($matrix, $q, $h, $d, $w) ? -1 : 0;

  for ($t = $q - 1; $t <= $q + 1; $t ++) {
    for ($z = $h - 1; $z <= $h + 1; $z ++) {
      for ($y = $d - 1; $y <= $d + 1; $y ++) {
        for ($x = $w - 1; $x <= $w + 1; $x ++) {
          $active += isActive4d($matrix, $t, $z, $y, $x) ? +1 : 0;
        }
      }
    }
  }

//  echo("countActive4d($h, $q, $d, $w) -> $active\n");
  return $active;
}

function isActive4d(array $matrix, $q, $h, $d, $w) {
  return isset($matrix[$q][$h][$d][$w]) && $matrix[$q][$h][$d][$w] === '#';
}

function setActive4d(array $matrix, $q, $h, $d, $w) {
//  echo("setActive4d($q, $h, $d, $w)\n");
  $matrix[$q][$h][$d][$w] = '#';
  return $matrix;
}

function setInactive4d(array $matrix, $q, $h, $d, $w) {
//  echo("setInactive4d($q, $h, $d, $w)\n");
  $matrix[$q][$h][$d][$w] = '.';
  return $matrix;
}


function printCubes(array $matrix, $height, $depth, $width, $cycle) {
  for ($h = -$cycle; $h < $height + $cycle; $h ++) {
    echo("z=$h\n");
    for ($d = -$cycle; $d < $depth + $cycle; $d ++) {
      for ($w = -$cycle; $w < $width + $cycle; $w ++) {
        echo(isActive($matrix, $h, $d, $w) ? '#': '.');
      }
      echo("\n");
    }
    echo("\n");
  }
}


// That's all, folks!
