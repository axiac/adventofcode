<?php
/**
 * Day 19: Monster Messages
 *
 * @link https://adventofcode.com/2020/day/19
 */

/**
 * Run the program as:
 *   php 19-program.php
 * or
 *   php 19-program.php 19-example-1
 */

// Input file
$inputfile = dirname($argv[0]).'/19-input';
if ($argc >= 2) {
  $inputfile = $argv[1];
}

// Read the input data
$input = explode("\n", trim(file_get_contents($inputfile)));

// Parse the input
$rules = [];
$words = [];

// Rules
for ($index = 0; $index < count($input); $index ++) {
  $line = $input[$index];
  if ($line === '') {
    break;
  }

  list($ruleNb, $ruleBody) = parseRule($line);
  $rules[$ruleNb] = $ruleBody;
}

function parseRule($line) {
  list($ruleNb, $rest) = explode(':', $line);

  $m = [];
  if (preg_match('/\s*"(\w)"/', $rest, $m)) {
    return [$ruleNb, ['terminal' => $m[1]]];
  }

  $alternatives = array_map(
    function ($alt) { return ['sequence' => explode(' ', trim($alt))]; },
    explode('|', $rest)
  );

  return [$ruleNb, ['alternatives' => $alternatives]];
}


// Words to analyze
$words = array_slice($input, $index + 1);


// Part 1
$regex = '/^'.generateRegex(0, $rules).'$/';

$match0 = count(array_filter(
  $words,
  function ($word) use ($regex) { return preg_match($regex, $word); }
));

echo("part 1: $match0\n");


// Part 2

//  '8: 42 | 42 8',
$rules[8] = ['repeat' => [42]];
//  '11: 42 31 | 42 11 31',
list($ruleNb, $ruleBody) = parseRule('11: 42 31 | 42 42 31 31 | 42 42 42 31 31 31 | 42 42 42 42 31 31 31 31 | 42 42 42 42 42 31 31 31 31 31');
$rules[11] = $ruleBody;
$regex = '/^'.generateRegex(0, $rules).'$/';

$match0 = count(array_filter(
  $words,
  function ($word) use ($regex) { return preg_match($regex, $word); }
));

echo("part 2: $match0\n");



function generateRegex($ruleNb, array $rules) {
  $rule = $rules[$ruleNb];
  if (array_key_exists('terminal', $rule)) {
    return $rule['terminal'];
  }

  if (array_key_exists('sequence', $rule)) {
    // return implode('', array_map(
    //   function ($ruleNb) use ($rules) { return generateRegex($ruleNb, $rules); },
    //   $rule['sequence']
    // ));
    return sequence($rule['sequence'], $rules);
  }

  if (array_key_exists('alternatives', $rule)) {
    $alternatives = implode('|', array_map(
      function (array $alt) use ($rules) {
        // return implode('', array_map(
        //   function ($ruleNb) use ($rules) { return generateRegex($ruleNb, $rules); },
        //   $alt['sequence']
        // ));
        return sequence($alt['sequence'], $rules);
      },
      $rule['alternatives']
    ));

    if (count($rule['alternatives']) > 1) {
      $alternatives = "($alternatives)";
    }

    return $alternatives;
  }

  if (array_key_exists('repeat', $rule)) {
    $base = sequence($rule['repeat'], $rules);
    if (count($rule['repeat']) > 1) {
      $base = "($base)";
    }
    return "${base}+";
  }
}


function sequence(array $items, array $rules) {
    return implode('', array_map(
      function ($ruleNb) use ($rules) { return generateRegex($ruleNb, $rules); },
      $items
    ));
}


// That's all, folks!
