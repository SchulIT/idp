---
sidebar_position: 100
---

# Benutzer

Unter *Benutzer* können die Benutzer verwaltet werden.

## Benutzer bearbeiten

Benutzer können über das Drei-Punkte-Menü bearbeitet werden. Dabei ist zu beachten, dass bei Benutzern, die aus dem 
Active Directory importiert wurden, einige Felder nicht bearbeitet werden können. Damit die Daten stets konsistent sind,
müssen solche Felder im Active Directory geändert werden und anschließend durch eine Synchronisation ins Single Sign-On
übertragen werden.

:::caution Achtung
Aktuell ist es nicht möglich, den Benutzernamen die Anmelde-E-Mail-Adresse eines Benutzers zu ändern. Dies muss bisher
manuell über die Datenbank erfolgen. [➜ GitHub Issue #74](https://github.com/SchulIT/idp/issues/74)
:::

## Passwort zurücksetzen

### Active Directory Accounts

Die Änderung erfolgt über den [Active Directory Authentication Server](../guides/active_directory/intro). Dazu benötigt
es jedoch einen Benutzeraccount, der Passwörter ändern kann (siehe Anleitung des Authentication Servers auf GitHub). Die
Zugangsdaten für diesen Passwortadministrator müssen unter *Admin-Benutzername* bzw. *Admin-Passwort* eingetragen werden.
Zusätzlich muss der Benutzer das neue Passwort zweimal eingeben.

:::note Hinweis
Der Benutzername kann entweder als Benutzername (`sAMAccountName`) oder als `UserPrincipalName` (also E-Mail-Adresse) angegeben werden.
:::

### Nicht-Active Directory Accounts

#### Explizite Änderung des Passwortes

Über die Bearbeiten-Funktion im Drei-Punkte-Menü eines Benutzers kann das Passwort händisch geändert werden. Dabei kann
auch festgelegt werden, dass das Passwort bei der nächsten Anmeldung geändert werden muss (empfohlen).

#### Änderung des Passwortes im Self-Service

Idealerweise geben die Eltern bei der Registrierung eine E-Mail-Adresse an. In diesem Fall kann die *Passwort vergessen*-Funktion
verwendet werden. Dabei verschickt das System eine E-Mail mit einem Link zum Ändern des Passwortes an den Benutzer. 

:::caution Wichtig
Der Link ist 24 Stunden gültig.
:::

#### Änderung des Passwortes durch Eltern

Wurde keine E-Mail-Adresse hinterlegt, so kann ebenso eine E-Mail mit einem Link zum Ändern des Passwortes an den Benutzer
versendet werden. Dazu über das Drei-Punkte-Menü die *Passwort zurücksetzen*-Funktion aufrufen und die E-Mail-Adresse der Eltern
händisch eintragen.

:::caution Wichtig
Der Link ist 24 Stunden gültig.
:::

## Benutzer löschen

Über das Drei-Punkte-Menü können Benutzer gelöscht werden. Es erfolgt keine Bestätigung.

:::tip Wichtiger Hinweis
Benutzer werden zunächst in den Papierkorb verschoben und nach 30 Tagen automatisch gelöscht. Über die Funktion *Gelöschte Benutzer*
können gelöschte (aber nicht endgültig gelöschte) Benutzer reaktiviert oder endgültig gelöscht werden.
:::

:::caution Wichtiger Hinweis bei der Verwendung von AD Connect
Verwendet man den AD Connect Client, so werden Benutzer beim ersten Löschen ebenfalls in den Papierkorb verschoben. Beim 
erneuten Löschen werden die Benutzer dann unwiederruflich gelöscht.
:::

## Benutzer wechseln

Siehe [Benutzer wechseln (Impersonation)](../guides/impersonation).

## Kiosk-Benutzer

Kiosk-Benutzer sind Benutzer für öffentliche Terminals, beispielsweise ein Terminal im Lehrerzimmer. Es handelt sich dabei
grundsätzlich um normale Benutzer, welche sich neben einer Kombination aus E-Mail-Adresse und Passwort auch über eine URL
anmelden können. Diese URL wird dann am Terminal als Start-URL festgelegt (Terminologie kann abhängig vom verwendeten System
variieren).

Um einen Kiosk-Benutzer zu erstellen, muss zunächst unter *Benutzer* ein Benutzer mit den entsprechend gewünschten Rechten
und freigeschalteten Diensten erstellt werden. Anschließend kann für diesen Benutzer eine Kiosk-URL erstellt werden, über
den der Benutzer automatisch angemeldet werden kann. Dabei muss auch eine (oder mehrere durch Komma getrennte) IP-Adresse(n) festgelegt werden,
für den diese URL gültig ist. Das verhindert, dass der Kiosk-Zugang z.B. von Zuhause aus funktioniert.

:::tip Tipp
Es ist ratsam, dem Kioskbenutzer ein nicht oder nur sehr schwer erratbares Passwort zu vergeben, da die Zugangsdaten (also E-Mail-Adresse
und Passwort) grundsätzlich von überall funktionieren. Die IP-Einschränkung gilt nur für die URL.
:::

:::warning Achtung
Die Angabe eines IP-Bereiches (bspw. mittels CIDR-Notation) wird nicht unterstützt.
:::

## Benutzer importieren

Siehe [CSV-Import](../import/csv).