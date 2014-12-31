PSR Police
==========

A handy tool to add a PHP syntax check and PSR-2 enforcement *git pre-commit hook* to your project.

Install it globally like:

```sh
phpcomposer global require tacone/psr-police
```

Then make sure `~/.composer/vendor/bin` is in your PATH.

From now on you can enforce PSR-2 on every commit by calling PSR Police (once per project):

```
cd /var/www/myproject
psr-police
```

