# Makefile
tests:
	docker exec -it zendash-php-1 php bin/console --env=test doctrine:database:drop --force --if-exists
	docker exec -it zendash-php-1 php bin/console --env=test doctrine:database:create
	docker exec -it zendash-php-1 php bin/console --env=test doctrine:migrations:migrate --no-interaction
	docker exec -it zendash-php-1 bin/phpunit
