# LESS-COMPILER

Schreibe less-CSS mit diesem phpwcms-Modul.
Einfach herunterladen und in 'include/inc_module/' ablegen. Eine weitere Tabelle in der Datenbank ist nicht erforderlich.

## Features:
-	less-Compiler (Thx http://leafo.net/lessphp/)
-	Compilen von freiwählbaren Gruppen von less-files
-	compiled (auf Wunsch auch automatisch oder bei Änderung der Datei) gespeicherte *.less Dateien aus dem 'inc_css'/'inc_less' Ordnern und speichert das Ergebnis in eine *.css Datei.
- 	~~Automatische Backups über Revisionen.~~ later update
-	~~Revisionen können wiedereingespielt werden~~ later update
-	Minify-Option

## Anwendung:
Erstelle in 'template/inc_css/' beliebig viele *.less Dateien.
Gehe in das Zentrale Modulbackend von less und wähle aus, welche dieser less Dateien compiled werden. Unter Optionen kannst du die Ausgabe noch ein wenig verfeinern und andere Optionen treffen, die dir das arbeiten mit less noch mehr erleichtern.


Entwickelt für phpwcms v.1.8.0.
Wird vermutlich aber auch mit älteren / neuen Versionen funktionieren.


----
At this moment english language is only partially supported by the less-compiler.
