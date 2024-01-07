<?php
/**
 * Day 12: Rain Risk
 *
 * @link https://adventofcode.com/2020/day/12
 */

/**
 * Run the program as:
 *   php 12-program.php
 * or
 *   php 12-program.php 12-example
 */

// Input file
$inputfile = dirname($argv[0]).'/12-input';
if ($argc >= 2) {
  $inputfile = $argv[1];
}

// Read the input data
$input = explode("\n", trim(file_get_contents($inputfile)));


$directions = ['north', 'east', 'south', 'west'];
$rotations = [
  [[ 1,  0], [ 0,  1]],    //   0
  [[ 0, -1], [+1,  0]],    //  90
  [[-1,  0], [ 0, -1]],    // 180
  [[ 0, +1], [-1,  0]],    // 270
];
$heading = 1;         // east

// Ship position: positive coordinates go north ($shipY1) and east ($shipX1)
// Step 1
$shipX1 = 0;
$shipY1 = 0;
// Step 2
$shipX2 = 0;
$shipY2 = 0;

// Waypoint position (relative to the ship)
$wpX = 10;
$wpY = 1;


// Part 1
foreach ($input as $line) {
  $command = $line[0];
  $param = intval(substr($line, 1));

  switch ($command) {
    case 'N':    // move north by the given value.
      $shipY1 += $param;
      $wpY += $param;
      break;
    case 'S':    // move south by the given value.
      $shipY1 -= $param;
      $wpY -= $param;
      break;
    case 'E':    // move east by the given value.
      $shipX1 += $param;
      $wpX += $param;
      break;
    case 'W':    // move west by the given value.
      $shipX1 -= $param;
      $wpX -= $param;
      break;
    case 'L':    // turn left the given number of degrees.
      if ($param % 90) { fprintf(STDERR, "Unexpected number of degrees ($param) to turn (line: '$line').\n"); exit(1); }
      $rotate = $param / 90;
      $heading = ($heading + (4 - $rotate)) % 4;
      $trans = $rotations[$rotate];
      $tempX = $wpX * $trans[0][0] + $wpY * $trans[0][1];
      $tempY = $wpX * $trans[1][0] + $wpY * $trans[1][1];
      $wpX = $tempX;
      $wpY = $tempY;
      break;
    case 'R':    // turn right the given number of degrees.
      if ($param % 90) { fprintf(STDERR, "Unexpected number of degrees ($param) to turn (line: '$line').\n"); exit(1); }
      $rotate = $param / 90;
      $heading = ($heading + $rotate) % 4;
      $trans = $rotations[4 - $rotate];
      $tempX = $wpX * $trans[0][0] + $wpY * $trans[0][1];
      $tempY = $wpX * $trans[1][0] + $wpY * $trans[1][1];
      $wpX = $tempX;
      $wpY = $tempY;
      break;
    case 'F':    // move forward by the given value in the direction the ship is currently facing.
      if ($heading % 2 == 0) {
        $shipY1 += (1 - $heading) * $param;
      } else {
        $shipX1 += (2 - $heading) * $param;
      }

      $shipX2 += $param * $wpX;
      $shipY2 += $param * $wpY;
      break;
    default:
      fprintf(STDERR, "Unknown command '$command' (line: '$line').\n");
      exit(1);
  }
}

echo('part 1: '.(abs($shipX1)+abs($shipY1))."\n");
echo('part 2: '.(abs($shipX2)+abs($shipY2))."\n");


// That's all, folks!
