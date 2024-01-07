<?php
/**
 * Day 13: Transparent Origami
 *
 * @link https://adventofcode.com/2021/day/13
 */

/**
 * Run the program as:
 *   php 13-program.php
 * or
 *   php 13-program.php 13-example
 */

// Input file
$inputfile = dirname($argv[0]).'/13-input';
if ($argc >= 2) {
  $inputfile = $argv[1];
}

// Read the input data
$inputdata = trim(file_get_contents($inputfile));

include '../../lib/Map.php';

// Parse the input data
$inputdata = explode("\n\n", $inputdata);

// Dots
$map = new Map();
foreach (explode("\n", $inputdata[0]) as $line) {
  list($x, $y) = explode(',', $line);
  $map->setAt(intval($y), intval($x), 1);
}

// Folds
$folds = [];
foreach (explode("\n", $inputdata[1]) as $line) {
  if (! preg_match('/fold along (\w)=(\d+)/', $line, $match)) {
    throw new Exception("line '$line' does not match the pattern");
  }

  $folds[] = ['along' => $match[1], 'coord' => intval($match[2])];
}

function foldAlongX(Map $map, int $c): Map {
  $dest = new Map();
  $map->run(function($value, $y, $x) use ($dest, $c) {
    switch (true) {
    case $x < $c:
      $dest->setAt($y, $x, $value);
      break;
    case $c < $x:
      $x = 2 * $c - $x;
      $value = $dest->getAt($y, $x) + $value;
      $dest->setAt($y, $x, $value);
      break;
    }
  });

  return $dest;
}

function foldAlongY(Map $map, int $r): Map {
  $dest = new Map();
  $map->run(function($value, $y, $x) use ($dest, $r) {
    switch (true) {
    case $y < $r:
      $dest->setAt($y, $x, $value);
      break;
    case $r < $y:
      $y = 2 * $r - $y;
      $value = $dest->getAt($y, $x) + $value;
      $dest->setAt($y, $x, $value);
      break;
    }
  });

  return $dest;
}

function fold(Map $map, array $fold): Map {
  if ($fold['along'] === 'x') {
    return foldAlongX($map, $fold['coord']);
  } else {
    return foldAlongY($map, $fold['coord']);
  }
}

function countDots(Map $map): int {
  $count = 0;
  $map->run(function($value) use (&$count){
    if ($value) {
      $count ++;
    }
  });

  return $count;
}


// Part 1
// Do the first fold
$map = fold($map, $folds[0]);
printf("part 1: %d\n", countDots($map));

// Part 2
for ($i = 1; $i < count($folds); $i ++) {
  $map = fold($map, $folds[$i]);
}
printf("part 2:\n");
$map->draw(fn($val)=>print($val ? '#' : ' '));


// That's all, folks!
