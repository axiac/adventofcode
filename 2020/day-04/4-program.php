<?php
/**
 * Day 4: Passport Processing
 *
 * @link https://adventofcode.com/2020/day/4
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
$lines = file($inputfile);
if (end($lines) !== "\n") {
  $lines[] = "\n";
}


$allFields = [
  'byr',     // Birth Year
  'iyr',     // Issue Year
  'eyr',     // Expiration Year
  'hgt',     // Height
  'hcl',     // Hair Color
  'ecl',     // Eye Color
  'pid',     // Passport ID
//  'cid',     // Country ID
];

$valid1 = 0;
$valid2 = 0;
$passport = [];
foreach ($lines as $line) {
  if ($line === "\n") {
    if (isValid1($passport)) {
      $valid1 ++;
      $valid2 += isValid2($passport);
    }
    $passport = [];
  } else {
    foreach (explode(' ', $line) as $piece) {
      list($key, $value) = explode(':', $piece);
      $passport[$key] = rtrim($value);
    }
  }
}
if (isValid1($passport)) {
  $valid1 ++;
  $valid2 += isValid2($passport);
}

echo("part 1: $valid1\n");
echo("part 2: $valid2\n");


function isValid1(array $passport): int {
  global $allFields;

  return
    count(array_filter(
      $allFields,
      fn($field) => array_key_exists($field, $passport)
    )) === count($allFields) ? 1 : 0;
}

function isValid2(array $passport): int {
  if (strlen($passport['byr']) !== 4 || intval($passport['byr']) < 1920 || 2002 < intval($passport['byr'])) { return 0; }
  if (strlen($passport['iyr']) !== 4 || intval($passport['iyr']) < 2010 || 2020 < intval($passport['iyr'])) { return 0; }
  if (strlen($passport['eyr']) !== 4 || intval($passport['eyr']) < 2020 || 2030 < intval($passport['eyr'])) { return 0; }
  $height = intval(substr($passport['hgt'], 0, -2));
  switch (substr($passport['hgt'], -2)) {
    case 'cm': if ($height < 150 || 193 < $height) { return 0; } break;
    case 'in': if ($height < 59 || 76 < $height) { return 0; } break;
    default: return false;
  }
  if (! preg_match('/^#[0-9a-f]{6}$/', $passport['hcl'])) { return 0; }
  if (! in_array($passport['ecl'], ['amb', 'blu', 'brn', 'gry', 'grn', 'hzl', 'oth'])) { return 0; }
  if (! preg_match('/^[0-9]{9}$/', $passport['pid'])) { return 0; }

  return 1;
}


// That's all, folks!
