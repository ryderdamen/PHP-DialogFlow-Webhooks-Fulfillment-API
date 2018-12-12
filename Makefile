.PHONY: test
test:
	@ echo "Running Tests"
	docker run -v $$(pwd):/code/ phpunit/phpunit:latest /code/tests/ --verbose

ifeq (example,$(firstword $(MAKECMDGOALS)))
  RUN_ARGS := $(wordlist 2,$(words $(MAKECMDGOALS)),$(MAKECMDGOALS))
  $(eval $(RUN_ARGS):;@:)
endif

.PHONY: example
example:
	docker run -v $$(pwd):/code/ php:latest  php /code/examples/$(RUN_ARGS)
	@printf "\n"
