---
sidebar_position: 2
---

# Download & Vorbereitung

## Quellcode herunterladen

```bash
$ git clone https://github.com/schulit/idp.git
$ git checkout -b 1.0.0
$ cd idp
```

Dabei muss `1.0.0` durch die gewünschte Version ersetzt werden.

## Abhängigkeiten installieren

```bash
$ composer install --no-dev --classmap-authoritative --no-scripts
$ npm install
```

## CSS- und JavaScript-Dateien erzeugen

```bash
$ npm run build
$ php bin/console assets:install
```

