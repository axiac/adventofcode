<?php
/**
 * Day 16: Ticket Translation
 *
 * @link https://adventofcode.com/2020/day/16
 */

/**
 * Run the program as:
 *   php 16-program.php
 * or
 *   php 16-program.php 16-example-1
 */

// Input file
$inputfile = dirname($argv[0]).'/16-input';
if ($argc >= 2) {
  $inputfile = $argv[1];
}

// Read the input data
$input = explode("\n", trim(file_get_contents($inputfile)));


// Parse the input data
$fields = [];
$myTicket = [];
$nearbyTickets = [];

// Fields and their valid ranges
$rowNb = 0;
while (strlen($input[$rowNb])) {
  $line = $input[$rowNb];
  $m = [];
  if (! preg_match('/^(.*?): (\d+)-(\d+) or (\d+)-(\d+)$/', $line, $m)) {
    fprintf(STDERR, "Cannot parse line '$line'.\n");
    exit(1);
  }

  $fields[$m[1]] = ['first' => ['min' => $m[2], 'max' => $m[3]], 'second' => ['min' => $m[4], 'max' => $m[5]]];
  $rowNb ++;
}
$rowNb ++;          // skip the empty line

// my ticket
if ($input[$rowNb] !== 'your ticket:') {
  fprintf(STDERR, "Expected the 'your ticket:' line. Found '$input[$rowNb]' at line ".(1+$rowNb).".\n");
  exit(1);
}
$rowNb ++;          // skip the 'your ticket:' line
$myTicket = array_map('intval', explode(',', $input[$rowNb]));
$rowNb ++;          // the ticket values line
$rowNb ++;          // skip the empty line

// nearby tickets
if ($input[$rowNb] !== 'nearby tickets:') {
  fprintf(STDERR, "Expected the 'nearby tickets:' line. Found '$input[$rowNb]' at line ".(1+$rowNb).".\n");
  exit(1);
}
$rowNb ++;          // skip the 'nearby tickets:' line
for (; $rowNb < count($input); $rowNb ++) {
  $nearbyTickets[] = array_map('intval', explode(',', $input[$rowNb]));
}



// Validate my ticket
$validTickets = [];
$invalidValues = getInvalidValues($myTicket);
if (count($invalidValues)) {
  fprintf(STDERR, "My ticket has the following invalid values: ".implode(', ', $invalidValues)."\n");
  exit(1);
}
$validTickets[] = $myTicket;


// Part 1
$total1 = 0;
foreach ($nearbyTickets as $ticket) {
  $invalidValues = getInvalidValues($ticket);
  $total1 += array_sum($invalidValues);

  if (count($invalidValues) === 0) {
    $validTickets[] = $ticket;
  }
}
echo("part 1: ${total1}\n");


// Part 2
$fieldsFound = [];
$fieldsNotFound = [];
for ($index = 0; $index < count($myTicket); $index ++) {
  $pos = 1 + $index;
  $potential = array_values(array_diff(array_keys($fields), array_keys($fieldsFound)));

  foreach ($validTickets as $k => $ticket) {
    $value = $ticket[$index];
    $valid = getFieldsForValue($value);
    $potential = array_values(array_intersect($potential, $valid));
  }

  switch (count($potential)) {
    case 0:
      fprintf(STDERR, "[Phase 1] Cannot find the field for position $pos\n");
      exit(1);
    case 1:
      $field = $potential[0];
      if (array_key_exists($field, $fieldsFound)) {
        fprintf(STDERR, "[Phase 2] The field '$field' is the only valid field for position $pos but it is also the only valid field for position ".(1+$fieldsFound[$field])."\n");
        exit(1);
      }
      $fieldsFound[$field] = $index;
      break;
    default:
      $fieldsNotFound[$index] = $potential;
      break;
  }
}


// Analyse the fields not found
do {
  $stillNotFound = [];

  foreach ($fieldsNotFound as $index => $potentialFields) {
    $pos = 1 + $index;
    $potential = array_values(array_diff($potentialFields, array_keys($fieldsFound)));
    switch (count($potential)) {
      case 0:
        fprintf(STDERR, "[Phase 2] Cannot find the field for position $pos\n");
        exit(1);
      case 1:
        $field = $potential[0];
        if (array_key_exists($field, $fieldsFound)) {
          fprintf(STDERR, "[Phase 2] The field '$field' is the only valid field for position $pos but it is also the only valid field for position ".(1+$validFields[$field])."\n");
          exit(1);
        }
        $fieldsFound[$field] = $index;
        break;
      default:
        $stillNotFound[$index] = $potential;
        break;
    }
  }
  $foundThisRound = count($fieldsNotFound) - count($stillNotFound);

  $fieldsNotFound = $stillNotFound;
} while (count($fieldsNotFound) > 0 && $foundThisRound > 0);

if (count($fieldsNotFound) > 0) {
  fprintf(STDERR, "Infinite loop detected. The following fields can still not be found: ".implode(', ', array_keys($fieldsNotFound)));
  exit(1);
}

// Identify the positions of the fields whose name starts with 'departure'
$product = array_product(
  array_map(
    function($field) use ($fieldsFound, $myTicket) { return $myTicket[$fieldsFound[$field]]; },
    array_filter(
      array_keys($fieldsFound),
      function($field) { return substr($field, 0, strlen('departure')) === 'departure'; }
    )
  )
);
echo("part 2: ${product}\n");



function getInvalidValues(array $ticket) {
  return array_filter(
    $ticket,
    function($value) { return ! isValidValue($value); }
  );
}

function isValidValue($value) {
  global $fields;

  foreach ($fields as $f) {
    if ($f['first']['min'] <= $value && $value <= $f['first']['max'] ||
        $f['second']['min'] <= $value && $value <= $f['second']['max']) {
      return true;
    }
  }

  return false;
}

function getFieldsForValue($value) {
  global $fields;

  $potential = [];
  foreach ($fields as $name => $f) {
    if ($f['first']['min'] <= $value && $value <= $f['first']['max'] ||
        $f['second']['min'] <= $value && $value <= $f['second']['max']) {
      $potential[] = $name;
    }
  }

  return $potential;
}


// That's all, folks!
