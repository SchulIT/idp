Installation
============

Schritt 1: Anwendung installieren
---------------------------------

Möglichkeit 1: Installation mit Git
###################################

Zunächst mittels Git den Quelltext des Projektes auschecken:

.. code-block:: shell

    $ git clone https://github.com/schulit/idp.git
    $ git checkout -b 1.0.0

Dabei muss ``1.0.0`` durch die gewünschte Version ersetzt werden.

Anschließend in das Verzeichnis ``icc`` wechseln und alle Abhängigkeiten installieren:

.. code-block:: shell

    $ composer install --no-dev --classmap-authoritative --no-scripts

Die Direktive ``no-scripts`` ist wichtig, da es ansonsten zu Fehlermeldungen kommt.

.. warning:: Der folgende Teil funktioniert nur, wenn Node und npm verfügbar sind. Falls die beiden Tools nicht verfügbar sind, müssen die Dateien manuell hochgeladen werden.

Nun müssen noch die Assets installiert werden:

.. code-block:: shell

    $ npm install
    $ npm install production

Möglichkeit 2: Installation ohne Git
####################################

Den Quelltext der Anwendung von `GitHub <https://github.com/schulit/idp/releases>`_ herunterladen und auf dem Webspace
entpacken. Anschließend kann mit der Konfiguration fortgefahren werden.

Schritt 2: Konfiguration
------------------------

Nachdem der Quelltext und seine Abhängigkeiten installiert sind, müssen alle benötigten Zertifikate und Konfigurationsdateien erstellt werden.

Schritt 2.1: Konfigurationsdatei erstellen
##########################################

Siehe `Konfiguration <configuration.html>`_.

Schritt 2.2: Zertifikate erstellen
##################################

Damit der Identity Provider sich gegenüber den einzelnen Anwendungen ausweisen kann, wird ein Zertifikat benötigt.
Das Zertifikat kann über die Konsole erstellt werden:

.. code-block:: shell

    $ php bin/console app:create-certificate --type saml

Anschließend werden einige Daten abgefragt. Diese können abgesehen vom ``commonName`` frei beantwortet werden.

- ``countryName``, ``stateOrProvinceName``, ``localityName`` geben den Standort der Schule an
- ``organizationName`` entspricht dem Namen der Schule
- ``organizationalUnitName`` entspricht der Fachabteilung der Schule, welche das ICC administriert, bspw. Schulname und IT-Suffix
- ``commonName`` Domainname des ICCs, bspw. ``sso.example.com``
- ``emailAddress`` entspricht der E-Mail-Adresse des Administrators

Schritt 3: Installation abschließen
-----------------------------------

Nun folgende Kommandos ausführen, um die Installation abzuschließen:

.. code-block:: shell

    $ php bin/console cache:clear
    $ php bin/console doctrine:migrations:migrate --no-interaction
    $ php bin/console app:setup
    $ php bin/console shapecode:cron:scan

Schritt 4: Ersten Benutzer erstellen
------------------------------------

Nun muss ein Benutzer erstellt werden. Dieser muss als Administrator eingerichtet werden, sodass
man alle weiteren Konfigurationsschritte über das Web Interface erledigen kann.

Als Benutzernamen wählt man eine (beliebige) E-Mail-Adresse aus. Diese muss nicht mit der echten E-Mail-Adresse
übereinstimmen. 

**Wichtig:** Der Benutzer muss als Administrator (Schritt 7) angelegt werden.

.. code-block:: shell

    $ php bin/console app:add-user

      Benutzername:
      > admin@example.com

      Vorname:
      > Erika

      Nachname:
      > Mustermann

      E-Mail:
      > admin@example.com

      Passwort:
      >

      Passwort wiederholen:
      >

      Ist der Benutzer ein Administrator? (yes/no) [yes]:
      > yes

      Benutzertyp wählen [user]:
        [0] user
      > user

      [OK] Benutzer erfolgreich erstellt

Schritt 5: Identity Provider im Webspace einrichten
---------------------------------------------------

Der Identity Provider muss auf einer Subdomain (bspw. ``sso.example.com``) betrieben werden. Das Betreiben des Identity Providers in einem Unterordner
wird nicht unterstützt.

.. warning:: Der Root-Pfad der Subdomain muss auf das ``public/``-Verzeichnis zeigen. Anderenfalls funktioniert das ICC nicht und es können wichtige Konfigurationsdaten abgerufen werden.