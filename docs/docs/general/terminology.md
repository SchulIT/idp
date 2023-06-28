---
sidebar_position: 1
---

# Begriffe

## SAML

Protokoll zum Austausch von Authentifizierungs- und Authorisierungsdaten.

Siehe auch:

* [Wikipedia (DE)](https://de.wikipedia.org/wiki/Security_Assertion_Markup_Language>)
* [Wikipedia (EN)](https://en.wikipedia.org/wiki/Security_Assertion_Markup_Language>)

### Identity Provider

Als Identity Provider wird diese Software bezeichnet, da sie für beliebige Dienste
eine Benutzerdatenbank sowie eine Anmeldeprozedur zur Verfügung stellt.

Siehe auch:

* [Wikipedia (DE)](https://de.wikipedia.org/wiki/Identit%C3%A4tsanbieter)
* [Wikipedia (EN)](https://en.wikipedia.org/wiki/Identity_provider)

### Service Provider / Dienst

Als Service Provider oder Dienst wird Software bezeichnet, die den Identity Provider
als Grundlage zur Authentifizierung nutzt.

Siehe auch:

* [Wikipedia (EN)](https://en.wikipedia.org/wiki/Service_provider_(SAML))

### Übertragene Attribute

Die folgenden SAML-Attribute werden standardmäßig übertragen:

| SAML-Attribut                                                      | Beschreibung                                                                                                                                                                                                                                                                                                                                            | Beispiel                                                                           |
|--------------------------------------------------------------------|---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|------------------------------------------------------------------------------------|
| urn:id                                                             | Die eindeutige ID (UUID) des Benutzers in der Benutzerdatenbank. Dieses Attribut kann verwendet werden, um Benutzer wiederzuerkennen, wenn sich z.B. der Benutzername geändert hat (z.B. aufgrund eines Nameswechsels).                                                                                                                                 | da1ada6a-e51f-4c46-b276-ea532e52eead                                               |
| http://schemas.xmlsoap.org/ws/2005/05/identity/claims/surname      | Nachname                                                                                                                                                                                                                                                                                                                                                | Mustermann                                                                         |
| http://schemas.xmlsoap.org/ws/2005/05/identity/claims/givenname    | Vorname                                                                                                                                                                                                                                                                                                                                                 | Erika                                                                              |
| http://schemas.xmlsoap.org/ws/2005/05/identity/claims/emailaddress | E-Mail-Adresse                                                                                                                                                                                                                                                                                                                                          | erika.musterfrau@example.com                                                       |
| urn:external-id                                                    | Externe ID des Benutzers. Wird benötigt, um einen Benutzer in anderen Diensten (z.B. dem ICC) wiederzuerkennen, wenn dort eine Lernenden- oder Lehrkräfte-Datenbank vorliegt. Dieses Attribut wird nur bei Eltern benötigt, da Lernende und Lehrkräfte mittels E-Mail-Adresse wiedererkannt werden. Dieses Attribut trennt mehrere Werte mittels Komma. | max.musterschueler@example.com, tanja.musterschuelerin@example.com                 |
| urn:services                                                       | JSON mit freigeschalteten Diensten. Wird von SchulIT Diensten verwendet, um eine einfache Navigation zwischen den Diensten bereitzustellen. Dieses Attribut lässt mehrere Werte zu.                                                                                                                                                                     | `{    "url": "...",   "name": "...",   "description": "...",   "icon": "..."  }  ` |
| urn:grade                                                          | Klasse des Benutzers oder `NULL`. Dieser Wert sollte nur bei Schülerinnen und Schülern gefüllt sein.                                                                                                                                                                                                                                                    | 05A                                                                                |
| urn:type                                                           | Benutzertyp (Alias) des Benutzers.                                                                                                                                                                                                                                                                                                                      | parent                                                                             |
| eduPersonAffiliation                                               | Art der Zugehörigkeit zur Schule (siehe unten). Dieses Attribut lässt mehrere Werte zu.                                                                                                                                                                                                                                                                 | affiliate                                                                          |

Darüber hinaus werden alle für den jeweiligen Dienst festgelegten Attrbute übertragen.

## Benutzertyp

Jedem Benutzer kann genau ein Benutzertyp zugeordnet werden (z.B. Elternteil, Hausmeister, Lehrkraft, Schülerin/Schüler, ...). 
Jedem Benutzertyp ist ein entsprechender Wert im SAML-Attribut `eduPersonAffiliation` zugeordnet. Dieses Attribut gibt an,
welche Beziehung der Benutzer im Hinblick auf die Schule hat.

Die Standardzuordnung ist:

* Elternteil ➜ affiliate
* Hausmeister ➜ staff
* Lehrkraft ➜ faculity
* Praktikant ➜ affiliate
* Schülerin/Schüler ➜ student
* Sekretariat ➜ staff
* User ➜ member

Das Attribut kann genutzt werden, um in einem Dienst die Zugehörigkeit des Benutzers zur Schule herauszufinden.

:::note Hinweis
Die SchulIT-Dienste nutzen das Attribut (Stand Sommer 2023) nicht. Stattdessen wird der genaue Benutzertyp verwendet.
Es wird als Attribut `urn:type` übertragen. 
:::

## Benutzergruppe