Synchronsiationsregeln
======================

Nutzt man die Möglichkeit, den Active Directory Authentication Server oder Connect Client zur
Authentifizierung bzw. Synchronisation der Benutzer zu verwenden, so müssen Regeln definiert
werden, um die Benutzer korrekt im Identity Provioder anzulegen.

.. warning:: Die Synchronisationsregeln werden bei jedem Import und bei der jeder erfolgreichen Anmeldung angewendet.

Die Synchronisationsregeln können in der Verwaltung :fa:`cogs` unter :fa:`refresh` *AD Synchronisationsregeln* verwaltet werden.

Regeln für Benutzertypen
########################

Wenn Lernende und Lehrkräfte über das Active Directory authentifiziert werden sollen, müssen für beide
Benutzertypen entsprechende Regeln erstellt werden. Dazu klickt man auf :fa:`plus` *Neue AD Synchronisationsregel*
und trägt folgende Informationen ein:

- Name: Der Anzeigename für die Regel.
- Beschreibung: Kurze Beschreibung (wird nur in der Verwaltung angezeigt).
- Active Directory Quelle: legt fest, welcher Wert für diese Regel verglichen werden soll.
    - Gruppe: Distinguished Name der Gruppe (bspw. ``CN=Administratoren,CN=Users,DC=ad,DC=meine-schule,DC=de``
    - Organisationseinheit: Distinguished Name der Organisationseinheit (bspw. ``OU=Students,DC=ad,DC=meine-schule,DC=de``)

  Das System überprüft dann, ob der Benutzer Mitglied der Gruppe ist oder Mitglied in der Organisationseinheit. Bei letzterem
  kann der Benutzer auch Mitglied einer Unter-Organisationseinheit sein. 
- Wert: Der zu vergleichende Wert (siehe Beispiele oben)
- Benutzertyp: Der zuzuweisende Benutzertyp.

UPN-Suffixe
###########

Damit der Identity Provioder weiß, welche Anmeldedaten zwecks Authentifizierung an den Authentifizierungsserver weitergeleitet
werden sollen, müssen Suffixe der Anmelde-E-Mail-Adressen definiert werden. Bei diesen Suffixen handelt es sich um die zugehörigen
E-Mail-Domains der Benutzer (d.h. der Teil hinter dem @-Zeichen).

Die Suffixe werden dabei explizit und ohne @-Zeichen angegeben. Wildcard-Suffixe werden nicht unterstützt. Subdomains müssen ebenfalls explizit angegeben werden.

.. warning:: Man muss die UPN-Suffixe unbedingt auch in den `Konfigurationsparameter REGISTRATION_DOMAIN_BLOCKLIST <../admin/configuration.html>`_ eintragen. Anderenfalls werden Authentifizierungsanfragen unnötig an den Authentifizierungsserver weitergeleitet.

Regeln für Benutzerrollen
#########################

Bei der Authentifizierung über das Active Directory können auch Benutzerrollen automatisch zugewiesen werden.
Dazu klickt man auf :fa:`user-tag` *Synchronisationsregeln für Benutzerrollen* und dort auf :fa:`plus` 
*Neue Synchronisationsregel* und trägt folgende Informationen ein:

- Name: Der Anzeigename für die Regel.
- Beschreibung: Kurze Beschreibung (wird nur in der Verwaltung angezeigt).
- Active Directory Quelle: legt fest, welcher Wert für diese Regel verglichen werden soll.
    - Gruppe: Distinguished Name der Gruppe (bspw. ``CN=Administratoren,CN=Users,DC=ad,DC=meine-schule,DC=de``
    - Organisationseinheit: Distinguished Name der Organisationseinheit (bspw. ``OU=Students,DC=ad,DC=meine-schule,DC=de``)

  Das System überprüft dann, ob der Benutzer Mitglied der Gruppe ist oder Mitglied in der Organisationseinheit. Bei letzterem
  kann der Benutzer auch Mitglied einer Unter-Organisationseinheit sein. 
- Wert: Der zu vergleichende Wert (siehe Beispiele oben)
- Benutzerrolle: Die zugewiesene Benutzerrolle.


.. warning:: Es werden nur Benutzerrollen hinzugefügt. Es werden grundsätzlich keine Benutzerrollen entfernt.

Regeln für Klassen
##################

Bei der Authentifizierung von Lernenden über das Active Directory können auch Klassen automatisch eingetragen werden.
Dazu klickt man auf :fa:`graduation-cap` *Synchronisationsregeln für Klassen* und dort auf :fa:`plus` 
*Neue Synchronisationsregel* und trägt folgende Informationen ein:

- Klasse: Die Klasse, die eingetragen werden soll.
- Active Directory Quelle: legt fest, welcher Wert für diese Regel verglichen werden soll.
    - Gruppe: Distinguished Name der Gruppe (bspw. ``CN=05A,CN=Users,DC=ad,DC=meine-schule,DC=de``
    - Organisationseinheit: Distinguished Name der Organisationseinheit (bspw. ``OU=05A,OU=Students,DC=ad,DC=meine-schule,DC=de``)

  Das System überprüft dann, ob der Benutzer Mitglied der Gruppe ist oder Mitglied in der Organisationseinheit. Bei letzterem
  kann der Benutzer auch Mitglied einer Unter-Organisationseinheit sein. 
- Wert: Der zu vergleichende Wert (siehe Beispiele oben).

