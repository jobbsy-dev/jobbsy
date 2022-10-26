SYMFONY_CLI=symfony
PHP_CS_FIXER=php ./tools/php-cs-fixer/vendor/bin/php-cs-fixer
PHP_STAN=php ./vendor/bin/phpstan
CONSOLE=bin/console
COMPOSER=composer
PHPUNIT=php ./bin/phpunit

.DEFAULT_GOAL := help
.PHONY: help phpcsfix fixtures

help:
	@grep -E '(^[a-zA-Z_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'

lint:								## Lint the code
	$(SYMFONY_CLI) console lint:yaml config --parse-tags
	$(SYMFONY_CLI) console lint:twig templates --env=prod
	$(SYMFONY_CLI) console lint:xliff translations
	$(SYMFONY_CLI) console lint:container --no-debug

validate: lint						## Validate the code, check composer.json and check security
	$(SYMFONY_CLI) $(COMPOSER) validate --strict
	$(SYMFONY_CLI) $(COMPOSER) audit

migrate:							## Run doctrine migrations
	$(SYMFONY_CLI) console doctrine:migration:migrate

phpcsfix: tools-vendor				## Run cs fixer
	$(SYMFONY_CLI) $(PHP_CS_FIXER) fix

phpstan:							## Run PHPStan
	$(SYMFONY_CLI) $(PHP_STAN) analyse --level 1 src/

fixtures:							## Load fixtures test env
	$(SYMFONY_CLI) console doctrine:fixtures:load --env=test --no-interaction

test:								## Run tests
	$(SYMFONY_CLI) $(PHPUNIT)

deploy:								## Deploy
	ansible-playbook --vault-password-file=.ansible/.vault_pass .ansible/deploy.yml -i .ansible/inventory.yml

decrypt-vault:						## Decrypt Ansible vault
	ansible-vault decrypt .ansible/vault.yml --vault-password-file .ansible/.vault_pass

encrypt-vault:						## Encrypt Ansible vault
	ansible-vault encrypt .ansible/vault.yml --vault-password-file .ansible/.vault_pass

bootstrap-tests:					## Bootstrap tests
	$(SYMFONY_CLI) console d:d:c --env=test
	$(SYMFONY_CLI) console d:m:m --env=test --no-interaction

# Rules from files

tools/php-cs-fixer/vendor/composer/installed.php: composer.lock
	$(SYMFONY_CLI) $(COMPOSER) install --working-dir=./tools/php-cs-fixer

tools-vendor: tools/php-cs-fixer/vendor/composer/installed.php
