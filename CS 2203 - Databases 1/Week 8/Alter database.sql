-- create a table
CREATE TABLE Employee (
    Emp_Id INT,
    Emp_Name VARCHAR(50),
    Emp_Salary DECIMAL(10, 2),
    Emp_City VARCHAR(50)
);

-- insert some values
INSERT INTO Employee (Emp_Id, Emp_Name, Emp_Salary, Emp_City) VALUES 
    (201, 'Abhay', 25000, 'Goa'),
    (202, 'Ankit', 45000, 'Delhi'),
    (203, 'Bheem', 30000, 'Goa'),
    (204, 'Ram', 29000, 'Goa'),
    (205, 'Sumit', 40000, 'Delhi');

-- Adding Columns with ALTER TABLE
ALTER TABLE Employee ADD Next_Kin VARCHAR(10); -- Single Column Addition
ALTER TABLE Employee ADD (age INT, Email_ID VARCHAR(30)); -- Multiple Columns Addition

-- Modifying Columns with ALTER TABLE
ALTER TABLE Employee MODIFY Emp_City VARCHAR(60); -- Single Column Modification
ALTER TABLE Employee MODIFY Next_Kin VARCHAR(50);
ALTER TABLE Employee MODIFY Emp_Salary DECIMAL(12, 2); -- MySQL does not support modifying multiple columns in a single MODIFY clause

-- Deleting Columns with ALTER TABLE
ALTER TABLE Employee DROP COLUMN age; -- Single Column Deletion
ALTER TABLE Employee DROP COLUMN Emp_Salary;
ALTER TABLE Employee DROP COLUMN Emp_City; -- Multiple Columns Deletion

-- Renaming Columns with ALTER TABLE
ALTER TABLE Employee RENAME COLUMN Next_Kin TO beneficiary;
-- fetch some values
SELECT * FROM Employee;