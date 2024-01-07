<?php
/**
 * Day 19: Beacon Scanner
 *
 * @link https://adventofcode.com/2021/day/19
 */

/**
 * Run the program as:
 *   php 19-program.php
 * or
 *   php 19-program.php 19-example
 */

// Input file
$inputfile = dirname($argv[0]).'/19-input';
if ($argc >= 2) {
  $inputfile = $argv[1];
}

// Read the input data
$inputdata = trim(file_get_contents($inputfile));

include('./geometry.php');

// Parse it
$scanners = [];
foreach (explode("\n\n", $inputdata) as $block) {
  $lines = explode("\n", $block);
  $line = array_shift($lines);
  if (! preg_match('/--- scanner (\d+) ---/', $line, $matches)) {
    throw new Exception("Line does not match the expected format ($line)");
  }
  $scannerNb = intval($matches[1]);

  $scanners[$scannerNb] = new Scanner($scannerNb, array_map(
    fn($line) => new Point(...array_map('intval', explode(',', $line))),
    $lines
  ));
}

// Part 1

// Process the data
$remaining = $scanners;

// The 24 possible rotations of the scanners
$rotations = [
  new Rotation([+1,  0, 0], [ 0, +1, 0], [0, 0, +1]),        // x, y, z
  new Rotation([ 0, -1, 0], [+1,  0, 0], [0, 0, +1]),        // -y, x, z (90)
  new Rotation([-1,  0, 0], [ 0, -1, 0], [0, 0, +1]),        // -x, -y, z (180)
  new Rotation([ 0, +1, 0], [-1,  0, 0], [0, 0, +1]),        // y, -x, z (270)

  new Rotation([-1,  0, 0], [ 0, +1, 0], [0, 0, -1]),
  new Rotation([ 0, -1, 0], [-1,  0, 0], [0, 0, -1]),
  new Rotation([+1,  0, 0], [ 0, -1, 0], [0, 0, -1]),
  new Rotation([ 0, +1, 0], [+1,  0, 0], [0, 0, -1]),

  new Rotation([+1, 0,  0], [0,  0, +1], [0, -1, 0]),
  new Rotation([ 0, 0, -1], [+1, 0,  0], [0, -1, 0]),
  new Rotation([-1, 0,  0], [0,  0, -1], [0, -1, 0]),
  new Rotation([ 0, 0, +1], [-1, 0,  0], [0, -1, 0]),

  new Rotation([+1, 0,  0], [0,  0, -1], [0, +1, 0]),
  new Rotation([ 0, 0, +1], [+1, 0,  0], [0, +1, 0]),
  new Rotation([-1, 0,  0], [0,  0, +1], [0, +1, 0]),
  new Rotation([ 0, 0, -1], [-1, 0,  0], [0, +1, 0]),

  new Rotation([0,  0, +1], [0, +1,  0], [-1, 0, 0]),
  new Rotation([0, -1,  0], [0,  0, +1], [-1, 0, 0]),
  new Rotation([0,  0, -1], [0, -1,  0], [-1, 0, 0]),
  new Rotation([0, +1,  0], [0,  0, -1], [-1, 0, 0]),

  new Rotation([0,  0, -1], [0, +1,  0], [+1, 0, 0]),
  new Rotation([0, -1,  0], [0,  0, -1], [+1, 0, 0]),
  new Rotation([0,  0, +1], [0, -1,  0], [+1, 0, 0]),
  new Rotation([0, +1,  0], [0,  0, +1], [+1, 0, 0]),
];
/*
cos x, -sin x, 0
sin x,  cos x, 0
    0,      0, 1
*/


// Start with scanner #0
$baseScanner = $remaining[0];
unset($remaining[0]);

$baseScanner->setPosition(new Point(0, 0, 0));
$baseScanner->setRotation(new Rotation([1, 0, 0], [0, 1, 0], [0, 0, 1]));
$allBeacons = $baseScanner->getBeacons();

$processed = [$baseScanner];
while (count($remaining) > 0) {
  // Get next scanner from the list of not processed yet
  $current = array_shift($remaining);
  // Find a processed scanner that overlaps with this scanner
  foreach ($processed as $base) {
    // If can find one then translate the coordinates to be relative to scanner #0
    if ($current->overlapsWith($base)) {
// echo("=========== scanner #".$current->getId()." =========\n");
// echo("base: scanner #".$base->getId()."\n");
      // find the rotation of $current relative to $base and the distance between them
      $orientation = $current->findOrientation($base, $rotations);
      if (! $orientation) {
        throw new Exception("cannot find rotation and offset for $current");
      }

      // translate the rotation and the distance to be relative to scanner #0
      $orientation = $base->translateOrientation($orientation);
      $current->setPosition($orientation->position);
      $current->setRotation($orientation->rotation);
      // translate the coordinate of all beacons detected by $current to the coordinates of scanner #0
      $translated = $current->translateBeaconPositions();
// echo("position: ".$current->getPosition()."\n");
// echo("rotation: ".$current->getRotation()."\n");
// foreach ($translated as $beacon) {
//   echo("$beacon\n");
// }
      // add the new beacons to the list
      $allBeacons = array_merge($allBeacons, $translated);

      // Processed. Hurray!
      $processed[] = $current;
      break;
    }
  }

  // If it cannot be processed at this stage then put it to the back of the remaining list
  if (! in_array($current, $processed)) {
    $remaining[] = $current;
  }
}
usort($allBeacons, fn(Point $a, Point $b) => $a->x <=> $b->x);
$allBeacons = array_unique(
  array_map(
    fn(Point $p) => "$p->x,$p->y,$p->z",
    $allBeacons
  )
);

//echo(implode("\n", $allBeacons)."\n");
$nbBeacons = count($allBeacons);
printf("part 1: %d\n", $nbBeacons);

// Part 2
$distances = [];
for ($i = 0; $i < count($processed); $i ++) {
  for ($j = $i + 1; $j < count($processed); $j ++) {
    $distances[] = $processed[$i]->getPosition()->manhattanDistance($processed[$j]->getPosition());
  }
}
$maxDistance = max($distances);

printf("part 2: %d\n", $maxDistance);


///////////////////////////////////////////////////////////////////////////
//
//

class Scanner {
  // The absolute position and rotation of this scanner
  // The absolute positions are relative to scanner's #0 position and rotation
  private Point $position;
  private Rotation $rotation;

  public function __construct(private int $id, private array $beacons) {}

  public function getId(): int { return $this->id; }
  public function getBeacons(): array { return $this->beacons; }
  public function getPosition(): Point { return $this->position; }
  public function getRotation(): Rotation { return $this->rotation; }

  public function overlapsWith(Scanner $other): bool {
    $nbCommon = count($this->getCommonBeacons($other));
    return $nbCommon >= 12 * 11 / 2;
  }

  public function findOrientation(Scanner $other, array $rotations): Orientation | null {
    // $beacons is indexed by distance; each item is a list of two lists
    // each inner list contains beacons
    $beacons = $this->getCommonBeacons($other);
    // Match the common beacons
    $matches = $this->matchTheCommonBeacons($beacons, $other);
// echo("common beacons: [\n".implode("\n", array_map(fn($m) => "$m", $matches))."\n]\n");
    // Try all rotations, stop when find one that moves all beacons over the beacons of the other scanner
    return findScannerOrientation($rotations, $matches);
  }

  public function setPosition(Point $position): void {
    $this->position = $position;
  }
  public function setRotation(Rotation $rotation): void {
    $this->rotation = $rotation;
  }

  // Translate the provided orientation from coordinates relative to this scanner to absolute coordinates
  public function translateOrientation(Orientation $orientation): Orientation {
    $point = $this->translatePosition($orientation->position);
    $rotation = $orientation->rotation->multiply($this->rotation);

    return new Orientation($point, $rotation);
  }

  public function translateBeaconPositions(): array {
    return array_map(
      fn(Point $beacon) => $this->translatePosition($beacon),
      $this->beacons
    );
  }

  protected function translatePosition(Point $position): Point {
    return $this->position->add($position->rotate($this->rotation->inverse()));
  }


  protected function getDistancesSquared(): array {
    $distances = [];

    for ($i1 = 0; $i1 < count($this->beacons); $i1 ++) {
      $b1 = $this->beacons[$i1];
      for ($i2 = $i1 + 1; $i2 < count($this->beacons); $i2++) {
        $b2 = $this->beacons[$i2];
        $d = $b2->getSqDistanceFrom($b1);

        if (! array_key_exists($d, $distances)) {
          $distances[$d] = [];
        }
        $distances[$d][] = [$i1, $i2];
      }
    }
    ksort($distances);

    return $distances;
  }

  // Return array of pairs of beacons from both scanners, indexed by distance
  protected function getCommonBeacons(Scanner $other): array {
    $thisDist = $this->getDistancesSquared();
    $otherDist = $other->getDistancesSquared();
    $commonDist = array_intersect(array_keys($thisDist), array_keys($otherDist));

    $commonBeacons = [];
    foreach ($commonDist as $dist) {
      $commonBeacons[$dist] = [$thisDist[$dist], $otherDist[$dist]];
    }

    return $commonBeacons;
  }

  protected function matchTheCommonBeacons(array $beacons, Scanner $other): array {
    $list1 = [];
    $list2 = [];

    foreach ($beacons as $dist => list($first, $second)) {
      foreach ($first as $f) {
        addToList($list1, $f[0], $dist);
        addToList($list1, $f[1], $dist);
      }
      foreach ($second as $s) {
        addToList($list2, $s[0], $dist);
        addToList($list2, $s[1], $dist);
      }
    }

    $beacons1 = [];
    foreach ($list1 as $id => $dist) {
      sort($dist);
      $beacons1[$id]=implode(',', $dist);
    }
    asort($beacons1);
    $beacons2 = [];
    foreach ($list2 as $id => $dist) {
      sort($dist);
      $beacons2[$id]=implode(',', $dist);
    }
    asort($beacons2);

    $beacons1 = array_keys($beacons1);
    $beacons2 = array_keys($beacons2);
    $matches = [];
    foreach ($beacons1 as $idx => $b1) {
      $b2 = $beacons2[$idx];
      $matches[] = new Pair($this->beacons[$b1], $other->beacons[$b2]);
    }

    return $matches;
  }
}

function getOrientation(Rotation $rotation, array $beacons): Orientation | null {
  // Compute the offset for the first pair of beacons
  $first = array_shift($beacons);
  $offset = $first->right->subtract($first->left->rotate($rotation));

  // Check the offset for the rest of pairs against the first one
  foreach ($beacons as $b) {
    // Rotate the orientation of the second beacon, subtract the orientation of the first beacon
    $off = $b->right->subtract($b->left->rotate($rotation));
    if (! $off->equals($offset)) {
      return null;
    }
  }

  // The rotation moves the beacons from the right over those from left (after the offset is applied)
  return new Orientation($offset, $rotation->inverse());
}

function findScannerOrientation(array $allRotations, array $beacons): Orientation | null {
  foreach ($allRotations as $rotation) {
    $orientation = getOrientation($rotation, $beacons);
    if ($orientation) {
      return $orientation;
    }
  }

  return null;
}

function addToList(array &$list, int $idx, int $dist) {
  if (! array_key_exists($idx, $list)) {
    $list[$idx] = [];
  }
  $list[$idx][] = $dist;
}

// That's all, folks!
