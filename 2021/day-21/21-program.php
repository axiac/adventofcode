<?php
/**
 * Day 21: Dirac Dice
 *
 * @link https://adventofcode.com/2021/day/21
 */

/**
 * Run the program as:
 *   php 21-program.php
 * or
 *   php 21-program.php 4 8
 */

// Input data
$start1 = 1;
$start2 = 10;
if ($argc >= 3) {
  $start1 = intval($argv[1]);
  $start2 = intval($argv[2]);
}


// Part 1
$score1 = 0;
$score2 = 0;
$pos1 = $start1 - 1;
$pos2 = $start2 - 1;
$dice = new DeterministicDice();
while (true) {
  // First player
  $forward = $dice->throw() + $dice->throw() + $dice->throw();
  $pos1 = ($pos1 + $forward) % 10;
  $score1 += $pos1 + 1;
  if ($score1 >= 1000) {
    $score = $score2 * $dice->getNbThrows();
    break;
  }

  // Second player
  $forward = $dice->throw() + $dice->throw() + $dice->throw();
  $pos2 = ($pos2 + $forward) % 10;
  $score2 += $pos2 + 1;
  if ($score2 >= 1000) {
    $score = $score1 * $dice->getNbThrows();
    break;
  }
}


printf("part 1: %d\n", $score);

// Part 2
$t2u = [
  // points from three throws => the number of universes where this happens
  3 => 1,
  4 => 3,
  5 => 6,
  6 => 7,
  7 => 6,
  8 => 3,
  9 => 1,
];

$pos1 = $start1 - 1;
$pos2 = $start2 - 1;
// The possible score after some step => the number of universes where it can be reached
// Before any step, the pawns are on the initial positions with score `0` in exactly one universe
$paths = [
  k($pos1, 0, $pos2, 0) => ['pos1' => $pos1, 'score1' => 0, 'pos2' => $pos2, 'score2' => 0, 'u' => 1],
];


// Simulate the game
$wins = [1 => 0, 2 => 0];
while (count($paths) > 0) {
  // Player 1
  $next = [];
  foreach ($paths as $p) {
    // Simulate all throw combinations for player 1
    foreach ($t2u as $s => $u) {
      $pos1 = ($p['pos1'] + $s) % 10;
      $score1 = $p['score1'] + ($pos1 + 1);
      $universes = $p['u'] * $u;
      if ($score1 >= 21) {
        // Player one wins; count the universes and do not keep track of this (path+combination) any more
        $wins[1] += $universes;
        continue;
      }

      // Keep track of the new score, position and the number of universes
      store($next, $pos1, $score1, $p['pos2'], $p['score2'], $universes);
    }
  }
  $paths = $next;

  // Player 2
  $next = [];
  foreach ($paths as $p) {
    // Simulate all throw combinations for player 2
    foreach ($t2u as $s => $u) {
      $pos2 = ($p['pos2'] + $s) % 10;
      $score2 = $p['score2'] + ($pos2 + 1);
      $universes = $p['u'] * $u;
      if ($score2 >= 21) {
        // Player one wins; count the universes and do not keep track of this (path+combination) any more
        $wins[2] += $universes;
        continue;
      }

      // Keep track of the new score, position and the number of universes
      store($next, $p['pos1'], $p['score1'], $pos2, $score2, $universes);
    }
  }
  $paths = $next;
}

printf("part 2: %d\n", max($wins));



//
//
//
function k(int $pos1, int $score1, int $pos2, int $score2): string {
  return sprintf('%02d:%1d::%02d:%1d', $score1, $pos1, $score2, $pos2);
}

function store(array &$next, int $pos1, int $score1, int $pos2, int $score2, int $universes) {
  $key = k($pos1, $score1, $pos2, $score2);
  $next[$key] = [
    'pos1' => $pos1,
    'score1' => $score1,
    'pos2' => $pos2,
    'score2' => $score2,
    'u' => ($next[$key]['u'] ?? 0) + $universes,
  ];
}

//
//
//
class DeterministicDice {
  private int $value;
  private int $nbThrows;

  public function __construct() {
    $this->value = 0;
    $this->nbThrows = 0;
  }

  public function getNbThrows(): int {
    return $this->nbThrows;
  }

  public function throw(): int {
    $value = $this->value + 1;
    $this->value = ($this->value + 1) % 100;
    $this->nbThrows ++;

    return $value;
  }
}



// That's all, folks!
