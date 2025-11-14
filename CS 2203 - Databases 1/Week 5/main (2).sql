
CREATE TABLE Books (
    ISBN VARCHAR(13) PRIMARY KEY, -- Unique identifier for books
    Title VARCHAR(255) NOT NULL,  -- Book title
    Author VARCHAR(255) NOT NULL, -- Book author
    Genre VARCHAR(100),           -- Genre of the book
    Quantity INT NOT NULL         -- Number of available copies
);

-- Insert values into Book's table
INSERT INTO Books (ISBN, Title, Author, Genre, Quantity) VALUES
('9780435905484', 'A Grain of Wheat', 'Ngũgĩ wa Thiong\'o', 'Historical Fiction, Political Fiction', 15),
('9789966463645', 'The River Between', 'Ngũgĩ wa Thiong\'o', 'Historical Fiction, Cultural', 12),
('9780435908300', 'Petals of Blood', 'Ngũgĩ wa Thiong\'o', 'Political Fiction, African Literature', 10),
('9780852555335', 'One Day I Will Write About This Place', 'Binyavanga Wainaina', 'Memoir, Non-Fiction', 8),
('9789966463607', 'The Promised Land', 'Grace Ogot', 'Fiction, African Literature', 10),
('9789966463492', 'Land Without Thunder', 'Grace Ogot', 'Short Stories, Fiction', 6),
('9780435902483', 'Going Down River Road', 'Meja Mwangi', 'Urban Fiction, African Literature', 14),
('9780435908225', 'The Cockroach Dance', 'Meja Mwangi', 'Fiction, Satire', 10),
('9789966463263', 'Kill Me Quick', 'Meja Mwangi', 'Fiction, African Literature', 9),
('9780385515995', 'Dust', 'Yvonne Adhiambo Owuor', 'Historical Fiction, African Literature', 11);

CREATE TABLE Members (
    MemberID INT PRIMARY KEY AUTO_INCREMENT, -- Unique identifier for members
    Name VARCHAR(255) NOT NULL,              -- Member name
    Email VARCHAR(255) NOT NULL,             -- Member email
    Phone VARCHAR(15)                        -- Member phone number
);
-- Insert values into member's table
INSERT INTO Members VALUES(1, 'John Doe', 'johndoe@example.com', '+1234567890');
INSERT INTO Members VALUES(2, 'Jane Smith', 'janesmith@domain.com', '+9876543210');
INSERT INTO Members VALUES(3, 'Mary Johnson', 'mary.johnson@webmail.com', '+1122334455');
INSERT INTO Members VALUES(4, 'Michael Brown', 'michaelbrown@company.com', '+5566778899');
INSERT INTO Members VALUES(5, 'Eunia Awuor', 'euniawuor2019@outlook.com', '+254700671346');
INSERT INTO Members VALUES(6, 'David Lee', 'davidlee@network.net', '+441234567890');
INSERT INTO Members VALUES(7, 'Emma Wilson', 'emma.wilson@mailservice.com', '+621234567890');
INSERT INTO Members VALUES(8, 'Liam Patel', 'liam.patel@techworld.com', '+341234567890');
INSERT INTO Members VALUES(9, 'Sophia Kim', 'sophia.kim@myemail.org', '+821234567890');
INSERT INTO Members VALUES(10, 'James Garcia', 'james.garcia@bizmail.com', '+501234567890');
INSERT INTO Members VALUES(11, 'Isabella Davis', 'isabelladavis@mailnet.com', '+301234567890');
INSERT INTO Members VALUES(12, 'Mason Martin', 'mason.martin@postmail.org', '+721234567890');
INSERT INTO Members VALUES(13, 'Olivia Lee', 'olivialee@school.edu', '+251234567890');
INSERT INTO Members VALUES(14, 'Ethan Young', 'ethanyoung@corporate.com', '+661234567890');
INSERT INTO Members VALUES(15, 'Ava Hernandez', 'avahernandez@servicemail.com', '+551234567890');
INSERT INTO Members VALUES(16, 'Lucas Clark', 'lucas.clark@live.com', '+841234567890');
INSERT INTO Members VALUES(17, 'Mia Thompson', 'mia.thompson@mailbox.com', '+371234567890');
INSERT INTO Members VALUES(18, 'Noah Martinez', 'noah.martinez@office.com', '+881234567890');
INSERT INTO Members VALUES(19, 'Amelia Taylor', 'amelia.taylor@personal.org', '+961234567890');
INSERT INTO Members VALUES(20, 'Alexander Anderson', 'alexander.anderson@university.edu', '+411234567890');

CREATE TABLE Loans (
    LoanID INT PRIMARY KEY AUTO_INCREMENT, -- Unique identifier for each loan
    MemberID INT,                          -- Foreign key referencing Members
    ISBN VARCHAR(13),                      -- Foreign key referencing Books
    LoanDate DATE,                         -- Date when the book was borrowed
    ReturnDate DATE,                       -- Date when the book was returned
    FOREIGN KEY (MemberID) REFERENCES Members(MemberID),
    FOREIGN KEY (ISBN) REFERENCES Books(ISBN)
);
-- Insert values into Loans table
INSERT INTO Loans (MemberID, ISBN, LoanDate, ReturnDate) VALUES
(1, '9780435905484', '2024-01-05', '2024-01-12'),
(2, '9789966463645', '2024-01-08', NULL),  -- Not returned yet
(3, '9780435908300', '2024-01-09', '2024-01-15'),
(4, '9780435905484', '2024-01-10', NULL),  -- Not returned yet
(5, '9789966463607', '2024-01-12', '2024-01-19'),
(6, '9789966463492', '2024-01-15', NULL),  -- Not returned yet
(7, '9780435905484', '2024-01-17', '2024-01-23'),
(8, '9780435908225', '2024-01-18', NULL),  -- Not returned yet
(9, '9789966463263', '2024-01-20', '2024-01-25'),
(10, '9780435905484', '2024-01-22', NULL); -- Not returned yet

CREATE TABLE Authors(
    AuthorID INT PRIMARY KEY AUTO_INCREMENT, -- Unique identifier for each author
    Name VARCHAR(255),                       -- Author's name
    Nationality VARCHAR(100),                -- Author's nationality
    BirthYear YEAR                           -- Author's year of birth
    
);
-- Insert values into Author's table
INSERT INTO Authors (AuthorID, Name, Nationality, BirthYear) VALUES
(1, 'Ngũgĩ wa Thiong\'o', 'Kenyan', 1938),
(2, 'Binyavanga Wainaina', 'Kenyan', 1971),
(3, 'Grace Ogot', 'Kenyan', 1930),
(4, 'Meja Mwangi', 'Kenyan', 1948),
(5, 'Yvonne Adhiambo Owuor', 'Kenyan', 1968);

-- Retrieve all books written by a specific author.
SELECT * FROM Books WHERE Author = 'Ngũgĩ wa Thiong\'o';
-- Drop the newly created Authors table.
DROP TABLE Authors;

-- Identify the names of all members who have borrowed a specific book.
SELECT M.Name
FROM Loans L
JOIN Members M ON L.MemberID = M.MemberID
WHERE L.ISBN = '9780435905484';  

-- Write an SQL query to alter the "Members" table to include this new attribute.
ALTER TABLE Members
ADD COLUMN MembershipType ENUM('Regular', 'Premium') NOT NULL DEFAULT 'Regular';
SHOW COLUMNS FROM Members; -- Show Member's tables to confirm the added column.
