---
sidebar_position: 1
---

# Updates

:::warning Achtung
Bitte immer zunächst prüfen, ob es eine entsprechende `UPDATE-X.XX.md` gibt, in der möglicherweise auf Inkompatibilitäten
oder Hinweise zum Update veröffentlicht werden.
:::

## Datensicherung

Bitte zunächst eine [Datensicherung](backup) anfertigen.

:::warning Achtung
Um ein Backup einzuspielen, muss die entsprechende Version des Quelltextes bekannt sein. Der folgende Befehle liefert
den aktuellen Git Commit-Hash, sodass dieser später wiederhergestellt werden kann:

```bash
$ git log --pretty=format:'%H' -n 1
```
:::

## Quelltext aktualisieren

Der Quelltext wird mittels Git aktualisiert:

```bash
$ git pull
$ git checkout -b 1.0.0
```

Dabei ist `1.0.0` durch die entsprechende Version zu ersetzen.

## Abhängigkeiten aktualisieren

```bash
$ composer install --no-dev --classmap-authoritative
$ npm install
```

## CSS- und JavaScript-Dateien erstellen

```bash
$ npm run build
$ php bin/console assets:install
```

## Aktualisierung der Anwendung und Datenbank

```bash
# Cache leeren und aufwärmen
$ php bin/console cache:clear
# Datenbank migrieren
$ php bin/console doctrine:migrations:migrate --no-interaction
# Anwendung installieren (führt ggf. durch das Update neue Schritte aus - bisherige Schritte werden übersprungen)
$ php bin/console app:setup
# Cronjobs registrieren
$ php bin/console shapecode:cron:scan
# Browscap aktualisieren
$ php bin/console app:browscap:update
# GeoIP aktualisieren
$ php bin/console geoip2:update
```

:::success Erfolg
Die Anwendung ist nun aktualisiert.
:::

## Wiederherstellen einer vorherigen Version

### Quelltext wiederherstellen
Zunächst die entsprechende Version wiederherstellen:

```bash
$ git checkout HASH
```

`HASH` entsprechend durch den Hash ersetzen (siehe oben).

### Datenbank wiederherstellen

Siehe [Backup](backup#datenbank-zurückspielen)

### Abhängigkeiten aktualisieren

```bash
$ composer install --no-dev --classmap-authoritative --no-scripts
$ npm install
```

### CSS- und JavaScript-Dateien erstellen

```bash
$ npm run build
$ php bin/console assets:install
```

### Aktualisierung der Anwendung und Datenbank

```bash
# Cache leeren und aufwärmen
$ php bin/console cache:clear
# Anwendung installieren (führt ggf. durch das Update neue Schritte aus - bisherige Schritte werden übersprungen)
$ php bin/console app:setup
# Cronjobs registrieren
$ php bin/console shapecode:cron:scan
```

