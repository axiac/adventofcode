<?php


// Describe the board and the relationships between cells
abstract class Board {
  protected array $energy = ['A' => 1, 'B' => 10, 'C' => 100, 'D' => 1000];
  protected array $hallway = [1, 2, 4, 6, 8, 10, 11];
  protected array $rooms;
  protected array $neighbours;
  protected array $targets;
  protected array $moves;

  public function __construct() {
    $this->computeMoves();
  }

  public function getDistance(int $cellNb, string $type): int {
    return count($this->getPath($cellNb, $this->targets[$type], $type)) * $this->energy[$type];
  }

  abstract public function getDistanceOffset(): int;

  public function getRoom(string $type): array {
    return array_reverse($this->rooms[$type]);
  }

  public function getHallway(): array {
    return $this->hallway;
  }

  public function isOnHallway(int $cellNb): bool {
    return in_array($cellNb, $this->hallway);
  }

  public function getPath(int $source, int $dest, string $type): array {
    return $this->moves[$type][$source][$dest];
  }

  public function computeEnergy(int $source, int $dest, string $type): int {
    return count($this->getPath($source, $dest, $type)) * $this->energy[$type];
  }

  abstract public function draw(Position $position): string;

  protected function computeMoves() {
    // Compute the valid moves for each amphipod type
    foreach ($this->rooms as $t => $room) {
      foreach ($room as $r) {
        // Any room (different than target) -> target room
        foreach ($this->rooms as $t2 => $room2) {
          // if ($t2 == $t) {
          //   continue;
          // }
          foreach ($room2 as $r2) {
            $this->moves[$t][$r2][$r] = $this->findPath($r2, $r);
          }
        }
        // Any room -> hallway
        foreach ($this->hallway as $h) {
          $p = $this->findPath($r, $h);
          // The amphipods of all types can go from this room to the hallway
          $this->moves['A'][$r][$h] = $p;
          $this->moves['B'][$r][$h] = $p;
          $this->moves['C'][$r][$h] = $p;
          $this->moves['D'][$r][$h] = $p;
          // Hallway -> target room
          // Only the amphipods of the right type can go from the hallway to this room
          $this->moves[$t][$h][$r] = $this->findPath($h, $r);
        }
      }
    }
  }

  protected function findPath(int $begin, int $end): array {
    $candidates = [[$begin]];
    while (count($candidates)) {
      $c = array_shift($candidates);
      $tail = end($c);
      if ($tail === $end) {
        return array_slice($c, 1);
      }
      foreach ($this->neighbours[$tail] as $to) {
        if (! in_array($to, $c)) {
          $next = $c;
          $next[] = $to;
          $candidates[] = $next;
        }
      }
    }

    throw new Exception("Cannot find a path between $begin and $end. This is a coding error.");
  }
}

class Board1 extends Board {
  protected array $rooms = ['A' => [12, 16], 'B' => [13, 17], 'C' => [14, 18], 'D' => [15, 19]];
  protected array $neighbours = [ 1 => [2], 2 => [1, 3], 3 => [2, 4, 12], 4 => [3, 5], 5 => [4, 6, 13], 6 => [5, 7], 7 => [6, 8, 14], 8 => [7, 9], 9 => [8, 10, 15], 10 => [9, 11], 11 => [10], 12 => [3, 16], 13 => [5, 17], 14 => [7, 18], 15 => [9, 19], 16 => [12], 17 => [13], 18 => [14], 19 => [15]];
  protected array $targets = ['A' => 16, 'B' => 17, 'C' => 18, 'D' => 19];

  public function getDistanceOffset(): int {
    return array_sum($this->energy);           // 1111
  }

  public function draw(Position $p): string {
    return <<< END
#############
#{$p->getAt(1)}{$p->getAt(2)}{$p->getAt(3)}{$p->getAt(4)}{$p->getAt(5)}{$p->getAt(6)}{$p->getAt(7)}{$p->getAt(8)}{$p->getAt(9)}{$p->getAt(10)}{$p->getAt(11)}#
###{$p->getAt(12)}#{$p->getAt(13)}#{$p->getAt(14)}#{$p->getAt(15)}###
  #{$p->getAt(16)}#{$p->getAt(17)}#{$p->getAt(18)}#{$p->getAt(19)}###
  #########
END;
  }
}

class Board2 extends Board {
  protected array $rooms = ['A' => [12, 16, 20, 24], 'B' => [13, 17, 21, 25], 'C' => [14, 18, 22, 26], 'D' => [15, 19, 23, 27]];
  protected array $neighbours =[ 1 => [2], 2 => [1, 3], 3 => [2, 4, 12], 4 => [3, 5], 5 => [4, 6, 13], 6 => [5, 7], 7 => [6, 8, 14], 8 => [7, 9], 9 => [8, 10, 15], 10 => [9, 11], 11 => [10], 12 => [3, 16], 13 => [5, 17], 14 => [7, 18], 15 => [9, 19], 16 => [12, 20], 17 => [13, 21], 18 => [14, 22], 19 => [15, 23], 20 => [16, 24], 21 => [17, 25], 22 => [18, 26], 23 => [19, 27], 24 => [20], 25 => [21], 26 => [22], 27 => [23]];
  protected array $targets = ['A' => 24, 'B' => 25, 'C' => 26, 'D' => 27];

  public function getDistanceOffset(): int {
    return 6 * array_sum($this->energy);           // 6666
  }

  public function draw(Position $p): string {
    return <<< END
#############
#{$p->getAt(1)}{$p->getAt(2)}{$p->getAt(3)}{$p->getAt(4)}{$p->getAt(5)}{$p->getAt(6)}{$p->getAt(7)}{$p->getAt(8)}{$p->getAt(9)}{$p->getAt(10)}{$p->getAt(11)}#
###{$p->getAt(12)}#{$p->getAt(13)}#{$p->getAt(14)}#{$p->getAt(15)}###
  #{$p->getAt(16)}#{$p->getAt(17)}#{$p->getAt(18)}#{$p->getAt(19)}#
  #{$p->getAt(20)}#{$p->getAt(21)}#{$p->getAt(22)}#{$p->getAt(23)}#
  #{$p->getAt(24)}#{$p->getAt(25)}#{$p->getAt(26)}#{$p->getAt(27)}#
  #########
END;
  }
}


class Position {
  private int $energy;
  private int $distance;
  private array $cells;

  public function __construct(array $position, private Board $board) {
    $this->cells = self::createAmphipods($position);
    $this->energy = 0;         // The first position is constructed; the others are cloned
    $this->computeDistance();
  }

  public function __clone() { $this->cells = array_map(fn($cell) => clone $cell, $this->cells); }
  public function sig(): string {
    return implode('', array_map(fn(CellContent $cell) => $cell->sig(), $this->cells));
  }
  public function compareWith(Position $other): int { return $this->distance + $this->energy <=> $other->distance + $other->energy; }
  public function isSolution(): int { return $this->distance === 0; }
  public function getAt(int $cellNb): string { return $this->cells[$cellNb]->sig(); }
  public function getDistance(): int { return $this->distance; }
  public function getEnergy(): int { return $this->energy; }
  public function draw(): string { return $this->board->draw($this)."\ndistance: {$this->distance}, energy: {$this->energy}"; }

  // Generate positions starting from this position (move each amphipod in all reachable valid positions)
  public function generateNext(): array {
    $next = [];
    foreach ($this->cells as $cellNb => $amp) {
      if ($amp->isEmptyCell()) {
        continue;
      }
//      echo("cellNb: $cellNb, amp={$amp}\n");

      // If the amphipod is on its final destination then ignore it (it must not move again)
      $type = $amp->sig();
      if ($this->positionIsFinal($cellNb, $type)) {
        continue;
      }

      // If the position contains an amphipod that cannot move then the position must not be reached during a game
      if (! $amp->canMoveAgain()) {
        // There is no continuation
        return [];
      }

      // Try to move the amphipod directly to a home position, if possible
      // (if there are only amphipods of the same type in the room and the path is clear)
      $position = $this->generateMoveToHome($amp);
      if ($position) {
//    echo($position->draw()."\n\n");
        $next[] = $position;
      }

      // If the amphipod is not on the hallway then generate all valid moves to the hallway positions
      //
      if (! $this->board->isOnHallway($cellNb)) {
        $positions = $this->generateMovesToHallway($amp);
    // foreach ($positions as $pos) {
    //   echo($pos->draw()."\n\n");
    // }

        array_push($next, ...$positions);
      }
    }

    return $next;
  }

  private static function createAmphipods(array $position): array {
    $cells = array_fill(1, max(array_keys($position)), new EmptyCell());
    foreach ($position as $cellNb => $type) {
      $cells[$cellNb] = new Amphipod($type, $cellNb);
    }
    return $cells;
  }

  private function computeDistance() {
    $distance = 0;
    foreach ($this->cells as $cellNb => $amp) {
      if ($amp->isEmptyCell()) {
        continue;
      }
      $distance += $this->board->getDistance($cellNb, $amp->sig());
    }
    $this->distance = $distance - $this->board->getDistanceOffset();
  }

  private function positionIsFinal(int $cellNb, string $type): bool {
    // The position is final if it is in the room of its type
    // and all positions below it are filled with amphipods of the same type
    $room = $this->board->getRoom($type);

    if (! in_array($cellNb, $room)) {
      // Not in the right room
      return false;
    }

    foreach ($room as $cell) {
      if ($cell == $cellNb) {
        // All cells below `$cellNb` are occupied by amphipods of the right type
        return true;
      }
      if ($this->cells[$cell]->sig() !== $type) {
        // Found an empty cell or an amphipod of a different type
        return false;
      }
    }
  }

  private function generateMoveToHome(Amphipod $amp): Position | null {
    $source = $amp->getCellNb();
    // Get the cells of the home room
    $type = $amp->sig();
    $room = $this->board->getRoom($type);

    // Find the first empty cell, given that all cells under it are occupied
    // by amphipods of the given type
    foreach ($room as $dest) {
      $x = $this->cells[$dest];
      if ($x->isEmptyCell()) {
        // Found the first empty cell (and all cells beneath)
        if ($this->pathIsClear($source, $dest, $type)) {
          return $this->generateNewPosition($amp, $dest);
        } else {
          return null;        // cannot go home, the path is not clear
        }
      }
      if ($x->sig() !== $type) {
        // The target room still contains amphipods of other types
        return null;
      }
    }

    throw new Exception("The room is full with amphipods of type '$type' but there is another one at '$source'");
  }

  private function generateMovesToHallway(Amphipod $amp): array {
    $positions = [];
    $source = $amp->getCellNb();
    $type = $amp->sig();
    $hallway = $this->board->getHallway();
    foreach ($hallway as $h) {
      if ($this->pathIsClear($source, $h, $type)) {
        $positions[] = $this->generateNewPosition($amp, $h);
      }
    }

    return $positions;
  }


  private function pathIsClear(int $source, int $dest, string $type): bool {
    $path = $this->board->getPath($source, $dest, $type);
    foreach ($path as $cell) {
      if (! $this->cells[$cell]->isEmptyCell()) {
        return false;
      }
    }

    return true;
  }

  private function generateNewPosition(Amphipod $amp, int $dest): Position {
    $source = $amp->getCellNb();
    $position = clone $this;
    $position->cells[$source] = new EmptyCell();
    $position->cells[$dest] = clone $amp;
    $energy = $this->board->computeEnergy($source, $dest, $amp->sig());
    $position->cells[$dest]->moveTo($dest, $energy);
    $position->energy += $energy;
    $position->computeDistance();

    return $position;
  }
}

abstract class CellContent {
  abstract public function sig();
  abstract public function isEmptyCell(): bool;
}

class EmptyCell extends CellContent {
  public function sig(): string { return '.'; }
  public function isEmptyCell(): bool { return true; }
  public function __toString(): string { return '.'; }
}

class Amphipod extends CellContent implements JsonSerializable {
  private int $energy = 0;
  private int $moves = 0;

  public function __construct(private string $type, private int $cellNb) { }

  public function sig(): string { return $this->type; }
  public function isEmptyCell(): bool { return false; }
  public function getCellNb(): int { return $this->cellNb; }
  public function moveTo(int $dest, int $energy): void { $this->cellNb = $dest; $this->energy += $energy; $this->moves ++; }
  public function canMoveAgain(): bool { return $this->moves < 2; }
  public function __toString(): string { return json_encode($this); }
  public function jsonSerialize(): array { return ['type' => $this->type, 'pos' => $this->cellNb, 'energy'=> $this->energy, 'moves'=> $this->moves]; }
}


// That's all, folks!
