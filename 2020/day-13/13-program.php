<?php
/**
 * Day 13: Shuttle Search
 *
 * @link https://adventofcode.com/2020/day/13
 */

/**
 * Run the program as:
 *   php 13-program.php
 * or
 *   php 13-program.php 13-example-1
 */

// Input file
$inputfile = dirname($argv[0]).'/13-input';
if ($argc >= 2) {
  $inputfile = $argv[1];
}

// Read the input data
$input = explode("\n", trim(file_get_contents($inputfile)));

// Part 1
$timestamp = intval($input[0]);
$buses = array_map(
  function ($busId) { return intval($busId); },
  array_filter(
    explode(',', $input[1]),
    function ($busId) { return $busId != 'x'; }
  )
);

$times = array_map(
  function ($busId) use ($timestamp) {
    $rest = $timestamp % $busId;
    if ($rest !== 0) {
      $rest = $busId - $rest;
    }
    return $rest;
  },
  $buses
);

$pos = array_search(min($times), $times);
echo('part 1: '.($times[$pos]*$buses[$pos])."\n");

// Part 2
$equations = [];
$buses = explode(',', $input[1]);
$x = 'a';
for ($i = 0; $i < count($buses); $i ++){
  $busId = $buses[$i];
  if ($busId != 'x') {
//    echo("z + $i = $x * $busId;\n");
    $x ++;
  }
}

$buses = array_filter(
  $buses,
  function ($time) { return $time !== 'x'; }
);
$product = array_product($buses);
$a = array_sum(
  array_map(
    function ($busId) use ($product) { return $product / $busId; },
    array_values($buses)
  )
);
$b = array_sum(
  array_map(
    function ($offset, $busId) use ($product) { return $product / $busId * $offset; },
    array_keys($buses),
    array_values($buses)
  )
);
$c = $product;

// echo("${a} * z + ${b} = ${c} * n\n");

// The equation:
//   731816704052077 * z + 30956970823675892 = 2283338533368659 * n
//
// The solution:
//   n = 731816704052077 * m + 74005291363791
//   z = 2283338533368659 * m + 230903629977901
//   (m: integer)

// Expected output
// part 2: 230903629977901


//echo("${a} * x0 + ${b} = ${c} * x1\n");
//
// // a * x0 + b = c * x1
// // x0 = (c * x1 - b) / a = [c/a] * x1 - [b/a] + (c%a * x1 - b%a) / a
// $x[] = [($c - $c % $a) / $a, -1, ($b - $b % $a) / $a, $c % $a, $b % $a, $a];
// // a * x2 = c%a * x1 - b%a = c1 * x1 - b1             // c1,   b1
// // x1 = (a * x2 + b1) / c1 = [a/c1] * x2 + [b1/c1] + (a%c1 * x2 + b1%c1) / c1
// $x[] = [($a - $a % $c1) / $c1, +1, ($b1 - $b1 % $c1) / $c1, $a % $c1, $b1 % $c1, $c1];
// // c1 * x3 = a2 * x2 + b2                                   // a2,    b2
// // x2 = (c1 * x3 - b2) / a2
// $x[] = [($c1 - $c1 % $a2) / $a2, -1, ($b2 - $b2 % $a2) / $a2, $c1 % $a2, $b2 % $a2, $a2];
// ...

$x = [];
$x[-1] = [0, +1, 0, $a, $b, $c];

$i = 0;
do {
  $s = $x[$i - 1][1];
  $a = $x[$i - 1][3];
  $b = $x[$i - 1][4];
  $c = $x[$i - 1][5];

  $c1 = $c % $a;
  $b1 = $b % $a;
  $x[$i] = [($c - $c1)/$a, -$s, ($b - $b1)/$a, $c1, $b1, $a];
  $i ++;
} while ($a !== 1);
$n = $i;
//display($x);

$reverse = [];
$reverse[$n + 1] = [0, 0];
$reverse[$n] = [1, 0];
for ($i = $n - 1; $i >= 0; $i --) {
  $r = $x[$i];
  $rev1 = $reverse[$i + 1];
  $rev2 = $reverse[$i + 2];
  $reverse[$i] = [$r[0] * $rev1[0] + $rev2[0], $r[0] * $rev1[1] + $r[1] * $r[2] + $rev2[1]];
}
//display_rev($reverse, $n);

//printf("n = %d * m %s %d\n", $reverse[1][0], $reverse[1][1] >= 0 ? '+' : '-', abs($reverse[1][1]));
//printf("z = %d * m %s %d\n", $reverse[0][0], $reverse[0][1] >= 0 ? '+' : '-', abs($reverse[0][1]));

// Part 2
for ($first = $reverse[0][1]; $first < 0; $first += $reverse[0][0]);
printf("part 2: %d\n", $first);


function display(array $x) {
  echo("\n");
  echo("-----+---------+-----+---------+---------+---------+---------+\n");
  echo(" i-1 |         | -s  |         |    a    |    b    |    c    |\n");
  echo("   i |   c/a   |  s  |   b/a   |   c%a   |   b%a   |    a    |\n");
  echo("=====+=========+=====+=========+=========+=========+=========+\n");
  foreach ($x as $i => $r) {
    printf(" %3d | %7d | %3d | %7d | %7d | %7d | %7d |", $i, $r[0], $r[1], $r[2], $r[3], $r[4], $r[5]);
   if ($i >= 0) {
      $s = $r[1] > 0 ? '+': '-';
      printf("  x%d = %d * x%d %s %d + (%d * x%d %s %d) / %d", $i, $r[0], $i + 1, $s, $r[2], $r[3], $i + 1, $s, $r[4], $r[5]);
    }
    echo("\n");
  }
  echo("-----+---------+-----+---------+---------+---------+---------+\n");
}

function display_rev(array $x, int $n) {
  echo("\n");
  echo("-----+---------+---------+\n");
  echo("   i |    a    |    b    |\n");
  echo("=====+=========+=========+\n");
  for ($i = 0; $i <= $n; $i ++) {
    $r = $x[$i];
    printf(" %3d | %7d | %7d |\n", $i, $r[0], $r[1]);
  }
  echo("-----+---------+---------+\n");
}


// That's all, folks!
