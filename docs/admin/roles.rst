Benutzerrollen
==============

ROLE_USER
#########

Diese Rolle muss jeder Benutzer haben und besitzt keine besonderen Zugriffsrechte.

ROLE_ADMIN
####################

Nutzer mit dieser Rolle dürfen Benutzer anlegen, bearbeiten, löschen und importieren. Selbiges gilt für
Registrierungscodes.

ROLE_SUPER_ADMIN
################

Diese Rolle beinhaltet die Rolle ``ROLE_ADMIN`` und erlaubt darüber hinaus, alle weiteren administrativen Aufgaben
zu erledigen. Dazu zählt die Verwaltung von Benutzertypen, -gruppen, Diensten, Attributen sowie die Anzeige
des Logs.

ROLE_CRON
#########

Diese Rolle darf nicht vergeben werden. Sie wird dem einzigen Cronjob-Benutzer automatisch zugewiesen.