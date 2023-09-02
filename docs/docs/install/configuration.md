---
sidebar_position: 3
---

# Konfigurationsdatei

## Konfigurationsdatei anlegen

Die Vorlage für die Konfigurationsdatei befindet sich in der Datei `.env`. Von dieser Datei muss eine Kopie `.env.local` erzeugt werden:

```bash
$ cp .env .env.local
```

## Konfigurationsdatei anpassen

Anschließend muss die Konfigurationsdatei angepasst werden.

### Symfony-Einstellungen

Die folgenden zwei Einstellungen werden für die Konfiguration des Symfony Frameworks verwendet.

#### APP_ENV

Dieser Wert muss immer `prod` enthalten, sodass das System in der Produktionsumgebung ist. 

:::caution Achtung
In einer Produktionsumgebung niemals `dev` verwenden.
:::

#### APP_SECRET

Dieser Wert muss eine zufällige Zeichenfolge beinhalten. Mit dem folgenden Kommando kann eine entsprechende Zeichenfolge
erstellt werden:

```bash
$ openssl rand -base64 32
```

### Active Directory Authentication Server

Möchte man ein vorhandenes Active Directory verwenden, um Benutzer zu authentifizieren, so müssen die folgenden Werte
konfiguriert werden. Weitere Informationen gibt es im Abschnitt zur [Active Directory Anbindung](../guides/active_directory/intro)

:::info
Die Konfigurationsparameter können zunächst übersprungen werden und zu einem späteren Zeitpunkt konfiguriert werden.
Standardmäßig ist die Authentifizierung mit einem Active Directory ausgeschaltet.
:::

### Allgemeine Einstellungen

#### APP_URL

Dieser Wert beinhaltet die URL zur Instanz, bspw. `https://sso.schulit.de/`.

#### APP_NAME

Dieser Wert beinhaltet den Namen der Anwendung. Standardmäßig ist dies `SchulIT Single Sign-On`, kann aber beliebig
geändert werden, um z.B. den Schulnamen zu integrieren.

#### APP_LOGO

Dieses Logo wird im Footer der Website angezeigt. Es werden alle Formate unterstützt, die auch von Browsern unterstützt
werden. Die Maße des Logos können frei gewählt werden. Das Logo wird automatisch auf eine Höhe von 80px vergrößert oder 
verkleinert (vom Browser). 

Für das Logo sollte im Ordner `public/images/` liegen. Der Pfad wird relativ zum `public`-Ordner angegeben.

Beispiel: Ist das Logo unter `public/images/logo.svg` gespeichert, muss `images/logo.svg` für den Parameter eingetragen werden.

#### APP_SMALLLOGO

Dieses Logo wird oben in der Navigationsleiste angezeigt. Es werden alle Formate unterstützt, die auch von Browsern unterstützt
werden. Das Logo sollte quadratisch sein und wird automatisch auf eine Breite bzw. Höhe von 32px vergrößert.

Für das Logo sollte im Ordner `public/images/` liegen. Der Pfad wird relativ zum `public`-Ordner angegeben.

Beispiel: Ist das Logo unter `public/images/smalllogo.svg` gespeichert, muss `images/smalllogo.svg` für den Parameter eingetragen werden.

#### SAML_ENTITY_ID

Die sogenannte Entity ID dieses Identity Providers. Hier trägt man in der Regel die URL des Dienstes ein, also denselben
Wert wie in `APP_URL`.

#### MAILER_FROM

Die Absende-E-Mail-Adresse für ausgehende E-Mails.

#### CRON_PASSWORD

Das Passwort für den Cronjob-Benutzer, sofern Cronjobs via HTTPs ausgeführt werden. Details siehe [Cronjobs](../guides/cronjobs).

:::info
Der Konfigurationsparameter können zunächst übersprungen werden und zu einem späteren Zeitpunkt konfiguriert werden.
:::

### Datenbank

#### DATABASE_URL

Verbindungszeichenfolge für die Datenbankverbindung, welche sich wie folgt zusammensetzt:

```
mysql://USERNAME:PASSWORD@HOST:3306/NAME
```

* `USERNAME`: Benutzername der Datenbank
* `PASSWORD`: zugehöriges Passwort des Datenbankbenutzers
* `HOST`: Hostname des Datenbankservers
* `NAME`: Name der Datenbank

Weitere Informationen zur Verbindungszeichenfolge gibt es [hier](https://www.doctrine-project.org/projects/doctrine-dbal/en/latest/reference/configuration.html#connecting-using-a-url).

### E-Mail

Damit das System E-Mails versenden kann, muss eine entsprechende Verbindungszeichenfolge zum Verbinden mit einem E-Mail-Postfach
konfiguriert werden.

Beispiele:

* Generischer SMTP-Versand: `smtp://SMTPSERVER:465?encryption=ssl&auth_mode=login&username=USERNAME&password=PASSWORD`
* Google Mail-Postfach: `gmail://USERNAME:PASSWORD@localhost`
* Microsoft 365-Postfach: `smtp://EMAIL:PASSWORD@smtp.office365.com:587`

Dabei sind die Parameter `SMTPSERVER`, `USERNAME` und `PASSWORD` entsprechend anzupassen.

:::warning Wichtig
Alle Parameter müssen URL-enkodiert angegeben werden. Zum Beispiel muss das `@`-Zeichen durch `%40` ersetzt werden. Siehe [Wikipedia](https://de.wikipedia.org/wiki/URL-Encoding)
:::

### PHP-Executable

Wenn Cronjobs ausgeführt werden, werden diese in einem separaten Prozess mithilfe dieser PHP Executable ausgeführt. Diese
Variable muss auf die entsprechende PHP-Version (z.B. ``/usr/bin/php`` oder ``/usr/bin/php8.2``) gesetzt werden. Anderenfalls
kann die Ausführung von Cronjobs fehlerhaft sein.

Den Pfad zur PHP-Executable findet man entweder in der Dokumentation des Webhosters oder man findet sie z.B. mittels `which` heraus:

```bash
$ which php
$ which php8.2
```

### MESSENGER_TRANSPORT_DSN

Diesen Parameter **NICHT** verändern. Standardmäßig ist er auf `doctrine://default` konfiguriert.

