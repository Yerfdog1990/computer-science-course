# Graded Quiz Unit 6 — Q&A

**Question 1 (1 point)**
Branch and Bound does not use bounding functions to prune the search space. Is this statement true or false?

- **False** ✅ (Correct)
- True

**Explanation:** Branch and Bound does use bounding functions to prune the search space — that's precisely what lets it discard subproblems whose bound can't beat the current best solution.

---

**Question 2 (1 point)**
The constraints in a Linear Programming problem are always expressed as:

- **Linear inequalities or equalities** ✅ (Correct)
- Quadratic equations
- Randomly generated values
- Non-linear functions

**Explanation:** LP constraints are, by definition, linear — expressed as inequalities (≤, ≥) or equalities among the decision variables.

---

**Question 3 (1 point)**
What is a classic application of Linear Programming?

- **Resource allocation problems** ✅ (Correct)
- Sorting algorithms
- Graph coloring
- String matching

**Explanation:** LP is widely used to allocate limited resources (budget, time, materials, etc.) optimally among competing activities.

---

**Question 4 (1 point)**
In Linear Programming, the constraints are typically formulated as:

- **Linear inequalities or equations** ✅ (Correct)
- Exponential equations
- Recursive equations
- Boolean expressions

**Explanation:** Same underlying concept as Question 2 — everything in an LP formulation, including the constraints, must be linear.

---

**Question 5 (1 point)**
What is the primary goal of Linear Programming?

- **Maximizing or minimizing a linear function** ✅ (Correct)
- Finding any feasible solution
- Sorting variables in a specific order
- Minimizing the number of constraints

**Explanation:** LP's objective is always to optimize (maximize or minimize) a linear objective function subject to the linear constraints.

---

**Question 6 (1 point)**
What is the space complexity of the Floyd-Warshall algorithm?

- O(n³)
- **O(n²)** ✅ (Correct)
- O(n log n)
- O(1)

**Explanation:** Floyd-Warshall maintains a distance matrix of size n×n to track shortest distances between every pair of vertices.

---

**Question 7 (1 point)**
What is the goal of the Traveling Salesperson Problem (TSP)?

- **Find the shortest tour that visits all cities exactly once and returns to the starting city** ✅ (Correct)
- Find the longest path in a graph
- Minimize the number of edges in a graph
- Find a spanning tree of minimum weight

**Explanation:** This is the classic definition of TSP — finding the minimum-cost Hamiltonian cycle through all the cities.

---

**Question 8 (1 point)**
In the Floyd-Warshall algorithm, what does the intermediate node k represent?

- **A potential node through which the shortest path may pass** ✅ (Correct)
- The starting node of the graph
- The node with the highest degree
- A node to be removed from the graph

**Explanation:** At each iteration, node k is used to check whether routing through it (i→k→j) produces a shorter path than the current known path i→j.

---

**Question 9 (1 point)**
What is the distance from a node to itself in a distance matrix?

- **Zero** ✅ (Correct)
- Infinity
- One
- Undefined

**Explanation:** By definition, the shortest path from any vertex to itself has length zero (no edges need to be traversed).

---

**Question 10 (1 point)**
What is the fundamental principle of Dynamic Programming?

- **Solving problems by breaking them into overlapping subproblems** ✅ (Correct)
- Solving problems using purely random search
- Solving problems using only a greedy strategy
- Solving problems by brute force without memoization

**Explanation:** Dynamic programming relies on optimal substructure combined with overlapping subproblems — solving each subproblem once and reusing the result.

---

**Question 11 (1 point)**
How does the N-Queens backtracking algorithm avoid row conflicts?

- **Queens are placed one row at a time.** ✅ (Correct)
- Queens are placed one column at a time.
- Queens are placed randomly on the board.
- Queens are placed only on even rows.

**Explanation:** Since exactly one queen is placed per row, no two queens can ever end up sharing a row, so row conflicts are automatically avoided.

---

**Question 12 (1 point)**
What happens when no valid column is found for a queen in the current row?

- **It backtracks to the previous queen's position and tries another column.** ✅ (Correct)
- It terminates the algorithm immediately.
- It skips the row and moves to the next one.
- It restarts the search from row one.

**Explanation:** The algorithm backtracks to the previous row and attempts the next available column there, rather than getting stuck.

---

**Question 13 (1 point)**
What kind of technique is backtracking?

- Breadth-first searching
- Dynamic programming
- **Top-down recursive** ✅ (Correct)
- Bottom-up iterative

**Explanation:** Backtracking is a systematic trial-and-error approach that uses recursion to explore the solution space, abandoning paths that can't lead to a valid solution.

---

**Question 14 (1 point)**
Backtracking systematically explores possible solutions. Is this true or false?

- **True** ✅ (Correct)
- False

**Explanation:** Backtracking builds candidate solutions incrementally and systematically, backing out of any path as soon as it's known to be invalid.

---

**Question 15 (1 point)**
What is the worst-case time complexity of the N-Queens problem using backtracking?

- **O(n!)** ✅ (Correct)
- O(n²)
- O(2^n)
- O(n log n)

**Explanation:** In the worst case, the backtracking search explores on the order of n! configurations of queen placements before finding all valid solutions (or determining none exist).