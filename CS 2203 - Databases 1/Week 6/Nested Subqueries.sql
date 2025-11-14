-- Create an employee table
CREATE TABLE Employee(
    employee_id INT PRIMARY KEY, 
    job_id INT NOT NULL, 
    last_name VARCHAR(255) NOT NULL, 
    department_id INT NOT NULL, 
    salary INT NOT NULL,
    employee_count INT  
);

-- Insert values into employee table
INSERT INTO Employee (employee_id, job_id, last_name, department_id, salary, employee_count) VALUES
(1, 101, 'Smith', 10, 5000, 8),    -- Assuming 8 employees in department 10
(2, 102, 'Johnson', 20, 6000, 3),  -- Assuming 3 employees in department 20
(3, 103, 'Williams', 10, 5500, 6), -- Assuming 6 employees in department 10
(4, 101, 'Brown', 30, 4800, 9),    -- Assuming 9 employees in department 30
(5, 104, 'Jones', 20, 6200, 1),     -- Assuming 4 employees in department 20
(6, 105, 'Garcia', 10, 7000, 6),   -- Assuming 6 employees in department 10
(7, 106, 'Miller', 30, 5800, 2);    -- Assuming 2 employees in department 30

-- Create job history table
CREATE TABLE Job_History(
    employee_id INT PRIMARY KEY, 
    job_id INT NOT NULL, 
    start_date DATE NOT NULL, 
    end_date DATE
);

-- Insert values into job history table
INSERT INTO Job_History VALUES
(1, 100, '2018-01-01', '2019-12-31'),
(2, 101, '2019-01-01', '2020-12-31'),
(3, 102, '2017-01-01', '2018-12-31'),
(4, 101, '2016-01-01', '2017-12-31'),
(6, 103, '2019-01-01', '2021-12-31'),
(7, 106, '2020-01-01', NULL);  -- NULL end date means the job is ongoing
-- 1. Subquery in the SELECT Clause - Used in the SELECT clause to compute a value for each row returned by the outer query.
-- Syntax:
    -- SELECT column1, column2, (SELECT expression FROM table WHERE condition) AS alias_name
    -- FROM main_table;
-- Example: Suppose we want to retrieve employee names along with their average salary in their respective departments.
SELECT 
    e.last_name, 
    (SELECT AVG(salary) 
     FROM Employee 
     WHERE department_id = e.department_id) AS avg_salary
FROM 
    Employee e;

-- 2. Subquery in the WHERE Clause - Used in the WHERE clause to filter results based on a condition.
-- Syntax: 
    -- SELECT column1, column2
    -- FROM main_table
    -- WHERE column1 = (SELECT expression FROM table WHERE condition);
-- Example: Retrieve the names of employees whose salaries are above the average salary in the company.
SELECT last_name 
FROM Employee 
WHERE salary > (SELECT AVG(salary) FROM Employee);

-- 3. Subquery in the FROM Clause - Used in the FROM clause as a derived table.
-- Syntax:
    -- SELECT column1, column2
    -- FROM (SELECT expression FROM table WHERE condition) AS alias_name
    -- WHERE outer_condition;
-- Example: Get the count of employees in each department, showing only departments with more than five employees.
SELECT department_id, employee_count
FROM (
    SELECT department_id, SUM(employee_count) AS employee_count
    FROM Employee
    GROUP BY department_id
) AS dept_count
WHERE employee_count > 5;

-- 4. Correlated Subquery in the WHERE Clause - A correlated subquery references a column from the outer query. It executes once for each row processed by the outer query.
-- Syntax:
    -- SELECT column1, column2
    -- FROM main_table
    -- WHERE column1 = (SELECT expression FROM table WHERE main_table.column_x = table.column_y);
-- Example: Find employees whose salaries are higher than the average salary in their respective departments.
SELECT last_name 
FROM Employee e 
WHERE salary > (
    SELECT AVG(salary) 
    FROM Employee 
    WHERE department_id = e.department_id
);

-- 5. Subquery in the HAVING Clause - A subquery can also be used in the HAVING clause to filter groups based on aggregate conditions.
    -- SELECT column1, aggregate_function(column2)
    -- FROM main_table
    -- GROUP BY column1
    -- HAVING aggregate_function(column2) > (SELECT expression FROM table WHERE condition);
-- Example: Get departments with an average salary greater than $60,000.
SELECT department_id, AVG(salary) AS avg_salary
FROM Employee
GROUP BY department_id
HAVING avg_salary > (
    SELECT AVG(salary) 
    FROM Employee
);

-- Nested Subquery in the EXISTS Clause - Used for conditional data retrieval where the relationship between tables needs to be validated.
-- Syntax:
    -- SELECT column1, column2
    -- FROM main_table
    -- WHERE EXISTS (SELECT expression FROM table WHERE condition);
-- Example: Get the last names of employees who have a job history recorded in the Job_History table
SELECT last_name 
FROM Employee e
WHERE EXISTS (
    SELECT * 
    FROM Job_History jh 
    WHERE jh.employee_id = e.employee_id
);
