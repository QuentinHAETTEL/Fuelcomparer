install:
	composer install
	npm install
	npm run build

cc:
	bin/console c:c

wp-watch:
	npm run watch

test:
	vendor/bin/phpstan analyse -c tests/phpstan.neon