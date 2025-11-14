-- Creating a table with Primary Key and NOT NULL Constraints
CREATE TABLE Employee (
    employee_id INT PRIMARY KEY,    -- Ensures employee_id is unique and non-null (entity integrity)
    first_name VARCHAR(255) NOT NULL, -- Ensures first_name cannot be NULL
    last_name VARCHAR(255) NOT NULL,  -- Ensures last_name cannot be NULL
    email VARCHAR(255) UNIQUE,     -- Ensures email is unique but allows NULL values
    department_id INT,
    salary DECIMAL(10, 2)
);

-- Inserting values into the Employee table
INSERT INTO Employee (employee_id, first_name, last_name, email, department_id, salary) VALUES
(1, 'John', 'Smith', 'john.smith@example.com', 10, 6000.00),
(2, 'Jane', 'Doe', 'jane.doe@example.com', 20, 7000.00),
(3, 'Mike', 'Johnson', 'mike.j@example.com', 10, 6500.00);

-- Attempting to insert a duplicate employee_id will throw an error due to the PRIMARY KEY constraint
-- INSERT INTO Employee (employee_id, first_name, last_name, email, department_id, salary)
-- VALUES (1, 'Sam', 'Brown', 'sam.brown@example.com', 30, 5800.00);

-- Inserting a duplicate email would also fail due to the UNIQUE constraint on the email column
-- INSERT INTO Employee (employee_id, first_name, last_name, email, department_id, salary)
-- VALUES (4, 'Sam', 'Brown', 'john.smith@example.com', 30, 5800.00);

-- Create view
    -- Syntax:
    -- CREATE VIEW view_name AS
    -- SELECT columns
    -- FROM table
    -- WHERE condition;

CREATE VIEW HighSalaryEmployees AS
SELECT employee_id, last_name, department_id, salary
FROM Employee
WHERE salary > 6000;

-- Query view
SELECT * FROM HighSalaryEmployees;
