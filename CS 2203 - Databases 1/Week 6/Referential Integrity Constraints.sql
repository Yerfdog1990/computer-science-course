-- Create a department table
CREATE TABLE Department (
    department_id INT PRIMARY KEY,
    department_name VARCHAR(255) NOT NULL
);

-- Create an employee table with foreign key
CREATE TABLE Employee (
    employee_id INT PRIMARY KEY,
    last_name VARCHAR(255) NOT NULL,
    department_id INT,
    salary DECIMAL(10, 2),
    CONSTRAINT fk_department
        FOREIGN KEY (department_id)
        REFERENCES Department(department_id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);

-- Insert values into Department table
INSERT INTO Department (department_id, department_name) VALUES
(1, 'HR'),
(2, 'Finance'),
(3, 'IT');

-- Display department table
SELECT * FROM Department;

-- Insert values into Employee table
INSERT INTO Employee (employee_id, last_name, department_id, salary) VALUES
(101, 'Smith', 1, 5000.00),
(102, 'Johnson', 2, 6000.00),
(103, 'Williams', 3, 5500.00);

-- Display Employee table
SELECT * FROM Employee;

-- Testing Referential Integrity
-- 1. Cascading Delete: If you delete a department, all employees in that department will also be deleted.
DELETE FROM Department WHERE department_id = 1;
-- This will delete the department with ID 1 and any employees associated with it.

-- Display department table after cascading delete
SELECT * FROM Department;
-- Display Employee table after cascading delete 
SELECT * FROM Employee;

-- 2. Cascading Update: If you update the department_id in the Department table, it will be reflected in the Employee table as well.
UPDATE Department SET department_id = 4 WHERE department_id = 2;
-- This will update the department_id in both Department and Employee tables.

-- Display department table after cascading update 
SELECT * FROM Department;
-- Display Employee table after cascading update 
SELECT * FROM Employee;
