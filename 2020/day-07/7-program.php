<?php
/**
 * Day 7: Handy Haversacks
 *
 * @link https://adventofcode.com/2020/day/7
 */

/**
 * Run the program as:
 *   php 7-program.php
 * or
 *   php 7-program.php 7-example-1
 */

// Input file
$inputfile = dirname($argv[0]).'/7-input';
if ($argc >= 2) {
  $inputfile = $argv[1];
}


// Read the input data
$lines = explode("\n", trim(file_get_contents($inputfile)));

$allContains = [];
$allIsContainedBy = [];
$allContainsWithAmount = [];
foreach ($lines as $line) {
  // A B bags contain x C D bags.
  // M N bags contain x P Q bags, x S T bags ... .
  // X Y bags contain no other bags.
  if (preg_match( '/^(\w+ \w+) bags contain no other bags\.$/', $line, $matches)) {
    $outerBag = $matches[1];
    $innerBags = [];
  } else {
    if (preg_match(
      '/^(\w+ \w+) bags contain (\d+) (\w+ \w+) bags?(?:, (\d+) (\w+ \w+) bags?)?(?:, (\d+) (\w+ \w+) bags?)?(?:, (\d+) (\w+ \w+) bags?)?\.$/',
      $line,
      $matches
    )) {
      $outerBag = $matches[1];
      $innerBags = array_chunk(array_slice($matches, 2), 2);
    } else {
      echo("not matching: $line\n");
      continue;
    }
  }

  $bagsInside = array_column($innerBags, 1);
  $allContainsWithAmount[$outerBag] = array_combine($bagsInside, array_column($innerBags, 0));
  $allContains[$outerBag] = $bagsInside;
  foreach ($bagsInside as $bag) {
    $allIsContainedBy[$bag][] = $outerBag;
  }
}

$start = 'shiny gold';

$target = [];
$border = array_key_exists($start, $allIsContainedBy) ? $allIsContainedBy[$start] : [];
while (count($border)) {
  $current = $border;
  $border = [];

  foreach ($current as $bag) {
    if (! in_array($bag, $target)) {
      $target[] = $bag;
    }
    if (array_key_exists($bag, $allIsContainedBy)) {
      $border = array_merge($border, $allIsContainedBy[$bag]);
    }
  }
}
echo('part 1: '.count($target)."\n");


function nbBags(string $bag): int {
  global $allContainsWithAmount;
  $count = 0;

  foreach ($allContainsWithAmount[$bag] as $inner => $howMany) {
    $count += intval($howMany) * (1 + nbBags($inner));
  }

  return $count;
}
echo('part 2: '.nbBags($start)."\n");


// That's all, folks
