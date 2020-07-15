Externe ID
==========

Die externe ID ist eine ID, mit der man einen Benutzer über mehrere Systeme hinweg zuordnen kann. 
Beispiel:

Eine Lehrkraft existiert einerseits als Benutzer im Identity Provider und andererseits als Lehrkraft in
Schulverwaltungssystemen. Damit bspw. das ICC weiß, dass Benutzer m.mustermann@schule.de zur Lehrkraft
Max Mustermann (Kürzel: MU) gehört, wird die externe ID benötigt.

Die externe ID kann durch die Schule vorgegeben werden. Wichtig ist, dass sie in allen Systemen identisch ist.
Das ICC bspw. importiert Daten wie den Vertretungplan aus Drittsystemen, sodass auch hier eine Zuordnung stattfinden
muss.

Die externe ID kann entweder eine beliebige Zeichenkette sein. Sie sollte muss nur eindeutig sein, sprich MU darf nur zur
Lehrkraft Max Mustermann gehören. Da man Zahlen auch als Zeichenkette darstellen kann, können auch Zahlen (bspw. IDs aus 
anderen Systemen) verwendet werden.

**Hinweis:** Das System überprüft nicht, ob eine eindeutige ID mehrfach vergeben wird. Das System speichert die ID ohne weitere Überprüfung.

Lehrkräfte
##########

Es kann sinnvoll sein, als externe ID das Kürzel der Lehrkraft zu verwenden. Dieses wird bei vielen Verwaltungsprogrammen 
(Stunden- oder Vertretungplan) verwendet.

Schülerinnen und Schüler
########################

Hier kann man die ID des Schülers oder der Schülerin im Schulverwaltungsprogramm (bspw. SchILD NRW) nutzen.

Eltern
######

Um Eltern ihren Kindern zuzuordnen, speichert man mehrere externe IDs in der externen ID. Da die ID grundsätzlich nur
einen Wert zulässt, werden die IDs der Kinder mittels Komma abgetrennt. 

Normale Benutzer
################

Benutzer, die man nicht zu Datensätzen aus anderen Systemen zuordnen muss, benötigen keine externe ID.