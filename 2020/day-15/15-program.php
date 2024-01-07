<?php
/**
 * Day 15: Rambunctious Recitation
 *
 * @link https://adventofcode.com/2020/day/15
 */

/**
 * Run the program as:
 *   php -d memory_limit=256M 15-program.php
 * or
 *   php -d memory_limit=256M 15-program.php 15-example-1
 */

// Input file
$inputfile = dirname($argv[0]).'/15-input';
if ($argc >= 2) {
  $inputfile = $argv[1];
}

// Read the input data
$input = explode(',', trim(file_get_contents($inputfile)));


// The starting numbers
$previous = [];
for ($index = 0; $index < count($input) - 1; $index ++) {
  $turn = $index + 1;
  $previous[$input[$index]] = $turn;
}

$last = end($input);
$turn ++;

$count = 30_000_000;
$dot   =  1_000_000;

for ($index = count($input); $index < $count; $index ++) {
  if ($index === 2020) {
    echo("part 1: ${last}\n");
    # Display a nice progress bar on stderr, if it is not redirected to a file
    progress(sprintf("%s\r", str_repeat('.', $count / $dot)));
  }
  if (isset($previous[$last])) {
    $current = $index - $previous[$last];
  } else {
    $current = 0;
  }
  $previous[$last] = $index;
  $last = $current;

  // Display a dot on `stderr` for each million of processed rows
  if ($index % $dot === 0) progress('#');
}
progress("#\n");

function progress($message) {
  // When STDERR is seekable it is redirected to a file
  // We don't want to display the progress bar in this case
  if (! stream_get_meta_data(STDERR)['seekable']) {
    fputs(STDERR, $message);
  }
}

echo("part 2: ${last}\n");


// That's all, folks!
