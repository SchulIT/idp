Active Directory-Anbindung
==========================

Hat man ein lokales Active Directory, lassen sich Benutzer einerseits aus dem lokalen Verzeichnis in den Identity Provider
importieren und weiter können Passwörter aus dem lokalen Schulnetzwerk zum Anmelden verwendet werden.

.. warning:: Bevor der Authentication Server oder Connect Client genutzt werden können, müssen die `Synchronisationsregeln <../configure/sync_rules.html>`_ erstellt werden.

Active Directory Authentication Server
######################################

Der Active Directory Authentication Server ist ein Programm, welches im Schulnetzwerk (besser: in einer DMZ) läuft und
Authentifizierungsanfragen (Benutzername und Passwort) entgegennimmt und gegen das Active Directory auswertet. Als Antwort
liefert es im Falle eines Erfolges Informationen über den Benutzer im Active Directory (objectGuid, E-Mail-Adresse,
Vorname, Nachname, Organisationeinheit, Gruppenmitgliedschaften).

Nach einer erfolgreichen Anmeldung wird der Benutzer - sofern er nicht bereits vorhanden ist - im Identity Provider hinterlegt.
Das Passwort wird kryptografisch sicher abgespeichert, sodass sich ein Benutzer auch anmelden kann, falls der Server
ausfällt.

Die Verbindung zwischen dem Identity Provider und dem Active Directory Authentication Server ist mit TLS verschlüsselt.

:fa:`arrow-right` `Zum Active Directory Authentication Server <https://github.com/SchulIT/adauth-server>`_

Active Directory Connect Client
###############################

Mithilfe des Active Directory Connect Clients werden Benutzer aus dem Active Directory im Identity Provider vorab provisioniert.
Dies ermöglicht es, Benutzer zu bearbeiten bevor sie sich initial anmelden. Außerdem übernimmt das Tool das Löschen von
nicht mehr vorhandenen Benutzern.

:fa:`arrow-right` `Zum Active Directory Connect Client <https://github.com/SchulIT/adauth-server>`_