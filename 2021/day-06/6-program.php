<?php
/**
 * Day 6: Lanternfish
 *
 * @link https://adventofcode.com/2021/day/6
 */

/**
 * Run the program as:
 *   php 6-program.php
 * or
 *   php 6-program.php 6-example
 */

// Input file
$inputfile = dirname($argv[0]).'/6-input';
if ($argc >= 2) {
  $inputfile = $argv[1];
}

// Read the input data
$inputdata = array_map(fn($value) => intval($value), explode(',', file_get_contents($inputfile)));

// Group/count and sort the fish by their timers
$timers = array_count_values($inputdata);
krsort($timers);

// Part 1
printf("part 1: %d\n", array_sum(simulate($timers, 80)));

// Part 2
printf("part 2: %d\n", array_sum(simulate($timers, 256)));


// Simulate the number of days days
function simulate(array $timers, int $n): array {
  for ($i = 0; $i < $n; $i ++) {
    $next = [];
    foreach ($timers as $timer => $count) {
      if ($timer > 0) {
        $timer --;
        $next[$timer] = $count;
      } else {
        $next[6] = ($next[6] ?? 0) + $count;
        $next[8] = ($next[8] ?? 0) + $count;
      }
    }

    $timers = $next;
    krsort($timers);
  }

  return $timers;
}


// That's all, folks!
