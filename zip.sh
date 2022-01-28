#!/usr/bin/env bash

# Update the Templates Module

rm -f zip.lst
rm -f files_package.zip

#find ./application/config/ -name "config.php" >> zip.lst
#find ./application/modules/menu -name "*.*" >> zip.lst
find ./application/modules/widget_member_stats/ -name "*.*" >> zip.lst
#find ./application/modules/templates -name "*.*" >> zip.lst
#find ./application/modules/admin/controllers -name "*.php" >> zip.lst
find ./assets/templates/hfs1/bower_components/chart.js/ -name "*.*" >> zip.lst

#find ./assets/templates/ -name "*.*" >> zip.lst

cat zip.lst | zip -@ files_package.zip
