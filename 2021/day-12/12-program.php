<?php
/**
 * Day 12: Passage Pathing
 *
 * @link https://adventofcode.com/2021/day/12
 */

/**
 * Run the program as:
 *   php 12-program.php
 * or
 *   php 12-program.php 12-example-1
 */

// Input file
$inputfile = dirname($argv[0]).'/12-input';
if ($argc >= 2) {
  $inputfile = $argv[1];
}

// Read the input data
$inputdata = array_map(fn($line) => trim($line), file($inputfile));

// Parse the input data
$connections = [];
foreach ($inputdata as $line) {
  list($from, $to) = explode('-', $line);
  // Do not store the paths back to 'start'
  if ($to !== 'start') {
    $connections[$from][] = $to;
  }
  if ($from !== 'start') {
    $connections[$to][] = $from;
  }
}
// Remove the paths that start from 'end'
unset($connections['end']);


// Recursive backtrack algorithm and interfaces for problem and solution candidates
include '../../lib/backtrack.php';


class CavesCandidateSolution implements CandidateInterface {
  public function __construct(protected array $path, protected string $tip, private int $idx) {
  }

  public function isEmpty(): bool {
    return false;
  }

  public function isComplete(): bool {
    return $this->tip === 'end';
  }

  // Part 1: the small caves must be visited only once
  public function isValid(): bool {
    return (is_small_cave($this->tip) && array_search($this->tip, $this->path) !== false) ? false : true;
  }

  public function display(): void {
    echo("{$this}\n");
  }

  public function extend(array $connections): CandidateInterface {
    $index = 0;
    $next = $connections[$this->tip][$index];
    $candidate = new static([...$this->path, $this->tip], $next, $index);

    return $candidate;
  }

  public function next(array $connections): CandidateInterface {
    $prev = end($this->path);
    $index = $this->idx;

    if ($index !== count($connections[$prev]) - 1) {
      $candidate = new static($this->path, $connections[$prev][$index + 1], $index + 1);
    } else {
      $candidate = new NullCandidateSolution();
    }

    return $candidate;
  }

  public function __toString(): string {
    return implode(',', [...$this->path, $this->tip]);
  }
}

class NullCandidateSolution implements CandidateInterface {
  public function __construct() {}

  public function isEmpty(): bool { return true; }

  public function __toString(): string { return '<null>'; }
}

class CavesCandidateSolutionPart2 extends CavesCandidateSolution {
  // Part 2: one and only one small cave, but not 'start' or 'end', can be visited at most twice
  public function isValid(): bool {
    // The large caves can be visited any number of times
    if (is_large_cave($this->tip)) {
      return true;
    }
    // The start and end caves can be visited only once
    if (in_array($this->tip, ['start', 'end']) && in_array($this->tip, $this->path)) {
      return false;
    }

    // At most one cave can be visited exactly twice
    $visitedTwice = array_filter(
      array_count_values(                                                    // count by cave
        array_filter([...$this->path, $this->tip], fn($cave) => is_small_cave($cave))   // the small caves in the path
      ),
      fn($count) => $count > 1                                               // keep only those visited twice
    );
    if (count($visitedTwice) > 2) {
      throw new Exception("There are two or more small caves visited more than once: ".json_encode($visitedTwice));
    }

    $isValid = count($visitedTwice) === 0 || count($visitedTwice) === 1 && reset($visitedTwice) <= 2;
    return $isValid;
  }
}


class CavesProblem implements ProblemInterface {
  private int $_nbSolutions;

  public function __construct(private array $connections) {
    $this->_nbSolutions = 0;
  }

  /**
   * Return the partial candidate at the root of the search tree.
   */
  public function root(): CandidateInterface {
    return new CavesCandidateSolution([], 'start', 0);
  }


  /**
   * Return TRUE only if the partial candidate $c is not worth completing.
   */
  public function reject(CandidateInterface $c): bool {
    return ! $c->isValid();
  }


  /**
   * Return TRUE if $c is a solution, and FALSE otherwise.
   */
  public function accept(CandidateInterface $c): bool {
    return $c->isComplete();
  }


  /**
   * Use the solution $c, as appropriate to the application.
   * @param CandidateInterface $c
   */
  public function output(CandidateInterface $c): void {
    //$c->display();
    $this->_nbSolutions ++;
  }


  /**
   * Generate the first extension of candidate $c.
   */
  public function first(CandidateInterface $c): CandidateInterface {
    return $c->extend($this->connections);
  }


  /**
   * Generate the next alternative extension of a candidate, after the extension $s.
   */
  public function next(CandidateInterface $c): CandidateInterface {
    return $c->next($this->connections);
  }

  public function nbSolutions(): int {
    return $this->_nbSolutions;
  }
}

class CavesProblemPart2 extends CavesProblem {
   public function root(): CandidateInterface {
    return new CavesCandidateSolutionPart2([], 'start', 0);
  }
}


// Part 1
$p = new CavesProblem($connections);
backtrack($p, $p->root());
printf("part 1: %d\n", $p->nbSolutions());

// Part 2
$p = new CavesProblemPart2($connections);
backtrack($p, $p->root());
printf("part 2: %d\n", $p->nbSolutions());


function is_small_cave(string $cave): bool {
  return $cave === strtolower($cave);
}

function is_large_cave(string $cave): bool {
  return $cave === strtoupper($cave);
}

// That's all, folks!
