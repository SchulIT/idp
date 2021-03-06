Cronjobs
========

Das System muss einige wiederkehrende Aufgaben ausführen, bspw. das Versenden von Benachrichtigungen. Der Administrator
muss daher sicherstellen, dass diese Aufgaben automatisiert ausgeführt werden.

Möglichkeit 1: Cronjobs mittels HTTP-Anfrage ausführen
------------------------------------------------------

Viele Webspace-Betreiber bieten die Möglichkeit, Cronjobs in Form von HTTP-Anfragen zu realisieren. Dazu wird dann in
einem fest definierten Zeitintervall eine Website vom System aufgerufen.

Vorbereitung: Passwort festlegen
################################

Zunächst muss das Passwort für den Cronjob generiert werden.

.. code-block:: shell

    $ php bin/console security:encode-password --no-interaction [password] Symfony\Component\Security\Core\User\User

In der Ausgabe ist das Passwort enthalten:

.. code-block:: shell

 ------------------ ---------------------------------------------------------------------------------------------------
  Key                Value
 ------------------ ---------------------------------------------------------------------------------------------------
  Encoder used       Symfony\Component\Security\Core\Encoder\MigratingPasswordEncoder
  Encoded password   $argon2id$v=19$m=65536,t=4,p=1$YXNrRzRXZGZwdi51S202eQ$DlMW6D+P896CMTj1U/Jn7KssfJqLcU98Q+lIm+AVOmk
 ------------------ ---------------------------------------------------------------------------------------------------

Der Wert von ``Encoded password`` muss in der ``.env.local`` unter ``CRON_PASSWORD`` eintragen werden.

.. warning:: Der Wert des Passwortes ändert sich mit jedem Aufruf des Kommandos, auch wenn das Passwort identisch ist.

Vorbereitung: PHP-Version spezifizieren
#######################################

Einige Webhoster bieten mehrere PHP-Versionen gleichzeitig an. Der Identity Provider benötigt jedoch mindestens PHP 7.4.
Falls die Ausgabe

.. code-block:: shell

    $ php --version

nicht PHP 7.4 anzeigt, muss noch angegeben werden, wo das Executable von PHP 7.4 zu finden ist. Bei manchen Hostern kann
man ``php74`` verwenden, um PHP 7.4 zu nutzen. Dann mittels ``which php74`` den Pfad zur Executable herausfinden und anschließend
in der ``.env.local`` (ganz unten) ergänzen:

.. code-block:: text

    PHP_BINARY=/usr/bin/php74

Cronjob einrichten
##################

Folgende Daten in der Cronjob-Verwaltung des Webspace festlegen

- URL: ``https://sso.example.com/cron`` (``sso.example.com`` durch die echte Adresse des ICC ersetzen)
- Intervall: alle zwei Minuten
- Authentifizierung:
    - Art: HTTP-Basic
    - Benutzername: cron
    - Passwort: wurde oben festgelegt

Möglichkeit 2: Cronjobs auf Betriebssystemebene ausführen
---------------------------------------------------------

Falls man einen eigenen Server betreibt, können Cronjob-Programme wie crontab oder systemd genutzt werden, um ein PHP-Skript
in regelmäßigen Abständen auszuführen. Dazu im Cronjob-Programm folgendes Skript alle zwei Minuten ausführen lassen:

.. code-block:: shell

    php /path/to/sso/bin/console shapecode:cron:run

