Evidencia zamestnancov
======================

Demo aplikácia postavená na [Nette](https://nette.org).

Požiadavky
------------

- PHP 8.1


Inštalácia
------------

	composer create-project romanmatyus/hr hr
	cd hr


Nastavenie `temp/` a `log/` na zapisovanie: `chmod a+rw temp log`.

	npm install
	grunt


Štart webservera
----------------

	php8.1 -S localhost:8000 -t www

Následne navštíviť `http://localhost:8000` v prehliadači.
