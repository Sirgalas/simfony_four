include .env

up: docker-up
init: docker-down-clear docker-pull docker-build docker-up composer-install

docker-up:
	docker-compose up -d

docker-down:
	docker-compose down --remove-orphans

docker-down-clear:
	docker-compose down -v --remove-orphans

docker-pull:
	docker-compose pull

docker-build:
	docker-compose build

composer-install:
	docker-compose run --rm php-cli composer install

composer-update:
	docker-compose run --rm php-cli composer update

add-controller:
	docker-compose run --rm php-cli php bin/console make:controller

add-entity:
	docker-compose run --rm php-cli php bin/console make:entity

add-crud:
	docker-compose run --rm php-cli php bin/console make:crud

cli:
	docker-compose run --rm php-cli php app/bin/console.php

migrate:
	docker-compose run --rm php-cli php bin/console doctrine:migrations:migrate

diff:
	docker-compose run --rm php-cli php bin/console doctrine:migrations:diff


test:
	docker-compose run --rm php-cli php ./vendor/bin/phpunit
build-production:
	docker build --pull --file=docker/nginx.docker --tag ${REGISTRY_ADDRESS}/nginx:${IMAGE_TAG} manager
	docker build --pull --file=docker/php-fpm.docker --tag ${REGISTRY_ADDRESS}/php-fpm:${IMAGE_TAG} manager
	docker build --pull --file=docker/php-cli.docker --tag ${REGISTRY_ADDRESS}/php-cli:${IMAGE_TAG} manager

push-production:
	docker push ${REGISTRY_ADDRESS}/nginx:${IMAGE_TAG}
	docker push ${REGISTRY_ADDRESS}/php-fpm:${IMAGE_TAG}
	docker push ${REGISTRY_ADDRESS}/php-cli:${IMAGE_TAG}

deploy-production:
	ssh -o ${PRODUCTION_HOST} -p ${PRODUCTION_PORT} 'rm -rf docker-compose.yml .env'
	scp -o ${PRODUCTION_PORT} docker-compose-production.yml ${PRODUCTION_HOST}:docker-compose.yml
	ssh -o ${PRODUCTION_HOST} -p ${PRODUCTION_PORT} 'echo "REGISTRY_ADDRESS=${REGISTRY_ADDRESS}" >> .env'
	ssh -o ${PRODUCTION_HOST} -p ${PRODUCTION_PORT} 'echo "IMAGE_TAG=${IMAGE_TAG}" >> .env'
	ssh -o ${PRODUCTION_HOST} -p ${PRODUCTION_PORT} 'docker-compose pull'
	ssh -o ${PRODUCTION_HOST} -p ${PRODUCTION_PORT} 'docker-compose --build -d'
