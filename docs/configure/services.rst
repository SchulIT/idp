Dienste
=======

Jede Anwendung, die diesen Identity Provider zu Authentifizierung und Authorisierung nutzt, wird als Dienst bezeichnet.
Grundsätzlich wird jede Anwendung unterstützt, die das SAML-Protokoll unterstützt.

Benötigte Daten
###############

Um einen neuen Dienst zu registrieren, werden neben dem gewünschten Anzeigenamen und einer Beschreibung weitere Daten
benötigt:

- Entity ID: eine Art ID, die den Dienst eindeutig identifiziert. Dies ist in der Regel die URL, unter der der Dienst erreichbar ist (bspw. ``https://icc.example.com/``)
- Assertion Customer Service URL: die URL, an der der Benutzer nach der erfolgreichen Anmeldung und Authorisierung weitergeleitet wird.
- Zertifikat: das Zertifikat des Dienstes, welches zur Verschlüsselung der Daten zwischen Identity Provider und Dienst genutzt wird.

Dienst erstellen
################

Den Dienst erstellt man in der :fa:`cogs` Verwaltung unter :fa:`th` Dienste. Dort trägt man alle benötigten Informationen ein.

Im Anschluss muss geprüft werden, ob für den Dienst bestimmte Attribute (bspw. zur Festlegung der Rollen) angelegt werden müssen.