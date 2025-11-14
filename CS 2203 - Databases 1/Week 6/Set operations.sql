-- Create an employee table
CREATE TABLE Employee(
    employee_id INT PRIMARY KEY, 
    job_id INT NOT NULL, 
    last_name VARCHAR(255) NOT NULL, 
    department_id INT NOT NULL, 
    salary INT NOT NULL
);
-- Insert values into employee table
INSERT INTO Employee VALUES
(1, 101, 'Smith', 10, 5000),
(2, 102, 'Johnson', 20, 6000),
(3, 103, 'Williams', 10, 5500),
(4, 101, 'Brown', 30, 4800),
(5, 104, 'Jones', 20, 6200),
(6, 105, 'Garcia', 10, 7000),
(7, 106, 'Miller', 30, 5800);
-- Create a job history table
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

-- 1. UNION Operation
SELECT employee_id, job_id 
FROM Employee
UNION -- combines the results of two or more SELECT queries and removes duplicate rows.
SELECT employee_id, job_id 
FROM Job_History;

-- 2. UNION ALL Operation
SELECT employee_id, job_id FROM Employee
UNION ALL -- combines the results of two or more SELECT queries, but it includes all duplicates.
SELECT employee_id, job_id FROM Job_History;

-- 3. INNER JOIN
SELECT e.employee_id, e.job_id 
FROM Employee e
INNER JOIN Job_History jh -- Retrieves only the rows that have matching values in both tables
ON e.employee_id = jh.employee_id 
AND e.job_id = jh.job_id;

-- 4. LEFT JOIN (or LEFT OUTER JOIN)
SELECT e.employee_id, e.job_id 
FROM Employee e
LEFT JOIN Job_History jh  -- Retrieves all rows from the left table (the first table in the query), and the matching rows from the right table.
ON e.employee_id = jh.employee_id 
AND e.job_id = jh.job_id 
WHERE jh.employee_id IS NULL;

-- 5. RIGHT JOIN
SELECT e.employee_id, e.job_id, jh.employee_id AS jh_employee_id, jh.job_id AS jh_job_id
FROM Employee e
RIGHT JOIN Job_History jh -- Retrieves all rows from the right table (the second table in the query), and the matching rows from the left table. If there is no match, the result will contain NULL for columns from the left table.
ON e.employee_id = jh.employee_id 
AND e.job_id = jh.job_id;

-- 6. FULL JOIN -- Retrieves all rows from both the left and right tables. If there is no match, NULL values are used for the missing side. This join shows every row from both tables, regardless of whether there’s a match.
SELECT e.employee_id, e.job_id, jh.employee_id AS jh_employee_id, jh.job_id AS jh_job_id
FROM Employee e
LEFT JOIN Job_History jh ON e.employee_id = jh.employee_id AND e.job_id = jh.job_id
UNION
SELECT e.employee_id, e.job_id, jh.employee_id AS jh_employee_id, jh.job_id AS jh_job_id
FROM Employee e
RIGHT JOIN Job_History jh ON e.employee_id = jh.employee_id AND e.job_id = jh.job_id;


