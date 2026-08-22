# Unit 3 Graded Quiz — Q&A

**Question 1 (1 point)**
Which algorithm uses the Divide and Conquer approach to reduce the number of multiplications in matrix multiplication?

- **Strassen's Algorithm** ✅ (Correct)
- Merge Sort
- Binary Search
- Quick Sort

**Explanation:** Strassen's Algorithm splits matrices into sub-matrices and combines them with fewer multiplications than the standard O(n³) method, achieving roughly O(n^2.81).

---

**Question 2 (1 point)**
Which of the following applications can Quick Sort be used for?

- **Sorting large datasets.** ✅ (Correct)
- Searching for elements in unsorted arrays.
- Solving problems with multiple constraints.
- Finding the shortest path in graphs.

**Explanation:** Quick Sort is a general-purpose, in-place comparison sort well suited to large datasets due to its average-case O(n log n) performance.

---

**Question 3 (1 point)**
What is the average-case time complexity of Quick Sort?

- O(n)
- O(log n)
- O(n^2)
- **O(n log n)** ✅ (Correct)

**Explanation:** Only in the worst case (already sorted data with a poor pivot choice) does Quick Sort degrade to O(n²).

---

**Question 4 (1 point)**
Why is Merge Sort better suited for external sorting?

- It requires no auxiliary memory.
- It uses in-place sorting.
- **It minimizes disk I/O by sequential access.** ✅ (Correct)
- It is faster than all other algorithms.

**Explanation:** Merge Sort processes data in sequential chunks/runs, which fits well with reading and writing large files from disk where random access is expensive.

---

**Question 5 (1 point)**
Which of the following is a major strength of Binary Search?

- **It has a logarithmic time complexity.** ✅ (Correct)
- It has a constant time complexity.
- It requires significant memory overhead.
- It works on unsorted data.

**Explanation:** Binary Search runs in O(log n), which is why it's efficient on large sorted datasets (but it requires the data to already be sorted).

---

**Question 6 (1 point)**
Which data structure is most suitable for efficiently implementing the greedy fractional knapsack algorithm?

- Linked List
- **Heap (Priority Queue)** ✅ (Correct)
- Queue
- Stack

**Explanation:** A heap efficiently retrieves the item with the best value-to-weight ratio at each step, which is exactly what the greedy strategy needs.

---

**Question 7 (1 point)**
What important set does Dijkstra's algorithm primarily focus on during its execution?

- **Visited vertices** ✅ (Correct)
- Inactive nodes in a game
- Types of computer programming languages
- Routes taken by postal services

**Explanation:** Dijkstra's algorithm maintains a set of vertices whose shortest distance from the source has already been finalized ("visited"/settled vertices).

---

**Question 8 (1 point)**
The greedy approach is considered a(n) _____________ algorithm.

- dynamic programming
- exact
- **approximation** ✅ (Correct)
- backtracking

**Explanation:** Greedy algorithms make locally optimal choices at each step and don't always guarantee a globally optimal solution, so they're generally classified as approximation algorithms.

---

**Question 9 (1 point)**
Which of these is NOT a characteristic of a greedy algorithm?

- Makes locally optimal choices.
- Simple implementation.
- Often efficient.
- **Always finds the optimal solution.** ✅ (Correct — this is NOT a characteristic)

**Explanation:** Greedy algorithms make locally optimal choices, are simple to implement, and are often efficient — but they do not always guarantee the globally optimal solution.

---

**Question 10 (1 point)**
In Huffman coding, the greedy approach involves combining the two most frequent symbols. Is it true or false?

- True
- **False** ✅ (Correct)

**Explanation:** Huffman coding greedily combines the two **least** frequent symbols/nodes at each step, not the most frequent.

---

**Question 11 (1 point)**
In the recurrence relation T(n)=T(n−1) +O(1), what is the time complexity of the algorithm?

- O(log n)
- O(1)
- O(n²)
- **O(n)** ✅ (Correct)

**Explanation:** Each step reduces the problem size by 1 and does constant work, giving a linear number of steps overall.

---

**Question 12 (1 point)**
If an algorithm's running time is 3n³ + 2n² + 100, what is its Big-O complexity?

- **O(n³)** ✅ (Correct)
- O(n²)
- O(1)
- O(n)

**Explanation:** Big-O keeps only the dominant term and drops constants/lower-order terms, so the n³ term dominates.

---

**Question 13 (1 point)**
A data structure that allows Last In First Out (LIFO) access is called a ____.

- Queue
- **Stack** ✅ (Correct)
- Linked list
- Array

**Explanation:** The most recently added element is the first one removed, which is the definition of a stack (LIFO).

---

**Question 14 (1 point)**
Using the Master Theorem, the solution to the recurrence T(n)=2T(n/2) +O(n) is:

- O(log n)
- **O(n log n)** ✅ (Correct)
- O(n²)
- O(n)

**Explanation:** This matches Case 2 of the Master Theorem (a=2, b=2, f(n)=O(n) is comparable to n^log_b(a) = n), giving O(n log n) — the same recurrence that describes Merge Sort.

---

**Question 15 (1 point)**
Which notation provides a lower bound of an algorithm's running time?

- Big-O
- Big-Θ
- Little-Ω
- **Big-Ω** ✅ (Correct)

**Explanation:** Big-Ω describes the asymptotic lower bound; Big-O describes the upper bound, and Big-Θ describes a tight bound (both upper and lower).