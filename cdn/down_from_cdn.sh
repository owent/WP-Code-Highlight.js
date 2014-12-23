#!/bin/sh

for file in $*; do
    cat "$file" | while read line 
    do
        REMOVE_PREFIX=${line##*highlight.js/};
        DIR_PATH=${REMOVE_PREFIX%/*};
        if [ ! -e "$REMOVE_PREFIX" ]; then
            mkdir -p "$DIR_PATH";
	        wget -c "$line" -O "$REMOVE_PREFIX";
        fi
    done
done
