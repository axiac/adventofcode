#
# Day 1: Report Repair
#
# @link https://adventofcode.com/2020/day/1
#

#
# Run the program as:
#   jq -fsr 1-program.jq < 1-input
#
# Explanation of the command line options:
#   -f = load the script from the file `1-program.jq` (otherwise it expects it to find the script after the options)
#   -s = read the entire input stream into a large array (each line is an entry) and run the filter(s) only once
#   -r = display raw output (i.e. do not encode the computed string as JSON but display its value)
#
#
# This code is small but very slow.
# It needs 48 seconds to find the sums of three numbers on the given input (200 numbers).
# (but it finds the solution for part 1 in less than 1 second)
#

(
  [                         # ---------------------------------------------------------------+
    [.,.]                   # duplicate the input list, put the copies into an array         |
    | combinations          # all combinations of elements of the two lists                  |
    | select(add == 2020)   # keep only the combinations that sum up to 2020                 |
    | .[0]*.[1]             # multiply the values of the combination                         |
  ]                         # wrap the products into an array; this is needed for `unique` <-+
  | unique                  # as it says, removes the duplicates generated by permutations
  | join(" ")               # unwrap the results
  | "part 1: \(.)"          # produce a nice output
)
,                           # send the same input to both filters separated by the comma
(                           # the same as above but with three numbers on each combination
  [
    [.,.,.]
    | combinations
    | select(add == 2020)
    | .[0]*.[1]*.[2]
  ]
  | unique
  | join(" ")
  | "part 2: \(.)"
)


# That's all, folks!