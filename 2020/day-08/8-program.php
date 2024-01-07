<?php
/**
 * Day 8: Handheld Halting
 *
 * @link https://adventofcode.com/2020/day/8
 */

/**
 * Run the program as:
 *   php 8-program.php 2>/dev/null
 * or
 *   php 8-program.php 8-example 2>/dev/null
 *
 * On `stderr`, the program dumps the reason for failing
 * when the patched program does not complete successfully (part 2)
 */

// Input file
$inputfile = dirname($argv[0]).'/8-input';
if ($argc >= 2) {
  $inputfile = $argv[1];
}

// Read the input data
$lines = explode("\n", trim(file_get_contents($inputfile)));


// Parse the program
$statements = [];
foreach ($lines as $line) {
  list($command, $argument) = explode(' ', $line);
  $statements[] = ['command' => $command, 'argument' => intval($argument)];
}

// Execute the original program
try {
  $result = execute($statements);
  echo("Program completed with success. Result=${result}\n");
} catch (InfiniteLoopException $e) {
  echo("part 1: {$e->value}\n");
} catch (Exception $e) {
  echo("Error: {$e->getMessage()}\n");
  exit(1);
}

// Patch the program to complete successfully
$count = count($statements);
for ($i = 0; $i < $count; $i ++) {
  // Patch a copy of the program
  $patched = $statements;
  switch ($patched[$i]['command']) {
  case 'jmp':
    $patched[$i]['command'] = 'nop';
    break;
  case 'nop':
    $patched[$i]['command'] = 'jmp';
    break;
  default:
    // This statement cannot be patched; skip it
    continue 2;
  }

  // Execute the patched program
  try {
    $result = execute($patched);
    echo("part 2: ${result}\n");
  } catch (Exception $e) {
    // Dump the error on `stderr` and continue
    fprintf(STDERR, "Patched statement #{$i}. Error: {$e->getMessage()}\n");
  }
}


class InfiniteLoopException extends Exception {
  public $value;

  public function __construct($value) {
    parent::__construct('Infinite loop');
    $this->value = $value;
  }
}

class InvalidJumpException extends Exception {
//  public $line;

  public function __construct($line) {
    parent::__construct("The 'jmp' command on line #{$line} moves the instruction pointer outside the program.");
    $this->line = $line;
  }
}

class UnknownCommandException extends Exception {
  public $command;
  // public $line;

  public function __construct($command, $line) {
    parent::__construct("Unknown command '$command' on line #{$line}\n");
    $this->command = $command;
    $this->line = $line;
  }
}

function execute(array $statements) {
  // Remember the lines that were executed
  $executed = [];
  // The computed value
  $accumulator = 0;

  // Run the program
  $count = count($statements);
  for ($ip = 0; $ip < $count; $ip += $delta, $delta = 0) {
    // Verify if the current statement was already been executed
    if (array_key_exists($ip, $executed)) {
      // Detected an infinite loop; print the computed value and exit
      throw new InfiniteLoopException($accumulator);
    }
    // Remember the statement has been executed
    $executed[$ip] = true;

    // Parse the current statement
    $command = $statements[$ip]['command'];
    $argument = $statements[$ip]['argument'];

    // Run the statement
    switch ($command) {
    case 'nop':
      $delta = 1;
      break;
    case 'acc':
      $accumulator += $argument;
      $delta = 1;
      break;
    case 'jmp':
      $delta = $argument;
      if ($ip + $delta < 0 || $count < $ip + $delta) {
        throw new InvalidJumpException($ip);
      }
      break;
    default:
      throw new UnknownCommandException($command, $ip);
    }
  }

  return $accumulator;
}


// That's all, folks!
