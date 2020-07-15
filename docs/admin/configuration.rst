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

ADAUTH_FINGERPRINT
##################

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
Als Entity ID wird in der Regel die URL der Anwendung (bspw. ``https://icc.example.com/`` verwendet).

MAILER_FROM
###########

E-Mail-Adresse des Absenders von E-Mails aus der Anwendung heraus, bspw. ``noreply@schulit.de``.

MAILER_LIMIT
############

Bei einigen Anbietern kann man nur eine bestimmte Anzahl an E-Mails pro Minute versenden (bspw. bei Office 365). Diese Anzahl
hier eintragen. Bei unbegrenzter Anzahl kann ein hoher Wert (bspw. ``99999``) angegeben werden.

CRON_PASSWORD
#############

Das Passwort für den Cronjob-Benutzer, welcher Cronjobs über eine HTTP-Anfrage ausführt. Siehe Cronjobs.

REGISTRATION_DOMAIN_BLOCKLIST
#############################

Hier können Domänen angegeben werden, die nicht als Anmelde-E-Mail-Adresse bei der Selbstregistrierung genutzt werden
dürfen. Mehrere Domänen werden mit einem Semikolon separiert. 

.. warning:: Die Domänen müssen explizit und exakt angegeben werden. Wildcards (``*.example.com``) werden aktuell nicht unterstützt.

PROVISIONING_LIMIT
##################

Wenn Benutzer über eine CSV-Datei importiert werden, werden die Passwörter zunächst als Klartext
abgespeichert, da das Hashen der Kennwörter sehr viel Zeit in Anspruch nimmt. Das Hashen übernimmt
eine Hintergrundaufgabe. Mit diesem Parameter wird angegeben, wie viele Benutzer die Aufgabe pro Durchlauf
abarbeiten soll.

Hinweis: Die meisten Webspace-Anbieter garantieren eine Skript-Laufzeit von maximal 30s. 

HELPDESK_MAIL
#############

Hier kann eine E-Mail-Adresse angegeben werden, die unterhalb der Anmeldemaske als E-Mail-Adresse bei Problemen
angezeigt wird. Wenn dieser Parameter leer ist, so wird die Information nicht angezeigt.

DATABASE_URL
############

Verbindungszeichenfolge für die Datenbankverbindung. Aktuell unterstützt das ICC ausschließlich MySQL/MariaDB-Datenbanken
ab Version MySQl 5.7. Die Zeichenfolge setzt sich dabei folgendermaßen zusammen:

.. code-block:: shell

    mysql://USERNAME:PASSWORD@HOST:3306/NAME

- ``USERNAME``: Benutzername der Datenbank
- ``PASSWORD``: zugehöriges Passwort des Datenbankbenutzers
- ``HOST``: Hostname des Datenbankservers
- ``NAME``: Name der Datenbank

Weitere Informationen (englisch) gibt `hier <https://www.doctrine-project.org/projects/doctrine-dbal/en/latest/reference/configuration.html#connecting-using-a-url>`_.

MAILER_URL
##########

Verbindungszeichenfolge für das E-Mail-Postfach, welches zum Versand von E-Mails verwendet werden soll. Beispiele:

- Generischer SMTP-Versand: ``smtp://SMTPSERVER:465?encryption=ssl&auth_mode=login&username=USERNAME&password=PASSWORD``
- Google Mail-Postfach: ``gmail://USERNAME:PASSWORD@localhost``

Dabei sind die Parameter ``SMTPSERVER``, ``USERNAME`` und ``PASSWORD`` entsprechend anzupassen.
