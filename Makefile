.PHONY: test
test:
	@ echo "Running Tests"
	docker run -v $$(pwd):/php_webhooks/ phpunit/phpunit:latest /php_webhooks/tests/ --verbose
