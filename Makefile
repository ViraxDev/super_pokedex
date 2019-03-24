.PHONY: install server help initialize
# Commande à lancer par défaut si on lance que make
.DEFAULT_GOAL = help

NO_COLOR=\033[39m
OK_COLOR=\033[92m
ERROR_COLOR=\033[31m
WARN_COLOR=\033[33m

OK_STRING=$(OK_COLOR)[OK]$(OK_COLOR)😀$(NO_COLOR)\n
ERROR_STRING=$(ERROR_COLOR)[ERRORS]$(ERROR_COLOR)$(NO_COLOR)\n
WARN_STRING=$(WARN_COLOR)[WARNINGS]$(WARN_COLOR)$(NO_COLOR)\n

vendor: composer.json ## Install le dossier vendor via composer
	composer install

composer.lock: composer.json
	composer update

install: vendor composer.lock

server: install ## Lance le serveur interne de PHP
	php bin/console server:run

initialize: composer.lock vendor ## Va initialiser le projet. Utile lorsqu'on démarre le projet pour la 1ère fois
	php bin/console doctrine:fixture:load
	php bin/console server:run

help:
	@grep -E '(^[a-zA-Z_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[32m%-10s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'