# Upgrade auf 2.0

Beim Upgrade auf Version 1.4 müssen folgende Dinge beachtet werden:

## Änderungen
* es wird PHP 8.2 vorausgesetzt
* Yarn wird nicht mehr verwendet (stattdessen ausschließlich NPM+Webpack)
* Active Directory Connect Client muss auf Version 2 aktualisiert werden
* Active Directory Authentication Server muss auf Version 2 aktualisiert werden
* In der Konfigurationsdatei `.env` wurde `PHP_BINARY` hinzugefügt
* Gelöschte Benutzer werden nicht mehr über die API Schnittstelle ausgegeben
* Das Feld E-Mail-Adresse ist eindeutig
* Alle System-Kommandos sprechen jetzt deutsch

# Neuerungen
* Alle gelöschten Benutzer können mit einem Klick gelöscht werden
* Das Zurücksetzen von Passwörtern ist möglich, wenn die Anmelde-Adresse nicht bekannt ist (sondern die E-Mail-Adresse)
* Das Ändern der Anmelde-E-Mail-Adresse ist nun über das Web GUI möglich
* Registrierungscodes werden nicht mehr in der Datenbank gelöscht, sondern nur als gelöscht markiert (sodass bereits ausgegebene und gelöschte Codes nicht erneut erzeugt werden können)
* Es werden aktive Sitzungen angezeigt mit der Option, sich überall abzumelden
* man kann beim CSV-Import eine echte Synchronisation durchführen (d.h. nicht vorhandene Benutzer in der CSV werden gelöscht)

## Upgrade

Beim Upgrade wird mindestens eine Migration ausgeführt.

**ACHTUNG:** Die Migration erkennt mehrfach genutzte E-Mail-Adressen und löscht diese
bei allen Nutzern. E-Mail-Adressen, die nicht mehrfach genutzt werden, bleiben erhalten.

