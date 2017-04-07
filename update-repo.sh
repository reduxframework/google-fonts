#!/bin/sh

setup_git() {
  git config --global user.email "travis@travis-ci.org"
  git config --global user.name "Travis CI"
}

commit_font_json() {
  git add google_fonts.json
  git commit --message "Travis build: $TRAVIS_BUILD_NUMBER"
}

upload_files() {
  git remote add origin-fonts https://${GH_TOKEN}@github.com/reduxframework/google-fonts.git > /dev/null 2>&1
  git push --set-upstream origin-fonts master
}

setup_git
commit_font_json
upload_files

echo "Fonts updated";
