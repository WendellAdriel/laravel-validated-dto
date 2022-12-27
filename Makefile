# Well documented Makefiles
DEFAULT_GOAL := help
help:
	@awk 'BEGIN {FS = ":.*##"; printf "\nUsage:\n  make \033[36m<target>\033[0m\n"} /^[a-zA-Z0-9_-]+:.*?##/ { printf "  \033[36m%-40s\033[0m %s\n", $$1, $$2 } /^##@/ { printf "\n\033[1m%s\033[0m\n", substr($$0, 5) } ' $(MAKEFILE_LIST)

##@ [Docker]
start: ## Spin up the container
	docker-compose up -d

stop: ## Shut down the containers
	docker-compose down

build: ## Build all docker images
	docker-compose build

##@ [Application]
composer: ## Run composer commands. Specify the command e.g. via "make composer ARGS="install|update|require <dependency>"
	docker-compose run app composer $(ARGS)

lint: ## Run the Linter
	docker-compose run app ./vendor/bin/pint

test: ## Run the tests. Apply arguments via make test ARGS="--init"
	docker-compose run app ./vendor/bin/pest $(ARGS)

prepare: ## Run the Linter
	docker-compose run app ./vendor/bin/pint && docker-compose run app ./vendor/bin/pest
