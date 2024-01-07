<?php
/**
 * Day 4: Giant Squid
 *
 * @link https://adventofcode.com/2021/day/4
 */

/**
 * Run the program as:
 *   php 4-program.php
 * or
 *   php 4-program.php 4-example
 */

// Input file
$inputfile = dirname($argv[0]).'/4-input';
if ($argc >= 2) {
  $inputfile = $argv[1];
}

// Read the input data
$inputdata = explode("\n\n", trim(file_get_contents($inputfile)));

// Parse the data
// The drawn numbers
$numbers = array_map(fn($nb) => intval($nb), explode(',', array_shift($inputdata)));

// The boards
$boards = array_map(
  fn($data) => array_map(
    fn($line) => array_map(
      fn($item) => intval($item),
      array_values(array_filter(
        explode(' ', $line),
        fn($item) => $item != ''
      )
    )),
    explode("\n", $data)
  ),
  $inputdata
);


// Part 1
// Play the game
do {
  $number = array_shift($numbers);
  if ($number === null) {
    echo("No winner\n");
    exit(1);
  }

  play_number($number, $boards);
  $winners = find_the_winners($boards);
} while (count($winners) === 0);

printf("part 1: %d\n", compute_score($number, $boards[$winners[0]]));


// Part 2
// Remove the winning boards and keep playing until the last remaining board wins
do {
  foreach ($winners as $winner) {
    unset($boards[$winner]);
  }

  // Keep playing
  do {
    $number = array_shift($numbers);
    if ($number === null) {
      break 2;
    }

    play_number($number, $boards);
    $winners = find_the_winners($boards);
  } while (count($winners) === 0);
} while (count($boards) > 1);

printf("part 2: %d\n", compute_score($number, array_shift($boards)));


function find_the_winners(array $boards): array {
  $winners = [];
  foreach ($boards as $b => $board) {
    $is_winner =
      count(array_filter($board, fn($row) => is_empty($row))) > 0 ||
      count(array_filter(range(0, 4), fn($c) => is_empty(array_column($board, $c)))) > 0;
    if ($is_winner) {
      $winners[] = $b;
    }
  }

  return $winners;
}

function is_empty(array $list): bool {
  return count(array_filter($list, fn($item) => $item !== null)) === 0;
}

function play_number(int $number, array &$boards) {
  foreach ($boards as $b => $board) {
    foreach ($board as $r => $row) {
      foreach ($row as $c => $cell) {
        if ($cell === $number) {
          $boards[$b][$r][$c] = null;
        }
      }
    }
  }
}

function compute_score(int $number, array $board): int {
  return $number * array_sum(array_map('array_sum', $board));
}


// That's all, folks!
