# mynote
Prosta aplikacja do zarządzania notatkami.

##Cechy: 
- Aplikacja została napisana przy pomocy Zend Framework 1.12
- Pełna responsywność (Bootstrap 3.3.5)

##Instalacja:

1. Utwórz bazę danych "mynote" (lub z dowolną inną nazwą) z kodowaniem UTF-8<br />
2. Zaimportuj plik mynote.sql z głównego katalogu projektu<br />
3. Skopiuj plik application/configs/application.ini.dist, usuń rozszerzenie ".dist" i zmień w nim następujące parametry:<br/>
  - resources.db.params.dbname (dostosuj do nazwy bazy)
  - resources.db.params.host
  - resources.db.params.username
  - resources.db.params.password
  - my.domain (np. domena.com.pl)
  - my.absolute_url (http://domena.com.pl)<br />

<strong>UWAGA:</strong>Stara wersja frameworka. Ze względów bezpieczeństwa nie powinno się używać aplikacji w środowisku produkcyjnym.
