#!/usr/bin/env bash

DOWN_SUCCESS=0;
DOWN_FAILED=0;
DOWN_SKIP=0;

for file in $*; do
    down_list=($(cat "$file"));
    for line in ${down_list[@]}; do
        REMOVE_PREFIX=${line##*highlight.js/};
        DIR_PATH=${REMOVE_PREFIX%/*};
        if [ ! -e "$REMOVE_PREFIX" ] || [ "$(ls -l $REMOVE_PREFIX | awk '{print $5}')" == "0" ]; then
            mkdir -p "$DIR_PATH";
            wget -c --no-check-certificate "$line" -O "$REMOVE_PREFIX";
            
            if [ "$(ls -l $REMOVE_PREFIX | awk '{print $5}')" == "0" ]; then
                let DOWN_FAILED=$DOWN_FAILED+1;
            else
                let DOWN_SUCCESS=$DOWN_SUCCESS+1;
            fi
        else
            echo "$REMOVE_PREFIX found, skip download.";
            let DOWN_SKIP=$DOWN_SKIP+1;
        fi
    done
done

echo "download from $file, jobs done. success $DOWN_SUCCESS, failed $DOWN_FAILED, skip $DOWN_SKIP";
