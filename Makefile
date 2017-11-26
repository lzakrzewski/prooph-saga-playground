playground:
	docker run -it --rm \
		--name prooph-saga-playground \
		-v "${PWD}":/prooph-saga-playground \
		-w /prooph-saga-playground \
		php:7.1-cli bin/console prooph:saga:playground