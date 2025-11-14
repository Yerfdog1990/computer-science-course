-- MySQL dump 10.13  Distrib 8.0.38, for Win64 (x86_64)
--
-- Host: localhost    Database: library_system
-- ------------------------------------------------------
-- Server version	8.0.39

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
-- Table structure for table `books`
--

DROP TABLE IF EXISTS books;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE books (
  ISBN varchar(13) NOT NULL,
  Title varchar(255) NOT NULL,
  Author varchar(250) NOT NULL,
  Genre varchar(100) DEFAULT NULL,
  Quantity int NOT NULL,
  PRIMARY KEY (ISBN)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `books`
--

LOCK TABLES books WRITE;
/*!40000 ALTER TABLE books DISABLE KEYS */;
INSERT INTO books VALUES ('1590282752','Python Programming: An Introduction to Computer Science','John M. Zelle','Science and Technology',5),('9780133761313','Introduction to Java Programming','Y. Daniel Liang','Science and Technology',10);
/*!40000 ALTER TABLE books ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `loans`
--

DROP TABLE IF EXISTS loans;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE loans (
  LoanID int NOT NULL AUTO_INCREMENT,
  MemberID int NOT NULL,
  ISBN varchar(13) NOT NULL,
  LoanDate date NOT NULL,
  ReturnDate date NOT NULL,
  PRIMARY KEY (LoanID),
  KEY fk_member_idx (MemberID),
  KEY fk_isbn_idx (ISBN),
  CONSTRAINT fk_isbn FOREIGN KEY (ISBN) REFERENCES books (ISBN) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_member FOREIGN KEY (MemberID) REFERENCES members (MemberID) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `loans`
--

LOCK TABLES loans WRITE;
/*!40000 ALTER TABLE loans DISABLE KEYS */;
INSERT INTO loans VALUES (1,1,'1590282752','2024-09-01','2024-09-15'),(2,2,'9780133761313','2024-09-03','2024-09-23');
/*!40000 ALTER TABLE loans ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `members`
--

DROP TABLE IF EXISTS members;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE members (
  MemberID int NOT NULL AUTO_INCREMENT,
  `Name` varchar(255) NOT NULL,
  Email varchar(255) NOT NULL,
  Phone varchar(15) NOT NULL,
  PRIMARY KEY (MemberID)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `members`
--

LOCK TABLES members WRITE;
/*!40000 ALTER TABLE members DISABLE KEYS */;
INSERT INTO members VALUES (1,'Cyril Okoth','c.okoth@myusername.org','0723456950'),(2,'Cyrek Odhiambo','c.odhiambo@myusername.org','0798345612');
/*!40000 ALTER TABLE members ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2024-10-04  5:09:12
