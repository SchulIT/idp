Benutzer anlegen
================

Das Anlegen der Benutzer kann entweder händisch erfolgen oder automatisiert. Anleitungen zum
automatisierten Import sind unter `Datenimport <../import/users.html>`_ zu finden.

Lehrkräfte und Lernende
#######################

Da Lehrkräfte und Lernende in der Regel in einer Datenbank (entweder Schulverwaltungsprogramm oder in
einem Active Directory/LDAP-Verzeichnis) gespeichert sind, kann ein Datenimport aus einer dieser
Quellen erfolgen.

Der Import aus CSV-Dateien wird `hier <../import/users.html>`_ erläutert. 

Um den Import weiter zu vereinfachen, wird der Import aus dem Active Directory empfohlen. Dazu werden zwei
Komponenten benötigt:

- Active Directory Authentication Server: Dieser Server beantwortet Authentifizierungsanfragen, sodass sich Benutzer mit ihrer E-Mail-Adresse und dem Passwort aus dem Schulnetzwerk anmelden können.
- Active Directory Connect (optional, aber empfolen): Diese Software synchronisiert alle Benutzer vorab zum Identity Provider, sodass Benutzer bereits vor ihrer ersten Anmeldung dort bekannt sind und bearbeitet werden können.

Damit die Benutzer für sie passende Rechte zugewiesen bekommen, müssen `Synchronisationsregeln <sync_rules.html>`_ erstellt werden.

Eltern
######

Eltern 