Dokumentation
=============

Diese Dokumentation dient einerseits einem Administrator zur Einrichtung
und Verwaltung der Software und andererseits den Anwendern als Hilfe
zu Workflows.

Zweck des Identity Providers
############################

Der Identity Provider stellt eine zentrale Datenbank für alle Benutzer bereit. Diese
wird von allen Diensten der SchulIT Software Suite genutzt, um Benutzer zu authenfizieren
und authorisieren.

Es handelt sich dabei um die zentrale Instanz für das Single-Sign-On. Dank Single-Sign-On
müssen Benutzer nur in einem System eingerichtet und verwaltet werden. Für Benutzer ergibt
sich der Vorteil, dass sie sich mit ihren Benutzerdaten an mehreren Diensten anmelden können.

.. toctree::
   :caption: Allgemeines
   :maxdepth: 2

   general/index
   general/external_id
   general/terminology

.. toctree::
   :caption: Installation und Wartung
   :maxdepth: 2

   admin/requirements
   admin/install
   admin/configuration
   admin/update
   admin/roles
   admin/cronjobs
   admin/active_directory

.. toctree::
   :caption: Einrichtung
   :maxdepth: 2

   configure/index
   configure/user_types
   configure/user_roles
   configure/users
   configure/sync_rules
   configure/services
   configure/attributes

.. toctree::
   :caption: API-Schnittstelle
   
   api/index.rst

.. toctree::
   :caption: Datenimport
   :maxdepth: 2

   import/index
   import/users
   import/codes

