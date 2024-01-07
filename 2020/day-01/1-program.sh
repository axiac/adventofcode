#!/usr/bin/env bash
#
# Day 1: Report Repair
#
# @link https://adventofcode.com/2020/day/1
#

#
# Run the program as:
#   ./1-program.sh < 1-input
#
# It needs about 20 seconds to process the input.
# It finds the first solution for part 1 in 7 seconds, the first solution for part 2 in about 9 seconds
# but it searches for all solutions.
#

numbers=()
while read -r x; do
  for (( j=0; j<${#numbers[*]}; j ++ )); do
    y=${numbers[j]}
    if [[ $((x+y)) == 2020 ]]; then
      echo "part 1: $((x*y))"           #  numbers: $y, $x
    fi

    for (( k=0; k<j; k++ )); do
      z=${numbers[k]}
      if [[ $((x+y+z)) == 2020 ]]; then
        echo "part 2: $((x*y*z))"       # numbers: $z, $y, $x
        exit
      fi
    done
  done
  numbers[${#numbers[*]}]=$x
done


# That's all, folks!
