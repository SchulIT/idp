---
sidebar_position: 1
---

# Einstellungen

Unter Verwaltung können im Menüpunkt *Einstellungen* einige Einstellungen vorgenommen werden.

## E-Mail-Adresse für Hilfe

Damit Benutzer (insb. Eltern) einen Ansprechpartner haben, kann eine E-Mail-Adresse für Hilfe angegeben werden. Diese
wird auf der Anmeldeseite angezeigt.

## Passwort-Check aktiv

Wenn diese Option aktiv ist, werden Passwörter mithilfe der Datenbank von [haveibeenpwned.com](https://haveibeenpwned.com)
abgeglichen. Ist das Passwort in der Datenbank, wird die Verwendung des Passwortes verboten.

Ist die Option nicht aktiv, wird keine Prüfung vorgenommen.

:::info
Passwörter werden dabei nicht im Klartext übertragen. Um Sicherheit des Passwortes zu gewährleisten, wird nur ein Teil
des SHA-Hashes übertragen. Weitere Informationen in der [Symfony Dokumentation](https://symfony.com/doc/current/reference/constraints/NotCompromisedPassword.html).
:::

## Mitteilung auf der Login-Seite

Hier kann eine Mitteilung definiert werden, die oberhalb des Logins angezeigt wird. Das ist zum Beispiel nützlich,
wenn es technische Probleme gibt, die Benutzer an der Anmeldung hintern können (z.B. Arbeiten, die den Active Directory
Authentication Server betreffen).

## E-Mail Domäne für Benutzernamen

Hier wird die E-Mail-Domäne angegeben für die Benutzeraccounts von Eltern angegeben. Eltern können den Teil vor dem @-Zeichen
der Anmelde-E-Mail-Adresse frei wählen.