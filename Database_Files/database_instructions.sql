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
	librarianID INT REFERENCES clients(cliID) NOT NULL,
	PRIMARY KEY (loanID)
);

DROP TABLE IF EXISTS fines;
CREATE TABLE fines(
	fineID SERIAL,
	loanID INT REFERENCES loans(loanID) NOT NULL,
	total_amount NUMERIC(10, 2) NOT NULL, 
	PRIMARY KEY (fineID)
);

DROP TABLE IF EXISTS returns;
CREATE TABLE breturns(
	returnID SERIAL,
	loanID INT REFERENCES loans(loanID) NOT NULL,
	actual_return_date DATE NOT NULL,
	fineID INT REFERENCES finess(fineID),
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

INSERT INTO editorials (name, countryID) VALUES ('Oxford University Press', 'GBR'), ('Bantam Spectra', 'USA'), ('DAW Books', 'USA'), ('Ballantine Books', 'USA');

INSERT INTO books (title, editorialID, edition, translator, language, daily_fine_amount, stock, pages, publishing_date) VALUES ('A Pattern Language', 1, 1, NULL, 'En', 34.99, 3, 1171, '1977-01-01'),('A Game of Thrones', 2, 5, NULL, 'En', 50.00, 15, 694, '1996-08-01'),('A Clash of Kings', 2, 5, NULL, 'En', 55.00, 17, 768, '1998-01-01'),('A Storm of Swords', 2, 4, NULL, 'En', 60.00, 20, 973, '2000-01-01'),('A Feast for Crows', 2, 3, NULL, 'En', 40.00, 7, 976, '2005-01-01'), ('A Dance with Dragons', 2, 2, NULL, 'En', 40.00, 7, 1040, '2011-07-12'),('The Name of The Wind', 3, 2, NULL, 'En', 50.00, 15, 662, '2007-03-27'),('Farenheit 451', 4, 10, NULL, 'En', 55.50, 20, 358, '1953-01-01');

INSERT INTO authors (name, nationality) VALUES('Christopher Alexander', 'AUT'), ('Sara Ishikawa', 'JPN'),('Murray Silverstein', 'USA'),('George R. R. Martin', 'USA'),('Patrick Rothfuss', 'USA'),('Ray Bradburry', 'USA');

INSERT INTO authors_books (authorID, bookID) VALUES(1, 1),(2, 1),(3, 1),(4, 2),(4, 3),(4, 4),(4, 5),(4, 6),(5, 7),(6, 8);

INSERT INTO clients (name, telephone, address, email, debt) VALUES ('Angel Bob Menendez', '2227564899', 'Avenida Siempre Viva #2000', 'mrbob@gmail.com', 4321.00), ('Carlos McLovin Manilla', '2224564788', 'Blvd. Gutenberg, #4 Int. 20', 'mclov@domains.com', 0.00);

INSERT INTO librarians (name, telephone, address, shift_begin, shift_end) VALUES ('Pepe Toño Pantufla', '22245453467', 'Evergreen Terrace Road 48', '07:00:00', '20:00:00'), ('Daniel P. Irri', '22245453667', NULL, '20:00:01', '06:00:59');

INSERT INTO loans (clientID, loan_date, return_date, librarianID) VALUES (1, '2018-03-01', '2018-03-08', 1), (2, '2018-02-18', '2018-02-25', 2);

INSERT INTO books_loans (bookID, loanID) VALUES (2, 1), (3, 1), (4, 1), (2, 2), (3, 2), (4, 2), (5, 2), (6, 2);

INSERT INTO fines ( loandID, totalAmount) VALUES (2, 735.00);

INSERT INTO breturns (loanID, actual_return_date, fineID) VALUES (2, '2018-02-28', 1);



######################################################
#			          	STORED PROGRAMS				 #
######################################################

####### FUNCTIONS #######

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
$finePerBook$ LANGUAGE plpsql;

CREATE OR REPLACE FUNCTION CalculateTotalFine (INT loanID)
RETURNS NUMERIC (10, 2) AS $total$ 
DECLARE 
	total NUMERIC (10, 2);
	originalReturnDate DATE;
BEGIN
	SELECT return_date INTO originalReturnDate,  
	FROM loans l WHERE l.loanID = loanID;

	SELECT SUM(FinePerBook(b.bookID, originalReturnDate)) INTO total FROM books b 
	WHERE books b INNER JOIN books_loans bl ON bl.loanID = loanID;

	RETURN total;
END;
$total$ LANGUAGE plpgsql;

####### TRIGGERS  #######

CREATE OR REPLACE TRIGGER  after_insert_breturns  AFTER INSERT ON breturns FOR EACH ROW
EXECUTE PROCEDURE DoFine();

####### STORED PROCEDURES  #######

CREATE OR REPLACE FUNCTION DoFine()
    RETURNS TRIGGER AS $after_insert_breturns$
    DECLARE
        return_date_loan DATE;
        new_fineID INT;
    BEGIN
        SELECT loans.return_date INTO return_date_loan
        FROM loans
        WHERE loans.loanID = NEW.loanID;
        IF return_date_loan > NEW.actual_return_date THEN 
                SELECT CreateFine(NEW.loanID) INTO new_fineID;
                NEW.fineID:=new_fineID;
        ELSE 
            NEW.fineID:=NULL;
        END IF;
    END;
$finePerBook$ LANGUAGE plpsql;

CREATE OR REPLACE FUNCTION CreateFine(loID INT)
    RETURNS INT AS $fID$
    DECLARE
        fID INT;
        amount NUMERIC(10,2);
    BEGIN
        SELECT GetFineAmount(loanID) INTO amount;
        INSERT INTO fines (loanID, total_amount) VALUES (loID, amount) RETURNING fineID INTO fID;
        RETURN fID;    
    END;
$fID$ LANGUAGE plpsql;

