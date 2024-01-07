<?php
/**
 * Day 17: Trick Shot
 *
 * @link https://adventofcode.com/2021/day/17
 */

/**
 * Run the program as:
 *   php 17-program.php
 * or
 *   php 17-program.php 17-example
 */

// Input file
$inputfile = dirname($argv[0]).'/17-input';
if ($argc >= 2) {
  $inputfile = $argv[1];
}

// Read the input data
$inputdata = trim(file_get_contents($inputfile));

if (! preg_match('/target area: x=(-?\d+)\.\.(-?\d+), y=(-?\d+)\.\.(-?\d+)/', $inputdata, $matches)) {
  throw new Exception("The input data does not match the expected format");
}

$tx0 = intval($matches[1]);
$tx1 = intval($matches[2]);
$ty0 = intval($matches[3]);
$ty1 = intval($matches[4]);

// Part 1
// Computing the max height is easy.
// Let's assume that there is an initial x-velocity that makes the probe's
// x-velocity go down to zero in the target area (on the x axis).
// And lets assume that when the probe is thrown upwards, it needs more time
// to reach the target area then its x-velocity needs to go down to zero.
//
// Both these assumptions can be easily verified but we won't verify them now
// We just assume that the values are carefully crafted to let these conditions
// be true.
//
// On the y axis, the movement of the probe on the positive half of the scale
// is symmetrical. It starts with the velocity vy0 (vy0 > 0) and when its
// y coordinate is back to 0, its velocity is -vy0. The height reached by
// the probe is sum(vy0, vy0-1, ... 1, 0) which is vy0*(vy0+1)/2.
// The bigger vy0, the bigger the maximum reached height.
//
// On the next step, its y coordinate is -vy0-1 and, for the maximum height,
// this must be equal to the deepest coordinate of the target area
// (that is $ty0 above).
//
$maxHeight = $ty0 * ($ty0 + 1) / 2;
printf("part 1: %d\n", $maxHeight);


// Part 2

// To reach the target, the initial x-velocity must be at least
// (sqrt(8 * $tx0 + 1) - 1) / 2, otherwise the x-velocity goes down to zero
// before reaching the target area (on the x axis) and at most $tx1 (reach
// the target area in one step). Greater values makes the probe go beyond the
// target area on the first step and there is no way to return.
// On the y axis, the y-velocity can vary between $ty0 (reach the bottom of
// the target area in one step) and -$ty0 + 1 (go up then down to zero then
// reach the bottom of the target area, as described on part 1)

// Compute the valid interval for v0x
$v0xMin = ceil((sqrt(8 * $tx0 + 1) - 1) / 2);
$v0xMax = $tx1;

// Compute the valid interval for v0y
$v0yMin = $ty0;
$v0yMax = -$ty0 + 1;

/*
$x += $vx;
$y += $vy;
$vx += -($vx <=> 0);
$vy -= 1;
*/

// Do it the hard way: check all trajectories, step by step
$nbUseful = 0;
for ($v0x = $v0xMin; $v0x <= $v0xMax; $v0x ++) {
  for ($v0y = $v0yMin; $v0y <= $v0yMax; $v0y ++) {
    $x = 0; $y = 0; $vx = $v0x; $vy = $v0y;
    // Run the minimum amount of steps until either the probe hits the
    // target area or it passes beyond it
    do {
      $x += $vx; $y += $vy; $vx += -($vx <=> 0); $vy -= 1;
      if ($tx0 <= $x && $x <= $tx1 && $ty0 <= $y && $y <= $ty1) {
        // Reached the target area; there is no need to go further
        $nbUseful ++;
        break;
      }
    } while ($x <= $tx1 && $ty0 <= $y);      // did not pass beyond the far boundary
                                             // of the target area (there is no way to return)
  }
}
printf("part 2: %d\n", $nbUseful);


// That's all, folks!
