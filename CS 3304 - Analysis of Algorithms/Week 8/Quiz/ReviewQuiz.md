# Review Quiz — Q&A

**Question 1 (1 point)**
If we use a greedy approach to solve the 0/1 Knapsack Problem the solution obtained will be:

- Impossible to determine
- Always optimal
- **Possibly optimal** ✅ (Correct)
- Always suboptimal

**Explanation:** Unlike the Fractional Knapsack Problem, a greedy approach to 0/1 Knapsack is not guaranteed to find the optimal solution — it may or may not, depending on the specific item weights/values, because items can't be split.

---

**Question 2 (1 point)**
The greedy choice property refers to:

- **Making a choice that seems best at the current moment.** ✅ (Correct)
- Randomly choosing an element.
- Always selecting the largest element.
- Selecting the element with the smallest value.

**Explanation:** The greedy choice property means a locally optimal (best-looking-right-now) choice is made at each step, without reconsidering it later.

---

**Question 3 (1 point)**
How does Dijkstra's algorithm avoid the pitfalls of a purely greedy approach?

- By checking all possible paths.
- **By using a priority queue to intelligently explore paths.** ✅ (Correct)
- By backtracking to find better solutions.
- By employing dynamic programming techniques.

**Explanation:** Dijkstra's algorithm always expands the vertex with the smallest known distance next (via a priority queue/min-heap), which keeps it correct for non-negative weights instead of just greedily grabbing any nearby edge.

---

**Question 4 (1 point)**
Dijkstra's algorithm is often described as greedy but uses a priority queue for optimality with non-negative weights. Is it true or false?

- **True** ✅ (Correct)
- False

**Explanation:** Dijkstra's greedy strategy (always process the closest unvisited vertex) combined with a priority queue guarantees the optimal shortest path as long as edge weights are non-negative.

---

**Question 5 (1 point)**
A greedy algorithm for shortest paths might make locally optimal choices. This means:

- It revisits previous choices if a better path is found.
- It always chooses the globally shortest path.
- **It chooses the shortest edge from the current vertex without considering future implications.** ✅ (Correct)
- It makes choices based on the entire graph.

**Explanation:** A locally optimal choice looks only at what seems best right now (the nearest edge/vertex) rather than evaluating the full graph or future consequences.

---

**Question 6 (1 point)**
Backtracking is a __________ technique.

- Breadth-first searching
- Dynamic programming
- **Top-down recursive** ✅ (Correct)
- Bottom-up iterative

**Explanation:** Backtracking systematically builds a solution incrementally and recursively, abandoning ("backtracking" from) a path as soon as it determines that path cannot lead to a valid solution.

---

**Question 7 (1 point)**
How does the backtracking algorithm start solving the 8 Queens problem?

- **By placing a queen in the first row and first column.** ✅ (Correct)
- By placing queens in all rows simultaneously.
- By randomly placing a queen on the board.
- By checking all columns in the first row.

**Explanation:** The algorithm starts at row 1 and attempts to place a queen in the first column, then recursively moves to the next row, backtracking whenever a placement leads to a conflict.

---

**Question 8 (1 point)**
Which of the following is not typically solved using backtracking?

- 8 Queens Problem
- **Traveling Salesperson Problem** ✅ (Correct — this is NOT typically solved via backtracking)
- Sudoku puzzle
- Sum of Subsets Problem

**Explanation:** 8 Queens, Sudoku, and Sum of Subsets are classic backtracking problems. TSP is instead typically approached with dynamic programming, branch and bound, or approximation algorithms due to its scale.

---

**Question 9 (1 point)**
In the Sum of Subsets problem, if a subset's sum exceeds the target, what happens?

- **The algorithm backtracks to explore other options.** ✅ (Correct)
- The algorithm stores the subset as a partial solution.
- The algorithm terminates.
- The algorithm skips to the next element without backtracking.

**Explanation:** Exceeding the target sum means this branch can't lead to a valid solution, so the algorithm prunes it and backtracks to try a different combination.

---

**Question 10 (1 point)**
What is the stopping condition for recursion in the 8 Queens backtracking algorithm?

- When a valid position is found for a queen.
- When all rows have been checked.
- **When all 8 queens are placed without conflict.** ✅ (Correct)
- When all diagonal positions are invalid.

**Explanation:** The recursion successfully terminates (returns a solution) once all 8 queens have been placed on the board with no two attacking each other.

---

**Question 11 (1 point)**
The Vertex Cover Problem aims to find:

- The longest path in a graph
- The maximum independent set of vertices
- **The smallest set of vertices that cover all edges of a graph** ✅ (Correct)
- The shortest path between two vertices

**Explanation:** A vertex cover is a set of vertices such that every edge in the graph is incident to at least one vertex in the set; the problem seeks the smallest such set.

---

**Question 12 (1 point)**
Which of the following is a typical approach for designing approximation algorithms?

- Exact dynamic programming
- **Greedy techniques** ✅ (Correct)
- Brute-force search
- Divide and conquer

**Explanation:** Greedy heuristics are one of the most common building blocks for approximation algorithms, trading guaranteed optimality for speed on hard (e.g., NP-hard) problems.

---

**Question 13 (1 point)**
The Traveling Salesman Problem is classified as __________.

- A decision problem
- **NP-hard** ✅ (Correct)
- Polynomial-time solvable
- P-complete

**Explanation:** The optimization version of TSP (find the minimum-cost tour) is NP-hard — no known polynomial-time algorithm solves it exactly for all cases.

---

**Question 14 (1 point)**
Which of the following is a common technique used in approximation algorithms for Vertex Cover?

- Dynamic programming
- Divide and conquer
- Backtracking
- **Greedy approach** ✅ (Correct)

**Explanation:** A well-known 2-approximation for Vertex Cover greedily picks edges and adds both endpoints to the cover until all edges are covered.

---

**Question 15 (1 point)**
One approach to solving the Sum of Subsets Problem is:

- Dynamic programming
- Divide and conquer
- **Backtracking** ✅ (Correct)
- Greedy algorithm

**Explanation:** Backtracking explores possible subsets incrementally, pruning branches once the running sum exceeds the target, making it a standard approach for this problem.

---

**Question 16 (1 point)**
What is the average-case time complexity of Quick Sort?

- **O(n log n)** ✅ (Correct)
- O(n^2)
- O(log n)
- O(n)

**Explanation:** On average, Quick Sort's partitioning splits the array reasonably evenly, giving O(n log n); only poor pivot choices on adversarial input push it to O(n²).

---

**Question 17 (1 point)**
What is the first step in the Divide and Conquer strategy?

- Conquer
- **Divide** ✅ (Correct)
- Analyze
- Combine

**Explanation:** Divide and Conquer follows the sequence Divide (split the problem into subproblems) → Conquer (solve subproblems) → Combine (merge results).

---

**Question 18 (1 point)**
What is a key disadvantage of Quick Sort?

- **Its performance degrades for nearly sorted input.** ✅ (Correct)
- It is not a comparison-based algorithm.
- It cannot sort in place.
- Requires extra space for merging.

**Explanation:** With a naive pivot choice (e.g., first or last element), nearly sorted or sorted input causes highly unbalanced partitions, degrading performance to O(n²).

---

**Question 19 (1 point)**
What is the main purpose of the Divide step in Merge Sort?

- Merge the subarrays.
- **Split the array into two halves.** ✅ (Correct)
- Optimize the time complexity.
- Sort the subarrays.

**Explanation:** The Divide step recursively splits the array into two halves until each subarray contains a single element, which are trivially sorted.

---

**Question 20 (1 point)**
What is the primary purpose of using divide and conquer in matrix multiplication?

- **To reduce the number of calculations needed for multiplication.** ✅ (Correct)
- To ensure that each element in the matrix is visited multiple times.
- To avoid the use of recursion.
- To add additional steps for verification of the solution.

**Explanation:** Algorithms like Strassen's use divide and conquer to cut the number of scalar multiplications required, improving on the standard O(n³) approach.

---

**Question 21 (1 point)**
Which step comes first in the Branch and Bound method?

- Pruning based on an initial solution
- **Branching the problem into subproblems** ✅ (Correct)
- Checking random solutions
- Evaluating all subproblems simultaneously

**Explanation:** Branch and Bound first branches (splits) the problem into smaller subproblems before it can compute bounds and prune unpromising ones.

---

**Question 22 (1 point)**
How does Branch and Bound eliminate unpromising solutions?

- By brute-force searching all solutions
- **By comparing bounds with the current best solution** ✅ (Correct)
- By prioritizing random guesses
- By solving subproblems in no specific order

**Explanation:** If a subproblem's computed bound is worse than the best solution found so far, that branch can't possibly improve on it, so it is pruned.

---

**Question 23 (1 point)**
The feasible region in linear programming refers to:

- The points that do not violate any constraint
- The points that violate the constraints
- The optimal solution
- **The set of all points that satisfy the constraints** ✅ (Correct)

**Explanation:** The feasible region is the set of all points (values of the decision variables) that satisfy every constraint of the LP problem simultaneously.

---

**Question 24 (1 point)**
The __________ method is a widely used algorithm to solve linear programming problems.

- **Simplex** ✅ (Correct)
- Gradient descent
- Genetic algorithm
- Brute force

**Explanation:** The Simplex method moves along the vertices of the feasible region's polytope to efficiently find the optimal solution to an LP problem.

---

**Question 25 (1 point)**
Branch and Bound does not use bounding functions to prune the search space. Is this statement true or false?

- **False** ✅ (Correct)
- True

**Explanation:** Branch and Bound explicitly relies on bounding functions — computing a bound for each subproblem and discarding branches that can't beat the current best solution.

---

**Question 26 (1 point)**
What does SSS represent in the dynamic programming solution to TSP?

- **A subset of cities visited** ✅ (Correct)
- The weight of the edge
- The total distance traveled
- A single city

**Explanation:** In the Held-Karp DP formulation of TSP, the state is defined as (S, i) where S is the subset of cities visited so far and i is the current city.

---

**Question 27 (1 point)**
The Floyd-Warshall algorithm can detect:

- Minimum spanning trees
- Strongly connected components
- **Negative weight cycles** ✅ (Correct)
- Hamiltonian cycles

**Explanation:** If, after running Floyd-Warshall, any diagonal entry of the distance matrix (distance from a vertex to itself) is negative, the graph contains a negative weight cycle.

---

**Question 28 (1 point)**
Which of the following represents the space complexity of the Floyd-Warshall algorithm?

- O(n3)
- **O(n2)** ✅ (Correct)
- O(nlogn)
- O(1)

**Explanation:** Floyd-Warshall maintains an n×n distance matrix, giving it O(n²) space complexity.

---

**Question 29 (1 point)**
In TSP, if there are n cities, how many subsets of cities need to be considered?

- **2n** (i.e., 2ⁿ) ✅ (Correct)
- n2
- n!
- n

**Explanation:** The DP (Held-Karp) approach to TSP considers all possible subsets of the n cities, and there are 2ⁿ such subsets.

---

**Question 30 (1 point)**
How does dynamic programming improve efficiency compared to recursion?

- By reducing memory usage
- By avoiding the use of functions
- **By storing and reusing solutions to subproblems** ✅ (Correct)
- By skipping unnecessary steps

**Explanation:** Dynamic programming caches (memoizes) the results of overlapping subproblems so they're computed once and reused, avoiding the redundant recomputation that plain recursion does.

---

**Question 31 (1 point)**
Which of the following problems has a known lower bound of O(n log n)?

- Merging two sorted arrays
- **Sorting using comparisons** ✅ (Correct)
- Searching in an unsorted array
- Finding the maximum element

**Explanation:** Any comparison-based sorting algorithm requires at least Ω(n log n) comparisons in the worst case, a bound proven using decision-tree arguments.

---

**Question 32 (1 point)**
How does computational complexity influence resource allocation in software development?

- It ensures zero software bugs.
- It determines the cost of the development team.
- **It helps allocate computational resources effectively for efficient algorithm execution.** ✅ (Correct)
- It defines the type of programming language used.

**Explanation:** Understanding an algorithm's time/space complexity lets developers predict how much CPU, memory, and other resources it will need, guiding infrastructure and design decisions.

---

**Question 33 (1 point)**
Which computational complexity class is best suited for algorithms expected to run on large-scale data?

- O(2^n)
- O(n!)
- **O(log n)** ✅ (Correct)
- O(n^2)

**Explanation:** O(log n) grows the slowest of the options as input size increases, making it the most scalable for large datasets.

---

**Question 34 (1 point)**
What distinguishes NP-complete problems from NP-hard problems?

- NP-complete problems have polynomial-time solutions, while NP-hard problems do not.
- **NP-complete problems are in NP, while NP-hard problems may not be.** ✅ (Correct)
- NP-complete problems are undecidable, while NP-hard problems are not.
- NP-complete problems require non-deterministic algorithms, while NP-hard problems do not.

**Explanation:** NP-complete = in NP and at least as hard as every problem in NP. NP-hard = at least as hard as every problem in NP, but not necessarily itself verifiable in polynomial time (may not be in NP).

---

**Question 35 (1 point)**
In a comparison-based algorithm, what determines the lower bound?

- The size of the input
- The order of elements in input
- **The height of the decision tree** ✅ (Correct)
- The number of recursive calls

**Explanation:** Modeling all possible comparison sequences as a decision tree, the minimum number of comparisons needed in the worst case equals the tree's height, which is what establishes the Ω(n log n) lower bound.

---

**Question 36 (1 point)**
A non-recursive algorithm with a linear number of operations always has a time complexity of O(n). Is this statement True or False?

- **True** ✅ (Correct)
- False

**Explanation:** If the total number of basic operations grows linearly with input size n, then by definition the time complexity is O(n).

---

**Question 37 (1 point)**
What data structure would you use to implement a recursive function?

- **Stack** ✅ (Correct)
- Binary Search Tree
- Queue
- Array

**Explanation:** Each recursive call is pushed onto the call stack (with its local variables and return address) and popped off when it returns, which is why deep recursion can cause a stack overflow.

---

**Question 38 (1 point)**
If an algorithm's running time is 3n³ + 2n² + 100, what is its Big-O complexity?

- O(1)
- O(n)
- **O(n³)** ✅ (Correct)
- O(n²)

**Explanation:** Big-O keeps only the dominant (fastest-growing) term and drops constants and lower-order terms, so the n³ term dominates.

---

**Question 39 (1 point)**
Which of the following is an example of an algorithm with constant time complexity O(1)?

- Insertion Sort
- Binary Search
- Linear Search
- **Accessing an array element by index** ✅ (Correct)

**Explanation:** Arrays support direct indexed access, so retrieving any element by its index takes the same constant amount of time regardless of array size.

---

**Question 40 (1 point)**
The time complexity of the binary search algorithm is:

- O(n)
- O(1)
- O(n²)
- **O(log n)** ✅ (Correct)

**Explanation:** Binary Search halves the search space with each comparison, so the number of steps needed grows logarithmically with input size.