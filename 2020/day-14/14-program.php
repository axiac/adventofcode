<?php
/**
 * Day 14: Docking Data
 *
 * @link https://adventofcode.com/2020/day/14
 */

/**
 * Run the program as:
 *   php 14-program.php
 * or
 *   php 14-program.php 14-example-1
 */

// Input file
$inputfile = dirname($argv[0]).'/14-input';
if ($argc >= 2) {
  $inputfile = $argv[1];
}

// Read the input data
$input = explode("\n", trim(file_get_contents($inputfile)));


// The algorithm
function process(array $input, Callable $fnMask, Callable $fnData) {
  $memory = [];
  foreach ($input as $line) {
    list($dest, $value) = array_map('trim', explode('=', $line));
    if ($dest === 'mask') {
      $mask = $fnMask($value);
      continue;
    }

    $matches = [];
    if (! preg_match('/^mem\[(\d+)\]$/', $dest, $matches)) {
      fprintf(STDERR, "The line '$line' does not match the expected format for input.\n");
      exit(1);
    }

    $address = intval($matches[1]);
    foreach ($fnData($address, $value, $mask) as $row) {
        $memory[$row['address']] = $row['data'];
    }
  }

  return $memory;
}

// Part 1
function mask1($mask) {
  return [
    'clear' => bindec(str_replace(['1', 'X'], ['0', '1'], $mask)),
    'set'   => bindec(str_replace('X', '0', $mask)),
  ];
}

function data1($address, $data, array $mask) {
  return [
    [ 'address' => $address, 'data' => ($data & $mask['clear']) | $mask['set'] ],
  ];
}

$memory = process($input, 'mask1', 'data1');
echo('part 1: '.array_sum($memory)."\n");

// Part 2
function mask2($mask) {
  return $mask;
}

function data2($address, $data, $mask) {
  $addrBits = str_pad(decbin($address), strlen($mask), '0', STR_PAD_LEFT);
  $maskBits = [];
  for ($i = 0; $i < strlen($mask); $i ++) {
    switch ($mask[$i]) {
      case '0': $maskBits[$i] = $addrBits[$i]; break;
      case '1': $maskBits[$i] = '1'; break;
      case 'X': $maskBits[$i] = 'X'; break;
      default: fprintf(STDERR, "Unexpected bit '$mask[$i]' in mask.\n"); exit(1);
    }
  }

  $addresses = [''];
  for ($i = 0; $i < count($maskBits); $i ++) {
    $bit = $maskBits[$i];
    if ($bit !== 'X') {
      $addresses = array_map(
        function ($addr) use ($bit) {
          return "{$addr}{$bit}";
        },
        $addresses
      );
    } else {
      $addresses = array_reduce(
        $addresses,
        function (array $acc, $address) {
          $acc[] = "{$address}0";
          $acc[] = "{$address}1";
          return $acc;
        },
        []
      );
    }
  }

  return array_map(
    function ($address) use ($data) {
      return [ 'address' => bindec($address), 'data' => $data ];
    },
    $addresses
  );
}

$memory = process($input, 'mask2', 'data2');
echo('part 2: '.array_sum($memory)."\n");


// That's all, folks!
