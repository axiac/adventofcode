<?php
/**
 * Day 21: Allergen Assessment
 *
 * @link https://adventofcode.com/2020/day/21
 */

/**
 * Run the program as:
 *   php 21-program.php
 * or
 *   php 21-program.php 21-example
 */

// Input file
$inputfile = dirname($argv[0]).'/21-input';
if ($argc >= 2) {
  $inputfile = $argv[1];
}

// Read the input data
$input = explode("\n", trim(file_get_contents($inputfile)));

// Map ingredients to allergens and the other way around
// Each allergen is found in exactly one ingredient
// Each ingredient contains zero or one allergen
$mapA2I = [];           // allergen => ingredient
$mapI2A = [];           // ingredient => allergen

// Parse the input
$foods = [];
foreach ($input as $line) {
  if (!preg_match('/^(.*) \(contains (.*)\)$/', $line, $m)) {
    fprintf(STDERR, "Line does not match the expected format: '%s'\n", $line);
    exit(1);
  }

  $foods[] = [
    'ingredients' => explode(' ', trim($m[1])),
    'allergens' => explode(',', str_replace(' ', '', $m[2])),
  ];
}

// The big lists of allergens and ingredients
$allIngredients = array_count_values(call_user_func_array('array_merge', array_column($foods, 'ingredients')));
$allAllergens = array_count_values(call_user_func_array('array_merge', array_column($foods, 'allergens')));


$potentialA2I = array_fill_keys(array_keys($allAllergens), array_keys($allIngredients));
//$potentialI2A = array_fill_keys(array_keys($allIngredients), array_keys($allAllergens));

do {
  $found = false;
  foreach ($foods as $food) {
    foreach ($food['allergens'] as $a) {
      if (array_key_exists($a, $mapA2I)) {
        // Already found the ingredient that contains this allergen, skip it
        continue;
      }

      // The list of ingredients that could contain this allergen
      $ingredients = array_diff(array_intersect($potentialA2I[$a], $food['ingredients']), array_keys($mapI2A));

      if (count($ingredients) === 0) {
        fprintf(STDERR, "Allergen '%s' needs to be present in more than one ingredient ([%s]^[%s])/[%s].\n",
        $a, implode(', ', $potentialA2I[$a]), implode(', ', $food['ingredients']), implode(', ', array_keys($mapI2A)));
        exit(1);
      }
      if (count($ingredients) === 1) {
        // Found the ingredient
        $i = reset($ingredients);
        $mapI2A[$i] = $a;
        $mapA2I[$a] = $i;
        $found = true;
      }
      $potentialA2I[$a] = $ingredients;
    }
  }
} while($found);

if (count($mapI2A) !== count($allAllergens)) {
  fprintf(STDERR, "Cannot solve?\n");
  exit(1);
}

// Ingredients that do not contain any allergens
$freeIngredients = array_diff(array_keys($allIngredients), array_keys($mapI2A));

$part1 = array_sum(
  array_map(
    function($i) use ($allIngredients) { return $allIngredients[$i]; },
    $freeIngredients
  )
);
printf("part 1: %d\n", $part1);


// Part 2
$dangerous = $mapA2I;
ksort($dangerous, SORT_STRING);
printf("part 2: %s\n", implode(',', $dangerous));


// That's all, folks!
