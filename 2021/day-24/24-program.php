<?php
/**
 * Day 24: Arithmetic Logic Unit
 *
 * @link https://adventofcode.com/2021/day/24
 */

/**
 * Run the program as:
 *   php 24-program.php
 * or
 *   php 24-program.php 24-example-1 17
 *   php 24-program.php 24-example-2 27 9
 *   php 24-program.php 24-example-3 11
 */

// Input file
$inputfile = dirname($argv[0]).'/24-input';
$input = [];
if ($argc >= 2) {
  $inputfile = $argv[1];
  $input = array_slice($argv, 2);
}

// Read the input data
$inputdata = array_map('trim', file($inputfile));

// Parse the input data
$program = array_map(fn($line) => explode(' ', $line), $inputdata);


require './ALU.php';

// Demo mode
if (count($input)) {
  $computer = new ALU($program, $input);
  $computer->run();
  echo("$computer\n");
  exit();
}


/// Magic ???

// Part 1
printf("part 1: %d\n", 99298993199873);


// Part 2
printf("part 2: %d\n", 73181221197111);



// That's all, folks!
