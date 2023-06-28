---
sidebar_position: 1
---

# Allgemeines

Die API-Schnittstelle ermöglicht es, Daten automatisiert in das System zu importieren.

## Token erstellen

Um die API nutzen zu können, muss zunächst ein passendes Token erzeugt werden. Dieses Token muss bei jedem Import im
Kopfbereich der HTTP-Anfrage als `X-Token` mitgesendet werden.

Das Token erzeugt man unter Verwaltung ➜ Anwendungen.

Beim Erzeugen muss ausgewählt werden, welche Funktionalität das Token hat:

* Allgemeine API: erlaubt die Nutzung der Endpunkte `/api/user_types`, `/api/user` und `/api/registration_codes`
*AD Connect: erlaubt die Nutzung der Endpunkte `/api/ad_connect`. Wird für AD Connect Clients benötigt.

Nachdem das Token erstellt wurde, wird es auf der Übersichtsseite angezeigt.

## Dokumentation der API

Die API ist dokumentiert. Die Dokumentation findet man unter Verwaltung ➜ API Dokumentation.

