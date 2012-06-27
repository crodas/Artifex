test: test53 test54

test53:
	phpunit --stop-on-failure --coverage-html coverage || exit -1

test54:
	/usr/local/php-5.4/bin/php /usr/bin/phpunit --stop-on-failure || exit -1

grammar:
	phplemon lib/Artifex/Parser.y

autoloader:
	php ../Autoloader/autoloader.phar generate --library lib/Artifex/autoload.php lib/
