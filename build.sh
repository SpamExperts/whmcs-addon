#!/bin/bash
if [ -f ./latest.tar.gz ]; then
    rm ./latest.tar.gz
fi

tar czf ./latest.tar.gz * --exclude=build.sh --exclude=trigger_build.sh --exclude=.svn --exclude=*.tar.gz
