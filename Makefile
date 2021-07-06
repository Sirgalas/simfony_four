include .env

up: docker-up
down: docker-down
restart: docker-down docker-up
restart-clear: docker-down-clear docker-build docker-up
init: docker-down-clear clear docker-build docker-up oauth-keys wait-db migrations fixtures assets-install composer-install ready
asset-init: assets-install assets-watch
app-init: composer-install assets-install migrations fixtures

clear:
	docker run --rm -v ${pwd}/app --workdir=/app alpine rm -f .ready

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
	docker-compose run --rm node npm run watc

ready:
	docker run --rm -v ${pwd}/app --workdir=/app alpine touch .ready

composer-install:
	docker-compose run --rm php-cli composer install

composer-update:
	docker-compose run --rm php-cli composer update

composer-need-update:
	docker-compose run --rm php-cli composer outdated

oauth-keys:
	docker-compose run --rm php-cli mkdir -p var/oauth
	docker-compose run --rm php-cli openssl genrsa -out var/oauth/private.key 2048
	docker-compose run --rm php-cli openssl rsa -in var/oauth/private.key -pubout -out var/oauth/public.key
	docker-compose run --rm php-cli chmod 644 var/oauth/private.key var/oauth/public.key

wait-db:
	until docker-compose exec -T db pg_isready --timeout=0 --dbname=app ; do sleep 1 ; done

migrations:
	docker-compose run --rm php-cli php bin/console doctrine:migrations:migrate --no-interaction

fixtures:
	docker-compose run --rm php-cli php bin/console doctrine:fixtures:load --no-interaction

add-controller:
	docker-compose run --rm php-cli php bin/console make:controller

add-command:
	docker-compose run --rm php-cli php bin/console make:command

add-entity:
	docker-compose run --rm php-cli php bin/console make:entity

add-crud:
	docker-compose run --rm php-cli php bin/console make:crud

cli:
	docker-compose run --rm php-cli php bin/console

cache-clear:
	docker-compose run --rm php-cli php bin/console cache:clear

migrate:
	docker-compose run --rm php-cli php bin/console doctrine:migrations:migrate --no-interaction

diff:
	docker-compose run --rm php-cli php bin/console doctrine:migrations:diff

test:
	docker-compose run --rm php-cli php ./vendor/bin/phpunit

test-unit:
	docker-compose run --rm php-cli php ./vendor/bin/phpunit --testsuite=unit


build-production:
	docker build --pull --file=./docker/nginx.docker --tag ${REGISTRY_ADDRESS}/nginx:${IMAGE_TAG} manager
	docker build --pull --file=./docker/php-fpm.docker --tag ${REGISTRY_ADDRESS}/php-fpm:${IMAGE_TAG} manager
	docker build --pull --file=./docker/php-cli.docker --tag ${REGISTRY_ADDRESS}/php-cli:${IMAGE_TAG} manager
	docker build --pull --file=./docker/production/redis.docker --tag ${REGISTRY_ADDRESS}/redis:${IMAGE_TAG} manager
	docker build --pull --file=centrifugo/docker/production/centrifugo.docker --tag ${REGISTRY_ADDRESS}/centrifugo:${IMAGE_TAG} centrifugo

push-production:
	docker push ${REGISTRY_ADDRESS}/nginx:${IMAGE_TAG}
	docker push ${REGISTRY_ADDRESS}/php-fpm:${IMAGE_TAG}
	docker push ${REGISTRY_ADDRESS}/php-cli:${IMAGE_TAG}
	docker push ${REGISTRY_ADDRESS}/redis:${IMAGE_TAG}
	docker push ${REGISTRY_ADDRESS}/centrifugo:${IMAGE_TAG}

deploy-production:
	ssh -o ${PRODUCTION_HOST} -p ${PRODUCTION_PORT} 'rm -rf docker-compose.yml .env'
	scp -o ${PRODUCTION_PORT} docker-compose-production.yml ${PRODUCTION_HOST}:docker-compose.yml
	ssh -o ${PRODUCTION_HOST} -p ${PRODUCTION_PORT} 'echo "REGISTRY_ADDRESS=${REGISTRY_ADDRESS}" >> .env'
	ssh -o ${PRODUCTION_HOST} -p ${PRODUCTION_PORT} 'echo "IMAGE_TAG=${IMAGE_TAG}" >> .env'
	ssh -o StrictHostKeyChecking=no ${PRODUCTION_HOST} -p ${PRODUCTION_PORT} 'echo "CENTRIFUGO_WS_HOST=${CENTRIFUGO_WS_HOST}" >> .env'
	ssh -o StrictHostKeyChecking=no ${PRODUCTION_HOST} -p ${PRODUCTION_PORT} 'echo "CENTRIFUGO_API_KEY=${CENTRIFUGO_API_KEY}" >> .env'
	ssh -o StrictHostKeyChecking=no ${PRODUCTION_HOST} -p ${PRODUCTION_PORT} 'echo "CENTRIFUGO_SECRET=${CENTRIFUGO_SECRET}" >> .env'
	ssh -o ${PRODUCTION_HOST} -p ${PRODUCTION_PORT} 'docker-compose pull'
	ssh -o ${PRODUCTION_HOST} -p ${PRODUCTION_PORT} 'docker-compose --build -d'
	ssh -o StrictHostKeyChecking=no ${PRODUCTION_HOST} -p ${PRODUCTION_PORT} 'echo "REDIS_PASSWORD=${REDIS_PASSWORD}" >> .env'
	ssh -o StrictHostKeyChecking=no ${PRODUCTION_HOST} -p ${PRODUCTION_PORT} 'echo "MANAGER_MAILER_URL=${MANAGER_MAILER_URL}" >> .env'
	ssh -o StrictHostKeyChecking=no ${PRODUCTION_HOST} -p ${PRODUCTION_PORT} 'echo "OAUTH_FACEBOOK_SECRET=${OAUTH_FACEBOOK_SECRET}" >> .env'
	ssh -o StrictHostKeyChecking=no ${PRODUCTION_HOST} -p ${PRODUCTION_PORT} 'echo "STORAGE_FTP_HOST=${STORAGE_FTP_HOST}" >> .env'
	ssh -o StrictHostKeyChecking=no ${PRODUCTION_HOST} -p ${PRODUCTION_PORT} 'echo "STORAGE_FTP_USERNAME=${STORAGE_FTP_USERNAME}" >> .env'
	ssh -o StrictHostKeyChecking=no ${PRODUCTION_HOST} -p ${PRODUCTION_PORT} 'echo "STORAGE_BASE_URL=${STORAGE_BASE_URL}" >> .env'
	ssh -o StrictHostKeyChecking=no ${PRODUCTION_HOST} -p ${PRODUCTION_PORT} 'echo "STORAGE_FTP_PASSWORD=${STORAGE_FTP_PASSWORD}" >> .env'
