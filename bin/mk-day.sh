#!/usr/bin/env bash

set -e

# Extract arguments
if [ $# -lt 1 ]; then echo "Usage: ${0##*/} day [year]" 1>&2; exit 1; fi
DAY=$1
YEAR=$(date +%Y)
if [ $# -ge 2 ]; then YEAR=$2; fi

# Start from the parent directory of this file
if ! cd "${0%/*}" || ! cd ..; then echo "Cannot change directory to the project directory" 1>&2; exit 2; fi

# Get problem title
TMP_FILE=$(mktemp -t adventofcode)
URL=https://adventofcode.com/${YEAR}/day/${DAY}
STATUS=$(curl --silent "$URL" -o "$TMP_FILE" -w '%{http_code}')
if [ "$STATUS" != 200 ]; then echo "Problem not found. Check the complete output in '$TMP_FILE'"; exit 2; fi

# Extract the title
TITLE=$(grep '<article class="day-desc">' "$TMP_FILE" | sed -E 's#^.*<article class="day-desc"><h2>--- Day [0-9]*: (.*) ---</h2>.*$#\1#')

# Extract the first code block and store it in the example file; it might be wrong
EXAMPLE=$(sed -n '/<pre><code>/,/<\/code><\/pre>/p;/<\/code><\/pre>/q' "$TMP_FILE" | sed 's/<pre><code>//;s#</code></pre>##')

# Get the input
INPUT=$(curl --silent --cookie "$(< tmp/cookie)" "$URL/input")

# Create the directory
DIRNAME=$(printf "%s/day-%02d" "$YEAR" "$DAY")
mkdir -pv "$DIRNAME"

# Create the files
touch "$DIRNAME/$DAY-"{program.php,example,input,output}
# Generate the program
sed "s#{day}#${DAY}#g;s#{year}#${YEAR}#g;s#{title}#${TITLE}#g" < ./template/program.php > "$DIRNAME/$DAY-program.php"
# Save the example (it might be wrong)
echo "$EXAMPLE" > "$DIRNAME/$DAY-example"
# Save the input; it might be an error message if the cookie is incorrect or missing
echo "$INPUT" > "$DIRNAME/$DAY-input"

# Show the generated files
ls -l "$DIRNAME"

rm -f "$TMP_FILE"


# That's all, folks!
