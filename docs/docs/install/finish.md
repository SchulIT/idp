---
sidebar_position: 4
---

# Installation abschließen

## SAML-Zertifikat erstellen

Es wird ein selbst-signiertes Zertifikat mittels OpenSSL erzeugt. Dazu das folgende Kommando ausführen:

```bash
$ php bin/console app:create-certificate --type saml
```

Anschließend werden einige Daten abgefragt. Diese können abgesehen vom `commonName` frei gewählt werden:

* `countryName`, `stateOrProvinceName`, `localityName` geben den Standort der Schule an
* `organizationName` entspricht dem Namen der Schule
* `organizationalUnitName` entspricht der Fachabteilung der Schule, welche für die Administration zuständig ist
* `commonName` Domainname des Single Sign-Ons, bspw. `sso.schulit.de`
* `emailAddress` entspricht der E-Mail-Adresse des Administrators

:::info
Das Zertifikat ist standardmäßig 10 Jahre gültig.
:::

## Finale Installationsschritte

Nun folgende Kommandos ausführen, um die Installation abzuschließen

```bash
# Cache leeren und aufwärmen
$ php bin/console cache:clear
# Datenbank erstellen
$ php bin/console doctrine:migrations:migrate --no-interaction
# Anwendung installieren
$ php bin/console app:setup
# Cronjobs registrieren
$ php bin/console shapecode:cron:scan
# Browscap aktualisieren
$ php bin/console app:browscap:update
```

## Ersten Benutzer erstellen

Nun muss ein Benutzer erstellt werden. Dieser muss als Administrator eingerichtet werden, sodass
man alle weiteren Konfigurationsschritte über das Web Interface erledigen kann.

Als Benutzernamen wählt man eine (beliebige) E-Mail-Adresse aus. Diese muss nicht mit der echten E-Mail-Adresse
übereinstimmen. 

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

## Webserver konfigurieren

Die Konfiguration des Webservers kann in der [Symfony Dokumentation](https://symfony.com/doc/current/setup/web_server_configuration.html)
nachgelesen werden.

:::danger Wichtig
Es ist wichtig, dass das `public/`-Verzeichnis als Wurzelverzeichnis im Webserver hinterlegt ist. Anderenfalls können
Konfigurationsdateien abgefangen werden.
:::