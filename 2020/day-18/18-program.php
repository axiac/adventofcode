<?php
/**
 * Day 18: Operation Order
 *
 * @link https://adventofcode.com/2020/day/18
 */

/**
 * Run the program as:
 *   php 18-program.php
 * or
 *   php 18-program.php 18-example-1
 */

// Input file
$inputfile = dirname($argv[0]).'/18-input';
if ($argc >= 2) {
  $inputfile = $argv[1];
}

// Read the input data
$input = explode("\n", trim(file_get_contents($inputfile)));

$part1 = array_sum(
  array_map(
    function($line) {
      $result = evaluate1($line);
      if ($result['length'] !== 0) {
        fprintf(STDERR, "Error evaluating line '$line' ({$result['length']}).\n");
        exit(1);
      }
      return $result['value'];
    },
    $input
  )
);
echo("part 1: $part1\n");


$part2 = array_sum(
  array_map(
    function($line) { return evaluate2(str_replace(' ', '', $line)); },
    $input
  )
);
echo("part 2: $part2\n");

function evaluate1($expression) {
  $operator = '+';
  $value = 0;

  for ($index = 0; $index < strlen($expression); $index ++) {
    $char = $expression[$index];
    switch ($char) {
      case '+':
        $operator = '+';
        break;
      case '*':
        $operator = '*';
        break;
      case '(':
        $result = evaluate1(substr($expression, $index + 1));
        if ($operator === '+') {
          $value = $value + $result['value'];
        } else {
          $value = $value * $result['value'];
        }
        $index += $result['length'];
        break;
      case ')':
        return ['value' => $value, 'length' => $index + 1];
      case ' ':
        break;
      default:
        if ($char < '0' || '9' < $char) {
          fputs(STDERR, "Unknown character '$char' encountered at offset $index.");
          exit(1);
        }
        $number = 0;
        sscanf(substr($expression, $index), "%d", $number);
        if ($operator === '+') {
          $value = $value + $number;
        } else {
          $value = $value * $number;
        }
        $index += strlen($number) - 1;
    }
  }

  return ['value' => $value, 'length' => 0];
}

function evaluate2($expression) {
  $pos = findOperator('*', $expression);
  if ($pos >= 0) {
    return evaluate2(substr($expression, 0, $pos)) * evaluate2(substr($expression, $pos + 1));
  }

  $pos = findOperator('+', $expression);
  if ($pos >= 0) {
    return evaluate2(substr($expression, 0, $pos)) + evaluate2(substr($expression, $pos + 1));
  }

  if ($expression[0] === '(') {
    if (substr($expression, -1) !== ')') {
      fputs(STDERR, "Unbalanced '('");
      exit(1);
    }

    return evaluate2(substr($expression, 1, -1));
  }

  $value = intval($expression);
  if (strval($value) !== $expression) {
      fputs(STDERR, "Expression is not a number ($expression)");
      exit(1);
  }

  return $value;
}

function findOperator($operator,  $expression) {
  $open = 0;

  for ($index = 0; $index < strlen($expression); $index ++) {
    $char = $expression[$index];
    switch ($char) {
      case $operator:
        if ($open > 0) {
          break;
        }
        return $index;
      case '(':
        $open ++;
        break;
      case ')':
        if ($open <= 0) {
          fputs(STDERR, 'Closed parenthesis found without open parenthesis.');
          exit(1);
        }
        $open --;
        break;
    }
  }

  return -1;
}


// That's all, folks!
