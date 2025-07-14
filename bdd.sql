CREATE TABLE IF NOT EXISTS membre (
    id_membre INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(50) NOT NULL,
    date_naissance DATE NOT NULL,
    genre VARCHAR(10),
    email VARCHAR(100) NOT NULL UNIQUE,
    ville VARCHAR(50) NOT NULL,
    mdp VARCHAR(255) NOT NULL,
    image_profil VARCHAR(255) DEFAULT 'default.jpg'
);
CREATE TABLE IF NOT EXISTS categorie_objet (
    id_categorie INT AUTO_INCREMENT PRIMARY KEY,
    nom_categorie VARCHAR(50) NOT NULL
);
CREATE TABLE IF NOT EXISTS objet (
    id_objet INT AUTO_INCREMENT PRIMARY KEY,
    nom_objet VARCHAR(100) NOT NULL,
    id_categorie INT,
    id_membre INT,
    FOREIGN KEY (id_categorie) REFERENCES categorie_objet(id_categorie),
    FOREIGN KEY (id_membre) REFERENCES membre(id_membre)
);
CREATE TABLE IF NOT EXISTS image_objet (
    id_image INT AUTO_INCREMENT PRIMARY KEY,
    id_objet INT NOT NULL,
    nom_image VARCHAR(255) NOT NULL DEFAULT 'PARDEFAUT.jpg',
    FOREIGN KEY (id_objet) REFERENCES objet(id_objet)
);
CREATE TABLE IF NOT EXISTS emprunt (
    id_emprunt INT AUTO_INCREMENT PRIMARY KEY,
    id_objet INT NOT NULL,
    id_membre INT NOT NULL,
    date_emprunt DATE NOT NULL,
    date_retour DATE,
    FOREIGN KEY (id_objet) REFERENCES objet(id_objet),
    FOREIGN KEY (id_membre) REFERENCES membre(id_membre)
);

INSERT INTO membre (nom, date_naissance, genre, email, ville, mdp, image_profil) VALUES
    ('Alice Martin', '1990-05-15', 'Femme', 'alice.martin@email.com', 'Paris', '1', 'alice.jpg'),
    ('Bob Dupont', '1985-08-22', 'Homme', 'bob.dupont@email.com', 'Lyon', '1', 'bob.jpg'),
    ('Claire Dubois', '1992-12-03', 'Femme', 'claire.dubois@email.com', 'Marseille', '1', 'claire.jpg');

    INSERT INTO categorie_objet (nom_categorie) VALUES
                                                    ('esthétique'),
                                                    ('bricolage'),
                                                    ('mécanique'),
                                                    ('cuisine');


    INSERT INTO objet (nom_objet, id_categorie, id_membre) VALUES
                                                               ('Fer à lisser professionnel', 1, 1),
                                                               ('Sèche-cheveux ionique', 1, 1),
                                                               ('Kit manucure complet', 1, 1),
                                                               ('Perceuse visseuse', 2, 1),
                                                               ('Niveau à bulle 80cm', 2, 1),
                                                               ('Scie sauteuse', 2, 1),
                                                               ('Clés à molette jeu de 3', 3, 1),
                                                               ('Cric hydraulique 2T', 3, 1),
                                                               ('Robot pâtissier', 4, 1),
                                                               ('Autocuiseur 6L', 4, 1);

    INSERT INTO objet (nom_objet, id_categorie, id_membre) VALUES
                                                               ('Tondeuse à barbe', 1, 2),
                                                               ('Rasoir électrique', 1, 2),
                                                               ('Marteau de charpentier', 2, 2),
                                                               ('Tournevis électrique', 2, 2),
                                                               ('Ponceuse orbitale', 2, 2),
                                                               ('Mètre laser', 2, 2),
                                                               ('Compresseur portable', 3, 2),
                                                               ('Clés plates jeu complet', 3, 2),
                                                               ('Barbecue électrique', 4, 2),
                                                               ('Plancha gaz', 4, 2);

    INSERT INTO objet (nom_objet, id_categorie, id_membre) VALUES
                                                               ('Epilateur électrique', 1, 3),
                                                               ('Brosse soufflante', 1, 3),
                                                               ('Miroir grossissant LED', 1, 3),
                                                               ('Défonceuse', 2, 3),
                                                               ('Etabli pliant', 2, 3),
                                                               ('Chandelles de sécurité', 3, 3),
                                                               ('Manomètre pneus', 3, 3),
                                                               ('Machine à pain', 4, 3),
                                                               ('Centrifugeuse', 4, 3),
                                                               ('Four à pizza portable', 4, 3);

    INSERT INTO emprunt (id_objet, id_membre, date_emprunt, date_retour) VALUES
                                                                             (1, 2, '2025-07-10', NULL),
                                                                             (11, 1, '2025-07-12', NULL),
                                                                             (21, 2, '2025-07-13', NULL),
                                                                             (5, 3, '2025-07-14', NULL),
                                                                             (15, 3, '2025-07-05', '2025-07-08'),
                                                                             (3, 2, '2025-07-01', '2025-07-03'),
                                                                             (27, 1, '2025-06-28', '2025-07-02'),
                                                                             (18, 3, '2025-06-25', '2025-06-30'),
                                                                             (9, 2, '2025-06-20', '2025-06-25'),
                                                                             (25, 1, '2025-06-15', '2025-06-22');

CREATE OR REPLACE VIEW vue_objets_emprunts AS
SELECT 
    o.id_objet,
    o.nom_objet,
    o.id_categorie,
    c.nom_categorie,
    o.id_membre as proprietaire_id,
    proprietaire.nom as proprietaire_nom,
    e.id_emprunt,
    e.id_membre as emprunteur_id,
    emprunteur.nom as emprunteur_nom,
    e.date_emprunt,
    e.date_retour,
    CASE 
        WHEN e.date_retour IS NULL AND e.id_emprunt IS NOT NULL THEN 'Emprunte'
        ELSE 'Disponible'
    END as statut_emprunt,
    CASE 
        WHEN e.date_retour IS NULL AND e.id_emprunt IS NOT NULL 
        THEN DATE_ADD(e.date_emprunt, INTERVAL 7 DAY)
        ELSE NULL
    END as date_retour_prevue
FROM objet o
LEFT JOIN categorie_objet c ON o.id_categorie = c.id_categorie
LEFT JOIN membre proprietaire ON o.id_membre = proprietaire.id_membre
LEFT JOIN emprunt e ON o.id_objet = e.id_objet AND e.date_retour IS NULL
LEFT JOIN membre emprunteur ON e.id_membre = emprunteur.id_membre;