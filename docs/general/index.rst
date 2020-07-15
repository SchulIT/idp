Allgemeine Funktionsweise
=========================

Benutzerdatenbank
#################

Dieser Identity Provider stellt zunächst eine Datenbank von Benutzern zur Verfügung. 
Zu jedem Benutzer gibt es einige Standarddaten (bspw. Vorname, Nachname, Anmelde-E-Mail-Adresse, ...),
die gespeichert werden. Zusätzlich ist es möglich, beliebige weitere Daten für Benutzer
abzuspeichern (diese nennt man Attribute).

Benutzertypen und -gruppen
--------------------------

Jedem Benutzer wird genau ein Benutzertyp zugeordnet. Diese entsprechen der Zugehörigkeit innerhalb der Schule (bspw.
Lehrkraft, Schülerin/Schüler, Elternteil, Sekretariat, Andere). Es lassen sich auch eigene Benutzertypen definieren.

Zusätzlich dazu lassen sich Benutzergruppen definieren. Benutzer können Mitglied mehrerer Benutzergruppen sein. Benutzergruppen
lassen sich zum Beispiel für Arbeitsgemeinschaften oder Administratoren definieren.

Der Vorteil der Benutzertypen und -gruppen wird im Rechtesystem ausgespielt.

Zentraler Anmeldedienst
#######################

Der Identity Provider ist als Benutzerdatenbank die Anmeldeschnittstelle, Stichwort Single-Sign-On. Dies wird mithilfe
des `SAML-Protokolls <https://de.wikipedia.org/wiki/Security_Assertion_Markup_Language>`_ realisiert.

Der Ablauf sieht dabei folgendermaßen aus:

.. figure:: https://upload.wikimedia.org/wikipedia/en/0/04/Saml2-browser-sso-redirect-post.png
   :align: center

   Ablauf eines SAML-Authentifizierungsanfrage (© Tom Scavo, CC BY-SA 3.0, `zum Bild <https://en.wikipedia.org/wiki/File:Saml2-browser-sso-redirect-post.png>`_)

1. Der Benutzer möchte sich an einem Dienst (bspw. dem ICC) anmelden und öffnet dazu die URL des Dienstes.
   Der Dienst schaut nach, welche Identity Provider findet. Dabei ist dieser Identity Provider als einziger hinterlegt.
2. Der Dienst antwortet dem Benutzer mit einer Weiterleitungsseite zum Identity Provider.
   Diese Seite enthält die Anfrage für das Single-Sign-On.
3. Der Browser navigiert automatisch zum Identity Provider (mit der Anfrage). Dort muss der Benutzer
   sich authentifizieren. Es wird anschließend geprüft, ob der Benutzer die Berechtigung besitzt, den Dienst
   zu nutzen.
4. Der Identity Provider antwortet mit einer Weiterleitungsseite, die zurück zum Dienst weiterleitet.
   Ähnlich zu Schritt 2 enthält diese Weiterleitung die Antwort auf die Anfrage (aus Schritt 2).
5. Der Dienst nimmt die Anfrage an und wertet diese aus. Beim ICC erfolgt nun die Verknüpfung von Benutzern
   zu Schülerinnen und Schülern bzw. Lehrkräften.

Ab nun ist der Benutzer beim Dienst angemeldet.

Rechtesystem
############

Dienste und Attributwerte lassen sich sowohl pro Benutzertyp als auch pro Benutzergruppe und Benutzer freischalten bzw.
definieren.

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