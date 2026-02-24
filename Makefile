.PHONY: tests

tests:
	docker exec -e APP_ENV=test -e APP_DEBUG=1 -it zendash-php-1 php bin/console --env=test doctrine:database:drop --force --if-exists
	docker exec -e APP_ENV=test -e APP_DEBUG=1 -it zendash-php-1 php bin/console --env=test doctrine:database:create
	docker exec -e APP_ENV=test -e APP_DEBUG=1 -it zendash-php-1 php bin/console --env=test doctrine:migrations:migrate --no-interaction
	docker exec -e APP_ENV=test -e APP_DEBUG=1 -it zendash-php-1 bin/phpunit
