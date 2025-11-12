-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : mer. 05 nov. 2025 à 22:49
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `bibliotheque_crud`
--

-- --------------------------------------------------------

--
-- Structure de la table `abonne`
--

CREATE TABLE `abonne` (
  `id_abonne` int(3) NOT NULL,
  `civilite` varchar(5) NOT NULL,
  `nom` varchar(25) NOT NULL,
  `prenom` varchar(15) NOT NULL,
  `email` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Déchargement des données de la table `abonne`
--

INSERT INTO `abonne` (`id_abonne`, `civilite`, `nom`, `prenom`, `email`) VALUES
(1, 'M.', 'Dubois', 'Guillaume', 'guillaume.dubois@email.fr'),
(2, 'M.', 'Martin', 'Benoit', 'benoit.martin@email.fr'),
(3, 'Mme', 'Bernard', 'Chloe', 'chloe.bernard@email.fr'),
(4, 'Mme', 'Petit', 'Laura', 'laura.petit@email.fr'),
(5, 'M.', 'Rousseau', 'Thomas', 'thomas.rousseau@email.fr'),
(6, 'Mme', 'Leroy', 'Marie', 'marie.leroy@email.fr'),
(7, 'M.', 'Moreau', 'Lucas', 'lucas.moreau@email.fr'),
(8, 'Mme', 'Simon', 'Emma', 'emma.simon@email.fr'),
(9, 'M.', 'Laurent', 'Hugo', 'hugo.laurent@email.fr'),
(10, 'Mme', 'Lefebvre', 'Lea', 'lea.lefebvre@email.fr'),
(11, 'M.', 'Michel', 'Nathan', 'nathan.michel@email.fr'),
(12, 'Mme', 'Garcia', 'Camille', 'camille.garcia@email.fr'),
(13, 'M.', 'David', 'Antoine', 'antoine.david@email.fr'),
(14, 'Mme', 'Bertrand', 'Julie', 'julie.bertrand@email.fr'),
(15, 'M.', 'Roux', 'Alexandre', 'alexandre.roux@email.fr'),
(16, 'Mme', 'Vincent', 'Sarah', 'sarah.vincent@email.fr'),
(17, 'M.', 'Fournier', 'Maxime', 'maxime.fournier@email.fr'),
(18, 'Mme', 'Girard', 'Manon', 'manon.girard@email.fr'),
(19, 'M.', 'Andre', 'Nicolas', 'nicolas.andre@email.fr'),
(20, 'Mme', 'Mercier', 'Sophie', 'sophie.mercier@email.fr'),
(21, 'M.', 'Blanc', 'Pierre', 'pierre.blanc@email.fr'),
(22, 'Mme', 'Dupont', 'Amelie', 'amelie.dupont@email.fr'),
(23, 'M.', 'Lambert', 'Julien', 'julien.lambert@email.fr'),
(24, 'Mme', 'Fontaine', 'Elise', 'elise.fontaine@email.fr'),
(25, 'M.', 'Bonnet', 'Clement', 'clement.bonnet@email.fr'),
(26, 'Mme', 'Robert', 'Mathilde', 'mathilde.robert@email.fr'),
(27, 'M.', 'Richard', 'Romain', 'romain.richard@email.fr'),
(28, 'Mme', 'Durand', 'Charlotte', 'charlotte.durand@email.fr'),
(29, 'M.', 'Petit', 'Valentin', 'valentin.petit@email.fr'),
(30, 'Mme', 'Lemoine', 'Louise', 'louise.lemoine@email.fr'),
(31, 'M.', 'Gauthier', 'Theo', 'theo.gauthier@email.fr'),
(32, 'Mme', 'Perrin', 'Alice', 'alice.perrin@email.fr'),
(33, 'M.', 'Morel', 'Arthur', 'arthur.morel@email.fr'),
(34, 'Mme', 'Giraud', 'Lucie', 'lucie.giraud@email.fr'),
(35, 'M.', 'Nicolas', 'Raphael', 'raphael.nicolas@email.fr');

-- --------------------------------------------------------

--
-- Structure de la table `administrateur`
--

CREATE TABLE `administrateur` (
  `id_admin` int(3) NOT NULL,
  `login` varchar(30) NOT NULL,
  `mot_de_passe` varchar(255) NOT NULL,
  `nom` varchar(25) NOT NULL,
  `prenom` varchar(25) NOT NULL,
  `email` varchar(50) NOT NULL,
  `date_creation` datetime NOT NULL DEFAULT current_timestamp(),
  `dernier_acces` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Déchargement des données de la table `administrateur`
--

INSERT INTO `administrateur` (`id_admin`, `login`, `mot_de_passe`, `nom`, `prenom`, `email`, `date_creation`, `dernier_acces`) VALUES
(1, 'admin', '$2y$10$5DJajd4i4u9ywCfKmzUsp.5GuzRXLqCp4cyjBo4deKKcu3Xtnb/mm', 'Administrateur', 'Principal', 'admin@bibliotheque.fr', '2018-01-01 10:00:00', '2025-11-02 00:22:12'),
(2, 'bibliothecaire1', '$2y$10$5DJajd4i4u9ywCfKmzUsp.5GuzRXLqCp4cyjBo4deKKcu3Xtnb/mm', 'Lemaire', 'Sophie', 'sophie.lemaire@bibliotheque.fr', '2018-01-05 09:00:00', '2018-01-28 16:45:00'),
(3, 'gestion', '$2y$10$5DJajd4i4u9ywCfKmzUsp.5GuzRXLqCp4cyjBo4deKKcu3Xtnb/mm', 'Roussel', 'Marc', 'marc.roussel@bibliotheque.fr', '2018-01-10 11:30:00', '2018-01-27 10:15:00');

-- --------------------------------------------------------

--
-- Structure de la table `emprunt`
--

CREATE TABLE `emprunt` (
  `id_emprunt` int(3) NOT NULL,
  `id_livre` int(3) DEFAULT NULL,
  `id_abonne` int(3) DEFAULT NULL,
  `date_sortie` date NOT NULL,
  `date_rendu` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Déchargement des données de la table `emprunt`
--

INSERT INTO `emprunt` (`id_emprunt`, `id_livre`, `id_abonne`, `date_sortie`, `date_rendu`) VALUES
(1, 100, 1, '2011-12-17', '2011-12-18'),
(2, 101, 2, '2011-12-18', '2011-12-20'),
(3, 100, 3, '2011-12-19', '2011-12-22'),
(4, 103, 4, '2011-12-19', '2011-12-22'),
(5, 104, 1, '2011-12-19', '2011-12-28'),
(6, 105, 2, '2012-03-20', '2012-03-26'),
(7, 105, 3, '2013-06-13', NULL),
(8, 100, 2, '2013-06-15', NULL),
(9, 106, 5, '2014-01-10', '2014-01-24'),
(10, 107, 6, '2014-02-14', '2014-02-28'),
(11, 108, 7, '2014-03-05', '2014-03-19'),
(12, 109, 8, '2014-04-12', '2014-04-26'),
(13, 110, 9, '2014-05-18', '2014-06-01'),
(14, 111, 10, '2014-06-22', '2014-07-06'),
(15, 112, 11, '2014-07-15', '2014-07-29'),
(16, 113, 12, '2014-08-20', '2014-09-03'),
(17, 114, 13, '2014-09-10', '2014-09-24'),
(18, 115, 14, '2014-10-05', '2014-10-19'),
(19, 116, 15, '2014-11-12', '2014-11-26'),
(20, 117, 16, '2014-12-08', '2014-12-22'),
(21, 118, 17, '2015-01-15', '2015-01-29'),
(22, 119, 18, '2015-02-20', '2015-03-06'),
(23, 120, 19, '2015-03-25', '2015-04-08'),
(24, 121, 20, '2015-04-18', '2015-05-02'),
(25, 122, 21, '2015-05-22', '2015-06-05'),
(26, 123, 22, '2015-06-30', '2015-07-14'),
(27, 124, 23, '2015-07-18', '2015-08-01'),
(28, 125, 24, '2015-08-25', '2015-09-08'),
(29, 126, 25, '2015-09-12', '2015-09-26'),
(30, 127, 26, '2015-10-20', '2015-11-03'),
(31, 128, 27, '2015-11-15', '2015-11-29'),
(32, 129, 28, '2015-12-10', '2015-12-24'),
(33, 130, 29, '2016-01-08', '2016-01-22'),
(34, 131, 30, '2016-02-14', '2016-02-28'),
(35, 132, 31, '2016-03-18', '2016-04-01'),
(36, 133, 32, '2016-04-22', '2016-05-06'),
(37, 134, 33, '2016-05-15', '2016-05-29'),
(38, 135, 34, '2016-06-20', '2016-07-04'),
(39, 100, 35, '2016-07-12', '2016-07-26'),
(40, 101, 5, '2016-08-18', '2016-09-01'),
(41, 102, 6, '2016-09-10', '2016-09-24'),
(42, 103, 7, '2016-10-15', '2016-10-29'),
(43, 104, 8, '2016-11-20', '2016-12-04'),
(44, 105, 9, '2016-12-12', '2016-12-26'),
(45, 106, 10, '2017-01-18', '2017-02-01'),
(46, 107, 11, '2017-02-22', '2017-03-08'),
(47, 108, 12, '2017-03-15', '2017-03-29'),
(48, 109, 13, '2017-04-10', '2017-04-24'),
(49, 110, 14, '2017-05-18', '2017-06-01'),
(50, 111, 15, '2017-06-12', '2017-06-26'),
(51, 112, 16, '2017-07-20', '2017-08-03'),
(52, 113, 17, '2017-08-15', '2017-08-29'),
(53, 114, 18, '2017-09-10', '2017-09-24'),
(54, 115, 19, '2017-10-18', '2017-11-01'),
(55, 116, 20, '2017-11-12', '2017-11-26'),
(56, 117, 21, '2017-12-08', '2017-12-22'),
(57, 118, 22, '2018-01-15', NULL),
(58, 119, 23, '2018-01-20', NULL),
(59, 120, 24, '2018-01-22', NULL),
(60, 121, 25, '2018-01-25', NULL),
(61, 122, 1, '2017-03-10', '2017-03-24'),
(62, 123, 2, '2017-04-15', '2017-04-29'),
(63, 124, 3, '2017-05-20', '2017-06-03'),
(64, 125, 4, '2017-06-12', '2017-06-26'),
(65, 126, 1, '2017-07-18', '2017-08-01'),
(66, 127, 2, '2017-08-22', '2017-09-05'),
(67, 128, 3, '2017-09-15', '2017-09-29'),
(68, 129, 4, '2017-10-10', '2017-10-24'),
(69, 130, 5, '2017-11-18', '2017-12-02'),
(70, 131, 6, '2017-12-12', '2017-12-26'),
(71, 100, 26, '2016-05-10', '2016-05-24'),
(72, 101, 27, '2016-06-15', '2016-06-29'),
(73, 102, 28, '2016-07-20', '2016-08-03'),
(74, 103, 29, '2016-08-12', '2016-08-26'),
(75, 104, 30, '2016-09-18', '2016-10-02'),
(76, 105, 31, '2016-10-22', '2016-11-05'),
(77, 106, 32, '2016-11-15', '2016-11-29'),
(78, 107, 33, '2016-12-10', '2016-12-24'),
(79, 108, 34, '2017-01-18', '2017-02-01'),
(80, 109, 35, '2017-02-22', '2017-03-08');

-- --------------------------------------------------------

--
-- Structure de la table `livre`
--

CREATE TABLE `livre` (
  `id_livre` int(3) NOT NULL,
  `auteur` varchar(25) NOT NULL,
  `titre` varchar(30) NOT NULL,
  `couverture` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Déchargement des données de la table `livre`
--

INSERT INTO `livre` (`id_livre`, `auteur`, `titre`, `couverture`) VALUES
(100, 'GUY DE MAUPASSANT', 'Une vie', 'images/couvertures/une_vie.jpg'),
(101, 'GUY DE MAUPASSANT', 'Bel-Ami', 'images/couvertures/bel_ami.jpg'),
(102, 'HONORE DE BALZAC', 'Le pere Goriot', 'images/couvertures/pere_goriot.jpg'),
(103, 'ALPHONSE DAUDET', 'Le Petit chose', 'images/couvertures/petit_chose.jpg'),
(104, 'ALEXANDRE DUMAS', 'La Reine Margot', 'images/couvertures/reine_margot.jpg'),
(105, 'ALEXANDRE DUMAS', 'Les Trois Mousquetaires', 'images/couvertures/trois_mousquetaires.jpg'),
(106, 'VICTOR HUGO', 'Les Miserables', 'images/couvertures/les_miserables.jpg'),
(107, 'VICTOR HUGO', 'Notre-Dame de Paris', 'images/couvertures/notre_dame.jpg'),
(108, 'EMILE ZOLA', 'Germinal', 'images/couvertures/germinal.jpg'),
(109, 'EMILE ZOLA', 'Nana', 'images/couvertures/nana.jpg'),
(110, 'GUSTAVE FLAUBERT', 'Madame Bovary', 'images/couvertures/madame_bovary.jpg'),
(111, 'GUSTAVE FLAUBERT', 'L Education sentimentale', 'images/couvertures/education_sentimentale.jpg'),
(112, 'STENDHAL', 'Le Rouge et le Noir', 'images/couvertures/rouge_noir.jpg'),
(113, 'STENDHAL', 'La Chartreuse de Parme', 'images/couvertures/chartreuse_parme.jpg'),
(114, 'MARCEL PROUST', 'Du cote de chez Swann', 'images/couvertures/swann.jpg'),
(115, 'ALBERT CAMUS', 'L Etranger', 'images/couvertures/etranger.jpg'),
(116, 'ALBERT CAMUS', 'La Peste', 'images/couvertures/peste.jpg'),
(117, 'JEAN-PAUL SARTRE', 'La Nausee', 'images/couvertures/nausee.jpg'),
(118, 'VOLTAIRE', 'Candide', 'images/couvertures/candide.jpg'),
(119, 'MOLIERE', 'Le Tartuffe', 'images/couvertures/tartuffe.jpg'),
(120, 'MOLIERE', 'Dom Juan', 'images/couvertures/dom_juan.jpg'),
(121, 'JEAN RACINE', 'Phedre', 'images/couvertures/phedre.jpg'),
(122, 'PIERRE CORNEILLE', 'Le Cid', 'images/couvertures/le_cid.jpg'),
(123, 'JULES VERNE', 'Vingt mille lieues', 'images/couvertures/vingt_mille_lieues.jpg'),
(124, 'JULES VERNE', 'Le Tour du monde', 'images/couvertures/tour_du_monde.jpg'),
(125, 'JULES VERNE', 'Voyage au centre', 'images/couvertures/voyage_centre.jpg'),
(126, 'VICTOR HUGO', 'Les Contemplations', 'images/couvertures/contemplations.jpg'),
(127, 'CHARLES BAUDELAIRE', 'Les Fleurs du Mal', 'images/couvertures/fleurs_mal.jpg'),
(128, 'ARTHUR RIMBAUD', 'Illuminations', 'images/couvertures/illuminations.jpg'),
(129, 'PAUL VERLAINE', 'Poemes saturniens', 'images/couvertures/poemes_saturniens.jpg'),
(130, 'ANTOINE DE SAINT-EXUP', 'Le Petit Prince', 'images/couvertures/petit_prince.jpg'),
(131, 'MARGUERITE DURAS', 'L Amant', 'images/couvertures/amant.jpg'),
(132, 'ANDRE GIDE', 'Les Faux-Monnayeurs', 'images/couvertures/faux_monnayeurs.jpg'),
(133, 'FRANCOISE SAGAN', 'Bonjour tristesse', 'images/couvertures/bonjour_tristesse.jpg'),
(134, 'SIMONE DE BEAUVOIR', 'Le Deuxieme Sexe', 'images/couvertures/deuxieme_sexe.jpg'),
(135, 'BORIS VIAN', 'L Ecume des jours', 'images/couvertures/ecume_jours.jpg'),
(136, 'GEORGE SAND', 'La Mare au diable', 'images/couvertures/mare_diable.jpg'),
(137, 'THEOPHILE GAUTIER', 'Le Capitaine Fracasse', 'images/couvertures/capitaine_fracasse.jpg'),
(138, 'PROSPER MERIMEE', 'Carmen', 'images/couvertures/carmen.jpg'),
(139, 'ALFRED DE MUSSET', 'Lorenzaccio', 'images/couvertures/lorenzaccio.jpg'),
(140, 'HONORE DE BALZAC', 'Eugenie Grandet', 'images/couvertures/eugenie_grandet.jpg');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `abonne`
--
ALTER TABLE `abonne`
  ADD PRIMARY KEY (`id_abonne`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Index pour la table `administrateur`
--
ALTER TABLE `administrateur`
  ADD PRIMARY KEY (`id_admin`),
  ADD UNIQUE KEY `login` (`login`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Index pour la table `emprunt`
--
ALTER TABLE `emprunt`
  ADD PRIMARY KEY (`id_emprunt`),
  ADD KEY `id_abonne` (`id_abonne`),
  ADD KEY `id_livre` (`id_livre`);

--
-- Index pour la table `livre`
--
ALTER TABLE `livre`
  ADD PRIMARY KEY (`id_livre`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `abonne`
--
ALTER TABLE `abonne`
  MODIFY `id_abonne` int(3) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT pour la table `administrateur`
--
ALTER TABLE `administrateur`
  MODIFY `id_admin` int(3) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `emprunt`
--
ALTER TABLE `emprunt`
  MODIFY `id_emprunt` int(3) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=81;

--
-- AUTO_INCREMENT pour la table `livre`
--
ALTER TABLE `livre`
  MODIFY `id_livre` int(3) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=141;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `emprunt`
--
ALTER TABLE `emprunt`
  ADD CONSTRAINT `emprunt_ibfk_1` FOREIGN KEY (`id_abonne`) REFERENCES `abonne` (`id_abonne`) ON DELETE SET NULL ON UPDATE SET NULL,
  ADD CONSTRAINT `emprunt_ibfk_2` FOREIGN KEY (`id_livre`) REFERENCES `livre` (`id_livre`) ON DELETE SET NULL ON UPDATE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
