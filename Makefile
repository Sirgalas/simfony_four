include .env

up: docker-up
down: docker-down
restart: docker-down docker-up
restart-clear: docker-down docker-up
init: docker-down-clear docker-build docker-up assets-install composer-install
asset-init: assets-install assets-watch

app-init: composer-install assets-install migrations fixtures

clear:
	docker run --rm -v ${PWD}/app:/app --workdir=/app alpine rm -f .ready

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

assets-install:
	docker-compose run --rm node yarn install

assets-dev:
	docker-compose run --rm node npm run dev

assets-watch:
	docker-compose run --rm node npm run watch

ready:
	docker run --rm -v ${PWD}/manager:/app --workdir=/app alpine touch .ready

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
	docker-compose run --rm php-cli php bin/console doctrine:migrations:migrate --no-interaction

fixtures:
	docker-compose run --rm php-cli php bin/console doctrine:fixtures:load --no-interaction

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
	ssh -o StrictHostKeyChecking=no ${PRODUCTION_HOST} -p ${PRODUCTION_PORT} 'echo "MANAGER_OAUTH_FACEBOOK_SECRET=${MANAGER_OAUTH_FACEBOOK_SECRET}" >> .env'
