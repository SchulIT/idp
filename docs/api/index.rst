API-Schnittstelle
=================

Die API-Schnittstelle ermöglicht es, Daten automatisiert in das System zu importieren.

Token erstellen
###############

Um die API nutzen zu können, muss zunächst ein passendes Token erzeugt werden. Dieses Token muss bei jedem Import im
Kopfbereich der HTTP-Anfrage als ``X-Token`` mitgesendet werden.

Das Token erzeugt man in der :fa:`cogs` Verwaltung unter :fa:`key` Anwendungen.

Beim Erzeugen muss ausgewählt werden, welche Funktionalität das Token hat:

- Allgemeine API: erlaubt die Nutzung der Endpunkte ``/api/user_types``, ``/api/user`` und ``/api/registration_codes``
- IdP Exchange: erlaubt die Nutzung der Endpunkte ``/exchange/``. Diese Endpunkte werden für IdP Exchange-Funktionalitäten genutzt. Diese werden bei den jeweiligen Anwendungen (ICC, ...) beschrieben
- AD Connect: erlaubt die Nutzung der Endpunkte ``/api/ad_connect``. Wird für AD Connect Clients benötigt.

Sofern man den Zugriffsbereich IdP Exchange auswählt, muss auch ein Dienst angegeben werden. Nur so wird sichergestellt,
dass nur für den Dienst bestimmte Benutzerdaten übertragen werden.

Nachdem das Token erstellt wurde, wird es auf der Übersichtsseite angezeigt.

Dokumentation der API
#####################

Die Schnittstellen für IdP Exchange und AD Connect sind aktuell nicht dokumentiert, da für sie enstsprechende Bibliotheken
oder Clients existieren, die diese API-Schnittstelle (exklusiv) nutzen.

Die Allgemeine API ist entsprechend dokumentiert. Die Dokumentation findet man in der :fa:`cogs` Verwaltung unter *API Dokumentation*.

