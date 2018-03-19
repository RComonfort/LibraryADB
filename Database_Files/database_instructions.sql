DROP TABLE IF EXISTS clients;
CREATE TABLE clients(
	clientID SERIAL,
	name VARCHAR (150) NOT NULL,
	telephone VARCHAR (16),
	address VARCHAR(100),
	email VARCHAR(30) NOT NULL,
	debt NUMERIC(10, 2) NOT NULL,
	PRIMARY KEY (clientID)
);

DROP TABLE IF EXISTS librarians;
CREATE TABLE librarians(
	librarianID SERIAL,
	name VARCHAR (150) NOT NULL,
	telephone VARCHAR (16),
	address VARCHAR(100),
	shift_begin TIME NOT NULL,
	shift_end TIME NOT NULL,
	PRIMARY KEY (librarianID)
);

DROP TABLE IF EXISTS loans;
CREATE TABLE loans(
	loanID SERIAL,
	clientID INT REFERENCES clients(clientID) NOT NULL,
	loan_date DATE NOT NULL,
	return_date DATE,
	librarianID INT REFERENCES librarians(librarianID) NOT NULL,
	PRIMARY KEY (loanID)
);

DROP TABLE IF EXISTS fines;
CREATE TABLE fines(
	fineID SERIAL,
	loanID INT REFERENCES loans(loanID) NOT NULL,
	total_amount NUMERIC(10, 2) NOT NULL, 
	PRIMARY KEY (fineID)
);

DROP TABLE IF EXISTS breturns;
CREATE TABLE breturns(
	returnID SERIAL,
	loanID INT REFERENCES loans(loanID) NOT NULL,
	actual_return_date DATE NOT NULL,
	fineID INT REFERENCES fines(fineID),
	PRIMARY KEY (returnID)
);


DROP TYPE IF EXISTS country;
CREATE TYPE country AS ENUM (
  'AUT', --Austria
  'GBR', --Great Britain
  'JPN', --JAPAN  
  'USA', --United States
  'UKWN' --Unkwown nationality
  );

DROP TABLE IF EXISTS editorials;
CREATE TABLE editorials(
	editorialID SERIAL,
	name VARCHAR(30) NOT NULL,
	nationality country NOT NULL,
	PRIMARY KEY (editorialID)
);


DROP TABLE IF EXISTS authors;
CREATE TABLE authors(
	authorID SERIAL,
	name VARCHAR(150) NOT NULL,
	nationality country NOT NULL,
	PRIMARY KEY (authorID)
);

DROP TYPE IF EXISTS lan;
CREATE TYPE lan AS ENUM (
  'En', 
  'Es', 
  'Fr');

DROP TABLE IF EXISTS books;
CREATE TABLE books(
	bookID SERIAL,
	title VARCHAR(150) NOT NULL,
	editorialID INT REFERENCES editorials(editorialID),
	edition INT NOT NULL,
	translator VARCHAR(150),
	language lan NOT NULL, 
	daily_fine_amount NUMERIC(10,2) NOT NULL,
	stock INT NOT NULL,
	pages INT,
	publishing_date DATE,
	PRIMARY KEY (bookID)
);

DROP TABLE IF EXISTS authors_books;
CREATE TABLE authors_books(
	authorID INT REFERENCES authors(authorID),
	bookID INT REFERENCES books(bookID),
	PRIMARY KEY (authorID, bookID)
);

DROP TABLE IF EXISTS books_loans;
CREATE TABLE books_loans(
	bookID INT REFERENCES books(bookID),
	loanID INT REFERENCES loans(loanID),
	PRIMARY KEY (bookID, loanID)
);

######################################################
#			          	Insertions					  #
######################################################

INSERT INTO editorials (name, nationality) VALUES ('Oxford University Press', 'GBR'), ('Bantam Spectra', 'USA'), ('DAW Books', 'USA'), ('Ballantine Books', 'USA');

INSERT INTO books (title, editorialID, edition, translator, language, daily_fine_amount, stock, pages, publishing_date) VALUES ('A Pattern Language', 1, 1, NULL, 'En', 34.99, 3, 1171, '1977-01-01'),('A Game of Thrones', 2, 5, NULL, 'En', 50.00, 15, 694, '1996-08-01'),('A Clash of Kings', 2, 5, NULL, 'En', 55.00, 17, 768, '1998-01-01'),('A Storm of Swords', 2, 4, NULL, 'En', 60.00, 20, 973, '2000-01-01'),('A Feast for Crows', 2, 3, NULL, 'En', 40.00, 7, 976, '2005-01-01'), ('A Dance with Dragons', 2, 2, NULL, 'En', 40.00, 7, 1040, '2011-07-12'),('The Name of The Wind', 3, 2, NULL, 'En', 50.00, 15, 662, '2007-03-27'),('Farenheit 451', 4, 10, NULL, 'En', 55.50, 20, 358, '1953-01-01');

INSERT INTO authors (name, nationality) VALUES('Christopher Alexander', 'AUT'), ('Sara Ishikawa', 'JPN'),('Murray Silverstein', 'USA'),('George R. R. Martin', 'USA'),('Patrick Rothfuss', 'USA'),('Ray Bradburry', 'USA');

INSERT INTO authors_books (authorID, bookID) VALUES(1, 1),(2, 1),(3, 1),(4, 2),(4, 3),(4, 4),(4, 5),(4, 6),(5, 7),(6, 8);

INSERT INTO clients (name, telephone, address, email, debt) VALUES ('Angel Bob Menendez', '2227564899', 'Avenida Siempre Viva #2000', 'mrbob@gmail.com', 4321.00), ('Carlos McLovin Manilla', '2224564788', 'Blvd. Gutenberg, #4 Int. 20', 'mclov@domains.com', 0.00);

INSERT INTO librarians (name, telephone, address, shift_begin, shift_end) VALUES ('Pepe Toño Pantufla', '22245453467', 'Evergreen Terrace Road 48', '07:00:00', '20:00:00'), ('Daniel P. Irri', '22245453667', NULL, '20:00:01', '06:00:59');

INSERT INTO loans (clientID, loan_date, return_date, librarianID) VALUES (1, '2018-03-01', '2018-03-08', 1), (2, '2018-02-18', '2018-02-25', 2), (1, '2018-03-01', '2018-03-08', 1);

INSERT INTO books_loans (bookID, loanID) VALUES (2, 1), (3, 1), (4, 1), (2, 2), (3, 2), (4, 2), (5, 2), (6, 2), (8,3);

INSERT INTO fines ( loanID, total_amount) VALUES (2, 735.00);

INSERT INTO breturns (loanID, actual_return_date, fineID) VALUES (2, '2018-02-28', 1);

######################################################
#			          	STORED PROGRAMS				 #
######################################################

####### FUNCTIONS #######

--Adds 7 to the given date, which is the default amount of days that any book is borrowed

CREATE OR REPLACE FUNCTION CalculateReturnDate (loan_date DATE)
	RETURNS DATE AS $return_date$
	DECLARE
		return_date DATE;
	BEGIN
		SELECT loan_date + INTEGER '7' INTO return_date;
		RETURN return_date;
	END;
$return_date$ LANGUAGE plpgsql;
	

CREATE OR REPLACE FUNCTION FinePerBook(bookI INT, returnDate DATE)
    RETURNS NUMERIC(10,2) AS $finePerBook$
    DECLARE
        finePerBook NUMERIC(10,2);
        bookFine NUMERIC(10,2);
    BEGIN
        SELECT books.daily_fine_amount INTO bookFine FROM books WHERE books.bookID = bookI;
        finePerBook:= bookFine * (CURRENT_DATE - returnDate);
        RETURN  finePerBook;
    END;
$finePerBook$ LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION CalculateTotalFine (lID INT)
RETURNS NUMERIC (10, 2) AS $total$ 
DECLARE 
	total NUMERIC (10, 2);
	originalReturnDate DATE;
BEGIN
	SELECT return_date INTO originalReturnDate  
	FROM loans l WHERE l.loanID = lID;

	SELECT SUM(FinePerBook(bl.bookID, originalReturnDate)) INTO total FROM books_loans bl WHERE bl.loanID = lID;

	RETURN total;
END;
$total$ LANGUAGE plpgsql;

####### TRIGGERS  #######

CREATE TRIGGER  after_insert_breturns  AFTER INSERT ON breturns FOR EACH ROW
EXECUTE PROCEDURE DoFine();

####### STORED PROCEDURES  #######

CREATE OR REPLACE FUNCTION DoFine()
    RETURNS TRIGGER AS $after_insert_breturns$
    DECLARE
        return_date_loan DATE;
        new_fineID INT;
    BEGIN
        SELECT loans.return_date INTO return_date_loan FROM loans WHERE loans.loanID = NEW.loanID;
		UPDATE books SET stock = (stock + 1) WHERE bookID IN (SELECT bl.bookID FROM books_loans bl INNER JOIN loans l ON bl.loanID=l.loanID WHERE l.loanID=NEW.loanID);
        
		IF return_date_loan < NEW.actual_return_date THEN 
            SELECT CreateFine(NEW.loanID) INTO new_fineID;
            NEW.fineID:=new_fineID;
        ELSE 
            NEW.fineID:=NULL;
        END IF;

		DELETE FROM books_loans WHERE books_loans.loanID=NEW.loanID;

        RETURN NEW;
    END;
$after_insert_breturns$ LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION CreateFine(loID INT)
    RETURNS INT AS $fID$
    DECLARE
        fID INT;
        amount NUMERIC(10,2);
    BEGIN
        SELECT CalculateTotalFine(loID) INTO amount;
        INSERT INTO fines (loanID, total_amount) VALUES (loID, amount) RETURNING fineID INTO fID;
        RETURN fID;    
    END;
$fID$ LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION DeleteLoan(loID INT)
    RETURNS VOID AS $$
    BEGIN
		DELETE FROM breturns WHERE loanID = loID;
		DELETE FROM fines WHERE loanID = loID;
        DELETE FROM books_loans WHERE loanID = loID;
		DELETE FROM loans WHERE loanID = loID;
    END;
$$ LANGUAGE plpgsql;

####### Transaction  #######

CREATE OR REPLACE FUNCTION LoanBook(loID INT, boID INT)
    RETURNS VOID AS $$
    DECLARE
        book_stock INT;
    BEGIN
    	LOCK TABLE books IN ACCESS EXCLUSIVE MODE;
        SELECT books.stock INTO book_stock
        FROM books
        WHERE books.bookID = boID;
        IF book_stock > 0 THEN
        	INSERT INTO books_loans(bookID, loanID) VALUES (boID, loID);
        	UPDATE books SET stock = (stock - 1) WHERE books.bookID = boID;
        ELSE
        	ROLLBACK;
        END IF;
    END;
$$ LANGUAGE plpgsql;


####### VIEWS  #######

CREATE VIEW allBooks AS
SELECT *
FROM books;


####### QUERIES  #######

SELECT b.title, a.name AS Author_Name
FROM authors a INNER JOIN (books b INNER JOIN authors_books ab ON b.bookID=ab.bookID) ON a.authorID=ab.authorID;

SELECT name, address, telephone
FROM clients
UNION
SELECT name, address, telephone
FROM librarians;

SELECT * FROM loans l WHERE l.loanID NOT IN (SELECT br.loanID FROM breturns br);

SELECT br2.returnID, br2.loanID, br2.fineID, l2.loan_date, br2.actual_return_date as return_date FROM breturns br2 INNER JOIN (SELECT * FROM loans l WHERE l.loanID IN (SELECT br.loanID FROM breturns br)) AS l2 ON br2.loanID = l2.loanID;

####### DROP TABLES  #######

DROP TABLE IF EXISTS books_loans;
DROP TABLE IF EXISTS authors_books;
DROP TABLE IF EXISTS books;
DROP TYPE IF EXISTS lan;
DROP TABLE IF EXISTS authors;
DROP TABLE IF EXISTS editorials;
DROP TYPE IF EXISTS country;
DROP TABLE IF EXISTS breturns;
DROP TABLE IF EXISTS fines;
DROP TABLE IF EXISTS loans;
DROP TABLE IF EXISTS librarians;
DROP TABLE IF EXISTS clients;

####### TRUNCATE TABLES  #######


TRUNCATE TABLE editorials;
TRUNCATE TABLE books;
TRUNCATE TABLE authors;
TRUNCATE TABLE authors_books;
TRUNCATE TABLE clients;
TRUNCATE TABLE librarians;
TRUNCATE TABLE loans;
TRUNCATE TABLE books_loans;
TRUNCATE TABLE fines;
TRUNCATE TABLE breturns;