ai-boost:
	php artisan boost:install

pint:
	./vendor/bin/pint

migrate:
	php artisan migrate
fresh:
	php artisan migrate:fresh --seed
rollback:
	php artisan migrate:rollback
