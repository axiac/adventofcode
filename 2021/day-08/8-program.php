<?php
/**
 * Day 8: Seven Segment Search
 *
 * @link https://adventofcode.com/2021/day/8
 */

/**
 * Run the program as:
 *   php 8-program.php
 * or
 *   php 8-program.php 8-example-1
 */

// Input file
$inputfile = dirname($argv[0]).'/8-input';
if ($argc >= 2) {
  $inputfile = $argv[1];
}

// Read the input data
$inputdata = array_map(fn($line) => explode(' | ', trim($line)), file($inputfile));

// Part 1
$signals = array_map(
  fn(array $data) => [
    'input'  => array_map(fn($signal) => segments($signal), explode(' ', $data[0])),
    'output' => array_map(fn($digit) => segments($digit), explode(' ', $data[1])),
  ],
  $inputdata
);
function segments(string $digit): array {
  $segments = str_split($digit);
  sort($segments);
  return $segments;
}

$count1478 = array_sum(
  array_map(
    fn(array $signal) => count(array_filter(
      $signal['output'],
      fn($item) => in_array(count($item), [2, 4, 3, 7])
    )),
    $signals
  )
);
printf("part 1: %d\n", $count1478);

$response = array_sum(array_map(
  fn($signal) => implode('', analyze($signal)),
  $signals
));
printf("part 2: %d\n", $response);

//  AAAA
// B    C
// B    C
//  DDDD
// E    F
// E    F
//  GGGG

function analyze(array $signal): array {
  $input = $signal['input'];
  $output = $signal['output'];
  usort($input, fn($a, $b) => count($a) <=> count($b));

  // Find the segments
  $segments = [];
  $digits = [1 => 0, 4 => 2, 7 => 1, 8 => 9];                             // -> 1, 4, 7, 8
  $segments['A'] = array_values(array_diff($input[1], $input[0]))[0];     // -> 'A' (7 vs. 1)
  $BandD = array_values(array_diff($input[2], $input[0]));                // 4 vs. 1
  for ($i = 6; $i <= 8; $i ++) {                                          // 6 and 9 and 0 (in any order)
    $D = array_values(array_diff($BandD, $input[$i]));
    if (count($D) === 1) {
      $segments['D'] = $D[0];                                             // -> 'D'
      $digits[0] = $i;                                                    // -> 0
      break;
    }
  }
  $segments['B'] = array_values(array_diff($BandD, $D))[0];               // -> 'B'
  $ABCDF = array_merge($segments, $input[0]);                             // 1
  for ($i = 3; $i <= 5; $i ++) {                                          // 2 and 3 and 5 (in any order)
    $diff = array_values(array_diff($ABCDF, $input[$i]));
    if (count($diff) == 2) {
      $BandF = $diff;
      $F = in_array($BandF[0], $input[0]) ? $BandF[0] : $BandF[1];
      $segments['F'] = $F;                                                // -> 'F'
      $digits[2] = $i;                                                    // -> 2
    }
    if (in_array($segments['B'], $input[$i])) {
      $digits[5] = $i;                                                    // -> 5
    }
  }
  $digits[3] = 12 - ($digits[2] + $digits[5]);                            // -> 3
  $segments['E'] = array_values(array_diff($input[$digits[2]], $input[$digits[3]]))[0];   // -> 'E'
  for ($i = 6; $i <= 8; $i ++) {                                          // 6 and 9 and 0 (in any order)
    if (! in_array($segments['E'], $input[$i])) {
      $digits[9] = $i;                                                    // -> 9
    }
    $C = array_values(array_diff($input[0], $input[$i]));
    if (count($C) === 1) {
      $segments['C'] = $C[0];                                             // -> 'C'
      $digits[6] = $i;                                                    // -> 6
    }
  }
  $segments['G'] = array_values(array_diff($input[$digits[9]], $input[2], [$segments['A']]))[0];

  // Transform from incorrect segments (lowercase) to correct segments (uppercase)
  $reversed = array_flip($segments);
  return array_map(
    fn(array $digit) => reverse($digit, $reversed),
    $output
  );
}

function reverse(array $digit, array $reversed): int {
  $correct = [
    'ABCEFG'  => 0,
    'CF'      => 1,
    'ACDEG'   => 2,
    'ACDFG'   => 3,
    'BCDF'    => 4,
    'ABDFG'   => 5,
    'ABDEFG'  => 6,
    'ACF'     => 7,
    'ABCDEFG' => 8,
    'ABCDFG'  => 9,
  ];

  $rev = array_map(
    fn($seg) => $reversed[$seg],
    $digit
  );
  sort($rev);

  return $correct[implode('', $rev)];
}


// That's all, folks!
