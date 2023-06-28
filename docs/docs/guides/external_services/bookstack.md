# BookStack

BookStack ist eine Software zum Erstellen von Dokumentationen.

## Voraussetzungen

* eine BookStack-Installation
* ein SAML-Zertifikat (dafür wird OpenSSL benötigt)

### SAML-Zertifikat generieren

In einer Shell folgenden Befehl ausführen:

```bash
$ openssl req -nodes -x509 -newkey rsa:4096 -keyout key.pem -out cert.pem -sha256 -days 3650
```

:::tip
Als `Common Name` trägt man die Domain ein, unter der BookStack läuft.
:::

Damit wird ein Zertifikat erzeugt, was 10 Jahre gültig ist. Der Schlüssel wird in `key.pem` und das Zertifikat in `cert.pem`
abgespeichert.

## Konfiguration

Die grundsätzliche Konfiguration ist [in der Dokumentation von BookStack](https://www.bookstackapp.com/docs/admin/saml2-auth/)
beschrieben.

Folgende Werte sind zu setzen:

| Variable                      | Wert                                                                                                                                                                                  |
|-------------------------------|---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| AUTH_AUTO_INITIATE            | true                                                                                                                                                                                  |
| SAML2_NAME                    | SchulIT Single Sign-On (oder ein beliebiger anderer Name)                                                                                                                             |
| SAML2_EMAIL_ATTRIBUTE         | http://schemas.xmlsoap.org/ws/2005/05/identity/claims/emailaddress                                                                                                                    |
| SAML2_EXTERNAL_ID_ATTRIBUTE   | urn:id                                                                                                                                                                                |
| SAML2_DISPLAY_NAME_ATTRIBUTES | http://schemas.xmlsoap.org/ws/2005/05/identity/claims/givenname\|http://schemas.xmlsoap.org/ws/2005/05/identity/claims/surname                                                        |
| SAML2_IDP_ENTITYID            | Entity ID des Identity Providers, also bspw. `https://sso.schulit.de`                                                                                                                 |
| SAML2_AUTOLOAD_METADATA       | false                                                                                                                                                                                 |
| SAML2_IDP_SSO                 | `https://sso.schulit.de/idp/saml`, wobei `sso.schulit.de` durch die URL des Single Sign-Ons zu ersetzen ist.                                                                          |
| SAML2_IDP_x509                | Hier trägt man das Zertifikat des Single Sign-Ons (zu finden unter `certs/idp.crt`) ein. Da es sich um mehrere Zeilen handelt, muss der Inhalt in doppelten Anführungszeichen stehen. |
| SAML2_IDP_AUTHNCONTEXT        | true                                                                                                                                                                                  |
| SAML2_SP_x509                 | Hier trägt man den Inhalt des vorhin erzeugten Zertifikats (`cert.pem`) ein. Da es sich um mehrere Zeilen handelt, muss der Inhalt in doppelten Anführungszeichen stehen.             |
| SAML2_SP_KEY                  | Hier trägt man den Inhalt des vorhin erzeugten privaten Schlüssels (`key.pem`) ein. Da es sich um mehrere Zeilen handelt, muss der Inhalt in doppelten Anführungszeichen stehen.      |
| SAML2_USER_TO_GROUPS          | true                                                                                                                                                                                  |
| SAML2_GROUP_ATTRIBUTE         | urn:role                                                                                                                                                                              |
| SAML2_REMOVE_FROM_GROUPS      | true                                                                                                                                                                                  |

## Single Sign-On konfigurieren

### Dienst erstellen

Unter *Verwaltung ➜ Dienste* einen neuen SAML-Dienst erstellen.

Einige Metadaten lassen sich automatisiert laden, indem man zunächst die Metadaten-XML `https://bookstack.schulit.de/saml2/metadata`
(`bookstack.schulit.de` durch die BookStack-Domain ersetzen) einträgt und auf *Herunterladen* klicken.

:::tip Fast fertig
Die Felder Entity ID, Assertion Customer Service URL und Zertifikat sind bereits ausgefüllt.
:::

Folgende Werte setzen bzw. ändern:

| Option                 | Wert                                                             |
|------------------------|------------------------------------------------------------------|
| URL                    | Die URL zur BookStack-App, bspw. `https://bookstack.schulit.de/` |
| Entity ID              | *nicht ändern*                                                   |
| Name                   | *beliebig*                                                       |
| Beschreibung           | *beliebig*                                                       |
| Icon                   | *beliebig*                                                       |
| Assertion Customer URL | *nicht ändern*                                                   |
| Zertifikat             | *nicht ändern*                                                   |  

### Attribut für Rolle erstellen

Unter *Verwaltung ➜ Attribute* ein neues Attribut erstellen.

| Option                                 | Wert                                |
|----------------------------------------|-------------------------------------|
| Name                                   | bookstack-roles                     |
| Anzeigename                            | BookStack-Rolle                     |
| Beschreibung                           | *beliebig*                          |
| Benutzer können dieses Attribut ändern | ❌ Häckchen nicht setzen             |
| SAML Attribut-Name                     | urn:role                            |
| Typ                                    | Auswahlfeld                         |
| Dienste                                | Hier den BookStack-Dienst auswählen |

Unter *Optionen* muss die Option *Mehrfach-Auswahl möglich* deaktiviert bleiben.

Folgende Optionen eintragen:

| Schlüssel        | Wert          |
|------------------|---------------|
| admin            | Administrator |
| editor           | Editor        |
| viewer           | Viewer        |
| public           | Public        |

:::success Fertig
Der Dienst ist nun eingerichtet und kann Benutzern zugewiesen werden.
:::