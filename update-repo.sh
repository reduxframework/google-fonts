#!/usr/bin/env bash

if [[ "$TRAVIS_PULL_REQUEST" == "false" && "$TRAVIS_JOB_NUMBER" == *.1 ]]; then
git add google_fonts.json
git commit -m "Updated fonts"
git push
fi