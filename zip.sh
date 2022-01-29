#!/usr/bin/env bash

# Update the Templates Module

rm -f zip.lst
rm -f files_package.zip

find ./app -name "*.*" >> zip.lst
find ./config -name "*.*" >> zip.lst
#
find ./public/css -name "*.*" >> zip.lst
find ./public/js -name "*.*" >> zip.lst
find ./public/light-bootstrap -name "*.*" >> zip.lst
#
find ./routes -name "*.*" >> zip.lst
#

find ./ -maxdepth 1 -type f -name "composer.*" >> zip.lst
cat zip.lst | zip -@ files_package.zip
