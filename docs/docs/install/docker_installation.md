---
sidebar_position: 6
---

# Docker Installation

Der IdP der SchulIT Software kann alternativ auch über Docker installiert werden. Diese Variante erfordert Wissen über `Docker` und `Docker Compose`, ist aber in der Regel weniger fehleranfällig und das berühmte Phänomen "Auf meiner Maschine läuft's aber" tritt nicht auf.

## Voraussetzungen

* Ein Server mit Docker und Docker Compose
* Terminal Zugriff (z.B. via SSH) auf diesen Server
* Server hat eine aktive Internetverbindung

## Installation mit Docker Image aus Docker Hub (empfohlen)

Stelle eine Verbindung mit dem Terminal des Servers her.

### Dateistruktur

Erstelle ein Verzeichnis in dem die Daten des Dienstes gespeichert werden sollen. Hier: `/home/docker/schulit/idp`

```bash
mkdir /home/docker/schulit/idp
```

:::tip Backups
Bitte sichere dieses Verzeichnis in regelmäßigen Abständen für Backups der Datenbank und des Dienstes
:::

Als nächstes erstellen wir zwei Ordner, wo die DB und der IdP ihre Daten ablegen:

```bash
cd /home/docker/schulit/idp
mkdir ./certs
mkdir ./db_data
mkdir ./own_assets
```

### Docker Compose 

Erstelle nun die Docker Compose Datei. Diese Datei beschreibt die Konfiguration eines Dienstes in Docker. 

```bash
nano docker-compose.yml
```

Kopiere den Inhalt aus unserer Vorlage und passe ihn nach deinen belieben an.

```yml title=docker-compose.yml
version: '3.8'

services:
  web:
    image: simonfrank/schulit-idp:latest # use a fixed image tag like v1.0 in prod environments
    restart: always
    ports:
      # change the first port to any port you like
      - "8080:80"
    depends_on:
      db:
        condition: service_healthy
    env_file:
      - .env
    volumes:
      - /home/docker/schulit/idp/certs:/var/www/html/certs
      # if you want to modify the apperance of this app mount this folder
      # - /home/docker/schulit/idp/own_assets:/var/www/html/public/own_assets
      # nginx configuration file - uncomment below if you want to use an own nginx config

      # - ./nginx.conf:/etc/nginx/sites-enabled/default




  db:
    image: mariadb:10.4
    restart: always
    env_file:
      - .env
    volumes:
      - /home/docker/schulit/idp/db_data:/var/lib/mysql
    healthcheck:
      test: mysqladmin ping -h 127.0.0.1 -u $$MYSQL_USER --password=$$MYSQL_PASSWORD
      interval: 5s
      timeout: 20s
      retries: 10
```

Drücke zum Speicher `strg` + `o` und anschließend zum Verlassen des Text Editors `strg` + `x`.

### Umgebungsvariablen anlegen

Als nächstes legen wir die Umgebungsvariablen für den IdP an.

Erstelle dazu eine neue Datei `.env`

```bash 
nano .env
```

Diese Datei sollte die folgenden Variablen aus dem Template benutzen. Hier sollten einige Anpassungen vorgenommen werden. Mehr Infos zu den Anpassungen findest du auf der Seite [Konfigurationsdatei](./configuration).

```text title=.env
###> symfony/framework-bundle ###
APP_ENV=prod
APP_SECRET=ChangeThisToASecretString
###< symfony/framework-bundle ###

###> schulit/adauth-bundle ###
ADAUTH_ENABLED=false
ADAUTH_URL="tls://dc01.ad.schulit.de:55117"
ADAUTH_PEERNAME="dc01.ad.schulit.de"
ADAUTH_PEERFINGERPRINT=""
###< schulit/adauth-bundle ###

###> schulit/common-bundle ###
APP_URL="https://sso.schulit.de/"
APP_NAME="SchulIT Single Sign-On"
# Pfade relativ zum public/-Verzeichnis
APP_LOGO="" # Müssen in der Compose gemountet werden und Dateien dann in "own_asstes" abgelegt werden
APP_SMALLLOGO="" # Müssen in der Compose gemountet werden und Dateien dann in "own_asstes" abgelegt werden
###< schulit/common-bundle

###> CUSTOM ###
SAML_ENTITY_ID="https://sso.schulit.de/"
MAILER_FROM="noreply@sso.schulit.de"
CRON_PASSWORD=
###< CUSTOM ###

###> doctrine/doctrine-bundle ###
# Siehe https://www.doctrine-project.org/projects/doctrine-dbal/en/latest/reference/configuration.html#connecting-using-a-url
DATABASE_URL="mysql://db_user:db_password@localhost:3306/db_name"
MYSQL_ROOT_PASSWORD=changeThisToASecurePassword
MYSQL_DATABASE=db_name
MYSQL_USER=db_user
MYSQL_PASSWORD=db_password
###< doctrine/doctrine-bundle ###

###> symfony/messenger ###
MESSENGER_TRANSPORT_DSN=doctrine://default
###< symfony/messenger ###

###> symfony/mailer ###
MAILER_DSN=native://default
###< symfony/mailer ###

PHP_BINARY=/usr/bin/php

### Docker ###
TZ=Europe/Berlin
```

Drücke zum Speicher `strg` + `o` und anschließend zum Verlassen des Text Editors `strg` + `x`.

### Webserver konfigurieren (optional)

Falls du die Config vom nginx Webserver anpassen möchtest, musst du das zweite Volume in der Compose einkommentieren. Anschließend kannst du die Config anpassen. 

Die Default Config, die wir im Container verwenden ist die folgende:

```text title=nginx.conf
server {
    listen 80;
    server_name localhost;

    root /var/www/html/public;

    location / {
        # try to serve file directly, fallback to index.php
        try_files $uri /index.php$is_args$args;
    }

    # optionally disable falling back to PHP script for the asset directories;
    # nginx will return a 404 error when files are not found instead of passing the
    # request to Symfony (improves performance but Symfony's 404 page is not displayed)
    # location /bundles {
    #     try_files $uri =404;
    # }

    location ~ ^/index\.php(/|$) {
        fastcgi_pass 127.0.0.1:9000;
        fastcgi_split_path_info ^(.+\.php)(/.*)$;
        include fastcgi_params;
        
        # When you are using symlinks to link the document root to the
        # current version of your application, you should pass the real
        # application path instead of the path to the symlink to PHP
        # FPM.
        # Otherwise, PHP's OPcache may not properly detect changes to
        # your PHP files (see https://github.com/zendtech/ZendOptimizerPlus/issues/126
        # for more information).
        # Caveat: When PHP-FPM is hosted on a different machine from nginx
        #         $realpath_root may not resolve as you expect! In this case try using
        #         $document_root instead.
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        fastcgi_param DOCUMENT_ROOT $realpath_root;

        proxy_set_header Host $http_host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
        # Prevents URIs that include the front controller. This will 404:
        # http://domain.tld/index.php/some-path
        # Remove the internal directive to allow URIs like this
        # internal;
    }

    # return 404 for all other php files not matching the front controller
    # this prevents access to other php files you don't want to be accessible.
    location ~ \.php$ {
        return 404;
    }

    # Optional logging
    error_log /var/log/nginx/error.log;
    access_log /var/log/nginx/access.log;
}
```

### Dienst starten 

Als nächstes können wir den Dienst starten:

```bash
docker compose up
```

Der Container startet jetzt. Dies kann 1-2 Minuten dauern. 

### IdP Konfigurieren

Als letzten Schritt müssen wir jetzt noch einen Admin User anlegen. Das geht über die Konsole des Dienstes im Docker Container.

```bash
docker exec -it <mycontainer> sh
```

`<mycontainer>` ist dabei entweder der Name oder die ID des Containers. Um diese herauszufinden führe folgenden Befehl aus und kopiere die ID oder den Namen

```bash
$ docker ps
CONTAINER ID   IMAGE          COMMAND                  CREATED       STATUS                   PORTS                            NAMES
af38ee24c451   idp-web        "docker-php-entrypoi…"   2 hours ago   Up 3 minutes             9000/tcp, 0.0.0.0:8080->80/tcp   idp-web-1
```

In diesem Fall wäre der Name `idp-web-1` und die ID `af38ee24c451`.

Wir würden also 
```bash
docker exec -it af38ee24c451 sh
```
ausführen.

Wir legen jetzt einen Admin User an:

:::caution Achtung
Der Benutzer muss als Administrator (Schritt 7) angelegt werden.
:::

```bash
$ php bin/console app:add-user
  Benutzername:
  > admin@example.com

  Vorname:
  > Erika

  Nachname:
  > Mustermann

  E-Mail:
  > admin@example.com

  Passwort:
  >

  Passwort wiederholen:
  >

  Ist der Benutzer ein Administrator? (yes/no) [yes]:
  > yes

  Benutzertyp wählen [user]:
    [0] user
  > user

  [OK] Benutzer erfolgreich erstellt
```

Das war's! Logge dich unter [http://server_ip:8080](http://server_ip:8080) in den IdP ein.

## Installation über Repository
Falls Anpassungen am Quellcode vorgenommen wurden, ist auch eine Installation über das Repository möglich.

In dem Fall liegt eine leicht modifizierte docker-compose Datei im Repo. Diese setzt die Verfügbarkeit einer lokalen `.env` Datei voraus. Diese kann mit folgendem Befehl angelegt und modifiziert werden:

```bash
cp .env .env.local
```

Wenn alle Einstellungen vorgenommen wurden, kann der Build des Containers und das Deployment über den Befehl

```bash
docker compose up --build
```

gestartet werden.

Anschließend muss auch ein Admin User angelegt werden (s.o.)