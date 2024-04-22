# Gestionale Aule Innovative (Carrelli)
## Introduzione al progetto
Il Gestionale Aule Innovative verte alla distribuzione di un servizio completo per la gestione e condivisione ordinata di carrelli provvisti di apparecchiature tecnologiche nel complesso scolastico IIS N.Copernico A.Carpeggiani.
L'applicazione **Apache MariaDB** mette a disposizione un pannello di controllo per i tecnici, nonché l'inserimento dell'orario ed un pannello di interazione per i docenti in modo da prenotare i dispositivi.
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
[x] Fabio   Baldassari
[x] Michele Debiagi
[x] Alessio Faggioli
[x] Giacomo Zampini
