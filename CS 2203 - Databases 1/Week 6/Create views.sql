-- Create the Employee table
CREATE TABLE Employee (
    employee_id INT PRIMARY KEY,
    job_id INT NOT NULL,
    last_name VARCHAR(255) NOT NULL,
    department_id INT NOT NULL,
    salary INT NOT NULL
);

-- Create the Job_History table
CREATE TABLE Job_History (
    employee_id INT,
    job_id INT NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE,
    PRIMARY KEY (employee_id, job_id),
    FOREIGN KEY (employee_id) REFERENCES Employee(employee_id)
);

-- Insert values into the Employee table
INSERT INTO Employee (employee_id, job_id, last_name, department_id, salary) VALUES
(1, 101, 'Smith', 10, 5000),
(2, 102, 'Johnson', 20, 6000),
(3, 103, 'Williams', 10, 5500),
(4, 101, 'Brown', 30, 4800);

-- Insert values into the Job_History table
INSERT INTO Job_History (employee_id, job_id, start_date, end_date) VALUES
(1, 101, '2023-01-01', '2023-06-30'),
(2, 102, '2023-02-01', NULL),
(3, 103, '2023-03-01', '2023-04-30');

-- Create a View
-- Example: A view that shows the last names of employees along with their job IDs and start dates from the job history
CREATE VIEW EmployeeJobHistory AS
SELECT e.last_name, jh.job_id, jh.start_date
FROM Employee e
JOIN Job_History jh ON e.employee_id = jh.employee_id;

-- Query the View
SELECT * FROM EmployeeJobHistory;

