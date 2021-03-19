#!/bin/bash

#DOCS
#https://github.com/koalaman/shellcheck/wiki/SC2164
#https://github.com/koalaman/shellcheck/wiki/SC2086
#http://www.gnu.org/software/bash/manual/html_node/Conditional-Constructs.html#index-case

BASEDIR=$(dirname "$0")
cd "$BASEDIR/.." || exit

PURGE_AND_CREATE=true
FIXTURE_COMMAND='php bin/console doctrine:fixtures:load --no-interaction';

# N.B ;& for NOT break, ;; for break
case "$@" in
  *--append*)
    PURGE_AND_CREATE=false;
    FIXTURE_COMMAND+=" --append"
    ;&
esac

if $PURGE_AND_CREATE; then
  php bin/console doctrine:database:drop --force
  php bin/console doctrine:database:create
  php bin/console doctrine:schema:update --force
fi

eval "$FIXTURE_COMMAND"
php bin/console doctrine:database:import src/DataFixtures/sql/catastali.sql

php bin/console fos:js-routing:dump --format=json --target=public/js/fos_js_routes.json