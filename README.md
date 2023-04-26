# Identity Provider

![PHP 8.1](https://img.shields.io/badge/PHP-8.1-success.svg?style=flat-square) 
![AGPL-3.0](https://img.shields.io/github/license/schulit/idp.svg?style=flat-square)

Ein SAML Identity Provider, welcher für die SchulIT Software Suite benötigt wird.

## Funktionen

Dieser Identity Provider ist das Herzstück der SchulIT Software Suite, da er für die Anmeldung der Benutzer zuständig ist.

* Benutzerverwaltung
    * Cloud-Benutzer können über das Web-Interface angelegt werden
    * On-Premise-Benutzer können aus dem Active Directory synchronisiert werden, sodass ein Single-Sign-On mit dem Kennwort aus der Schule möglich ist (zusätzliche Software benötigt)
* Benutzertypen- und -rollenmanagement
    * jeder Benutzer ist einem Benutzertypen (Lehrkraft, Schülerin/Schüler, ...) zugeordnet
    * jeder Benutzer kann Mitglied in mehreren Benutzergruppen sein
    * jedem Benutzertyp und jeder -rolle können individuelle Rechte (bspw. Rollen in einer Software oder Zugriff auf eine Software) vergeben werden, die auf alle Benutzer übertragen wird
* Dienstverwaltung
    * alle integrierten Anwendungen können als Dienst integriert werden
    * Zugriffsverwaltung erfolgt über diesen Identity Provider
    * Zugriffssteuerung basierend auf Benutzertypen und -rollen
* Eltern-Registrierung im Self-Service
    * Eltern erhalten Code über die Schule und können sich damit selbstständig registrieren
    * Verknüpfung mit Kind eigenständig oder durch Administrator möglich

## Handbuch

Die Installation und Nutzung sind im [Handbuch](https://schulit-idp.readthedocs.org) beschrieben.

## Lizenz

[AGPL-3.0](LICENSE)