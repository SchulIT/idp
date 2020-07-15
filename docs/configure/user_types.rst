Benutzertypen
=============

Jedem Benutzer wird genau ein Benutzertyp zugeordnet. Dieser kann (muss aber nicht) seiner Funktion
an der Schule entsprechen (bspw. Lehrkraft, Sekretariat, Lernende, ...).

Der Benutzertyp wird in Form zweier Attribute (sowohl das SAML-Attribut ``eduPersonAffiliation`` als auch das 
selbst-definierte Attribut ``urn:type``) an Dienste wie das ICC, das Service Center oder andere SAML-kompatible 
Dienste weitergegeben.

Standard-Benutzertyp Benutzer
#############################

Standardmäßig gibt es den Benutzertyp *Benutzer*. Dieser Benutzertyp sollte allen Benutzern zugewiesen
werden, die keine bestimmte Funktion an der Schule einnehmen (bspw. externe Benutzer oder Administratoren).

Standard-Typen erzeugen
#######################

In der Verwaltung :fa:`cogs` können unter :fa:`user-cog` *Benutzertypen* die Standard-Benutzertypen
für Eltern, Lehrkräfte, Lernende und Sekretariat angelegt werden. 

Weitere Benutzertypen erzeugen
##############################

Neben den Standard-Typen lassen sich auch weitere Benutzertypen nach belieben erstellen. Dazu müssen folgende Informationen
eingegeben werden:

- Name: Anzeigename des Benutzertyps
- Alias: Kurzname des Benutzertypes (sollte keine Leer- oder Sonderzeichen enthalten). Wird als Attributwert genutzt bei der Weitergabe an Dienste genutzt.
- eduPersonAffiliation: Gibt an, welche Rolle Benutzer dieses Benutzertyps im Hinblick auf die Schule einnehmen.
- Dienste (optional): Die hier angegebenen Dienste sind für Benutzer mit diesem Benutzertyp immer freigeschaltet.

Unter Attribute können nun einzelne Attributwerte festgelegt werden. **Wichtig:** Diese Werte können durch Benutzerrollen oder explizit beim Benutzer gesetzte Werte
überschrieben werden (sie werden nicht zusammengeführt).

