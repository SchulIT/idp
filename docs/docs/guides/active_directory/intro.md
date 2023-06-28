---
sidebar_position: 1
---

# Einführung

Hat man ein lokales Active Directory, kann mithilfe der Tools Active Directory Authentication Server und Active Directory
Connect Client ein Single-Sign-On ermöglicht werden. Dann können sich die Benutzer mit denselben Anmeldedaten wie im
pädagogischen Netzwerk anmelden.

:::warning Achtung
Bevor der Authentication Server oder Connect Client genutzt werden können, müssen [Synchronisationsregeln](./sync_rules) erstellt werden.
:::

## Voraussetzungen

Damit die Anbindung an ein lokales Active Directory funktioniert, müssen folgende Voraussetzungen erfüllt sein:

* Active Directory Connect Client muss im lokalen Active Directory installiert und konfiguriert sein
* Active Directory Authentication Server muss im lokalen Active Directory installiert und konfiguriert sein
* für den Connect Client und den Authentication Server muss ein Windows Server 2016+ oder ein Windows 10/11-Client ausgeführt werden
* es wird eine Port-Freigabe für den Authentication Server benötigt
* Synchronisationsregeln müssen im Single Sign-On konfiguriert sein

## Active Directory Connect Client

Mithilfe des Active Directory Connect Clients werden Benutzer aus dem Active Directory im Identity Provider provisioniert
und aktualisiert. Nur über diesen Client angelegte Benutzer können sich mittels Single-Sign-On (s.o.) anmelden.

Der Vorteil bei der Nutzung dieses Clients besteht darin, dass Änderungen in der Benutzerverwaltung nur im Active Directory
vorgenommen werden müssen (das erledigt man in der Regel über die Benutzerverwaltung der verwendeten pädagogische Oberfläche).
Anschließend werden alle Lernenden und Lehrkräfte synchronisiert (das impliziert auch, dass nicht mehr vorhandene Benutzer
automatisch gelöscht werden und dass Klassenzugehörigkeiten aktualisiert werden).

➜ [Zum Active Directory Connect Client](https://schulit.de/software/ad-connect/)

## Active Directory Authentication Server

Der Active Directory Authentication Server ist ein Programm, welches im Schulnetzwerk (besser: in einer DMZ) läuft und
Authentifizierungsanfragen (Benutzername und Passwort) entgegennimmt und gegen das Active Directory auswertet. Als Antwort
liefert es im Falle eines Erfolges Informationen über den Benutzer im Active Directory (objectGuid, E-Mail-Adresse,
Vorname, Nachname, Organisationeinheit, Gruppenmitgliedschaften).

Nach einer erfolgreichen Anmeldung wird das Passwort (in Form eines Hashes) in der Datenbank abgespeichert, sodass sich
ein Benutzer auch anmelden kann, falls der Authentication Server nicht erreichbar sein sollte.

:::caution Warnung
Hat sich ein Benutzer noch nie online angemeldet, so kann er sich nicht anmelden, falls der Authentication Server nicht erreichbar ist.
:::

Die Verbindung zwischen dem Identity Provider und dem Active Directory Authentication Server ist mittels TLS verschlüsselt.

➜ [Zum Active Directory Authentication Server](https://schulit.de/software/ad-auth/)

