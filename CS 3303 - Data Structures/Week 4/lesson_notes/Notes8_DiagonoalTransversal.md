
---

# ⭐ Lesson Notes: Diagonal Traversal of a Binary Tree

**Last Updated:** 23 Jul, 2025

---

## 1. What is Diagonal Traversal?

**Diagonal Traversal** of a binary tree is the process of printing all nodes **diagonally** from top-right to bottom-left.

**Rules:**

* Nodes in the same diagonal line belong together.
* For nodes in different subtrees, **left subtree elements appear before right subtree elements** in the same diagonal.

---

## 2. Example

**Input Tree:**

```
        8
      /   \
     3     10
    /     /  \
   1     6    14
        / \   /
       4   7 13
```

**Output (Diagonal Traversal):**

```
8 10 14 3 6 7 13 1 4
```

**Explanation:**

* First diagonal: 8, 10, 14
* Second diagonal: 3, 6, 7, 13
* Third diagonal: 1, 4

---

## 3. Approach 1: Using Recursion and HashMap

**Time Complexity:** O(n)
**Auxiliary Space:** O(n)

### Idea:

* Perform **recursive traversal** and store nodes in a HashMap based on their diagonal level.
* **Left child** increases the diagonal level.
* **Right child** stays on the same diagonal.

### Java Code (Recursive + HashMap)

```java
import java.util.*;

class Node {
    int data;
    Node left, right;
    Node(int x) { data = x; left = right = null; }
}

class GfG {

    static void diagonalRecur(Node root, int level, HashMap<Integer, ArrayList<Integer>> levelData) {
        if (root == null) return;

        levelData.computeIfAbsent(level, k -> new ArrayList<>()).add(root.data);
        diagonalRecur(root.left, level + 1, levelData);
        diagonalRecur(root.right, level, levelData);
    }

    static ArrayList<Integer> diagonal(Node root) {
        ArrayList<Integer> ans = new ArrayList<>();
        HashMap<Integer, ArrayList<Integer>> levelData = new HashMap<>();
        diagonalRecur(root, 0, levelData);

        int level = 0;
        while (levelData.containsKey(level)) {
            ans.addAll(levelData.get(level));
            level++;
        }
        return ans;
    }

    static void printList(ArrayList<Integer> v) {
        for (int val : v) System.out.print(val + " ");
        System.out.println();
    }

    public static void main(String[] args) {
        Node root = new Node(8);
        root.left = new Node(3);
        root.right = new Node(10);
        root.left.left = new Node(1);
        root.right.left = new Node(6);
        root.right.right = new Node(14);
        root.right.right.left = new Node(13);
        root.right.left.left = new Node(4);
        root.right.left.right = new Node(7);

        ArrayList<Integer> ans = diagonal(root);
        printList(ans);
    }
}
```

**Output:**

```
8 10 14 3 6 7 13 1 4
```

> **Note:** May cause TLE for large trees due to recursive traversal.

---

## 4. Approach 2: Iterative Diagonal Traversal Using Queue

### a) Using Queue with Delimiter

**Idea:**

* Traverse the tree **level-wise**.
* Push root and a `null` marker (delimiter) into the queue.
* For each node, add its value to the result and enqueue its **left child** if it exists.
* If a `null` is encountered, start a new diagonal.

**Time Complexity:** O(n)
**Auxiliary Space:** O(n)

```java
import java.util.*;

class GfGQueueDelimiter {
    static ArrayList<Integer> diagonalPrint(Node root) {
        ArrayList<Integer> ans = new ArrayList<>();
        if (root == null) return ans;

        Queue<Node> q = new LinkedList<>();
        q.add(root); q.add(null);

        while (!q.isEmpty()) {
            Node curr = q.poll();
            if (curr == null) {
                if (q.isEmpty()) break;
                q.add(null);
            } else {
                while (curr != null) {
                    ans.add(curr.data);
                    if (curr.left != null) q.add(curr.left);
                    curr = curr.right;
                }
            }
        }
        return ans;
    }

    static void printList(ArrayList<Integer> v) {
        for (int val : v) System.out.print(val + " ");
        System.out.println();
    }

    public static void main(String[] args) {
        Node root = new Node(8);
        root.left = new Node(3);
        root.right = new Node(10);
        root.left.left = new Node(1);
        root.right.left = new Node(6);
        root.right.right = new Node(14);
        root.right.right.left = new Node(13);
        root.right.left.left = new Node(4);
        root.right.left.right = new Node(7);

        ArrayList<Integer> ans = diagonalPrint(root);
        printList(ans);
    }
}
```

**Output:**

```
8 10 14 3 6 7 13 1 4
```

---

### b) Using Queue without Delimiter

**Idea:**

* Similar to the previous method but without using a `null` marker.
* Traverse current node → move to right → enqueue left children.

**Time Complexity:** O(n)
**Auxiliary Space:** O(n)

```java
import java.util.*;

class GfGQueue {
    static ArrayList<Integer> diagonalPrint(Node root) {
        ArrayList<Integer> ans = new ArrayList<>();
        if (root == null) return ans;

        Queue<Node> q = new LinkedList<>();
        q.add(root);

        while (!q.isEmpty()) {
            Node curr = q.poll();
            while (curr != null) {
                ans.add(curr.data);
                if (curr.left != null) q.add(curr.left);
                curr = curr.right;
            }
        }
        return ans;
    }

    static void printList(ArrayList<Integer> v) {
        for (int val : v) System.out.print(val + " ");
        System.out.println();
    }

    public static void main(String[] args) {
        Node root = new Node(8);
        root.left = new Node(3);
        root.right = new Node(10);
        root.left.left = new Node(1);
        root.right.left = new Node(6);
        root.right.right = new Node(14);
        root.right.right.left = new Node(13);
        root.right.left.left = new Node(4);
        root.right.left.right = new Node(7);

        ArrayList<Integer> ans = diagonalPrint(root);
        printList(ans);
    }
}
```

**Output:**

```
8 10 14 3 6 7 13 1 4
```

---

## ✅ Key Points

* Diagonal Traversal prints nodes **from top-right to bottom-left diagonally**.
* **Left child** → next diagonal, **Right child** → same diagonal.
* **Recursive + HashMap**: simple but may TLE for large trees.
* **Iterative with queue**: optimized, linear time and space.
* Both iterative approaches give the **same output**.

---

