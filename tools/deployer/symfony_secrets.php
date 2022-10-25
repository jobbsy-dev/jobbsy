<?php
/*

## Configuration

- **symfony_secrets/env** *(optional)*: define environment value.

## Usage

```php
after('deploy:vendors', 'symfony_secrets:decrypt_to_local');
after('symfony_secrets:decrypt_to_local', 'symfony_secrets:dump_env');
```

## Official documentation

See [How to Keep Sensitive Information Secret](https://symfony.com/doc/current/configuration/secrets.html)

 */
namespace Deployer;

set('symfony_secrets/env', 'prod');

desc('Decrypt all secrets and stores them in the local vault');
task('symfony_secrets:decrypt_to_local', function () {
    run("cd {{release_or_current_path}} && {{bin/console}} secrets:decrypt-to-local --force --env={{symfony_secrets/env}}");
});

desc('Composer dump env');
task('symfony_secrets:dump_env', function () {
   run("cd {{release_or_current_path}} && {{bin/composer}} dump-env {{symfony_secrets/env}}");
});
