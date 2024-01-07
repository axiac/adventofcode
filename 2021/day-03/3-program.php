<?php
/**
 * Day 3: Binary Diagnostic
 *
 * @link https://adventofcode.com/2021/day/3
 */

/**
 * Run the program as:
 *   php 3-program.php
 * or
 *   php 3-program.php 3-example
 */

// Input file
$inputfile = dirname($argv[0]).'/3-input';
if ($argc >= 2) {
  $inputfile = $argv[1];
}

// Read the input data
$inputdata = array_map(fn($line) => array_map('intval', str_split(trim($line))), file($inputfile));

// Part 1
$sums = array_map(fn($col) => array_sum(array_column($inputdata, $col)), range(0, count($inputdata[0]) - 1));
$half = count($inputdata) / 2;
$mostFreq = array_map(fn($sum) => $sum > $half ? 1 : 0, $sums);
$leastFreq = array_map(fn($digit) => 1 - $digit, $mostFreq);

$gamma = bindec(implode($mostFreq));
$epsilon = bindec(implode($leastFreq));

printf("part 1: %d\n", $gamma * $epsilon);


// Part 2
$oxygenList = $inputdata;
$co2List = $inputdata;
for ($col = 0; $col < count($inputdata[0]); $col ++) {
  if (count($oxygenList) > 1) {
    $oxygenDigit = freq($oxygenList, $col)[0];
    $oxygenList = array_filter($oxygenList, fn($row) => $row[$col] === $oxygenDigit);
  }
  if (count($co2List) > 1) {
    $co2Digit = freq($co2List, $col)[1];
    $co2List = array_filter($co2List, fn($row) => $row[$col] === $co2Digit);
  }
}

$oxygen = bindec(implode('', array_values($oxygenList)[0]));
$co2 = bindec(implode('', array_values($co2List)[0]));

printf("part 2: %d\n", $oxygen * $co2);


// Get the most frequent and the least frequent digit at the specified position (column)
function freq(array $a, int $col): array {
  $half = count($a) / 2;
  $nb = array_sum(array_column($a, $col));
  if ($nb >= $half) {
     return [1, 0];
  } else {
    return [0, 1];
  }
}


// That's all, folks!
