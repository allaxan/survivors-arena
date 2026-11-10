-- THE LAST OF US - Table personnages
CREATE DATABASE IF NOT EXISTS poo_sgbd CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE poo_sgbd;

DROP TABLE IF EXISTS personnages;
CREATE TABLE personnages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(50) NOT NULL,
    type VARCHAR(20) NOT NULL,
    atk INT NOT NULL,
    defense INT DEFAULT 0,
    pv INT NOT NULL,
    energie INT DEFAULT 0,
    rarete VARCHAR(20) DEFAULT 'commun',
    faction VARCHAR(30) DEFAULT 'indépendant',
    description TEXT,
    image VARCHAR(255)
);

-- Exemples de personnages TLOU
INSERT INTO personnages (nom, type, atk, defense, pv, energie, rarete, faction, description, image) VALUES
('Joel', 'survivant', 30, 10, 120, 50, 'rare', 'Fireflies', 'Survivant endurci, expert en armes et en ruse.', 'joel.png'),
('Ellie', 'survivant', 25, 15, 100, 70, 'légendaire', 'Fireflies', 'Immunisée au cordyceps, agile et inventive.', 'ellie.png'),
('Clicker', 'infecté', 35, 5, 80, 0, 'commun', 'infectés', 'Aveugle mais très dangereux au corps à corps.', 'clicker.png'),
('Bloater', 'boss', 50, 20, 200, 0, 'épique', 'infectés', 'Infecté massif, lance des spores toxiques.', 'bloater.png');
    