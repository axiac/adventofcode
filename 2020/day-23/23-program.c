/**
 * Day 23: Crab Cups
 *
 * @link https://adventofcode.com/2020/day/23
 */

/**
 * How to execute the program.
 * Compile it:
 *   cc -o 23-program 23-program.c
 *
 * Part 1, test input:
 *   23-program 1 389125467 10
 *   23-program 1 389125467
 * Part 1, the real input:
 *   23-program 1 739862541
 * Part 2, test input:
 *   23-program 2 389125467
 * Part 2, the real input:
 *   23-program 2 739862541
 */

#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <time.h>

typedef struct _Cup {
  long label;
  struct _Cup *next;
} Cup;

void render(Cup *head);

/**
 * The expected arguments:
 *  - argv[1] - the step: 1 or 2
 *  - argv[2] - the input configuration (a string of unique digits from 1 to 9)
 *  - argv[3] - the number of nbIterations (optional)
 */
int main(int argc, char *argv[]) {
  // The current cup
  Cup *current = NULL;
  // The labels of the picked up cups and a pointer to the first of the three picked up cups
  long removedLabels[3];
  Cup *removed = NULL;
  // The destination label and a pointer to its cup
  long destLabel = 0;
  Cup *dest = NULL;

  // Working variables: the most recently created node, the current node, various indices
  Cup *last = NULL;
  Cup *node = NULL;
  long i = 0, r = 0;
  // The first cup; it is used only for debug (display of the list); the current cup can be used instead as well
  Cup *head = NULL;

  // Index the structures by their label
  Cup **table = NULL;


  // Validate the number of arguments
  if (argc < 3) {
    fputs("Insufficient number of arguments.\nExpected at least the part number (1 or 2) and the initial configuration (a 9-digit number with unique digits from 1 to 9).\nCannot continue.\n", stderr);
    exit(1);
  }

  // Default values for part 2
  long nbCups = 1000000;
  long nbIterations = 10000000;

  // Parse the arguments
  int part = atoi(argv[1]);
  if (part == 1) {
    // Default values for part 1
    nbCups = 9;
    nbIterations = 100;
  }
  // Adjust the number of iterations (both parts)
  if (argc >= 4) {
    nbIterations = atoi(argv[3]);
  }
  // The input
  char *prefix = argv[2];

  // Create the list and the table
  table = calloc(1 + nbCups, sizeof(Cup *));
  head = malloc(sizeof(Cup));
  head->label = prefix[0] - '0';
  last = head;
  table[last->label] = last;
  for (i = 1; i < strlen(prefix); i ++) {
    node = malloc(sizeof(Cup));
    node->label = prefix[i] - '0';
    last->next = node;
    last = node;
    table[last->label] = last;
  }
  for (i = strlen(prefix); i < nbCups; i ++) {
    node = malloc(sizeof(Cup));
    node->label = i + 1;
    last->next = node;
    last = node;
    table[last->label] = last;
  }
  last->next = head;

  time_t start = time(NULL);

  current = head;
  for (i = 0; i < nbIterations; i ++) {
//printf("============ %ld ===========\n", i + 1);
//render(head);
    // pick up the three cups that are immediately clockwise of the current cup.
    for (r = 0, node = current->next; r < 3; r ++, node = node->next) {
      removedLabels[r] = node->label;
    }
    // they are removed from the circle
    removed = current->next;
    current->next = node;

    // select a destination cup: the cup with a label equal to the current cup's label minus one.
    destLabel = current->label;
    do {
      destLabel = (destLabel == 1) ? nbCups : (destLabel - 1);
    } while (destLabel == removedLabels[0] || destLabel == removedLabels[1] || destLabel == removedLabels[2]);

    // find the destination cup
    dest = table[destLabel];
//printf("current=%ld, removed=[%ld, %ld, %ld], destination=%ld\n", current->label, removedLabels[0], removedLabels[1], removedLabels[2], dest->label);

    // place the cups it just picked up so that they are immediately clockwise of the destination cup.
    // they keep the same order as when they were picked up.
    removed->next->next->next = dest->next;
    dest->next = removed;

    // select a new current cup: the cup which is immediately clockwise of the current cup.
    current = current->next;
  }

  // final
//render(head);


  if (part == 1) {
    printf("part 1: ");
    node = table[1]->next;
    do {
      printf("%ld", node->label);
      node = node->next;
    } while (node != table[1]);
    printf("\n");
  } else {
    // Part 2
    long long next1 = table[1]->next->label;
    long long next2 = table[1]->next->next->label;
    printf("part 2: %lld\n", next1 * next2);
  }

  return 0;
}


void render(Cup *head) {
  Cup *node = NULL;

  printf("%ld", head->label);
  long i = 1;
  // Display only the first 20 values
  for (node = head->next; node != head && i < 20; node = node->next, i ++) {
    printf(", %ld", node->label);
  }
  puts("");
}


// That's all, folks!
