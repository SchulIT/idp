---
sidebar_position: 4
---

# Active Directory Authentication Server

Damit sich die aus dem Active Directory importierten Benutzer anmelden können, muss der Authentication Server implementiert werden.

## Voraussetzungen

* Windows 10/11 oder Windows Server 2016+
* Zugriff auf Domain Controller
* Standard-Domainnutzer mit dem Recht, Passwörter zu ändern

:::tip
In der Anleitung auf GitHub (siehe unten) wird beschrieben, wie ein solcher Benutzer anzulegen ist.
:::

## Installation & Konfiguration

Die Installation und Konfiguration sind [auf der GitHub-Seite](https://github.com/SchulIT/adauth-server/blob/master/README.md#installation--konfiguration)
in den entsprechenden Abschnitten beschrieben.

:::tip
Die Konfiguration des Servers kann über die GUI vorgenommen werden. So spart man sich lästiges Bearbeiten einer JSON-Datei 😉
:::

## Konfiguration des Single Sign-On

Damit das Single Sign-On mit dem Authentication Server kommunizieren kann, muss die Konfigurationsdatei `.env.local` entsprechend
angepasst werden. Im Folgenden werden die einzelnen Parameter dokumentiert.

### ADAUTH_ENABLED

Dieser Wert muss auf `true` gesetzt werden.

### ADAUTH_URL

Hier wird die URL angegeben, unter der der Authentication Server erreichbar ist. Dabei werden das Protokoll (stets `tls`)
sowie die IP-Adresse (in der Regel die IP-Adresse der Schule) und der zugehörige Port (standardmäßig `49117`) angegeben.

Beispiel: `tls://IP:49117` (`IP` entsprechend ändern, ggf. auch den Port ändern)

:::info
Bisher wurde das nur mit IPv4-Adressen getestet. Inwiefern auch IPv6 unterstützt wird, muss ausprobiert werden. Grundsätzlich
spricht jedoch nichts dagegen, eine IPv6-Adresse einzutragen, sofern alle Komponenten für IPv6 konfiguriert wurden.
:::

### ADAUTH_PEERNAME

Hier wird der `Common Name` des bei der Konfiguration des Authentication Servers erzeugten Zertifikat angegeben, z.B. `dc01.ad.schulit.de`.

### ADAUTH_PEERFINGERPRINT

Hier wird der Fingerabdruck des bei der Konfiguration des Authentication Servers erzeugten Zertifikat angegeben. Ohne Leerzeichen
oder Sonderzeichen (wie bspw. Doppelpunkte).

:::success Fertig
Wenn alles richtig konfiguriert wurde, können sich nun importierte Benutzer anmelden.
:::

## Zertifikat hinterlegen

Es muss das Zertifikat der Zertifizierungsstelle hinterlegt werden, womit das Zertifikat des Authentication Servers 
signiert wurde. Nutzt man ein selbst-signiertes Zertifikat, so muss dieses entsprechend hinterlegt werden.

Das Zertifikat muss im PEM-Format unter `certs/ca.crt` hinterlegt werden.

## Fehlerbehandlung

Funktioniert die Anmeldung nicht reibungslos, können folgende Dinge überprüft werden:

* Ist die Portweiterleitung aktiv und richtig konfiguriert?
* Läuft der Dienst auf dem Windows Server?
* Sind alle Konfigurationsparameter entsprechend der Dokumentation richtig eingetragen? (Rechtschreibfehler werden hart bestraft 😉)
* Das Anwendungslog vom Single Sign-On (zu finden unter *Verwaltung ➜ Logs*)
* Das Log vom Authentication Server (zu finden in der Ereignisanzeige von Windows ➜ Anwendungen)