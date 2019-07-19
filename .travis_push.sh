#!/bin/sh
# Credit: https://gist.github.com/willprice/e07efd73fb7f13f917ea
# https://www.vinaygopinath.me/blog/tech/commit-to-master-branch-on-github-using-travis-ci/

setup_git() {
  git config --global user.email "travis@travis-ci.org"
  git config --global user.name "Travis CI"
}

commit_fonts_file() {
  git checkout master
  # Current month and year, e.g: Apr 2018
  dateAndMonth=`date "+%b %Y"`
  # Stage the modified files in dist/output
  git add -f google_fonts.json
  # Create a new commit with a custom build message
  # with "[skip ci]" to avoid a build loop
  # and Travis build number for reference
  git commit -m "Travis update: $dateAndMonth (Build $TRAVIS_BUILD_NUMBER)" -m "[skip ci]"
}

upload_files() {
  # Remove existing "origin"
  git remote rm origin
  # Add new "origin" with access token in the git URL for authentication
  git remote add origin https://dovy:${NEW_GH_TOKEN}@github.com/reduxframework/google-fonts.git > /dev/null 2>&1
  git push origin master --quiet #  > /dev/null 2>&1
}

setup_git

commit_fonts_file

# Attempt to commit to git only if "git commit" succeeded
if [ $? -eq 0 ]; then
  echo "Google fonts has updated, uploading to GitHub"
  upload_files
else
  echo "No changes in Google Fonts. Nothing to do"
fi
