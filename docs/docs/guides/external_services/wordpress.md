# Wordpress

Es ist möglich, das Single Sign-On mit Wordpress zu verwenden. 

## Voraussetzungen

* eine geeignete WordPress-Installation
* OneLogin SAML SSO-Plugin
* ein SAML-Zertifikat (dafür wird OpenSSL benötigt)

## SAML-Zertifikat generieren

In einer Shell folgenden Befehl ausführen:

```bash
$ openssl req -nodes -x509 -newkey rsa:4096 -keyout key.pem -out cert.pem -sha256 -days 3650
```

:::tip
Als `Common Name` trägt man die Domain ein, unter der WordPress läuft.
:::

Damit wird ein Zertifikat erzeugt, was 10 Jahre gültig ist. Der Schlüssel wird in `key.pem` und das Zertifikat in `cert.pem`
abgespeichert.

## Plugin konfigurieren

### Status

#### Enable

Hier muss ein Häckchen gesetzt werden.

### Identity Provider Settings

| Option                      | Beschreibung                                                                                                                       |
|-----------------------------|------------------------------------------------------------------------------------------------------------------------------------|
| Idp Entity Id               | Hier trägt man die Entity ID des Identity Providers ein, bspw. `https://sso.schulit.de/`                                           |
| Single Sign On Service Url  | Hier trägt man `https://sso.schulit.de/idp/saml` ein, wobei `sso.schulit.de` durch die Domain des Single Sign-Ons zu ersetzen ist. |
| Single Sign Out Service Url | Feld bleibt leer, da nicht unterstützt.                                                                                            |
| X.509 Certificate           | Hier wird der Inhalt von `certs/idp.crt` (Single Sign-On Ordner) eingetragen.                                                      |

### Options

| Option                     | Beschreibung            |
|----------------------------|-------------------------|
| Create user if not exists  | ✔️ Häckchen setzen       |
| Update user data           | ✔️ Häckchen setzen       |
| Force SAML login           | optional                |
| Single Log Out             | ❌ Häckchen nicht setzen |
| Keep Local login           | optional                |
| Alternative ACS endpoint   | ❌ Häckchen nicht setzen |
| Match Wordpress account by | E-mail                  |

### Attribute mapping

| Option      | Beschreibung                                                       |
|-------------|--------------------------------------------------------------------|
| Username    | http://schemas.xmlsoap.org/ws/2005/05/identity/claims/emailaddress |
| E-mail      | http://schemas.xmlsoap.org/ws/2005/05/identity/claims/emailaddress |
| First Name  | http://schemas.xmlsoap.org/ws/2005/05/identity/claims/givenname    |
| Last Name   | http://schemas.xmlsoap.org/ws/2005/05/identity/claims/surname      |
| Nickname    | *leer*                                                             |
| Role        | urn:roles                                                          |
| Remember Me | *leer*                                                             |

### Role mapping

| Option                                           | Beschreibung            |
|--------------------------------------------------|-------------------------|
| Administrator                                    | ROLE_ADMIN              |
| Editor                                           | ROLE_EDITOR             |
| Author                                           | ROLE_AUTHOR             |
| Contributor                                      | ROLE_CONTRIBUTOR        |
| Subscriber                                       | ROLE_SUBSCRIBER         |
| BackWPUp*                                        | *leer*                  |
| Multiple role values in one saml attribute value | ❌ Häckchen nicht setzen |
| Regular expression for multiple role values      | *leer*                  |

### Role precendence

Hier können alle Felder leer gelassen werden.

### Customize actions and links

Hier ist es sinnvoll, die Häckchen bei den `Prevent ...`-Optionen zu setzen.

### Advanced settings

| Option                             | Beschreibung                                                          |
|------------------------------------|-----------------------------------------------------------------------|
| Service Provider Entity Id         | Entity ID der Wordpress-Installation, bspw. `https://www.schulit.de/` |
| Sign AuthnRequest                  | ✔️ Häckchen setzen                                                     |
| NameIDFormat                       | urn:oasis:names:tc:SAML:1.1:nameid-format:unspecified                 |
| requestAuthnContext                | *keine Option auswählen*                                              |
| Service Provider X.509 Certificate | Inhalt des vorhin erzeugten Zertifikats (`cert.pem`)                  |
| Service Provider Private Key       | Inhalt des vorhin erzeugten privaten Schlüssels (`key.pem`)           |
| Signature Algorithm                | http://www.w3.org/2001/04/xmldsig-more#rsa-sha256                     |
| Digest Algorithm                   | http://www.w3.org/2001/04/xmlenc#sha256                               |

## Single Sign-On konfigurieren

### Dienst erstellen

Unter *Verwaltung ➜ Dienste* einen neuen SAML-Dienst erstellen.

| Option                 | Wert                                                                                                                 |
|------------------------|----------------------------------------------------------------------------------------------------------------------|
| URL                    | Die Anmelde URL zum Wordpress, also bspw. `https://www.schulit.de/wp-login.php`                                      |
| Entity ID              | Die Entity ID der Wordpress-Installation, bspw. `https://www.schulit.de/`                                            |
| Name                   | *beliebig*                                                                                                           |
| Beschreibung           | *beliebig*                                                                                                           |
| Icon                   | *beliebig*                                                                                                           |
| Assertion Customer URL | `https://www.schulit.de/wp-login.php?saml_acs`, wobei `www.schulit.de` durch die Wordpress-Domain auszutauschen ist. |
| Zertifikat             | Zertifikat der Wordpress-Installation (Inhalt von `cert.pem`)                                                        |

### Attribut für Rollen erstellen

Unter *Verwaltung ➜ Attribute* ein neues Attribut erstellen.

| Option                                 | Wert                                |
|----------------------------------------|-------------------------------------|
| Name                                   | wp-roles                            |
| Anzeigename                            | Wordpress-Rolle                     |
| Beschreibung                           | *beliebig*                          |
| Benutzer können dieses Attribut ändern | ❌ Häckchen nicht setzen             |
| SAML Attribut-Name                     | urn:roles                           |
| Typ                                    | Auswahlfeld                         |
| Dienste                                | Hier den Wordpress-Dienst auswählen |

Unter *Optionen* kann die Option *Mehrfach-Auswahl möglich* aktiviert oder nicht aktiviert werden (je nach Anwendungswunsch).

Folgende Optionen eintragen:

| Schlüssel        | Wert          |
|------------------|---------------|
| ROLE_ADMIN       | Administrator |
| ROLE_EDITOR      | Editor        |
| ROLE_AUTHOR      | Author        |
| ROLE_CONTRIBUTOR | Contributor   |
| ROLE_SCUBSCRIBER | Subscriber    |

:::success Fertig
Der Dienst ist nun eingerichtet und kann Benutzern zugewiesen werden.
:::