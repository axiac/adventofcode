<?php
/**
 * Back to the track, recursively, by the book.
 * @link https://en.wikipedia.org/wiki/Backtracking
 *
 * In order to apply backtracking to a specific class of problems, one must provide the data `P`
 * for the particular instance of the problem that is to be solved, and six procedural parameters,
 * `root()`, `reject()`, `accept()`, `first()`, `next()`, and `output()`.
 * These procedures should take the instance data `P` as a parameter and should do the following:
 *
 * 1. `root(P)`: return the partial candidate at the root of the search tree.
 * 2. `reject(P,c)`: return `true` only if the partial candidate `c` is not worth completing.
 * 3. `accept(P,c)`: return `true` if `c` is a solution of `P`, and `false` otherwise.
 * 4. `first(P,c)`: generate the first extension of candidate `c`.
 * 5. `next(P,s)`: generate the next alternative extension of a candidate, after the extension `s`.
 * 6. `output(P,c)`: use the solution `c` of `P`, as appropriate to the application.
 *
 * The backtracking algorithm reduces the problem to the call `bt(root(P))`,
 * where `bt()` is the following recursive procedure:
 *
 * ```
 * procedure bt(c) is
 *   if reject(P,c) then return
 *   if accept(P,c) then output(P,c)
 *   s ← first(P,c)
 *   while s ≠ Λ do
 *     bt(s)
 *     s ← next(P,s)
 * ```
 */

interface ProblemInterface {
  /**
   * Return the partial candidate at the root of the search tree.
   */
  public function root(): CandidateInterface;


  /**
   * Return TRUE only if the partial candidate $c is not worth completing.
   */
  public function reject(CandidateInterface $c): bool;


  /**
   * Return TRUE if $c is a solution, and FALSE otherwise.
   */
  public function accept(CandidateInterface $c): bool;


  /**
   * Use the solution $c, as appropriate to the application.
   */
  public function output(CandidateInterface $c): void;


  /**
   * Generate the first extension of candidate $c.
   */
  public function first(CandidateInterface $c): CandidateInterface;


  /**
   * Generate the next alternative extension of a candidate, after the extension $s.
   */
  public function next(CandidateInterface $c): CandidateInterface;
}

interface CandidateInterface {
  public function isEmpty(): bool;
}

function backtrack(ProblemInterface $p, CandidateInterface $c) {
  if ($p->reject($c)) {
    return;
  }
  if ($p->accept($c)) {
    $p->output($c);
    return;
  }
  $s = $p->first($c);
  while (! $s->isEmpty()) {
    backtrack($p, $s);
    $s = $p->next($s);
  }
}


// That's all, folks!
