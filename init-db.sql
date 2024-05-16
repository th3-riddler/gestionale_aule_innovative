-- DROP DATABASE IF EXISTS gestionale_aule_innovative;
-- CREATE DATABASE IF NOT EXISTS gestionale_aule_innovative;

-- USE gestionale_aule_innovative;

CREATE TABLE IF NOT EXISTS teacher (
    email VARCHAR(255) NOT NULL PRIMARY KEY,
    name VARCHAR(30) NOT NULL,
    surname VARCHAR(30) NOT NULL,
    password VARCHAR(255) NOT NULL DEFAULT "3e64a091e9b1a6ec84714fc3d6e85ea042458d5c4f01034d2281d87d384b0ed1",
    profileImage BLOB DEFAULT NULL
);

CREATE TABLE IF NOT EXISTS subject(  
    subject_id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    subject_name VARCHAR(100) NOT NULL
);

CREATE TABLE IF NOT EXISTS teacher_subject(
    email VARCHAR(255) NOT NULL,
    subject_id INT NOT NULL,
    PRIMARY KEY (email, subject_id),
    FOREIGN KEY (email) REFERENCES teacher(email) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (subject_id) REFERENCES subject(subject_id) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE IF NOT EXISTS technician (
    email VARCHAR(255) NOT NULL PRIMARY KEY,
    name VARCHAR(30) NOT NULL,
    surname VARCHAR(30) NOT NULL,
    password VARCHAR(255) NOT NULL,
    profileImage BLOB DEFAULT NULL
);


-- Default Teacher's password: "docente"
INSERT IGNORE INTO teacher (email, name, surname, password) VALUES ("claudia.vallesi@iticopernico.it", "Claudia", "Vallesi", "3e64a091e9b1a6ec84714fc3d6e85ea042458d5c4f01034d2281d87d384b0ed1");
INSERT IGNORE INTO teacher (email, name, surname, password) VALUES ("giuseppe.mazziotti@iticopernico.it", "Giuseppe", "Mazziotti", "3e64a091e9b1a6ec84714fc3d6e85ea042458d5c4f01034d2281d87d384b0ed1");
INSERT IGNORE INTO teacher (email, name, surname, password) VALUES ("letizia.montanari@iticopernico.it", "Letizia", "Montanari", "3e64a091e9b1a6ec84714fc3d6e85ea042458d5c4f01034d2281d87d384b0ed1");
INSERT IGNORE INTO teacher (email, name, surname, password) VALUES ("cristina.frabetti@iticopernico.it", "Cristina", "Frabetti", "3e64a091e9b1a6ec84714fc3d6e85ea042458d5c4f01034d2281d87d384b0ed1");
INSERT IGNORE INTO teacher (email, name, surname, password) VALUES ("roberto.borghi@iticopernico.it", "Roberto", "Borghi", "3e64a091e9b1a6ec84714fc3d6e85ea042458d5c4f01034d2281d87d384b0ed1");
INSERT IGNORE INTO teacher (email, name, surname, password) VALUES ("alessandra.trambaiolli@iticopernico.it", "Alessandra", "Trambaiolli", "3e64a091e9b1a6ec84714fc3d6e85ea042458d5c4f01034d2281d87d384b0ed1");
INSERT IGNORE INTO teacher (email, name, surname, password) VALUES ("elia.melloni@iticopernico.it", "Elia", "Melloni", "3e64a091e9b1a6ec84714fc3d6e85ea042458d5c4f01034d2281d87d384b0ed1");
INSERT IGNORE INTO teacher (email, name, surname, password) VALUES ("andrea.bombonati@iticopernico.it", "Andrea", "Bombonati", "3e64a091e9b1a6ec84714fc3d6e85ea042458d5c4f01034d2281d87d384b0ed1");
INSERT IGNORE INTO teacher (email, name, surname, password) VALUES ("simone.ghetti@iticopernico.it", "Simone", "Ghetti", "3e64a091e9b1a6ec84714fc3d6e85ea042458d5c4f01034d2281d87d384b0ed1");
INSERT IGNORE INTO teacher (email, name, surname, password) VALUES ("lorenza.masini@iticopernico.it", "Lorenza", "Masini", "3e64a091e9b1a6ec84714fc3d6e85ea042458d5c4f01034d2281d87d384b0ed1");
INSERT IGNORE INTO teacher (email, name, surname, password) VALUES ("caterina.mestieri@iticopernico.it", "Caterina", "Mestieri", "3e64a091e9b1a6ec84714fc3d6e85ea042458d5c4f01034d2281d87d384b0ed1");
INSERT IGNORE INTO teacher (email, name, surname, password) VALUES ("matteo.lunati@iticopernico.it", "Matteo", "Lunati", "3e64a091e9b1a6ec84714fc3d6e85ea042458d5c4f01034d2281d87d384b0ed1");
INSERT IGNORE INTO teacher (email, name, surname, password) VALUES ("anna.morelli@iticopernico.it", "Anna", "Morelli", "3e64a091e9b1a6ec84714fc3d6e85ea042458d5c4f01034d2281d87d384b0ed1");
INSERT IGNORE INTO teacher (email, name, surname, password) VALUES ("francesca.grazzi@iticopernico.it", "Francesca", "Grazzi", "3e64a091e9b1a6ec84714fc3d6e85ea042458d5c4f01034d2281d87d384b0ed1");

-- Default Technician's password: "tecnico"
INSERT IGNORE INTO technician (email, name, surname, password) VALUES ("luca.bianconi@iticopernico.it", "Luca", "Bianconi", "0188ff70cece0fe771e79f3bed925ae70dc23e3295cd577bcf35f95b66f32934");


INSERT IGNORE INTO subject VALUES (1, "Letteratura"),
                                  (2, "Storia"), 
                                  (3, "Matematica"), 
                                  (4, "Informatica"),
                                  (5, "Fisica"), 
                                  (6, "Chimica"), 
                                  (7, "Sistemi e Reti"), 
                                  (8, "Tecnologie per la Progettazione di Sistemi Informatici e Telecomunicazioni"), 
                                  (9, "Inglese"), 
                                  (10, "Religione"), 
                                  (11, "Gestione Progetto ed Organizzazione d'Impresa"), 
                                  (12, "Tedesco"), 
                                  (13, "Big Data"), 
                                  (14, "Scienze Motorie e Sportive"), 
                                  (15, "Telecomunicazioni"), 
                                  (16, "Geografia"), 
                                  (17, "Diritto ed Economia"), 
                                  (19, "Disegno Tecnico"), 
                                  (20, "Biologia");

INSERT IGNORE INTO teacher_subject VALUES ("claudia.vallesi@iticopernico.it", 4), 
                                          ("claudia.vallesi@iticopernico.it", 13),
                                          ("giuseppe.mazziotti@iticopernico.it", 4), 
                                          ("giuseppe.mazziotti@iticopernico.it", 8), 
                                          ("letizia.montanari@iticopernico.it", 3), 
                                          ("cristina.frabetti@iticopernico.it", 9),
                                          ("roberto.borghi@iticopernico.it", 8),
                                          ("alessandra.trambaiolli@iticopernico.it", 7),
                                          ("elia.melloni@iticopernico.it", 7), 
                                          ("andrea.bombonati@iticopernico.it", 1),
                                          ("andrea.bombonati@iticopernico.it", 2),
                                          ("simone.ghetti@iticopernico.it", 11),
                                          ("lorenza.masini@iticopernico.it", 10),
                                          ("caterina.mestieri@iticopernico.it", 12),
                                          ("matteo.lunati@iticopernico.it", 14),
                                          ("anna.morelli@iticopernico.it", 6),
                                          ("francesca.grazzi@iticopernico.it", 15);





CREATE TABLE IF NOT EXISTS class (
    year INT NOT NULL,
    section VARCHAR(10) NOT NULL,
    PRIMARY KEY (year, section)
);

INSERT IGNORE INTO class (year, section)
VALUES (1, "A"), (1, "B"), (1, "C"), (1, "D"), (1, "E"), (1, "F"), (1, "G"), (1, "H"), (1, "I"), (1, "L"), (1, "M"), (1, "N"), (1, "O"), (1, "P"), (1, "Q"), (1, "R"), (1, "S"), (1, "T"), (1, "U"), (1, "V"), (1, "Z"), (2, "A"), (2, "B"), (2, "C"), (2, "D"), (2, "E"), (2, "F"), (2, "G"), (2, "H"), (2, "I"), (2, "L"), (2, "M"), (2, "N"), (2, "O"), (2, "P"), (2, "Q"), (2, "R"), (2, "S"), (2, "T"), (2, "U"), (2, "V"), (2, "Z"), (3, "A"), (3, "B"), (3, "C"), (3, "D"), (3, "E"), (3, "F"), (3, "G"), (3, "H"), (3, "I"), (3, "L"), (3, "M"), (3, "N"), (3, "O"), (3, "P"), (3, "Q"), (3, "R"), (3, "S"), (3, "T"), (3, "U"), (3, "V"), (3, "Z"), (4, "A"), (4, "B"), (4, "C"), (4, "D"), (4, "E"), (4, "F"), (4, "G"), (4, "H"), (4, "I"), (4, "L"), (4, "M"), (4, "N"), (4, "O"), (4, "P"), (4, "Q"), (4, "R"), (4, "S"), (4, "T"), (4, "U"), (4, "V"), (4, "Z"), (5, "A"), (5, "B"), (5, "C"), (5, "D"), (5, "E"), (5, "F"), (5, "G"), (5, "H"), (5, "I"), (5, "L"), (5, "M"), (5, "N"), (5, "O"), (5, "P"), (5, "Q"), (5, "R"), (5, "S"), (5, "T"), (5, "U"), (5, "V"), (5, "Z");

CREATE TABLE IF NOT EXISTS cart (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    cart_name VARCHAR(255) NOT NULL,
    pc_max INT,
    Room1 VARCHAR(10),
    Room2 VARCHAR(10),
    Room3 VARCHAR(10),
    Room4 VARCHAR(10),
    Room5 VARCHAR(10)
);

-- INSERT IGNORE INTO cart (id, cart_name) VALUES (1, "cart 1"), (2, "cart 2"), (3, "cart 3"), (4, "cart 4"), (5, "cart 5"), (6, "cart 6"), (7, "cart 7"), (8, "cart 8");
INSERT IGNORE INTO cart VALUES (1, "cart 1", 30, "A1", "A2", "A3", "A4", "A5"), (2, "cart 2", 30, "A6", "A7", "A8", "A9", "A10"), (3, "cart 3", 30, "A11", "A12", "A13", "A14", "A15"), (4, "cart 4", 30, "A16", "A17", "A18", "A19", "A20"), (5, "cart 5", 30, "A21", "A22", "A23", "A24", "A25"), (6, "cart 6", 30, "A26", "A27", "A28", "A29", "A30"), (7, "cart 7", 30, "A31", "A32", "A33", "A34", "A35"), (8, "cart 8", 30, "A36", "A37", "A38", "A39", "A40");


CREATE TABLE IF NOT EXISTS room_schedule (
    room VARCHAR(10) NOT NULL,
    weekday VARCHAR(30) NOT NULL,
    hour INT NOT NULL,
    teacher_email VARCHAR(255),
    class_year INT NOT NULL,
    class_section VARCHAR(10),
    PRIMARY KEY (room, weekday, hour),
    FOREIGN KEY (teacher_email) REFERENCES teacher(email) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (class_year, class_section) REFERENCES class(year, section)
);

INSERT IGNORE INTO room_schedule
VALUES ("A1", "Lunedì", 1, "letizia.montanari@iticopernico.it", 4, "P"), ("A1", "Lunedì", 2, "claudia.vallesi@iticopernico.it", 4, "P"), ("A1", "Lunedì", 3, "claudia.vallesi@iticopernico.it", 4, "P"), ("A2", "Lunedì", 4, "giuseppe.mazziotti@iticopernico.it", 4, "P"), ("A2", "Lunedì", 5, "giuseppe.mazziotti@iticopernico.it", 4, "P"), ("A1", "Lunedì", 6, "cristina.frabetti@iticopernico.it", 4, "P");

CREATE TABLE IF NOT EXISTS reservation (
    date DATE NOT NULL,
    pc_qt INT NOT NULL,
    teacher_note TEXT,
    technician_note TEXT,
    room VARCHAR(10) NOT NULL,
    weekday VARCHAR(30) NOT NULL,
    hour INT NOT NULL,
    cart_id INT NOT NULL,
    teacher_email VARCHAR(255) NOT NULL,
    FOREIGN KEY (room, weekday, hour) REFERENCES room_schedule(room, weekday, hour),
    FOREIGN KEY (cart_id) REFERENCES cart(id),
    FOREIGN KEY (teacher_email) REFERENCES teacher(email) ON DELETE CASCADE ON UPDATE CASCADE,
    PRIMARY KEY (date, room, weekday, hour)
);

CREATE TABLE IF NOT EXISTS api_teachertoken (
    token VARCHAR(64) NOT NULL PRIMARY KEY,
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    email VARCHAR(255) NOT NULL,
    FOREIGN KEY (email) REFERENCES teacher(email) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE IF NOT EXISTS api_techniciantoken (
    token VARCHAR(64) NOT NULL PRIMARY KEY,
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    email VARCHAR(255) NOT NULL,
    FOREIGN KEY (email) REFERENCES technician(email) ON DELETE CASCADE ON UPDATE CASCADE
);