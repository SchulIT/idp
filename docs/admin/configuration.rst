Konfiguration
=============

Konfigurationsdatei anlegen
---------------------------

Die Vorlage für die Konfigurationsdatei befindet sich in der Datei ``.env``. Von dieser Datei muss eine Kopie ``.env.local`` erzeugt werden.
Anschließend muss die Datei angepasst werden.

.. code-block:: shell

    $ cp .env .env.local

Konfigurationseinstellungen
---------------------------

APP_ENV
#######

Dieser Wert muss immer ``prod`` enthalten, sodass das System in der Produktionsumgebung ist.

.. warning:: Niemals ``dev`` in einer Produktivumgebung verwenden.

APP_SECRET
##########

Dieser Wert muss eine zufällige Zeichenfolge beinhalten. Diese kann beispielsweise mit ``openssl rand -base64 32`` erzeugt werden

APP_URL
#######

Dieser Wert beinhaltet die URL zur Instanz, bspw. https://sso.example.com/

ADAUTH_ENABLED
##############

Gibt an, ob die Anmeldung mittels Active Directory aktiviert ist.

ADAUTH_URL
##########

Gibt die URL zum Active Directory Authentication Server an, bspw. ``tls://your-static-ip:55117``. Dabei ist die 
``your-static-ip`` durch die IP-Adresse oder den Hostnamen zu ersetzen, unter dem der Server erreichbar ist. Die
Portnummer ``55117`` muss angepasst werden, sofern der Server nicht über diesen Standard-Port erreichbar ist.

ADAUTH_PEERNAME
###############

Peername des Serverzertifikats vom Active Directory Authentication Server.

ADAUTH_PEERFINGERPRINT
######################

Fingerabdruck des Serverzertifikats vom Active Directory Authentication Server.

APP_NAME
########

Name des Identity Providers, kann nach Belieben geändert werden.

APP_LOGO
########

Pfad zum großen Logo für den Fußbereich. Das Bild muss im ``public``-Ordner (oder einem Unterordner) abgelegt werden.

APP_SMALLLOGO
#############

Pfad zum kleinen Logo für den Kopfbereich. Das Bild muss im ``public``-Ordner (oder einem Unterordner) abgelegt werden.

SAML_ENTITY_ID
##############

ID des ICCs, welche für SAML-Anfragen (Authentifizierung) genutzt wird. Dieser Wert muss mit dem Wert im Identity Provider übereinstimmen.
Als Entity ID wird in der Regel die URL der Anwendung (bspw. ``https://sso.example.com/`` verwendet).

MAILER_FROM
###########

E-Mail-Adresse des Absenders von E-Mails aus der Anwendung heraus, bspw. ``noreply@schulit.de``.

CRON_PASSWORD
#############

Das Passwort für den Cronjob-Benutzer, welcher Cronjobs über eine HTTP-Anfrage ausführt. Siehe Cronjobs.

DATABASE_URL
############

Verbindungszeichenfolge für die Datenbankverbindung. Aktuell unterstützt das ICC ausschließlich MySQL/MariaDB-Datenbanken
ab Version MariaDB 10.4 Die Zeichenfolge setzt sich dabei folgendermaßen zusammen:

.. code-block:: shell

    mysql://USERNAME:PASSWORD@HOST:3306/NAME?serverVersion=mariadb-10.4.0

- ``USERNAME``: Benutzername der Datenbank
- ``PASSWORD``: zugehöriges Passwort des Datenbankbenutzers
- ``HOST``: Hostname des Datenbankservers
- ``NAME``: Name der Datenbank
- den Parameter `serverVersion` entsprechend der MariaDB-Version anpassen

Weitere Informationen (englisch) gibt `hier <https://www.doctrine-project.org/projects/doctrine-dbal/en/latest/reference/configuration.html#connecting-using-a-url>`_.

MAILER_DSN
##########

Verbindungszeichenfolge für das E-Mail-Postfach, welches zum Versand von E-Mails verwendet werden soll. Beispiele:

- Generischer SMTP-Versand: ``smtp://SMTPSERVER:465?encryption=ssl&auth_mode=login&username=USERNAME&password=PASSWORD``
- Google Mail-Postfach: ``gmail://USERNAME:PASSWORD@localhost``

Dabei sind die Parameter ``SMTPSERVER``, ``USERNAME`` und ``PASSWORD`` entsprechend anzupassen.

PHP_BINARY
##########

Wenn Cronjobs ausgeführt werden, werden diese in einem separaten Prozess mithilfe dieser PHP Executable ausgeführt. Diese
Variable sollte auf die entsprechende PHP-Version (z.B. ``/usr/bin/php`` oder ``/usr/bin/php8.2``) gesetzt werden. Anderenfalls
kann die Ausführung von Cronjobs fehlerhaft sein.