#!/bin/sh

for i in ru; do
  cd $i
  echo -n "Indexing ${i}... "
  rm -rf JavaHelpSearch
  java -jar ../../../lib/jhindexer.jar .
  cd ..
  echo "ok."
done;

