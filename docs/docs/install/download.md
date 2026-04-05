---
sidebar_position: 2
---

# Download & Vorbereitung

## Quellcode herunterladen

```bash
$ git clone https://github.com/schulit/idp.git
$ cd idp
$ git checkout -b 1.0.0
```

Dabei muss `1.0.0` durch die gewünschte Version ersetzt werden.

## Abhängigkeiten installieren

```bash
$ composer install --no-dev --classmap-authoritative --no-scripts
```

## CSS- und JavaScript-Dateien erzeugen

```bash
$ php bin/console importmap:install
$ php bin/console asset-map:compile
$ php bin/console assets:install
```

