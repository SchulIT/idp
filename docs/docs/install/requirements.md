---
sidebar_position: 1
---

# Voraussetzungen

## Software
* Webserver
  * Apache 2.4+ oder
  * nginx
* PHP 8.2+ mit folgenden Erweiterungen
  * ctype
  * dom
  * filter
  * iconv
  * json
  * mbstring
  * openssl
  * pdo_mysql
  * phar
  * simplexml
  * sodum
  * tokenizer
  * xml
  * xmlwriter
* MariaDB 10.11+ (ein kompatibles MySQL kann funktionieren, ist jedoch nicht getestet)
* Composer 2+
* Git (zum einfachen Einspielen der Software)
* NodeJS >= 18 inkl NPM (zum Erstellen der Javascript- und CSS-Dateien)

Die Software muss auf einer Subdomain betrieben werden. Das Betreiben in einem Unterverzeichnis wird nicht unterstützt.

:::tip Hinweis
Theoretisch ist es auch ohne Git und NodeJS möglich, die Software zu installieren. Dazu kann der Quelltext mittels GitHub
heruntergeladen werden. Die Assets müssen dann jedoch auf einer Maschine erzeugt werden, wo Node und NPM verfügbar sind.
Dann muss das gesamte `/public/build`-Verzeichnis nach dem Erstellen der Assets auf den Webspace kopiert werden.
:::

### Empfohlene Software (optional)

* PHP acpu-Erweiterung

## Hardware

An die Hardware stellt das System keine besonderen Anforderungen. Die Datenbankgröße wird - abhängig von der Anzahl der
Benutzer - vermutlich unter 100 MB bleiben.