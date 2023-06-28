---
sidebar_position: 2
---

# Synchronisationsregeln

Die Synchronisationsregeln können über das Web Interface verwaltet werden (im Punkt *Verwaltung ➜ AD Synchronisationsregeln*).

## Synchronisationsregeln (allgemein)

Allgemeine Synchronisationsregeln legen zunächst nur fest, welche Benutzer sich grundsätzlich über das Active Directory
verwaltet werden. In der Regel sind dies Lehrkräfte und Schülerinnen und Schüler.

Bei den Regeln wird festgelegt, welchem Benutzertyp ein Benutzer zugeordnet wird, wenn er aus dem Active Directory importiert
wird. Die Zuordnung erfolgt entweder aufgrund der Organisationseinheit des Benutzers im Active Directory oder anhand 
einer Gruppe. 

:::warning Warnung
Bei der Verwendung von Gruppen muss darauf geachtet werden, dass ein Benutzer nicht in mehreren Gruppen Mitglied ist, für
die es auch Synchronisationsregeln gibt. In diesem Fall ist nicht spezifiziert, welche Regel "gewinnt".
:::

:::tip Tipp
Es wird empfohlen, die Regeln basierend auf Organisationseinheiten festzulegen.
:::

Die Angabe der Organisationseinheit bzw. der Gruppe erfolgt grundsätzlich mittels `Distringuished Name` (DN). Bei Organisationseinheiten
gilt zudem, dass auch untergeordnete Organisationseinheiten einbezogen werden.

Beispiel: Regel mit OU: `OU=Schueler,DC=ad,DC=schulit,DC=de` schließt auch Benutzer in der untergeordneten OU `OU=5A,OU=Schueler,DC=ad,DC=schulit,DC=de` ein.

## Synchronisationsregeln für Klassen

Die Synchronisationsregeln für Klassen legen fest, welchen Wert das Attribut `Klasse` automatisch zugewiesen wird. 

Es gelten alle Regeln, Tipps etc. aus dem Abschnitt *Synchronisationsregeln (allgemein)*.

## Synchronisationsregeln für Benutzerrollen

Die Synchronisationsregeln für Benutzerrollen legen fest, welchen Benutzerrollen einem Benutzer automatisch zugewiesen werden.
Sind mehrere Regeln zutreffend, werden alle entsprechenden Benutzerrollen zugewiesen.

Es gelten alle Regeln, Tipps etc. aus dem Abschnitt *Synchronisationsregeln (allgemein)* (mit Ausnahme der Empfehlung,
Organisationseinheiten zu verwenden).

:::info
Wurde einem Benutzer eine Rolle über eine Synchronisationsregel zugeordnet, so bleibt diese so lange aktiv, bis sie
dem Benutzer händisch entzogen wird. Eine Nicht-Mitgliedschaft in einer Organisationseinheit/Gruppe sort nicht dafür,
dass die Rolle automatisch entzogen wird.
:::