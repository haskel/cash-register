.PHONY: up down run-tests run-tests-debug test php-console-zsh php-console-bash build-worker-image push-worker-image update-worker-image build-api-image push-api-image update-api-image deploy-api deploy-worker

# Check mandatory tools
executables = docker docker-compose
K := $(foreach exec, $(executables), $(if $(shell which $(exec)),imposible_string_086676774066,$(error "'$(exec)' is not installed. Install it or check $$PATH")))

# Common variables
env 		 = dev
http_port 	 = 8001
project_name = cash-register

# Docker vars
php_docker_compose_service 			= php
php_docker_service_container_prefix = service.cash-register.php
php_docker_service_container 		= $(php_docker_service_container_prefix).$(env)

# Set env variables. It is useful to control docker compose variables
export APP_HTTP_PORT = $(http_port)
export COMPOSE_PROJECT_NAME = $(project_name)-$(env)
export DOCKER_BUILDKIT=1

DIR =
CFLAGS = -c .

# Up and down containers
up:
	@docker-compose -f docker-compose.$(env).yml up --build -d

down:
	@docker-compose -f docker-compose.$(env).yml down

# Database migrations
migrate:
	@docker exec -e XDEBUG_MODE=off $(php_docker_service_container_prefix).dev php /var/www/app/bin/console doctrine:migrations:migrate --no-interaction


# Tests
run-tests:
	@docker exec -e XDEBUG_MODE=off -e APP_ENV=test $(php_docker_service_container_prefix).dev php /var/www/app/bin/phpunit --no-coverage /var/www/app/tests

run-tests-debug:
	@docker exec -e APP_ENV=test $(php_docker_service_container) php /var/www/app/bin/phpunit --no-coverage /var/www/app/tests

test: run-tests

# Run terminal in containers
php-console-zsh:
	@docker exec -it $(php_docker_service_container) zsh

php-console-bash:
	@docker exec -it $(php_docker_service_container) bash

# Code quality tools
psalm:
	@docker exec -e XDEBUG_MODE=off $(php_docker_service_container_prefix).dev php /var/www/app/vendor/bin/psalm

phpstan:
	@docker exec -e XDEBUG_MODE=off $(php_docker_service_container_prefix).dev php /var/www/app/vendor/bin/phpstan