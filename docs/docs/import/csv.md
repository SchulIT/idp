---
sidebar_position: 2
---

# CSV

Zunächst muss eine CSV-Datei mit den folgenden Spalten erzeugt werden:

```csv
E-Mail,Passwort,Vorname,Nachname,Klasse,ID
max.mustermann@mail.example.com,Test1234$,Max,Mustermann,5A,42
erika.musterfrau@mail.example.com,Test1234$,Erika,Musterfrau,EF,100
```

Erläuterungen:
* Die Felder Passwort, Vorname, Nachname, Klasse und ID dürfen leer sein.
* Das Feld ID speichert eine ID für das Feld `externe ID` - anhand dieser ID können Benutzer aktualisiert werden, wenn sich z.B. der Name und somit die E-Mail-Adresse ändert
* Die E-Mail-Adresse ist gleichzeitig auch die Anmelde-E-Mail-Adresse. 
* Die Reihenfolge der Spalten ist beliebig.

:::info
Das Erstellen von Elternaccounts mittels CSV wird nicht unterstützt (auch wenn dies theoretisch möglich ist). Siehe [hier](../general/parent_accounts).
:::

## Benutzer importieren

Den Import startet man über das Web Interface über *Benutzer ➜ Benutzer importieren*. Dort wählt man zunächst die CSV-Datei aus.
Anschließend überprüft man, ob das Trennzeichen (, oder ;) passt. Zum Schluss wählt man noch aus, welchem Benutzertyp die 
importieren Benutzer zugewiesen werden sollen.

![](/img/import/csv/csv-1.png)

Nun überprüft man die Daten, ändert sie ggf. ab und klickt auf *➜ fertigstellen*.

![](/img/import/csv/csv-2.png)

:::caution Warnung
Nach dem CSV-Import dauert es eine Weile, bis die Benutzer provisioniert wurden. Da das Hashen der Passwörter Zeit in 
Anspruch nimmt, wird diese Aufgabe von einer Hintergrundaufgabe erledigt.
:::

Man erkennt noch nicht bereitgestellte Benutzer am Tag *Bereitstellung ausstehend*:

![](/img/import/csv/csv-provisioning-pending.png)

Sobald das Tag verschwunden ist, kann sich der Benutzer anmelden.