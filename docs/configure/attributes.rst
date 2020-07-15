Attribute
=========

Mithilfe von Attributen lassen sich Daten für Benutzer im Identity Provider ablegen. Unterstützt werden dabei:

- einfache Zeichenketten (Freitext)
- vordefinierte Werte (Zeichenketten), hier ist eine Mehrfachauswahl möglich

Standard-Attribute
##################

Jeder Benutzer verfügt standardmäßig über folgende Attribute:

======================================================================  ========================================================================================================
Attribut                                                                Beschreibung
======================================================================  ========================================================================================================
urn:id                                                                  Eindeutige UUID, die der Benutzer im Identity Provider besitzt.
http://schemas.xmlsoap.org/ws/2005/05/identity/claims/surname           Nachname
http://schemas.xmlsoap.org/ws/2005/05/identity/claims/givenname         Vorname
http://schemas.xmlsoap.org/ws/2005/05/identity/claims/emailaddress      E-Mail-Adresse
urn:external-id                                                         `Externe ID <../general/external_id.html>`_
urn:services                                                            Liste von Diensten, für die der Benutzer freigeschaltet ist. Wird jeweils als JSON-Objekt serialisiert.
urn:grade                                                               Klasse des Benutzers (falls vorhanden)
urn:type                                                                Alias des Benutzertyps
eduPersonAffiliation                                                    eduPersonAffiliation des Benutzertyps
======================================================================  ========================================================================================================

Benutzerdefinierte Attribute
############################

Neben den Standard-Attributen lassen sich auch weitere Attribute definieren. Dabei lässt sich auch festlegen, welche
Dienste das Attribut auslesen können. Sie lassen sich in der :fa:`cogs` Verwaltung unter :fa:`list-alt` Attribute verwalten.

Beim Anlegen müssen folgende Informationen angegeben werden:

- Name: eindeutiger Name des Attributes. Wird intern verwendet und darf nicht mehrfach verwendet werden.
- Anzeigename: wird als Anzeigename bei der Anzeige der Attribute verwendet.
- Beschreibung: kurze Beschreibung, wozu das Attribut verwendet wird. Wird den Benutzern angezeigt.
- Die Option "Benutzer können dieses Attribut ändern" ist selbsterklärend
- SAML Attribut-Name: entspricht dem Attributsnamen, der bei der SAML Antwort eingetragen wird.
- Typ: Text (Freitext) oder Auswahlfeld (vordefinierte Optionen, Mehrfachauswahl möglich, s.u.)
- Dienste: gibt die Dienste an, bei denen das Attibut in der SAML Antwort enthalten sein soll.

Wenn als Typ *Auswahlfeld* ausgewählt wurde, lassen sich die einzelnen Optionen über das :fa:`plus` angeben. Der Schlüssel
entspricht dem Wert, der in der SAML Antwort übertragen wird. Als Wert trägt man den Anzeigenamen ein.

