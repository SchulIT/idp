# Installationsanleitung

## Anforderungen/Vorbereitungen

Folgende Software wird zum Deployment benötigt:

* PHP 7.2+
* MySQL 5.8+
* composer
* node
* yarn
* git (Alternativ das ZIP-Archiv von GitHub herunterladen)

## Installation

### Quelltext

Zunächst das Git-Archiv klonen:
   
    $ git clone https://github.com/schoolit-de/idp
    
Anschließend die gewünschte Version auschecken (oder `master` zum Entwickeln):

    $ git checkout vX.X.X

### Abhängigkeiten herunterladen

In das Verzeichnis mit dem Quelltext wechseln.

    $ composer install
    $ yarn install

### Konfiguration

    $ cp .env.dist .env

In der Datei `.env` die Konfiguration vornehmen.

### Assets

    $ yarn encore dev

### Datenbank

    $ php bin/console doctrine:database:create
    $ php bin/console doctrine:schema:update --force
    $ php bin/console app:setup

### Benutzer anlegen

    $ php bin/console app:create-user

## Zertifikate

Nun müssen noch Zertifikate für den Identity Provider erstellt werden. Dazu kann das folgende Kommando genutzt werden:

    $ php bin/console app:create-certificate
