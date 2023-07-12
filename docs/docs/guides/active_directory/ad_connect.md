---
sidebar_position: 3
---

# Active Directory Connect

Mithilfe des Active Directory Connect Clients können Benutzer aus dem lokalen Active Directory in das Single Sign-On
importiert werden.

:::info
Bei diesem Prozess werden keine Passwörter übertragen. Die Benutzer werden lediglich angelegt, aktualisiert oder gelöscht.
:::

## Voraussetzungen

* Windows 10/11 oder Windows Server 2016+
* Zugriff auf Domain Controller
* Standard-Domainnutzer (welche die Rechte haben sollte, Daten aller zu synchronisierenden Benutzer zu lesen)

## Installation

Die Installationsdateien können [von GitHub heruntergeladen](https://github.com/SchulIT/adconnect-client/releases) werden.

## Konfiguration

Nach dem ersten Start muss das Tool zunächst konfiguriert werden. Dazu in den Reiter *Einstellungen* wechseln.

### Art des Benutzernamens

Hier hat man die Wahl zwischen *UserPrincipalName* und *sAMAccountName*. Nutzt man *UserPrincipalName*, so ist der
Benutzername gleich der Anmelde-E-Mail-Adresse im Active Directory. Wählt man *sAMAccountName* aus, so ist der Active Directory
Benutzername der Benutzername im Single Sign-On.

:::danger Warnung
Hier sollte *UserPrincipalName* ausgewählt werden, da das Single Sign-On E-Mail-Adressen als Benutzername erwartet und es
anderenfalls zu einem unerwarteten Verhalten kommen kann.
:::

### URL des Identity Providers

Hier trägt man die URL zum Single Sign-On ein, also bspw. `https://sso.schulit.de`.

### API-Token

Das API-Token erzeugt man über das Web Interface des Single Sign-Ons über *Anwendungen ➜ Neue Anwendung*. Hier wählt man
als *Zugriffsbereich* *AD Connect* aus. Als Dienst kann *None* ausgewählt bleiben.

Anschließend kann das Token übertragen werden.

### Domain Controller

Hier trägt man den vollständigen Hostnamen des Domain Controllers ein, bspw. `dc01.ad.schulit.de`.

### LDAP Port

Hier trägt man den LDAP-Port ein. Standardmäßig ist das `389`. Nutzt man SSL, so muss hier alternativ Port `636` eingetragen werden.

### LDAPS verwenden

Gibt an, ob SSL (LDAPS) verwendet werden soll. Standardmäßig ist dies deaktiviert. Empfohlen wird TLS (STARTTLS). 

:::note Hinweis
Wenn SSL verwendet wird, muss LDAP-Port `636` angegeben werden.
:::

### TLS verwenden

Gibt an, ob TLS mittels STARTTLS verwendet werden soll.

:::tip Empfehlung
Es wird empfohlen, TLS zu verwenden. Es ist aber nicht zwingend notwendig, da es zusätzliche Konfiguration des Domain Controllers
voraussetzt.
:::

### Fingerabdruck des Zertifikats

Verwendet man SSL oder TLS, so muss der Fingerabdruck des Zertifikats hier eingetragen werden. Ohne Leerzeichen oder andere Sonderzeichen
(wie bspw. Doppelpunkte).

### Fully-Qualified Domain Name

Hier trägt man den vollständigen Domainnamen ein, bspw. `ad.schulit.de`.

### NetBIOS-Name der Domain

Hier trägt man den NetBIOS-Namen der Domain ein, bspw. `AD`.

### Benutzername

Hier trägt man den Benutzernamen (Format: DOMAIN\BENUTZERNAME) eines Standard-Active Directory-Benutzers ein. Dieser Benutzer
muss (und sollte ausschließlich) lesenden Zugriff auf das Active Directory haben.

:::tip Hinweis
Jeder Standard-Active Directory-Benutzer kann auch Attribute anderer Benutzer auslesen.
:::

### Passwort

Das entsprechende Passwort zum Benutzer.

### Organisationseinheiten

Hat man alle Daten eingetragen, klickt man zunächst auf *Speichern*. Nun klickt man auf *Organisationseinheiten laden*,
sodass alle OUs geladen werden. 

Anschließend wählt man die gewünschten OUs aus, aus denen die Benutzer synchronisiert werden sollen. Auch hier gilt wieder,
dass untergeordnete Organisationseinheiten implizit einbezogen werden.

:::tip Empfehlung
Hier wählt man die Organisationseinheiten aus, in denen sich Lernende und Lehrkräfte befinden.
:::

:::info
Beim Import in das Single Sign-On werden nur Benutzer berücksichtigt, für die auch eine entsprechende Synchronisationsregel
angelegt wurde.
:::

Anschließend klickt man erneut auf *Speichern*.

## Benutzer importieren oder aktualisieren

Möchte man nun Benutzer importieren oder aktualisieren, so geschieht dies über den Reiter *Benutzer provisionieren*. Hier
wählt man die zu provisionierenden Benutzer aus und klickt anschließend auf *ausgewählte Benutzer provisionieren*.

Je nach Anzahl der Benutzer und Geschwindigkeit des Single Sign-Ons ist der Import in wenigen Augenblicken abgeschlossen.

## Benutzer löschen

Über den Reiter *Benutzer löschen* können Benutzer aus dem Single Sign-On entfernt werden. Dabei werden alle Benutzer gesucht,
die noch online vorhanden sind, aber nicht im lokalen Active Directory bzw. in den angegebenen Organisationseinheiten.

Auch hier können die einzelnen Benutzer ausgewählt werden, die gelöscht werden sollen. Anschließend mittels *ausgewählte Benutzer löschen*
das Löschen anstoßen.

:::tip Hinweis
Die Benutzer werden zunächst in den Papierkorb verschoben und können bis zu 30 Tage reaktiviert werden.
:::