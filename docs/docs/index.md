---
sidebar_position: 1
slug: /
---

# Einführung

Der SchulIT Single Sign-On Dienst stellt die zentrale Benutzerdatenbank zur Verfügung. 
Zu jedem Benutzer gibt es einige Standarddaten (bspw. Vorname, Nachname, Anmelde-E-Mail-Adresse etc.),
die gespeichert werden. Zusätzlich ist es möglich, beliebige weitere Daten zu speichern.

Aus Sicht der Benutzer stellt dieser Dienst eine Anwendung zur Verfügung, die für die Anmeldung
zuständig ist. Ist ein Benutzer einmal angemeldet, so kann er alle für ihn/sie freigeschalteten
Dienste - ohne weitere Anmeldung - nutzen.

Jedem Benutzer wird genau ein Benutzertyp zugeordnet. Diese entsprechen der Zugehörigkeit innerhalb der Schule (bspw.
Lehrkraft, Schülerin/Schüler, Elternteil, Sekretariat, Andere). Es lassen sich auch eigene Benutzertypen definieren.

Zusätzlich dazu lassen sich Benutzergruppen definieren. Benutzer können Mitglied mehrerer Benutzergruppen sein. Benutzergruppen
lassen sich zum Beispiel für Arbeitsgemeinschaften oder Administratoren definieren.

Der Vorteil der Benutzertypen und -gruppen wird im Rechtesystem ausgespielt.

## Rechtesystem

Freigeschaltete Dienste und Attributwerte lassen sich sowohl pro Benutzertyp als auch pro Benutzergruppe und Benutzer festlegen.

Um zu entscheiden, welche Dienste ein Benutzer verwenden darf und welchen Attributwert einzelne Attribute annehmen, geht
der Identity Provider in der folgenden Reihenfolge vor:

1. Benutzertyp
2. Benutzergruppe
3. Benutzer

Attributswerte einer Benutzergruppe überschreiben also Werte, die beim Benutzertyp festgelegt wurden, und werden (falls
vorhanden) von Werten des Benutzers überschrieben.

Eine Freischaltung von Diensten erfolgt in der gleichen Reihenfolge. Allerdings lässt sich eine Freischaltung nicht überschreiben.
Wenn also Dienst A für den Benutzertyp freigeschaltet ist, reicht dies aus. Der Dienst kann dann nicht mehr durch eine
Benutzergruppe oder den Benutzer selbst gesperrt werden.

## Verwendete Technologien

Die Software ist so programmiert, dass sie - theoretisch - auf gängigen Webspaces lauffähig ist:

* PHP 8
* MariaDB

Die Software verwendet das [Symfony Framework](https://www.symfony.com). Die genauen Voraussetzungen
sind [hier](#) aufgeführt.

## SAML

Die Single Sign-On Funktionalität wird mithilfe des [SAML-Protokolls](https://de.wikipedia.org/wiki/Security_Assertion_Markup_Language)
realisiert. Diese Anwendung fungiert dabei als *Identity Provider*. Anwendungen wie z.B. andere SchulIT Anwendungen, Wordpress etc.
fungieren dabei als *Service Provider*.

![Ablauf](https://upload.wikimedia.org/wikipedia/commons/0/04/Saml2-browser-sso-redirect-post.png)

(© Tom Scavo, CC BY-SA 3.0, [Zum Bild](https://en.wikipedia.org/wiki/File:Saml2-browser-sso-redirect-post.png))

* Schritt 1: Der Benutzer möchte sich an einem Dienst (bspw. dem ICC) anmelden und öffnet dazu die URL des Dienstes.
   Der Dienst schaut nach, welche Identity Provider findet. Dabei ist dieser Identity Provider als einziger hinterlegt.
* Schritt 2: Der Dienst antwortet dem Benutzer mit einer Weiterleitungsseite zum Identity Provider.
   Diese Seite enthält die Anfrage für das Single-Sign-On. 
* Schritt 3: Der Browser navigiert automatisch zum Identity Provider (mit der Anfrage). Dort muss der Benutzer
   sich authentifizieren (hier wird dann ggf. auch ein zweiter Faktor eingefordert). 
   Es wird anschließend geprüft, ob der Benutzer die Berechtigung besitzt, den Dienst
   zu nutzen. 
* Schritt 4: Der Identity Provider antwortet mit einer Weiterleitungsseite, die zurück zum Dienst weiterleitet.
   Ähnlich zu Schritt 2 enthält diese Weiterleitung die Antwort auf die Anfrage (aus Schritt 2). 
* Schritt 5-8: Der Dienst nimmt die Anfrage an und wertet diese aus. Beim ICC erfolgt nun die Verknüpfung von Benutzern
   zu Schülerinnen und Schülern bzw. Lehrkräften.

Ab nun ist der Benutzer beim Dienst angemeldet und kann diesen nutzen.

## Zwei-Faktor-Authentifizierung

Das Single Sign-On unterstützt Zwei-Faktor-Authentifizierung mittels [Time-based One-time Passwords](https://de.wikipedia.org/wiki/Time-based_One-time_Password_Algorithmus)
und ist mit der Google Authenticator-App kompatibel.

[WebAuthn](https://de.wikipedia.org/wiki/WebAuthn) bzw. [FIDO2](https://de.wikipedia.org/wiki/FIDO2) werden aktuell (noch) nicht unterstützt.

## Wiedererkennung in anderen Diensten

Damit Benutzer in anderen Anwendungen (wie beispielsweise dem ICC) zugeordnet werden können, wird die E-Mail-Adresse
des Benutzers verwendet (nicht der Anmeldename). Bei Elternaccounts wird ein zusätzliches SAML-Attribut `urn:external-id` verwendet,
in dem jeweils die E-Mail-Adressen der Kinder kommasepariert hinterlegt sind.

:::caution Achtung
Es ist wichtig, dass die E-Mail-Adressen der Benutzer stets in allen Anwendungen korrekt hinterlegt sind.
:::