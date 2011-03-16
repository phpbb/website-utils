#!/bin/bash
# create the docs folders 30x, 31x
# on area51.phpbb.com/docs
rm -rf 30x 31x
mkdir 30x 31x
git clone https://github.com/phpbb/phpbb3.git
cd phpbb3
git checkout master
cp -r phpBB/docs/* ../30x/
git checkout develop
cp -r phpBB/docs/* ../31x/
cd ..
rm -rf phpbb3
