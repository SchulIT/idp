---
sidebar_position: 2
---

# Datensicherung

Die Anwendung besitzt von sich aus kein Backup-Skript zur Verfügung. Das Backup muss daher händisch erfolgen.

## Datenbank

Zur Sicherung der Datenbank kann (zum Beispiel) das Tool `mysqlbackup` verwendet werden. 

### Datenbank sichern

Mit dem folgenden Kommando kann die Datenbank gesichert werden:

```bash
$ mysqlbackup -u USER -p DATENBANK > sso.sql
```

`USER` und `DATENBANK` müssen entsprechend durch den Datenbanknutzer und den Namen der Datenbank ersetzt werden. Der 
Parameter `-p` bewirkt, dass das Passwort eingegeben werden muss.

### Datenbank zurückspielen

Mit dem folgenden Kommando kann die Datenbank zurückgespielt werden:

```bash
$ mysql -u USER -p DATENBANK < sso.sql
```

`USER` und `DATENBANK` müssen entsprechend durch den Datenbanknutzer und den Namen der Datenbank ersetzt werden. Der
Parameter `-p` bewirkt, dass das Passwort eingegeben werden muss.

## Wichtige Dateien

Folgende Dateien müssen im Backup enthalten sein:

* `.env.local`
* `certs/idp.crt`
* `certs/idp.key`
* optional: `certs/ca.crt` (bei der Nutzung des Active Directory Authentication Servers)
* optional: `assets/css/custom/*.scss`
* optional: `assets/css/custom/*.scss`
* optional: `public/images/*`