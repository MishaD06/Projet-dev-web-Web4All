-- MySQL dump 10.13  Distrib 8.0.36, for Linux (x86_64)
--
-- Host: 127.0.0.1    Database: stagelab
-- ------------------------------------------------------
-- Server version	8.0.45-0ubuntu0.24.04.1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Dumping data for table `application`
--

LOCK TABLES `application` WRITE;
/*!40000 ALTER TABLE `application` DISABLE KEYS */;
/*!40000 ALTER TABLE `application` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `company`
--

LOCK TABLES `company` WRITE;
/*!40000 ALTER TABLE `company` DISABLE KEYS */;
INSERT INTO `company` VALUES (34,'CodeFlow SAS','Développement logiciel agile et cloud','Paris','rhcodeflow@entreprise.fr','01 40 12 34 01','2026-04-02 23:40:26'),(35,'DataMind Solutions','IA et analyse prédictive','Lyon','rhdatamind@entreprise.fr','04 72 34 56 02','2026-04-02 23:41:13'),(36,'CyberSecure','Cybersécurité et conformité RGPD','Marseille','rhcybersecure@entreprise.fr','04 91 23 45 03','2026-04-02 23:42:59'),(37,'DevBoost','Accélération de projets DevOps','Toulouse','rhdevboost@entreprise.fr','05 61 12 34 04','2026-04-02 23:43:50'),(38,'CloudNest','Infogérance cloud et SaaS','Nice','rhcloudnest@entreprise.fr','04 93 45 67 05','2026-04-02 23:44:37'),(39,'WebCraft Studio','Création de sites web et e-commerce','Nantes','rhwebcraftstudio@entreprise.fr','02 40 56 78 06','2026-04-02 23:45:47'),(40,'AlgoRhythm','Algorithmes sur mesure et optimisation','Strasbourg','rhalgorythm@entreprise.fr','03 88 12 34 07','2026-04-02 23:46:51'),(41,'NetGuard','Sécurité réseau et audit','Lille','rhnetguard@entreprise.fr','03 20 45 67 08','2026-04-02 23:47:59'),(42,'GreenITech','Green IT et data centers écologiques','Bordeaux','rhgreenitech@entreprise.fr','05 56 78 90 09','2026-04-02 23:49:02'),(43,'SoftInnov','Éditeur de logiciels métier','Rennes','rhsoftinnov@entreprise.fr','02 99 12 34 10','2026-04-02 23:49:58'),(44,'BlockSecure','Blockchain et traçabilité sécurisée','Montpellier','rhblockscure@entreprise.fr','04 67 89 01 11','2026-04-02 23:51:31'),(45,'DataViz Corp','Visualisation et big data','Grenoble','rhdatavizcorp@entreprise.fr','04 76 45 67 12','2026-04-02 23:52:29'),(46,'IT-Consult Pro','Conseil en transformation numérique','Dijon','rhitconsultpro@entreprise.fr','03 80 56 78 13','2026-04-02 23:53:26'),(47,'SysAdmin Solutions','Administration systèmes et réseaux','Clermont-Ferrand','rhsysadminsolutions@entreprise.fr','04 73 34 56 14','2026-04-02 23:54:31'),(48,'AI Edge','Intelligence artificielle embarquée','Aix-en-Provence','rhaiedge@entreprise.fr','04 42 12 34 15','2026-04-02 23:55:23'),(49,'API Factory','Création et gestion d’API','Metz','rhapifactory@entreprise.fr','03 87 45 67 16','2026-04-02 23:56:25'),(50,'AppSecure','Sécurité des applications mobiles','Nancy','rhappsecure@entreprise.fr','03 83 78 90 17','2026-04-02 23:57:20'),(51,'LowCodeLab','Plateforme low-code / no-code','Tours','rhlowcodelab@entreprise.fr','02 47 12 34 18','2026-04-02 23:58:22'),(52,'EdgeComputing','Informatique en périphérie et IoT','Saint-Étienne','rhedgecomputing@entreprise.fr','04 77 45 67 19','2026-04-02 23:59:10'),(53,'QA Automate','Tests automatisés et QA','Le Havre','rhqaautomate@entreprise.fr','02 35 56 78 20','2026-04-03 00:00:17');
/*!40000 ALTER TABLE `company` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `company_account`
--

LOCK TABLES `company_account` WRITE;
/*!40000 ALTER TABLE `company_account` DISABLE KEYS */;
/*!40000 ALTER TABLE `company_account` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `offer`
--

LOCK TABLES `offer` WRITE;
/*!40000 ALTER TABLE `offer` DISABLE KEYS */;
INSERT INTO `offer` VALUES (29,'Développeur Fullstack Node.js/Angular','Développement d’applications web internes',800.00,'2026-04-02',34,'2026-04-03 00:16:03',5),(30,'Assistant DevOps','Mise en place de pipelines CI/CD',900.00,'2026-04-02',34,'2026-04-03 00:17:52',6),(31,'Data Scientist Junior','Analyse et modélisation de données',1000.00,'2026-04-02',35,'2026-04-03 00:18:33',6),(32,'Ingénieur IA embarquée','Déploiement de modèles légers',950.00,'2026-04-02',35,'2026-04-03 00:19:07',5),(33,'Analyste Cybersécurité','Audit et correction de failles',850.00,'2026-04-02',36,'2026-04-03 00:19:58',5),(34,'DevOps Engineer','Automatisation déploiements',1100.00,'2026-04-02',37,'2026-04-03 00:20:39',6),(35,'Développeur Backend Java','Refonte d’API',900.00,'2026-04-02',37,'2026-04-03 00:21:13',5),(36,'Cloud Developer','Migration serverless',1000.00,'2026-04-02',38,'2026-04-03 00:22:02',6),(37,'Support Cloud','Monitoring et ticketing',750.00,'2026-04-02',38,'2026-04-03 00:22:46',4),(38,'Intégrateur Web','Création de templates responsive',700.00,'2026-04-02',39,'2026-04-03 00:23:29',4),(39,'Algorithmicien Junior','Optimisation d’algorithmes',950.00,'2026-04-02',40,'2026-04-03 00:24:20',6),(40,'Data Engineer','Pipeline ETL',900.00,'2026-04-02',40,'2026-04-03 00:25:07',5),(41,'Administrateur Réseau','Configuration pare-feux',850.00,'2026-04-02',41,'2026-04-03 00:26:02',5),(42,'Analyste SOC','Surveillance sécurité',800.00,'2026-04-02',41,'2026-04-03 00:26:52',5),(43,'Éco-concepteur Web','Optimisation énergétique sites',850.00,'2026-04-02',42,'2026-04-03 00:27:36',5),(44,'Développeur Laravel','Refonte application métier',850.00,'2026-04-02',43,'2026-04-03 00:28:22',6),(45,'Développeur Blockchain','Smart contracts',1000.00,'2026-04-02',44,'2026-04-03 00:29:20',6),(46,'Analyste Sécurité Blockchain','Audit de contrats',950.00,'2026-04-02',44,'2026-04-03 00:30:52',5),(47,'Data Analyst','Création dashboards',900.00,'2026-04-02',45,'2026-04-03 00:31:36',5),(48,'Ingénieur Big Data','Optimisation requêtes',1000.00,'2026-04-02',45,'2026-04-03 00:32:23',6),(49,'Consultant Technique','Analyse SI',850.00,'2026-04-02',46,'2026-04-03 00:33:11',5),(50,'Assistant AMOA','Rédaction spécifications',850.00,'2026-04-02',46,'2026-04-03 00:33:49',5),(51,'Administrateur Systèmes','Supervision serveurs',850.00,'2026-04-02',47,'2026-04-03 00:34:32',5),(52,'Ingénieur Machine Learning','Modèles embarqués',1100.00,'2026-04-03',48,'2026-04-03 09:07:20',6),(53,'Data Scientist','Traitement signal',1000.00,'2026-04-03',48,'2026-04-03 09:08:10',5),(54,'Développeur API REST','Conception endpoints',900.00,'2026-04-03',49,'2026-04-03 09:10:33',5),(55,'Intégrateur GraphQL','Migration GraphQL',950.00,'2026-04-03',49,'2026-04-03 09:11:14',5),(56,'Concepteur Low-Code','Création d’applications no-code',800.00,'2026-04-03',51,'2026-04-03 09:12:16',4),(57,'Développeur IoT','Firmware et capteurs',900.00,'2026-04-03',52,'2026-04-03 09:13:13',5),(58,'Ingénieur Edge IA','Déploiement modèles légers',900.00,'2026-04-03',52,'2026-04-03 09:14:13',6),(59,'Automaticien de Tests','Framework Cypress',850.00,'2026-04-03',53,'2026-04-03 09:15:28',5),(60,'Spécialiste CI/CD pour tests','Intégration continue',900.00,'2026-04-03',53,'2026-04-03 09:16:46',5);
/*!40000 ALTER TABLE `offer` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `offer_skill`
--

LOCK TABLES `offer_skill` WRITE;
/*!40000 ALTER TABLE `offer_skill` DISABLE KEYS */;
INSERT INTO `offer_skill` VALUES (38,2),(43,2),(44,3),(38,4),(43,4),(31,5),(32,5),(39,5),(47,5),(48,5),(52,5),(53,5),(57,5),(58,5),(40,6),(47,6),(48,6),(49,6),(47,7),(30,8),(31,8),(32,8),(34,8),(36,8),(57,8),(60,8),(36,9),(37,9),(48,9),(58,9),(42,12),(43,13),(44,13),(49,13),(50,13),(56,13),(59,13),(50,14),(56,14),(59,14),(38,15),(33,17),(37,17),(41,17),(51,17),(33,18),(42,18),(46,18),(29,21),(29,22),(45,22),(54,22),(44,24),(35,27),(49,27),(35,28),(55,29),(29,33),(35,33),(45,33),(54,33),(59,33),(55,34),(31,35),(32,35),(39,35),(52,35),(53,35),(58,35),(40,36),(47,36),(53,36),(40,37),(48,37),(54,37),(34,38),(60,39),(30,40),(60,40),(34,41),(51,42),(30,43),(37,43),(41,43),(51,43),(36,44),(36,45),(33,46),(41,46),(42,46),(46,46),(39,47),(59,47),(43,48),(50,48),(56,48),(45,49),(55,49),(49,50);
/*!40000 ALTER TABLE `offer_skill` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `pilote_account`
--

LOCK TABLES `pilote_account` WRITE;
/*!40000 ALTER TABLE `pilote_account` DISABLE KEYS */;
/*!40000 ALTER TABLE `pilote_account` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `review`
--

LOCK TABLES `review` WRITE;
/*!40000 ALTER TABLE `review` DISABLE KEYS */;
INSERT INTO `review` VALUES (17,34,16,4,'Petit bémol sur les délais, mais la qualité du code compense largement.','2026-04-03 00:01:04'),(18,35,16,5,'Accompagnement personnalisé et résultats au rendez-vous.','2026-04-03 00:01:30'),(19,36,16,4,'Très bon audit de sécurité. Ils ont identifié des failles critiques que nous avions ignorées.','2026-04-03 00:01:52'),(20,36,137,3,'Service correct, mais le support après-vente est un peu lent.','2026-04-03 00:02:17'),(21,37,137,5,'Les outils d’automatisation proposés sont bluffants.','2026-04-03 00:02:33'),(22,38,138,4,'Migration vers le cloud parfaitement orchestrée. Zéro temps d’arrêt.','2026-04-03 00:03:05'),(23,39,138,3,'Sites web esthétiques, mais SEO un peu négligé.','2026-04-03 00:03:21'),(24,39,139,4,'Très bonne gestion du e-commerce. Interface admin claire et intuitive.','2026-04-03 00:03:45'),(25,40,139,5,'Les développeurs sont des pointures, un plaisir de collaborer.','2026-04-03 00:04:01'),(26,41,139,4,'Documentation un peu légère.','2026-04-03 00:04:18'),(27,41,137,3,'Correct sans plus. Support technique parfois difficile à joindre.','2026-04-03 00:04:43'),(28,42,137,5,'Une approche éco-responsable rafraîchissante. Nos serveurs consomment 30 % d’énergie en moins.','2026-04-03 00:04:58'),(29,43,137,4,'Logiciel métier stable et évolutif. Interface un peu vieillotte mais fonctionnelle.','2026-04-03 00:05:17'),(30,44,138,4,'La phase d’intégration était un peu longue, mais le résultat en vaut la peine.','2026-04-03 00:05:49'),(31,45,138,5,'Ils ont su vulgariser des données complexes.','2026-04-03 00:06:11'),(32,45,16,3,'Très bonne qualité visuelle, mais le prix des licences est élevé.','2026-04-03 00:06:37'),(33,46,16,4,'Un vrai coup de pouce pour notre transformation numérique.','2026-04-03 00:06:56'),(34,47,16,3,'Dépannage correct, mais pas de gestion proactive.','2026-04-03 00:08:53'),(35,47,137,4,'Excellents sur l’automatisation des backups et la supervision.','2026-04-03 00:09:18'),(36,48,138,5,'L’IA embarquée fonctionne parfaitement sur nos capteurs industriels.','2026-04-03 00:11:46'),(37,49,138,4,'API robustes et bien documentées. Montée en charge sans souci.','2026-04-03 00:12:14'),(38,49,139,3,'Fait le job, mais l’interface de gestion des clés API est peu pratique.','2026-04-03 00:12:53'),(39,50,139,5,'Rapports clairs même pour nos équipes non techniques.','2026-04-03 00:13:09'),(40,51,139,4,'En 2 semaines, on avait une application métier opérationnelle.','2026-04-03 00:13:25'),(41,52,139,4,'Déploiement réussi sur 200 sites IoT. Latence quasi nulle.','2026-04-03 00:13:42'),(42,52,138,3,'Concept intéressant, mais l’outil de monitoring est perfectible.','2026-04-03 00:14:02'),(43,53,138,5,'Une équipe de QA ultra professionnelle et pédagogique.','2026-04-03 00:14:15');
/*!40000 ALTER TABLE `review` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `skill`
--

LOCK TABLES `skill` WRITE;
/*!40000 ALTER TABLE `skill` DISABLE KEYS */;
INSERT INTO `skill` VALUES (30,'.NET'),(16,'Adobe XD'),(13,'Agile'),(12,'Anglais'),(21,'Angular'),(42,'Ansible'),(33,'API REST'),(9,'AWS'),(44,'Azure'),(39,'CI/CD'),(25,'Django'),(8,'Docker'),(15,'Figma'),(26,'Flask'),(31,'Flutter'),(45,'GCP'),(49,'Git'),(40,'GitHub Actions'),(34,'GraphQL'),(4,'HTML/CSS'),(27,'Java'),(2,'JavaScript'),(14,'Jira'),(38,'Kubernetes'),(24,'Laravel'),(43,'Linux Bash'),(35,'Machine Learning'),(37,'MongoDB'),(3,'MySQL'),(22,'Node.js'),(46,'OWASP'),(36,'Pandas/NumPy'),(18,'Pentest'),(1,'PHP'),(7,'Power BI'),(5,'Python'),(19,'React'),(32,'React Native'),(17,'Réseau'),(50,'Scrum'),(11,'SEA'),(10,'SEO'),(28,'Spring Boot'),(6,'SQL'),(23,'Symfony'),(41,'Terraform'),(47,'Tests Unitaires'),(29,'TypeScript'),(48,'UI/UX Design'),(20,'Vue.js');
/*!40000 ALTER TABLE `skill` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `student_account`
--

LOCK TABLES `student_account` WRITE;
/*!40000 ALTER TABLE `student_account` DISABLE KEYS */;
/*!40000 ALTER TABLE `student_account` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `user`
--

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` VALUES (16,'Admin','Admin','admin@viacesi.fr','$2y$10$pJkQHua0wWv6mzj1exwt8.pCXxFoUdf9tzAwO38f4smGlKMY4BFIK','admin',NULL,'2026-03-25 19:18:04',NULL,NULL),(137,'Lefebvre','Sophie','sophie.lefebvre@viacesi.fr','$2y$10$4DR.ZeqW5/hzm0CFww0SzePip3nT7S1niIHhb.f/LUYK2Os9A6IrK','pilote',NULL,'2026-04-02 23:02:21',NULL,'06 98 76 54 01'),(138,'Morel','Philippe','philippe.morel@viacesi.fr','$2y$10$npSERMDI4SZI/9c2gwxb6Omh/ZL36xjpqj0WyM/L54j/CRAdME8gy','pilote',NULL,'2026-04-02 23:03:32',NULL,'06 98 76 54 02'),(139,'Caron','Valérie','valerie.caron@viacesi.fr','$2y$10$sZ3a/cSckuPqsoAH89JQBOSevtwf42Cx7DcM.jtQyScsIQbHSMqMG','pilote',NULL,'2026-04-02 23:03:55',NULL,'06 98 76 54 03'),(140,'Martin','Léa','lea.martin@viacesi.fr','$2y$10$eWl2WcW54msKATn1uycA0ek9UdesHyO5LnSFpYUhJApZKgopJUF6.','etudiant',137,'2026-04-02 23:05:01',NULL,'06 12 34 56 01'),(141,'Bernard','Lucas','lucas.bernard@viacesi.fr','$2y$10$ZaOOuu4118TBKiKS0uQW5.GTU1M.OUSHZvnv8VW9RZrc3kl0l6mDm','etudiant',137,'2026-04-02 23:05:46',NULL,'06 12 34 56 02'),(142,'Dubois','Chloé','chloe.dubois@viacesi.fr','$2y$10$j4rTS.Qi9Z4ajWAhD4BKee.H9fKqYEK3OD4bgP9TR7SgfHt8vEIVW','etudiant',137,'2026-04-02 23:06:14',NULL,'06 12 34 56 03'),(143,'Thomas','Enzo','enzo.thomas@viacesi.fr','$2y$10$KzKbdqJtbTe2RAtcLHnF/e0wumCwKEhh2Nn3e0OAa.DUUdMOfp1fq','etudiant',137,'2026-04-02 23:06:41',NULL,'06 12 34 56 04'),(144,'Robert','Manon','manon.robert@viacesi.fr','$2y$10$QKZsVZ6HljRB7ABsu3Dyuuca1qFngl8RIXxS16QOL5Awab6pmet1G','etudiant',137,'2026-04-02 23:07:23',NULL,'06 12 34 56 05'),(145,'Richard','Jules','jules.richard@viacesi.fr','$2y$10$FK/JV3KZvhpclBwLd1/yE.NrmQm8kOYeAnPIO/uxPz6Tu9zf3RUdi','etudiant',137,'2026-04-02 23:08:17',NULL,'06 12 34 56 06'),(146,'Petit','Emma','emma.petit@viacesi.fr','$2y$10$cQmze1Psuq8so.NsB9TJPO24M5cjgTcICMEj9ztb0/xcQ/IZvqVIW','etudiant',137,'2026-04-02 23:08:57',NULL,'06 12 34 56 07'),(147,'Durand','Louis','louis.durand@viacesi.fr','$2y$10$iAucTA7anWoXa5/RgMrJg.Z72Gn0aL9e5kn6nev71BvxzRmM1CXsi','etudiant',137,'2026-04-02 23:09:23',NULL,'06 12 34 56 08'),(148,'Leroy','Sarah','sarah.leroy@viacesi.fr','$2y$10$lU4yRYW1djENrPWlJEB0nOoWhKA2wR3uHtH/zowijXO4s.9A6rBli','etudiant',137,'2026-04-02 23:10:20',NULL,'06 12 34 56 09'),(149,'Moreau','Mathis','mathis.moreau@viacesi.fr','$2y$10$X/CN8Muoa5ox46xPrmKBV.zwfRPE9xc2jDE82omZQ8G84yDqkt0OS','etudiant',137,'2026-04-02 23:11:38',NULL,'06 12 34 56 10'),(150,'Simon','Camille','camille.simon@viacesi.fr','$2y$10$s4aTITUth5qiWqZOJ4McT.vgfQvfBC1VFosNCzjEiIcIs3Nxs/4Vq','etudiant',138,'2026-04-02 23:12:19',NULL,'06 12 34 56 11'),(151,'Laurent','Nathan','nathan.laurent@viacesi.fr','$2y$10$rW84g7hoEuBFcmU.i9jhQ.LipjAN9plaNnHpeEYeT2jqQ7s/g/w16','etudiant',138,'2026-04-02 23:12:44',NULL,'06 12 34 56 12'),(152,'Michel','Lola','lola.michel@viacesi.fr','$2y$10$JcKNJvZ.4QDpx2a/jOC2Qe6uaTQza9TEAEERrPjfj0IOjFpRLPgT6','etudiant',138,'2026-04-02 23:13:13',NULL,'06 12 34 56 13'),(153,'Garcia','Théo','theo.garcia@viacesi.fr','$2y$10$e3RrDabqn8IYLLZFNt6xg.x5frvhvK34t0ow2.Ssd3MpShR9aT1mW','etudiant',138,'2026-04-02 23:13:43',NULL,'06 12 34 56 14'),(154,'David','Inès','ines.david@viacesi.fr','$2y$10$qN/ORQluw4Q4j1dwxc7HmOwhttaoJYMDwVSaW1iiW5QAPcZa/Wifq','etudiant',138,'2026-04-02 23:14:12',NULL,'06 12 34 56 15'),(155,'Bertrand','Maxime','maxime.bertrand@viacesi.fr','$2y$10$jvlZEzHSUl869VmRTXyv5uJaaU1hLQmvQ78mB3X92Y7nrQTo44Bl2','etudiant',138,'2026-04-02 23:14:52',NULL,'06 12 34 56 16'),(156,'Roux','Alice','alice.roux@viacesi.fr','$2y$10$vPI27VOD15PLSghBAkNeV.L0Vibk7TS/I06r/Sy/Co5N9YEqJuC/S','etudiant',138,'2026-04-02 23:15:37',NULL,'06 12 34 56 17'),(157,'Vincent','Hugo','hugo.vincent@viacesi.fr','$2y$10$WNoQ3rc5LQnIz3PPrdjXWOL2SaHwoYYdahFY91QU7onDMpq/N4UQu','etudiant',138,'2026-04-02 23:16:26',NULL,'06 12 34 56 18'),(158,'Fournier','Juliette','juliette.fournier@viacesi.fr','$2y$10$GdqZ/6FxiSfx6Bw7STV8SOAr9aU74riMQT/pN5QW7LBzQGMJFHV7y','etudiant',138,'2026-04-02 23:16:55',NULL,'06 12 34 56 19'),(159,'Girard','Tom','tom.girard@viacesi.fr','$2y$10$us9SLc6g8.67yvRTutgQvuLeuvQGK9l7Dl.aRNPVJ9H1M4knPOHQa','etudiant',138,'2026-04-02 23:18:14',NULL,'06 12 34 56 20'),(160,'André','Pauline','pauline.andre@viacesi.fr','$2y$10$sRJEn/jNi4CTgnAquBqn8eGlqs0RUCPQH5kO/1vo.fQtqEyZ.J3GO','etudiant',139,'2026-04-02 23:18:56',NULL,'06 12 34 56 21'),(161,'Lefèvre','Antoine','antoine.lefevre@viacesi.fr','$2y$10$5u5jte.Lm5tDb1uZsNIW9uDwGLWDHaardmoTaoia24Wkptc7IixJu','etudiant',139,'2026-04-02 23:19:26',NULL,'06 12 34 56 22'),(162,'Mercier','Anaïs','anais.mercier@viacesi.fr','$2y$10$wWD6B4eT84yuiQN7xdvV6uzQZwEIeUpfkO1geg/fxeYBVpLXh1m3i','etudiant',139,'2026-04-02 23:20:35',NULL,'06 12 34 56 23'),(163,'Dupont','Arthur','arthur.dupont@viacesi.fr','$2y$10$pD1cYVdrHrJKlMXlsTVDBesybnmPtZKlcNUxD3596PuNLCrAn61mm','etudiant',139,'2026-04-02 23:21:22',NULL,'06 12 34 56 24'),(164,'Lambert','Marie','marie.lambert@viacesi.fr','$2y$10$EINt0pdLdtBktqdERqjPp.wCkBxS/YwR327IActUl80eCSxnfK6dq','etudiant',139,'2026-04-02 23:21:57',NULL,'06 12 34 56 25'),(165,'Faure','Clara','clara.faure@viacesi.fr','$2y$10$RyFxkdHIQwTqK3Y6SRX1B.4mS5QYIfRubtcHeK6lDYbzS8v.DH3LO','etudiant',139,'2026-04-02 23:22:32',NULL,'06 12 34 56 26'),(166,'Rousseau','Maxence','maxence.rousseau@viacesi.fr','$2y$10$evnWEpA1z9lB3Krof8RuDeZcJd0DsKFtNBy2v2SgKPgznuvxe2hI2','etudiant',139,'2026-04-02 23:23:00',NULL,'06 12 34 56 27'),(167,'Blanchard','Eva','eva.blanchard@viacesi.fr','$2y$10$2v1MCYmohDUws2svLD4HXeFQJcLcSpeGuVCo72ZQi7snFFDgIKTxe','etudiant',139,'2026-04-02 23:23:34',NULL,'06 12 34 56 28'),(168,'Guerin','Noé','noe.guerin@viacesi.fr','$2y$10$CdXsNWOMffPoQ54ajIkIKuadLMHO6JRzBOrX3Z8S6YAvz9uQYSMsi','etudiant',139,'2026-04-02 23:24:14',NULL,'06 12 34 56 29'),(169,'Chevalier','Nina','nina.chevalier@viacesi.fr','$2y$10$qZjQ.4qyhvVXhZLzGnvfVeuFEmQ7FRoYXbj0D8/kuuHToQb451W0i','etudiant',139,'2026-04-02 23:24:47',NULL,'06 12 34 56 30'),(170,'Dupont','Jean','jean.dupont@entreprise.fr','$2y$10$c1hdwzbtPpPlRzu77gpc4.ao.Rz0segFg7j5sQfJSfSGY7ewyjaoy','entreprise',NULL,'2026-04-02 23:40:27',34,NULL),(171,'Martin','Sophie','sophie.martin@entreprise.fr','$2y$10$VbbLcSkDT7FliNbng25jFu1IxP3dJoVp.lJVWPox7AtGs0WtWxf12','entreprise',NULL,'2026-04-02 23:41:14',35,NULL),(172,'Bernard','Lucas','lucas.bernard@entreprise.fr','$2y$10$lWQvbvKy47dJ32RY/86SDOH3wZZ3QmE2rKjd4jHAdmHGGQ2w4sVKa','entreprise',NULL,'2026-04-02 23:42:59',36,NULL),(173,'Dubois','Chloé','chloe.dubois@entreprise.fr','$2y$10$892RnvhwxVjfuIuo2c8vEuFRS49MAHaD84rnFavNd9ba0uOkg7PZW','entreprise',NULL,'2026-04-02 23:43:50',37,NULL),(174,'Thomas','Enzo','enzo.thomas@entreprise.fr','$2y$10$t849grGSZzw3QFT5Fe7mvepEsjWzvF6kiDAjWI7F/zpVf9vlNp.Fy','entreprise',NULL,'2026-04-02 23:44:37',38,NULL),(175,'Robert','Manon','manon.robert@entreprise.fr','$2y$10$7UYzfXamSiaLV3elylWCBuEeQG5d9CA5bLMxqwTyzhqlMA.QxyIPq','entreprise',NULL,'2026-04-02 23:45:47',39,NULL),(176,'Richard','Jules','jules.richard@entreprise.fr','$2y$10$tZ.wC7YY4Iz584QLX1KC.uJsyRqbsb.qe9cMDDRbLwU2v5sOB2CGS','entreprise',NULL,'2026-04-02 23:46:51',40,NULL),(177,'Petit','Emma','emma.petit@entreprise.fr','$2y$10$PCRGcHQTWGPSmG/DQuW8y.FFvsCuAKC.azqMjVhMUy1nvlFVvZcc2','entreprise',NULL,'2026-04-02 23:47:59',41,NULL),(178,'Durand','Louis','louis.durand@entreprise.fr','$2y$10$ZKZsbmg80zbMp4f8xQbkp.bNgK.iDym.6Hdfqowkv.czWUoLwypJK','entreprise',NULL,'2026-04-02 23:49:02',42,NULL),(179,'Leroy','Sarah','sarah.leroy@entreprise.fr','$2y$10$Xt0QhkGgtm4by0sujFM1Bu/W6/PwcoiR4dclPf19Zz9GlNvE57al6','entreprise',NULL,'2026-04-02 23:49:58',43,NULL),(180,'Moreau','Mathis','mathis.moreau@entreprise.fr','$2y$10$WRsC3Urm3uGIPmsZufrc2OlJyFsic48e8jz5ZO8zm4HnLyU8VnniC','entreprise',NULL,'2026-04-02 23:51:31',44,NULL),(181,'Simon','Camille','camille.simon@entreprise.fr','$2y$10$VpbHVXRx2uGrVUl2qQbTAeetzz7b0DoV8Vm4v4pLJijmnHIYFD1wm','entreprise',NULL,'2026-04-02 23:52:29',45,NULL),(182,'Laurent','Nathan','nathan.laurent@entreprise.fr','$2y$10$hjJirCtMTRRwmkf6XxCNE.3RDrxB8t317UHCrnD.edvKN0r0HrQwm','entreprise',NULL,'2026-04-02 23:53:26',46,NULL),(183,'Michel','Lola','lola.michel@entreprise.fr','$2y$10$ZZk9EhQdkXfwrB1wq60vmO3BE4.WMD0.bpkcZWdF.b8OzBvgE17Em','entreprise',NULL,'2026-04-02 23:54:32',47,NULL),(184,'Garcia','Théo','theo.garcia@entreprise.fr','$2y$10$JL63DhNnd/CqtQ02L.ufU.yCmRO3K5jg2zcN8Jt4J4iO1NLBdOAoC','entreprise',NULL,'2026-04-02 23:55:23',48,NULL),(185,'David','Ines','ines.david@entreprise.fr','$2y$10$hWWYZUal2IvAWY.bXMdZ5.rqe45Ns6HfEp2o07LAMCdq9ydxo5eLe','entreprise',NULL,'2026-04-02 23:56:25',49,NULL),(186,'Bertrand','Maxime','maxime.bertrand@entreprise.fr','$2y$10$GZiwNZwpi34o9MOePTFr2u59eIVGyLsjeIba4zSt9dnuamq/Nm85W','entreprise',NULL,'2026-04-02 23:57:20',50,NULL),(187,'Roux','Alice','alice.roux@entreprise.fr','$2y$10$aZedr0VtXHDlBMlZYLiyYekMnpQc5MD1iC6HXCDxFWYAW/t2lZUHW','entreprise',NULL,'2026-04-02 23:58:22',51,NULL),(188,'Vincent','Hugo','hugo.vincent@entreprise.fr','$2y$10$OlqOdUKESGPggudG9LR3K.vGnni/x9LrmZB6MRxVmIjtUHEz8xFOO','entreprise',NULL,'2026-04-02 23:59:10',52,NULL),(189,'Fournier','Juliette','juliette.fournier@entreprise.fr','$2y$10$sYJ0PXI9A.BlnPV.gLIX4O7C4GI/nb/8riHhgsJxpNDfyogFfGTce','entreprise',NULL,'2026-04-03 00:00:17',53,NULL);
/*!40000 ALTER TABLE `user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `wishlist`
--

LOCK TABLES `wishlist` WRITE;
/*!40000 ALTER TABLE `wishlist` DISABLE KEYS */;
INSERT INTO `wishlist` VALUES (40,140,59,'2026-04-03 09:19:31'),(41,140,58,'2026-04-03 09:19:34'),(42,140,55,'2026-04-03 09:19:37'),(43,140,56,'2026-04-03 09:19:41'),(44,141,53,'2026-04-03 09:20:05'),(45,141,58,'2026-04-03 09:20:08'),(46,141,54,'2026-04-03 09:20:11'),(47,141,49,'2026-04-03 09:20:14'),(48,141,57,'2026-04-03 09:20:17'),(49,145,58,'2026-04-03 09:20:42'),(50,145,55,'2026-04-03 09:20:53'),(51,145,49,'2026-04-03 09:21:00'),(52,148,58,'2026-04-03 09:21:34'),(53,148,55,'2026-04-03 09:21:38'),(54,148,50,'2026-04-03 09:21:42'),(55,148,54,'2026-04-03 09:21:45'),(56,150,53,'2026-04-03 09:22:56'),(57,150,50,'2026-04-03 09:23:08'),(58,154,50,'2026-04-03 09:23:31'),(59,154,52,'2026-04-03 09:23:37'),(60,157,56,'2026-04-03 09:24:03'),(61,157,53,'2026-04-03 09:24:07'),(62,157,52,'2026-04-03 09:25:04'),(63,167,49,'2026-04-03 09:25:19'),(64,167,51,'2026-04-03 09:25:22');
/*!40000 ALTER TABLE `wishlist` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-04-03  9:27:16
