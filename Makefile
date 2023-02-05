install:
	composer install
	npm install
	npm run build

cc:
	bin/console c:c

migrate:
	bin/console doctrine:migrations:migrate --no-interaction

wp-watch:
	npm run watch

test:
	vendor/bin/phpstan analyse -c tests/phpstan.neon
	vendor/bin/phpunit tests/ -c tests/phpunit.xml.dist --coverage-clover phpunit.coverage.xml