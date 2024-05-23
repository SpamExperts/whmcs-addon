#!/bin/bash
if [ -f ./latest.tar.gz ]; then
    rm ./latest.tar.gz
fi

tar -I 'gzip -n' -cf ./latest.tar.gz --owner=0 --group=0 ./modules