# Gestionale Aule Innovative (Carrelli)
## Introduzione al progetto
Il Gestionale Aule Innovative verte alla distribuzione di un servizio completo per la gestione e condivisione ordinata di carrelli provvisti di apparecchiature tecnologiche nel complesso scolastico IIS N.Copernico A.Carpeggiani.
L'applicazione **Apache MariaDB** mette a disposizione un pannello di controllo per i tecnici al fine di visualizzare le prenotazioni effettuate, inserire l'orario per le varie aule e modificare la conformazione dei carrelli *(nome, capienza massima di pc e le aule che questi gestiscono)*. È inoltre possibile per il tecnico prendere visione delle statistiche di ogni docente per quanto concerne le prenotazioni dei dispositivi. Per i professori viene messo a disposizione invece un pannello di interazione in modo da prenotare i dispositivi e controllare le proprie statistiche.
## Esecuzione
Per eseguire il programma, virtualizzato con docker, si consiglia la differenziazione dei due servizi presenti:
1. Set-up del **DBMS MariaDB** detached:
```bash
docker compose up -d database
```
2. Set-up della applicazione **Apache PHP** attached *(build required on change/first run)*:
```bash
docker compose up --build gestionale
```
## Crediti
- [x] Baldassari   Fabio
- [x] Debiagi Michele
- [x] Faggioli Alessio
- [x] Zampini Giacomo
