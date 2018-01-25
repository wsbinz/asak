
## Uwagi co dodać sebastian

![shu_nydmja](https://user-images.githubusercontent.com/12541118/31649628-1420571e-b314-11e7-8695-299e398872f3.png)
W powyższym zdjęciu dodał bym właśnie okiengo do zarządzania kontem uzytkownika.

##Uwagi od Adi
to_do list:
    - ekran pierwszy
    - ektran przed referencje z szukajka
    - raportowka
    - dopisac do bazy pole indeks - dostawca
    
    
    
##Jak dodawać logi do serwisu
    -Aby dodać log nalezy umieścić w danym miejscu funkcję fileLog();
    - Jako parametry przyjmuje $wiadomość oraz $status ( np. 'Error','Success','Info' )
    We wiadomości wpiszcie tylko potrzebne informację np o utworzeniu WZ o numerze $numer_wz
    Lub 'Utworzono regał o $numer_regału. Reszta czyli informację o uzytkowniku i dacie są robione automatycznie.
    Pozdro
    Przykład mojego użycia w produktach:
     fileLog("Pomyślnie dodano produkt o numerze:".$nr_mat." Nazwa produktu to: ".strtolower($this->input->post('name_short', true),'Success'));