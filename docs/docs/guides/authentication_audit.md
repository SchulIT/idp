---
sidebar_position: 5
---

# Anmelde-Log

Das Anmelde-Log protokolliert alle Anmeldungen am Single Sign-On. Dabei wird der Zeitstempel der Anmeldung, die IP-Adresse
und das zur IP-Adresse gehörige Land (sofern dies zugeordnet werden kann).

## Funktion aktivieren

Aus Datenschutzgründen ist die Funktion **standardmäßig deaktiviert** und muss über die Konfigurationsdatei aktiviert werden.

## Konfiguration (`.env.local`)

In der lokalen Konfigurationsdatei müssen folgende Optionen gesetzt werden:

```dotenv
AUTH_AUDIT_ENABLED=true
AUTH_AUDIT_RETENTION_DAYS=14
```

Der zweite Konfigurationsparameter gibt an, wie lange Daten im Audit-Log gespeichert werden sollen. Möchte man das Log nicht
zeitlich begrenzen, so wird das automatische Löschen mit dem Wert `0` deaktiviert. Dies wird jedoch aus Gründen des Datenschutzes
nicht empfohlen.

## MaxMind GeoIP-Datenbank

Um eine Zuordnung von IP-Adresse zu einem Land zu erhalten, muss ein kostenloser API-Schlüssel für die MaxMind GeoIP-Datenbank
erstellt werden.

* [Anmeldung für GeoLite2](https://www.maxmind.com/en/geolite2/signup)
* [Anleitung zum Erstellen eines API-Schlüssels](https://support.maxmind.com/hc/en-us/articles/4407111582235-Generate-a-License-Key)

Der API-Schlüssel muss ebenfalls in der `.env.local` gespeichert werden:

```dotenv
MAXMIND_LICENCE_KEY=Hier den Schlüssel einfügen
```

:::info Hinweis
Die Zuordnung von IP-Adresse zu Land ist [nicht immer akkurat](https://dev.maxmind.com/geoip/geolite2-free-geolocation-data/#understanding-ip-geolocation).
:::