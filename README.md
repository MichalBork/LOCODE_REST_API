# LOCODE_REST_API
Celem zadania było pobranie LOCODow ze strony internetowej i zapisanie ich w bazie. Do tego dochodzi zmiana funkcji numerycznej opisującej typ portu.

Dodałem do tego nową tabele code_function która przechowuje te wartości z relacją 1 do 1 z tabelą locode która posiada resztę kolumn z dołączonego pdf-a 

Komenda jest dodawana do crona przez skrypt z dockera ale możemy wywołać ją ręcznie 
```
php bin/console app:update-data
```

### Takie najważniejsze zagadnienia które sprawiły mi problem i ich rozwiązania 

 - Sprawdzenie czy potrzebny jest update polega na porównaniu wartości na stronie informującej o ostatniej dacie aktualizacji z wartością którą zapisujemy przy pierwszym uruchomieniu skryptu, jeśli data się zmienia to sprawdzamy rekordy które mają podaną wartość w Change Indicator, inaczej się nie zmienią.

 - Odczytywanie wartości z pliku csv przebiega na podstawie buffora linji. Jeśli otworzymy cały plik na raz występują wycieki pamięci. Pojawił się też problem z kodowaniem znaków, użyłem czegoś takiego do naprawy 

```
 $encode = function ($data) {           return iconv('windows-1250', "UTF-8", $data);       };
```

Mimo ustawienia w bazie danych kodowania UTF8mb4 i tak pojawiał się problem 


### ENDPOINTY

Wystawione są dwa endpointy 
- /api/locode?code=locode GET - Informacje o terminalu na podstawie LOCODU
- /api/byName?name=name GET - Informacje o terminalu na podstawie NameWoDiacritics
