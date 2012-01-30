# Makefile
#
#
ENV := dev
VARSION := v0.1.1

shell:
	phpsh app/shell.php

cc:
	php app/console cache:clear --env=$(ENV)

warmup:
	php app/console cache:warmup --env=$(ENV)

htaccess:
	php app/console router:dump-apache

deletelogs:
	rm app/logs/apache.log app/logs/access.log
	touch app/logs/apache.log app/logs/access.log

database:
	php app/console doctrine:database:drop --force --env=$(ENV)
	php app/console doctrine:database:create --env=$(ENV)
	php app/console doctrine:schema:create --env=$(ENV)

fixtures:
	php app/console doctrine:fixtures:load --env=$(ENV)

assets:
	app/console assets:install --symlink web
	app/console assetic:dump

permissions:
	setfacl -m default:group:www-data:rwX app/cache/
	setfacl -m default:user:$(USER):rwX app/cache/
	setfacl -m group:www-data:rwX app/cache/
	setfacl -m user:$(USER):rwX app/cache/
	setfacl -m default:user:$(USER):rwX app/logs/
	setfacl -m default:group:www-data:rwX app/logs/
	setfacl -m group:www-data:rwX app/logs/
	setfacl -m user:$(USER):rwX app/logs/
	rm -rf app/cache/* app/logs/*

test:
	phpunit -c app/ src/Storm

vendors:
	bin/vendors install
	make assets

build_bootstrap:
	php vendor/bundles/Sensio/Bundle/DistributionBundle/Resources/bin/build_bootstrap.php

reinstall:
	git reset --hard
	git pull
	make vendors
	make cc

ctags:
	ctags -R --languages=php .

check_cs:
	php bin/check_cs fix

install:
	git reset --hard $(VERSION)
	make vendors
	ENV=prod make database -e
	make deploy
	php app/console doctrine:fixtures:load --fixtures=src/Storm/AguilaBundle/DataFixtures/ORM/Prod --env=prod

deploy:
	git pull
	git reset --hard $(VERSION)
	ENV=prod make cc -e
	ENV=prod make warmup -e
	ENV=prod make assets -e
