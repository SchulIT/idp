Benutzer anlegen
================

Das Anlegen der Benutzer kann entweder händisch erfolgen oder automatisiert. Anleitungen zum
automatisierten Import sind unter `Datenimport <../import/users.html>`_ zu finden.

Lehrkräfte und Lernende
#######################

Da Lehrkräfte und Lernende in der Regel in einer Datenbank (entweder Schulverwaltungsprogramm oder in
einem Active Directory/LDAP-Verzeichnis) gespeichert sind, kann ein Datenimport aus einer dieser
Quellen erfolgen.

Der Import aus CSV-Dateien wird `hier <../import/users.html>`_ erläutert.

Der Einstieg zur Verwendung eines bereits vorhandenen Active Directories wird `hier <../admin/active-directory.html>`_ erläutert.

Eltern
######

Eltern erstellen ihren Benutzeraccount selbstständig mithilfe von `Registrierungscodes <../import/codes.html>`_. Elternaccounts
können mit beliebigen Schüleraccounts verknüpft werden (immer mit einem entsprechenden Registrierungscode), sodass
ein Account für mehrere Kinder genutzt werden kann.

Elternaccounts ohne verknüpftes Kind werden in regelmäßigen Abständen automatisch gelöscht (sofern Cronjobs funktionieren).
Verknüpfungen werden automatisch entfernt, wenn der Account des Kindes gelöscht wird.

Personal
########

Accounts für das Personal können händisch über die Weboberfläche angelegt werden.
