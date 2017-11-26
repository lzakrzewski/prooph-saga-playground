composer:
	docker run -it --rm \
	-v "${PWD}":/prooph-saga-playground \
	-w /prooph-saga-playground \
	prooph/composer:7.1 install

playground: composer
	docker run -it --rm \
		--name prooph-saga-playground \
		-v "${PWD}":/prooph-saga-playground \
		-w /prooph-saga-playground \
		php:7.1-cli bin/console prooph:saga:playground