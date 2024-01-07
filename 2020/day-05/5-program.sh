#!/usr/bin/env bash
#
# Day 5: Binary Boarding
#
# @link https://adventofcode.com/2020/day/5
#

#
# Run the program as:
#   ./5-program.sh 5-example
# or
#   ./5-program.sh 5-input
#


# If F=0 and B=1, L=0 and R=1 then each seat number is the binary representation of its ID
# Replace F and L with 0, B and R with 1 to get the seat ID represented in binary
# Prefix it with `2#` to tell `$(())` about its base
sorted_ids=$(
  tr FBLR 0101 < "$1" |
  sort |
  while read -r id; do
    echo $((2#$id))
  done
)

# The highest seat ID is the last one from the sorted list
max_seat_id=$(echo "$sorted_ids" | tail -n 1)
echo "part 1: $max_seat_id"

# Get the lowest seat ID and create a range of consecutive IDs
min_seat_id=$(echo "$sorted_ids" | head -n 1)
range_ids=$(for i in $(eval "echo {$min_seat_id..$max_seat_id}"); do echo "$i"; done)

# Compare the range of all consecutive IDs with the list of input sorted IDs
# Juggle with the format of diff's output to make it display directly the desired result :-)
diff --unchanged-line-format='' --old-line-format='part 2: %L' <(echo "$range_ids") <(echo "$sorted_ids")


# That's all, folks!
