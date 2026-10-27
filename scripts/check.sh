#!/bin/sh
set -eu
cd "$(dirname "$0")/.."
PHP_BINARY=${PHP_BINARY:-php}
export PHP_BINARY
python3 -m unittest discover -s tests -v
for test in style render native-adapters authorization form-labels search-result author-render photo-library photo-form comments reactions motion motion-integration motion-delivery motion-transcoder motion-replacement; do "$PHP_BINARY" -n "tests/$test.php"; done
find modules -name '*.php' -exec "$PHP_BINARY" -n -l {} \;
node --check modules/newton/my_una_styled_posts/js/composer.js
