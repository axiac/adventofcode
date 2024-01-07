<?php
/**
 * Day 22: Crab Combat
 *
 * @link https://adventofcode.com/2020/day/22
 */

/**
 * Run the program as:
 *   php 22-program.php
 * or
 *   php 22-program.php 22-example
 */

// Input file
$inputfile = dirname($argv[0]).'/22-input';
if ($argc >= 2) {
  $inputfile = $argv[1];
}

// Read the input data
$input = explode("\n", trim(file_get_contents($inputfile)));

// The two decks of cards; front items are on top of the card
$deck1 = [];
$deck2 = [];

$index = 0;
if ($input[$index] !== 'Player 1:') {
  fputs(STDERR, 'err');
  exit(1);
}
// Skip the 'Player 1:' line
$index ++;

// Read the deck
for (; $index < count($input) && $input[$index] !== ''; $index ++) {
  $deck1[] = intval($input[$index]);
}
// Skip the empty line
$index ++;

// Second player
if ($input[$index] !== 'Player 2:') {
  fputs(STDERR, 'err');
  exit(1);
}
// Skip the 'Player 2:' line
$index ++;

// Read the deck
for (; $index < count($input) && $input[$index] !== ''; $index ++) {
  $deck2[] = intval($input[$index]);
}

// Save the original decks for part 2
$original1 = $deck1;
$original2 = $deck2;


// Part 1
while (count($deck1) && count($deck2)) {
  $card1 = array_shift($deck1);
  $card2 = array_shift($deck2);
  if ($card1 === $card2) {
    fputs(STDERR, 'tie');
    exit(1);
  }

  if ($card1 > $card2) {
    $deck1[] = $card1;
    $deck1[] = $card2;
  } else {
    $deck2[] = $card2;
    $deck2[] = $card1;
  }
}

$winnerDeck = count($deck1) ? $deck1 : $deck2;
printf("part 1: %d\n", calculateScore($winnerDeck));


// Part 2
$deck1 = $original1;
$deck2 = $original2;


$result = recursiveCombat($deck1, $deck2);
if ($result['winner'] === 1) {
  $winnerDeck = $result['deck1'];
} else {
  $winnerDeck = $result['deck2'];
}
echo('part 2: '.calculateScore($winnerDeck)."\n");


$level = 0;
function recursiveCombat(array $deck1, array $deck2) {
global $level;
$level ++;
//printf(">> recursiveCombat[%d]([%s], [%s])\n", $level, implode(', ', $deck1), implode(', ', $deck2));

  $history = [];
  while (count($deck1) > 0 && count($deck2) > 0) {
    // Before either player deals a card, if there was a previous round in this game that had exactly the same cards
    // in the same order in the same players' decks, the game instantly ends in a win for player 1.
    if (in_array([$deck1, $deck2], $history)) {
//printf("<<< recursiveCombat[%d](): %d --- history repeating\n", $level, 1);
$level --;
      return ['winner' => 1, 'deck1' => $deck1, 'deck2' => $deck2];
    }

    $history[] = [$deck1, $deck2];

    // The players begin the round by each drawing the top card of their deck as normal.
    $card1 = array_shift($deck1);
    $card2 = array_shift($deck2);

    // If both players have at least as many cards remaining in their deck as the value of the card they just drew,
    // the winner of the round is determined by playing a new game of Recursive Combat
    if ($card1 <= count($deck1) && $card2 <= count($deck2)) {
      $winner = recursiveCombat(array_slice($deck1, 0, $card1), array_slice($deck2, 0, $card2))['winner'];
    } else {
      // Otherwise, the winner of the round is the player with the higher-value card.
      if ($card1 === $card2) { fputs(STDERR, 'tie'); exit(1); }
      if ($card1 > $card2) {
        $winner = 1;
      } else {
        $winner = 2;
      }
    }

    // the winner of the round (even if they won the round by winning a sub-game) takes the two cards dealt at the beginning
    // of the round and places them on the bottom of their own deck (again so that the winner's card is above the other card)
    if ($winner === 1) {
      $deck1[] = $card1;
      $deck1[] = $card2;
    } else {
      $deck2[] = $card2;
      $deck2[] = $card1;
    }
  }

  if (count($deck1) > 0) {
    $result = ['winner' => 1, 'deck1' => $deck1, 'deck2' => $deck2 ];
  } else {
    $result = ['winner' => 2, 'deck1' => $deck1, 'deck2' => $deck2 ];
  }

//printf("<<< recursiveCombat[%d](): %d\n", $level, $result['winner']);
$level --;
  return $result;
}

function calculateScore(array $deck) {
  return array_sum(
    array_map(
      function($card, $factor) { return $card * $factor; },
      $deck,
      range(count($deck), 1, -1)
    )
  );
}


// That's all, folks!
