<?php
/**
 * Day 20: Jurassic Jigsaw
 *
 * @link https://adventofcode.com/2020/day/20
 */

/**
 * Run the program as:
 *   php 20-program.php
 * or
 *   php 20-program.php 20-example-1
 */

// Input file
$inputfile = dirname($argv[0]).'/20-input';
if ($argc >= 2) {
  $inputfile = $argv[1];
}

// Read the input data
$input = explode("\n", trim(file_get_contents($inputfile)));


$allTiles = [];
$tileNb = null;
$tileData = [];

// Parse the input data
foreach ($input as $line) {
  if (preg_match('/Tile (\d+):/', $line, $m)) {
    $tileNb = intval($m[1]);
    continue;
  }

  if ($line === '') {
    $allTiles[$tileNb] = new Tile($tileNb, $tileData);
    $tileNb = null;
    $tileData = [];
    continue;
  }

  $tileData[] = str_split($line);
}
if ($tileNb !== null && count($tileData)) {
  $allTiles[$tileNb] = new Tile($tileNb, $tileData);
}


$nbRows = $nbCols = intval(sqrt(count($allTiles)));
if ($nbRows * $nbCols !== count($allTiles)) {
  fprintf(STDERR, "Incorrect number of tiles. %d x %d != %d\n", $nbRows, $nbCols, count($allTiles));
  exit(1);
}


// Collect the borders, map each to the tile(s) that own it
$allBorders = [];
foreach ($allTiles as $tile) {
  foreach ($tile->getAllBorders() as $border) {
    if (! array_key_exists($border, $allBorders)) {
      $allBorders[$border] = [];
    }
    $allBorders[$border][] = $tile->getTileNb();
  }
}
// Collect the borders that belong to only one tile; some of them are on the border of the image
$uniqueBorders = array_values(array_filter(
  array_keys($allBorders),
  function ($border) use ($allBorders) { return count($allBorders[$border]) === 1; }
));

// The reverse of some of the unique borders are also in the list of unique borders
// They are inner borders
$reversableBorders = array_intersect($uniqueBorders, array_map('strrev', $uniqueBorders));

// The borders on the border of the image
$bordersOnBorder = array_values(array_diff($uniqueBorders, $reversableBorders));
// Map the borders on the border of the image to the tiles where they belong
$borderOnBorderToTile = [];
foreach ($bordersOnBorder as $border) {
  $borderOnBorderToTile[$border] = $allBorders[$border][0];
}

// Find the tiles that have two borders on the border of the image
$cornerTiles = array_keys(array_filter(
  array_count_values(array_values($borderOnBorderToTile)),
  function ($count) { return $count === 2; }
));

$part1 = array_product($cornerTiles);
echo("part 1: ${part1}\n");


$imageTiles = [];
$unusedTiles = array_keys($allTiles);

// Place one corner tile at (0, 0)...
$tileNb = array_shift($cornerTiles);
// ...and fill the first row of tiles
$row = fillFirstRow($tileNb, $unusedTiles);
$imageTiles[0] = $row;
// Update the list of used tiles
$unusedTiles = array_diff($unusedTiles, $row);

// Validate the top-right corner
$tileNb = end($row);
if (! in_array($tileNb, $cornerTiles)) {
  fputs(STDERR, "The top-right corner found is not a corner tile.\n");
  exit(1);
}
$cornerTiles = array_diff($cornerTiles, [$tileNb]);

// Fill the other rows
for ($i = 1; $i < $nbRows; $i ++) {
  $row = fillNextRow($row, $unusedTiles);
  // Put the row in the image
  $imageTiles[$i] = $row;
  // Update the list of unused tiles
  $unusedTiles = array_diff($unusedTiles, $row);
}

// Build the image
$imagePixels = [];
$pixelsPerColumn = count(reset($allTiles)->render());
foreach ($imageTiles as $rowOfTiles) {
  $stripe = array_fill(0, $pixelsPerColumn, []);
  foreach ($rowOfTiles as $tileNb) {
    foreach ($allTiles[$tileNb]->render() as $rowNb => $rowOfPixels) {
      $stripe[$rowNb] = array_merge($stripe[$rowNb], $rowOfPixels);
    }
  }
  $imagePixels = array_merge($imagePixels, $stripe);
}

$seaMonster = [
  '                  # ',
  '#    ##    ##    ###',
  ' #  #  #  #  #  #   ',
];

$image = $imagePixels;
for ($flipped = 0; $flipped < 2; $flipped ++) {
  for ($rotated = 0; $rotated < 4; $rotated ++) {
    $result = findSeaMonster($seaMonster, $image);
    if ($result['nbMonsters'] > 0) {
      printf("part 2: %d\n", computeWaterRoughness($result['imageWithMonsters']));
      break 2;
    }
    $image = rotateClockwise($image);
  }
  $image = flipVertical($image);
}



class Tile {
  private $tileNb;
  private array $allPlacements;

  // Current position
  private $flipped;
  private $rotated;
  private array $borders;
  private array $pixels;

  public function __construct($tileNb, array $pixels) {
    $this->tileNb = $tileNb;
    $this->computeBorders($pixels);
    $this->pixels = $pixels;

    $this->flipped = 0;
    $this->rotated = 0;                          // 1, 2, 3 * 90 deg clockwise
  }

  public function getTileNb() {
    return $this->tileNb;
  }

  // Return all the borders when the tile is not flipped or rotated
  public function getAllBorders() {
    return array_values($this->allPlacements[0]);
  }

  // Return the borders in the current position
  public function getTopBorder() { return $this->borders['top']; }
  public function getRightBorder() { return $this->borders['right']; }
  public function getBottomBorder() { return $this->borders['bottom']; }
  public function getLeftBorder() { return $this->borders['left']; }

  // Change the position to match the provided borders
  public function arrangeTopLeft($borderTop, $borderLeft) {
    $placement = $this->findPlacementWithTwoBorders($borderTop, $borderLeft, 'top', 'left');
    if ($placement === -1) {
      return false;
    }

    // Flip and rotate
    $this->setPlacement($placement);
    return true;
  }

  // Change the position to match the provided border
  public function arrangeLeft($border) {
    $placement = $this->findPlacementWithOneBorder($border, 'left');
    if ($placement === -1) {
      return false;
    }

    // Flip and rotate
    $this->setPlacement($placement);
    return true;
  }

  public function arrangeTop($border) {
    $placement = $this->findPlacementWithOneBorder($border, 'top');
    if ($placement === -1) {
      return false;
    }

    // Flip and rotate
    $this->setPlacement($placement);
    return true;
  }


  public function render() {
    $nbRows = count($this->pixels);
    $nbCols = count($this->pixels[0]);
    if ($this->flipped === 0) {
      switch ($this->rotated) {
        case 0:
          $pixels = $this->pixels;
          break;
        case 1:
          $pixels = array_map(
            function($col) { return array_reverse(array_column($this->pixels, $col)); },
            range(0, $nbCols - 1)
          );
          break;
        case 2:
          $pixels = array_reverse(
            array_map(
              function(array $row) { return array_reverse($row); },
              $this->pixels
            )
          );
          break;
        case 3:
          $pixels = array_map(
            function($col) { return array_column($this->pixels, $col); },
            range($nbCols - 1, 0, -1)
          );
          break;
        default:
          fputs(STDERR, 'What?');
          exit(1);
      }
    } else {
      switch ($this->rotated) {
        case 0:       // flipped around the N-S axe
          $pixels = array_map(
            function(array $row) { return array_reverse($row); },
            $this->pixels
          );
          break;
        case 1:       // flipped, rotated 90 deg ccw (flipped around the NW-SE diagonal)
          $pixels = array_map(
            function($col) { return array_column($this->pixels, $col); },
            range(0, $nbCols - 1)
          );
          break;
        case 2:       // flipped, rotated 180 deg (flipped around the E-W axe)
          $pixels = array_reverse($this->pixels);
          break;
        case 3:       // flipped around the NE-SW axe
          $pixels = array_map(
            function($col) { return array_reverse(array_column($this->pixels, $col)); },
            range($nbCols - 1, 0, -1)
          );
          break;
        default:
          fputs(STDERR, 'What?');
          exit(1);
      }
    }

    // Remove the borders
    return array_map(
      function(array $row) { return array_slice($row, 1, -1); },
      array_slice($pixels, 1, -1)
    );
  }



  private function computeBorders(array $pixels) {
    $topS = implode('', $pixels[0]);
    $bottomS = implode('', $pixels[count($pixels) - 1]);
    $leftS = implode('', array_column($pixels, 0));
    $rightS = implode('', array_column($pixels, count($pixels[0]) - 1));

    $topR = strrev($topS);
    $rightR = strrev($rightS);
    $bottomR = strrev($bottomS);
    $leftR = strrev($leftS);

    $this->allPlacements = [
      [ 'top' => $topS,    'right' => $rightS,  'bottom' => $bottomS, 'left' => $leftS   ],   // not rotated, not flipped
      [ 'top' => $leftR,   'right' => $topS,    'bottom' => $rightR,  'left' => $bottomS ],   // rotated 90 deg cw, not flipped
      [ 'top' => $bottomR, 'right' => $leftR,   'bottom' => $topR,    'left' => $rightR  ],   // rotated 180 deg, not flipped
      [ 'top' => $rightS,  'right' => $bottomR, 'bottom' => $leftS,   'left' => $topR    ],   // rotated 90 deg ccw, not flipped

      [ 'top' => $topR,    'right' => $leftS,   'bottom' => $bottomR, 'left' => $rightS  ],   // flipped around the NS axe
      [ 'top' => $leftS,   'right' => $bottomS, 'bottom' => $rightS,  'left' => $topS    ],   // flipped, rotated 90 deg ccw
      [ 'top' => $bottomS, 'right' => $rightR,  'bottom' => $topS,    'left' => $leftR   ],   // flipped, rotated 180 deg
      [ 'top' => $rightR,  'right' => $topR,    'bottom' => $leftR,   'left' => $bottomR ],   // flipped around the NE-SW axe
    ];

    $this->borders = $this->allPlacements[0];        // not rotated
    //$this->reverse = end($this->allPlacements);      // flipped around the NE-SW axe
  }

  // Find a tile placement that puts the borders $border1 and $border2 on positions $p1 and $p2
  // (not necessarily in this order); the returned value encodes a flip and a rotation
  private function findPlacementWithTwoBorders($border1, $border2, $p1, $p2) {
    // try all combinations of straight and flipped borders into the required positions
    foreach ([$border1, strrev($border1)] as $b1) {
      foreach ([$border2, strrev($border2)] as $b2) {
        foreach ($this->allPlacements as $pos => $borders) {
          if ($b1 === $borders[$p1] && $b2 === $borders[$p2]) {
            // Found a tile placement where the borders match
            return $pos;
          }
        }
      }
    }

    return -1;
  }

  private function findPlacementWithOneBorder($border1, $p1) {
    // try all combinations of straight and flipped borders into the required positions
    foreach ([$border1, strrev($border1)] as $b1) {
      foreach ($this->allPlacements as $pos => $borders) {
        if ($b1 === $borders[$p1]) {
          // Found a tile placement where the borders match
          return $pos;
        }
      }
    }

    return -1;
  }

  private function setPlacement($placement) {
    $this->borders = $this->allPlacements[$placement];

    $this->rotated = $placement % 4;
    $this->flipped = ($placement - $this->rotated) / 4;
  }
}


// Fill the first row of tiles
// @param $tileNb - the number of the tile to put in the top-left corner
function fillFirstRow($tileNb, array $unusedTiles) {
  global $nbCols, $allTiles;

  // The row of tiles is built here
  $row = [];

  // Top-left tile
  placeTileOnTopLeftCorner($tileNb);
  // Put it on the row
  $row[0] = $tileNb;
  // Mark as used
  $unusedTiles = array_diff($unusedTiles, [$tileNb]);

  for ($i = 1; $i < $nbCols; $i ++) {
    // $tileNb is the last placed tile
    $border = $allTiles[$tileNb]->getRightBorder();
    // Find the next tile
    $tileNb = findAndPlaceTileAlignLeft($border, $unusedTiles);
    // Put it on the row
    $row[$i] = $tileNb;
    // Mark as used
    $unusedTiles = array_diff($unusedTiles, [$tileNb]);
  }

  return $row;
}

// Fill subsequent rows
function fillNextRow(array $previous, array $unusedTiles) {
  global $nbCols, $allTiles;

  // The row of tiles is built here
  $row = [];

  // The first tile needs to be aligned only with the bottom border of the first tile on the previous row
  $tileNb = $previous[0];
  $border = $allTiles[$tileNb]->getBottomBorder();
  $tileNb = findAndPlaceTileAlignTop($border, $unusedTiles);
  // Put it on the row
  $row[0] = $tileNb;
  // Mark as used
  $unusedTiles = array_diff($unusedTiles, [$tileNb]);

  for ($i = 1; $i < $nbCols; $i ++) {
    // $tileNb is the last placed tile
    $borderTop = $allTiles[$previous[$i]]->getBottomBorder();
    $borderLeft = $allTiles[$tileNb]->getRightBorder();
    // Find the next tile
    $tileNb = findAndPlaceTileAlignTopLeft($borderTop, $borderLeft, $unusedTiles);
    // Put it on the row
    $row[$i] = $tileNb;
    // Mark as used
    $unusedTiles = array_diff($unusedTiles, [$tileNb]);
  }

  return $row;
}




function placeTileOnTopLeftCorner($tileNb) {
  global $allTiles, $borderOnBorderToTile;

  // Get the borders on the border image that belong to this tile
  $borders = array_keys(array_filter(
    $borderOnBorderToTile,
    function($tileId) use ($tileNb) { return $tileId === $tileNb; }
  ));

  // Ask the tile to rotate and flip itself to have these two borders on top-left
  $tile = $allTiles[$tileNb];
  if (! $tile->arrangeTopLeft($borders[0], $borders[1]) &&
      ! $tile->arrangeTopLeft($borders[1], $borders[0])) {
    fputs(STDERR, "The tile cannot be arranged in the top-left corner ('$borders[0]', '$borders[1]')\n");
    exit(1);
  }

  return $tileNb;
}

function findAndPlaceTileAlignLeft($border, array $unusedTiles) {
  global $allTiles;

  foreach ($unusedTiles as $tileNb) {
    $tile = $allTiles[$tileNb];
    if ($tile->arrangeLeft($border)) {
      return $tileNb;
    }
  }

  fprintf(STDERR, "Cannot find a tile to have left border '%s' (straight or flipped).\n", $border);
  exit(1);
}

function findAndPlaceTileAlignTop($border, array $unusedTiles) {
  global $allTiles;

  foreach ($unusedTiles as $tileNb) {
    $tile = $allTiles[$tileNb];
    if ($tile->arrangeTop($border)) {
      return $tileNb;
    }
  }

  fprintf(STDERR, "Cannot find a tile to have top border '%s' (straight or flipped).\n", $border);
  exit(1);
}

function findAndPlaceTileAlignTopLeft($borderTop, $borderLeft, array $unusedTiles) {
  global $allTiles;

  foreach ($unusedTiles as $tileNb) {
    $tile = $allTiles[$tileNb];
    if ($tile->arrangeTopLeft($borderTop, $borderLeft)) {
      return $tileNb;
    }
  }

  fprintf(STDERR, "Cannot find a tile to have top border '%s' and left border '%s' (straight or flipped).\n", $borderTop, $borderLeft);
  exit(1);
}

function rotateClockwise(array $image) {
  return array_map(
    function($col) use ($image) { return array_reverse(array_column($image, $col)); },
    range(0, count($image) - 1)
  );
}

function flipVertical(array $image) {
  return array_reverse($image);
}

function drawImage(array $image) {
  return implode(
    '',
    array_map(
      function(array $row) { return implode('', $row)."\n"; },
      $image
    )
  );
}

function computeWaterRoughness(array $image) {
  return array_sum(
    array_map(
      function (array $row) { return array_count_values($row)['#']; },
      $image
    )
  );
}

function findSeaMonster(array $seaMonster, array $image) {
  $smRows = count($seaMonster);
  $smCols = strlen($seaMonster[0]);

  $imgRows = count($image);
  $imgCols = count($image[0]);

  $nbMonsters = 0;
  $imageWithMonsters = $image;
  for ($r = 0; $r < $imgRows - $smRows + 1; $r ++) {
    for ($c = 0; $c < $imgCols - $smCols + 1; $c ++) {
      if (isSeaMonsterHere($seaMonster, $image, $r, $c)) {
        $imageWithMonsters = markSeaMonsterHere($seaMonster, $imageWithMonsters, $r, $c);
        $nbMonsters ++;
      }
    }
  }

  return ['nbMonsters' => $nbMonsters, 'imageWithMonsters' => $imageWithMonsters];
}

function isSeaMonsterHere(array $seaMonster, array $image, $row, $col) {
  $smRows = count($seaMonster);
  $smCols = strlen($seaMonster[0]);

  for ($r = 0; $r < $smRows; $r ++) {
    for ($c = 0; $c < $smCols; $c ++) {
      if ($seaMonster[$r][$c] === '#' && $image[$row + $r][$col + $c] !== '#') {
        return false;
      }
    }
  }

  return true;
}

function markSeaMonsterHere(array $seaMonster, array $imageWithMonsters, $row, $col) {
  $smRows = count($seaMonster);
  $smCols = strlen($seaMonster[0]);

  for ($r = 0; $r < $smRows; $r ++) {
    for ($c = 0; $c < $smCols; $c ++) {
      if ($seaMonster[$r][$c] === '#') {
        $imageWithMonsters[$row + $r][$col + $c] = 'O';
      }
    }
  }

  return $imageWithMonsters;
}


// That's all, folks!
