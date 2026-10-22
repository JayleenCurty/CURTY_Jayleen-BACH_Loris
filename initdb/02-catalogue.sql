-- =====================================================================
-- GroupTrip - Jeu de données du catalogue
-- Destinations, vols (aller-retour depuis Genève) et hôtels
-- Données fictives destinées aux tests (prix, horaires et hôtels inventés)
-- Prérequis : 01-schema.sql (avec la colonne destination.image)
-- =====================================================================

-- ---------------------------------------------------------------------
-- Destinations
-- Les id sont fixés pour rendre les clés étrangères lisibles
-- ---------------------------------------------------------------------
INSERT INTO destination (id, pays, ville, code_aeroport, description, image, type_destination) VALUES
(1,  'Espagne',     'Barcelone',  'BCN', 'Ville balnéaire et culturelle : Sagrada Família, quartier gothique, plages urbaines et vie nocturne animée.', 'barcelone.jpeg', 'ville'),
(2,  'Portugal',    'Lisbonne',   'LIS', 'Capitale aux sept collines, tramways jaunes, miradouros et cuisine portugaise.', 'lisbonne.jpeg', 'ville'),
(3,  'Royaume-Uni', 'Londres',    'LGW', 'Musées gratuits, quartiers variés, pubs et grands parcs : idéal pour un séjour de groupe.', 'londres.jpeg', 'ville'),
(4,  'Pays-Bas',    'Amsterdam',  'AMS', 'Canaux, vélos, musées et ambiance décontractée.', 'amsterdam.jpeg', 'ville'),
(5,  'Grèce',       'Héraklion',  'HER', 'Porte d''entrée de la Crète : plages, sites antiques et villages de montagne.', 'heraklion.jpeg', 'plage'),
(6,  'Espagne',     'Palma',      'PMI', 'Majorque : criques turquoise, vieille ville et nombreuses activités nautiques.', 'palma.jpeg', 'plage'),
(7,  'Malte',       'La Valette', 'MLA', 'Petit archipel ensoleillé, eaux claires, plongée et patrimoine historique.', 'la-valette.jpeg', 'plage'),
(8,  'Islande',     'Reykjavik',  'KEF', 'Geysers, cascades, glaciers et aurores boréales : une destination nature hors du commun.', 'reykjavik.jpeg', 'nature'),
(9,  'Autriche',    'Innsbruck',  'INN', 'Ville alpine entourée de sommets : randonnée en été, ski en hiver.', 'innsbruck.jpeg', 'montagne'),
(10, 'Norvège',     'Bergen',     'BGO', 'Porte des fjords, maisons colorées et randonnées spectaculaires.', 'bergen.jpeg', 'nature');

-- ---------------------------------------------------------------------
-- Vols aller-retour au départ de Genève (GVA)
-- duree_vol : durée d'un trajet en minutes
-- prix_personne : prix de l'aller-retour par personne en CHF
-- ---------------------------------------------------------------------
INSERT INTO vol (compagnie, date_aller, date_retour, duree_vol, prix_personne, places_disponibles, id_destination) VALUES
-- Barcelone
('easyJet', '2027-05-14 07:10:00', '2027-05-17 20:30:00',  95, 189.00,  40, 1),
('Swiss',   '2027-05-14 12:05:00', '2027-05-17 18:45:00',  95, 320.00,  25, 1),
('Vueling', '2027-06-11 09:40:00', '2027-06-14 21:10:00',  95, 145.00,  60, 1),
('easyJet', '2027-09-03 06:55:00', '2027-09-10 19:20:00',  95, 230.00,  35, 1),

-- Lisbonne
('easyJet', '2027-05-14 06:45:00', '2027-05-17 21:00:00', 150, 210.00,  30, 2),
('TAP',     '2027-06-04 10:15:00', '2027-06-07 17:40:00', 150, 295.00,  45, 2),
('easyJet', '2027-10-08 07:30:00', '2027-10-11 20:10:00', 150, 175.00,  50, 2),

-- Londres
('easyJet', '2027-05-14 08:20:00', '2027-05-17 19:10:00', 100, 160.00,  55, 3),
('British Airways', '2027-05-14 13:30:00', '2027-05-17 17:15:00', 100, 285.00, 20, 3),
('easyJet', '2027-11-12 07:00:00', '2027-11-14 20:45:00', 100, 120.00,  70, 3),

-- Amsterdam
('KLM',     '2027-06-11 07:05:00', '2027-06-14 18:30:00', 100, 240.00,  35, 4),
('easyJet', '2027-09-17 09:50:00', '2027-09-20 21:25:00', 100, 165.00,  45, 4),

-- Héraklion
('easyJet', '2027-06-26 06:30:00', '2027-07-03 20:00:00', 175, 340.00,  40, 5),
('Edelweiss', '2027-07-10 08:15:00', '2027-07-17 16:50:00', 175, 410.00, 30, 5),
('easyJet', '2027-09-04 07:00:00', '2027-09-11 21:30:00', 175, 260.00,  50, 5),

-- Palma
('easyJet', '2027-06-04 07:20:00', '2027-06-07 21:45:00',  95, 195.00,  45, 6),
('Swiss',   '2027-07-10 11:40:00', '2027-07-17 19:05:00',  95, 365.00,  25, 6),
('easyJet', '2027-09-03 06:40:00', '2027-09-06 20:15:00',  95, 155.00,  60, 6),

-- La Valette
('Air Malta', '2027-05-28 09:30:00', '2027-06-01 18:20:00', 130, 280.00,  30, 7),
('easyJet',   '2027-09-17 06:50:00', '2027-09-24 20:40:00', 130, 230.00,  40, 7),

-- Reykjavik
('Icelandair', '2027-03-05 13:00:00', '2027-03-09 07:40:00', 225, 520.00,  30, 8),
('easyJet',    '2027-08-13 07:15:00', '2027-08-20 15:30:00', 225, 430.00,  35, 8),

-- Innsbruck
('Austrian', '2027-01-22 10:00:00', '2027-01-25 17:30:00',  70, 260.00,  20, 9),
('Austrian', '2027-07-02 09:10:00', '2027-07-05 18:00:00',  70, 240.00,  25, 9),

-- Bergen
('Norwegian', '2027-06-18 08:40:00', '2027-06-22 19:20:00', 165, 380.00, 30, 10),

-- Cas limites pour tester les filtres
-- Peu de places : exclu pour un groupe de plus de 6 personnes
('easyJet', '2027-05-14 15:30:00', '2027-05-17 22:10:00',  95, 110.00,   6, 1),
-- Très cher : exclu pour la plupart des budgets
('Swiss',   '2027-06-11 08:00:00', '2027-06-14 20:00:00', 150, 950.00,  50, 2);

-- ---------------------------------------------------------------------
-- Hôtels
-- prix_nuit : prix par personne et par nuit en CHF
-- capacite : nombre maximal de personnes accueillies pour un groupe
-- ---------------------------------------------------------------------
INSERT INTO hotel (nom, etoiles, prix_nuit, capacite, id_destination) VALUES
-- Barcelone
('Hôtel Rambla Sol',        4, 110.00,  40, 1),
('Barceloneta Beach Hostel', 2,  38.00,  80, 1),
('Casa Gràcia Suites',      5, 210.00,  20, 1),

-- Lisbonne
('Alfama Garden Hotel',     4,  95.00,  35, 2),
('Lisboa Central Hostel',   2,  32.00,  60, 2),

-- Londres
('Camden Lodge',            3, 120.00,  45, 3),
('Thames View Hotel',       4, 185.00,  25, 3),
('Southbank Hostel',        1,  48.00, 100, 3),

-- Amsterdam
('Canal House Hotel',       4, 150.00,  30, 4),
('Jordaan Budget Inn',      2,  65.00,  50, 4),

-- Héraklion
('Knossos Beach Resort',    4,  85.00,  70, 5),
('Villa Minoa',             3,  60.00,  25, 5),

-- Palma
('Cala Blava Resort',       4,  90.00,  80, 6),
('Palma Old Town Hostel',   2,  35.00,  40, 6),

-- La Valette
('Grand Harbour Hotel',     4, 100.00,  45, 7),
('Sliema Bay Apartments',   3,  70.00,  30, 7),

-- Reykjavik
('Aurora Guesthouse',       3, 140.00,  30, 8),
('Reykjavik Downtown Hostel', 2, 75.00, 50, 8),

-- Innsbruck
('Alpenhof Innsbruck',      4, 120.00,  35, 9),
('Nordkette Chalet',        3,  80.00,  20, 9),

-- Bergen
('Bryggen Fjord Hotel',     4, 160.00,  30, 10),

-- Cas limite : capacité trop faible pour un groupe
('Petit Riad Lisboa',       3,  55.00,   8, 2);
