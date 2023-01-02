Voraussetzungen
===============

Der Identity Provider ist so programmiert, dass es auf gängigen Webspaces installiert werden kann.

Obligatorische Software
-----------------------

- Webserver (Apache oder nginx)
    - der Identity Provider muss auf einer Subdomain laufen, also bspw. ``sso.example.com``
- PHP 8.1 oder höher
    - aktivierte Plugins: json, ctype, iconv, openssl, xml, gd
- MySQL 5.7+ oder MariaDB 10.3+
- SSH-Zugriff auf den Webspace
- Cronjobs (entweder als Skriptausführung oder HTTP-Anfrage)

Optionale Tools
---------------

Außerdem sollten folgende Tools auf dem Webserver installiert sein:

- git (wird zum Herunterladen des Codes benötigt)
- composer (wird zum Herunterladen von PHP Abhängigkeiten benötigt)
- nodejs und yarn (wird zum Kompilieren des Designs benötigt)

Falls die Tools nicht vorhanden sein sollten, kann man eine ZIP-Datei mit allen benötigten Dateien herunterladen und
auf dem Webspace hochladen.