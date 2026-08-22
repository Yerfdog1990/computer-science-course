# Final Exam — Q&A

**Question 1 (1 point)**
Which of the following data structures is best for implementing a priority queue?

- Singly Linked List
- **Binary Heap** ✅ (Correct)
- Queue
- Stack

**Explanation:** A binary heap supports O(log n) insertion and O(log n) extraction of the min/max element, which is exactly what a priority queue needs — far better than the O(n) operations a plain list, queue, or stack would require.

---

**Question 2 (1 point)**
Which of the following methods is commonly used to solve recurrences in divide-and-conquer algorithms?

- Polynomial Expansion
- **Recursion Tree Method** ✅ (Correct)
- Matrix Multiplication
- Brute Force Method

**Explanation:** The recursion tree method visualizes the cost at each level of recursive calls and sums them up, making it a standard tool (alongside the Master Theorem) for solving divide-and-conquer recurrences.

---

**Question 3 (1 point)**
What is the average-case time complexity of Quick Sort?

- O(n^2)
- **O(n log n)** ✅ (Correct)
- O(n)
- O(log n)

**Explanation:** On average, Quick Sort's partitioning splits the array reasonably evenly, giving O(n log n); only poor pivot choices on adversarial input push it to O(n²).

---

**Question 4 (1 point)**
Which of the following problems can be solved using Binary Search?

- Finding the smallest element in an array.
- Merging two arrays.
- **Searching in a sorted array.** ✅ (Correct)
- Sorting an unsorted array.

**Explanation:** Binary Search repeatedly halves the search space, which only works correctly when the array is already sorted.

---

**Question 5 (1 point)**
Which of these is NOT a characteristic of a greedy algorithm?

- Simple implementation.
- Makes locally optimal choices.
- **Always finds the optimal solution.** ✅ (Correct — this is NOT a characteristic)
- Often efficient.

**Explanation:** Greedy algorithms make locally optimal choices, are simple to implement, and are often efficient — but they do not always guarantee the globally optimal solution.

---

**Question 6 (1 point)**
What is the time complexity of a naive greedy approach (without a priority queue) to find a path assuming |V| vertices and |E| edges?

- **O(|V|^2)** ✅ (Correct)
- O(|V| + |E|)
- O(|V|)
- O(|E|)

**Explanation:** Without a priority queue, finding the next closest vertex requires a linear scan of all vertices at each of the |V| steps, giving O(|V|²) overall (this is the classic naive/array-based implementation of Dijkstra's algorithm).

---

**Question 7 (1 point)**
Which principle ensures the correctness of dynamic programming solutions?

- Principle of least effort
- Principle of independence
- Principle of randomness
- **Principle of optimality** ✅ (Correct)

**Explanation:** The principle of optimality states that an optimal solution to a problem is composed of optimal solutions to its subproblems — this optimal substructure is what makes DP solutions correct.

---

**Question 8 (1 point)**
Which of the following represents the space complexity of the Floyd-Warshall algorithm?

- O(nlogn)
- **O(n2)** ✅ (Correct)
- O(n3)
- O(1)

**Explanation:** Floyd-Warshall maintains a distance matrix of size n×n, giving it O(n²) space complexity.

---

**Question 9 (1 point)**
What is the complexity of solving the Sum of Subsets problem using backtracking in the worst case?

- O(log n)
- **O(2^n)** ✅ (Correct)
- O(n^2)
- O(n!)

**Explanation:** In the worst case, backtracking must explore all 2ⁿ possible subsets of the n elements before finding (or ruling out) a valid combination.

---

**Question 10 (1 point)**
Which data structure is commonly used to store intermediate subsets during backtracking?

- A hash table
- A queue
- A stack
- **An array or list** ✅ (Correct)

**Explanation:** As the algorithm recursively includes/excludes elements, the current partial subset is typically tracked in a simple array or list that's updated (and rolled back) as the recursion backtracks.

---

**Question 11 (1 point)**
In the Sum of Subsets problem, if a subset's sum exceeds the target, what happens?

- The algorithm stores the subset as a partial solution.
- **The algorithm backtracks to explore other options.** ✅ (Correct)
- The algorithm skips to the next element without backtracking.
- The algorithm terminates.

**Explanation:** Exceeding the target sum means this branch can't lead to a valid solution, so the algorithm prunes it and backtracks to try a different combination.

---

**Question 12 (1 point)**
What is the primary purpose of the Branch and Bound technique?

- To randomly search for solutions
- **To find the optimal solution to combinatorial problems efficiently** ✅ (Correct)
- To approximate solutions for easy problems
- To solve only linear equations

**Explanation:** Branch and Bound systematically explores the solution space while using bounds to prune branches that can't possibly beat the current best solution, making it efficient for finding exact optimal solutions to combinatorial optimization problems.

---

**Question 13 (1 point)**
In a linear programming problem, the feasible region is bounded if:

- **The constraints form a closed and finite region** ✅ (Correct)
- The objective function is quadratic
- The constraints are violated
- The constraints form an open region

**Explanation:** A bounded feasible region is one enclosed on all sides by the constraints, meaning the decision variables can't grow infinitely large while still satisfying every constraint.

---

**Question 14 (1 point)**
In a linear programming problem, the constraints are typically:

- Complex boolean expressions
- Randomized constraints
- Nonlinear equations
- **Linear inequalities or equations** ✅ (Correct)

**Explanation:** LP constraints are, by definition, linear — expressed as inequalities (≤, ≥) or equalities among the decision variables.

---

**Question 15 (1 point)**
Which of the following is an example of an NP-complete problem?

- MergeSort
- Binary Search
- QuickSort
- **Graph Coloring** ✅ (Correct)

**Explanation:** MergeSort, Binary Search, and QuickSort all run in polynomial time. Graph Coloring (deciding if a graph can be colored with k colors so no two adjacent vertices share a color) is a classic NP-complete problem.

---

**Question 16 (1 point)**
What is the significance of lower bounds in computational complexity?

- **They provide a baseline for comparing algorithm efficiency.** ✅ (Correct)
- They solve optimization problems.
- They determine the space complexity of an algorithm.
- They represent the highest possible runtime.

**Explanation:** A lower bound establishes the minimum amount of work any algorithm must do to solve a problem, giving a benchmark against which the efficiency of actual algorithms can be measured.

---

**Question 17 (1 point)**
If a problem is NP-hard, it must also be NP-complete. Is it true or false?

- **False** ✅ (Correct)
- True

**Explanation:** NP-hard problems are at least as hard as every problem in NP, but they aren't required to be in NP themselves (i.e., a solution isn't necessarily verifiable in polynomial time). NP-complete problems must additionally be in NP, so NP-hard does not imply NP-complete.

---

**Question 18 (1 point)**
In approximation algorithms, a solution with a 2-approximation factor means __________.

- The algorithm always takes twice as long to run
- The solution is exactly the optimal solution
- The solution is half the optimal solution
- **The solution is at most twice the optimal solution** ✅ (Correct)

**Explanation:** A 2-approximation guarantees the algorithm's output is never worse than 2× the cost/value of the true optimal solution, even though it may not find the optimal solution itself.

---

**Question 19 (1 point)**
What is a common characteristic of NP-hard problems?

- **They do not have polynomial-time exact solutions** ✅ (Correct)
- They always have deterministic solutions
- They can always be solved optimally in polynomial time
- They have efficient algorithms for all instances

**Explanation:** NP-hard problems are believed (though not proven, given P vs NP is open) to have no known polynomial-time algorithm that solves them exactly for all instances.

---

**Question 20 (1 point)**
In the Vertex Cover Problem, an edge is covered if:

- The edge is removed from the graph
- Both endpoints are included in the vertex cover
- Both vertices are excluded from the vertex cover
- **At least one of its endpoints is included in the vertex cover** ✅ (Correct)

**Explanation:** By definition, a vertex cover only needs to include one endpoint of each edge for that edge to be considered "covered" — it doesn't require both endpoints.