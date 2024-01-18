CREATE DATABASE IF NOT EXISTS gestionale_aule_innovative;

USE gestionale_aule_innovative;

CREATE TABLE IF NOT EXISTS docente (
    email VARCHAR(255) NOT NULL PRIMARY KEY,
    nome VARCHAR(30) NOT NULL,
    cognome VARCHAR(30) NOT NULL,
    password VARCHAR(255) NOT NULL
);

CREATE TABLE IF NOT EXISTS tecnico (
    email VARCHAR(255) NOT NULL PRIMARY KEY,
    nome VARCHAR(30) NOT NULL,
    cognome VARCHAR(30) NOT NULL,
    password VARCHAR(255) NOT NULL
);


/* USER INSERTION */

INSERT IGNORE INTO docente VALUES ("claudia.vallesi@iticopernico.it", "Claudia", "Vallesi", "a53dfa154cd75757b02f8d3a56959a5c85fc276d670758ea7c550f85f95159e5");
INSERT IGNORE INTO docente VALUES ("giuseppe.mazziotti@iticopernico.it", "Giuseppe", "Mazziotti", "a53dfa154cd75757b02f8d3a56959a5c85fc276d670758ea7c550f85f95159e5");
INSERT IGNORE INTO docente VALUES ("letizia.montanari@iticopernico.it", "Letizia", "Montanari", "a53dfa154cd75757b02f8d3a56959a5c85fc276d670758ea7c550f85f95159e5");
INSERT IGNORE INTO docente VALUES ("cristina.frabetti@iticopernico.it", "Cristina", "Frabetti", "a53dfa154cd75757b02f8d3a56959a5c85fc276d670758ea7c550f85f95159e5");
INSERT IGNORE INTO docente VALUES ("roberto.borghi@iticopernico.it", "Roberto", "Borghi", "a53dfa154cd75757b02f8d3a56959a5c85fc276d670758ea7c550f85f95159e5");

INSERT IGNORE INTO tecnico VALUES ("luca.bianconi@iticopernico.it", "Luca", "Bianconi", "0e949b5f9de9cfa8437d0beeca8d8c96a6b578caa889fd6659e85b7dff22e59b");


CREATE TABLE IF NOT EXISTS classe (
    numero INT NOT NULL,
    sezione VARCHAR(10) NOT NULL,
    PRIMARY KEY (numero, sezione)
);

INSERT IGNORE INTO classe (numero, sezione)
VALUES (1, "A"), (1, "B"), (1, "C"), (1, "D"), (1, "E"), (1, "F"), (1, "G"), (1, "H"), (1, "I"), (1, "L"), (1, "M"), (1, "N"), (1, "O"), (1, "P"), (1, "Q"), (1, "R"), (1, "S"), (1, "T"), (1, "U"), (1, "V"), (1, "Z"), (2, "A"), (2, "B"), (2, "C"), (2, "D"), (2, "E"), (2, "F"), (2, "G"), (2, "H"), (2, "I"), (2, "L"), (2, "M"), (2, "N"), (2, "O"), (2, "P"), (2, "Q"), (2, "R"), (2, "S"), (2, "T"), (2, "U"), (2, "V"), (2, "Z"), (3, "A"), (3, "B"), (3, "C"), (3, "D"), (3, "E"), (3, "F"), (3, "G"), (3, "H"), (3, "I"), (3, "L"), (3, "M"), (3, "N"), (3, "O"), (3, "P"), (3, "Q"), (3, "R"), (3, "S"), (3, "T"), (3, "U"), (3, "V"), (3, "Z"), (4, "A"), (4, "B"), (4, "C"), (4, "D"), (4, "E"), (4, "F"), (4, "G"), (4, "H"), (4, "I"), (4, "L"), (4, "M"), (4, "N"), (4, "O"), (4, "P"), (4, "Q"), (4, "R"), (4, "S"), (4, "T"), (4, "U"), (4, "V"), (4, "Z"), (5, "A"), (5, "B"), (5, "C"), (5, "D"), (5, "E"), (5, "F"), (5, "G"), (5, "H"), (5, "I"), (5, "L"), (5, "M"), (5, "N"), (5, "O"), (5, "P"), (5, "Q"), (5, "R"), (5, "S"), (5, "T"), (5, "U"), (5, "V"), (5, "Z");

CREATE TABLE IF NOT EXISTS carrello (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    nome_carrello VARCHAR(255) NOT NULL,
    pc_max INT NOT NULL,
    pc_disp INT NOT NULL,
    Aula1 VARCHAR(10) NOT NULL,
    Aula2 VARCHAR(10) NOT NULL,
    Aula3 VARCHAR(10) NOT NULL,
    Aula4 VARCHAR(10) NOT NULL,
    Aula5 VARCHAR(10) NOT NULL
);

INSERT IGNORE INTO carrello
VALUES (1, "Carrello 1", 30, 30, "A1", "A2", "A3", "A4", "A5"), (2, "Carrello 2", 30, 30, "A6", "A7", "A8", "A9", "A10"), (3, "Carrello 3", 30, 30, "A11", "A12", "A13", "A14", "A15"), (4, "Carrello 4", 30, 30, "A16", "A17", "A18", "A19", "A20"), (5, "Carrello 5", 30, 30, "A21", "A22", "A23", "A24", "A25"), (6, "Carrello 6", 30, 30, "A26", "A27", "A28", "A29", "A30"), (7, "Carrello 7", 30, 30, "A31", "A32", "A33", "A34", "A35"), (8, "Carrello 8", 30, 30, "A36", "A37", "A38", "A39", "A40");

CREATE TABLE IF NOT EXISTS orario_aula (
    aula VARCHAR(10) NOT NULL,
    giorno VARCHAR(30) NOT NULL,
    ora INT NOT NULL,
    email_docente VARCHAR(255),
    numero_classe INT NOT NULL,
    sezione VARCHAR(10),
    PRIMARY KEY (aula, giorno, ora),
    FOREIGN KEY (email_docente) REFERENCES docente(email),
    FOREIGN KEY (numero_classe, sezione) REFERENCES classe(numero, sezione)
);

INSERT IGNORE INTO orario_aula
VALUES ("A1", "Lunedì", 1, "letizia.montanari@iticopernico.it", 4, "P"), ("A1", "Lunedì", 2, "claudia.vallesi@iticopernico.it", 4, "P"), ("A1", "Lunedì", 3, "claudia.vallesi@iticopernico.it", 4, "P"), ("A2", "Lunedì", 4, "giuseppe.mazziotti@iticopernico.it", 4, "P"), ("A2", "Lunedì", 5, "giuseppe.mazziotti@iticopernico.it", 4, "P"), ("A1", "Lunedì", 6, "cristina.frabetti@iticopernico.it", 4, "P");

CREATE TABLE IF NOT EXISTS prenotazione (
    data DATE NOT NULL,
    numero_computer INT NOT NULL,
    nota_docente TEXT,
    nota_tecnico TEXT,
    aula VARCHAR(10) NOT NULL,
    giorno VARCHAR(30) NOT NULL,
    ora INT NOT NULL,
    id_carrello INT NOT NULL,
    FOREIGN KEY (aula, giorno, ora) REFERENCES orario_aula(aula, giorno, ora),
    FOREIGN KEY (id_carrello) REFERENCES carrello(id),
    PRIMARY KEY (data, aula, giorno, ora)
);