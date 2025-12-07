
---

# ⭐ Lesson Notes: Level Order Traversal (Breadth-First Search) of a Binary Tree

**Last Updated: 07 Oct, 2025**

---

## 1. What Is Level Order Traversal?

Level Order Traversal is a tree-traversal technique where you visit the nodes of the binary tree **level by level**.

This means:

* First, visit all nodes at **level 0** (the root)
* Next, visit all nodes at **level 1**
* Then visit all nodes at **level 2**, and so on…

This technique is also known as **Breadth-First Search (BFS)** because it explores the tree *breadth-wise*, not depth-wise.

---

## 2. What Are We Given?

You are given the **root** of a binary tree.
Your task is to return its **Level Order Traversal**.

---

## 3. Example

### Input Tree (represented from “112” example)

```
        5
      /   \
    12     13
   /  \      \
  7    14      2
 / \   / \    / \
17 23 27  3  8  11
```

### Output:

```
[[5], 
 [12, 13], 
 [7, 14, 2], 
 [17, 23, 27, 3, 8, 11]]
```

### Explanation (Level by Level):

* **Level 0:** `[5]`
* **Level 1:** `[12, 13]`
* **Level 2:** `[7, 14, 2]`
* **Level 3:** `[17, 23, 27, 3, 8, 11]`

This is exactly the Level Order Traversal result.

---

# 5. Table of Contents

1. Approach 1 — Using Recursion (O(n) time, O(n) space)
2. Approach 2 — Using Queue (Iterative BFS) **Expected Approach**
3. Code examples
4. Outputs
5. Time & Space Complexity

---

# ⭐ Approach 1: Level Order Traversal Using Recursion

**Time Complexity:** O(n)
**Space Complexity:** O(n)

### How It Works

* Start from the root at **level 0**
* When visiting a node:

    * Add its value to a result list corresponding to its level index
* Recursively process:

    * Left child → level + 1
    * Right child → level + 1

This approach simulates BFS using recursion by grouping nodes by levels.

---
![img_28.png](img_28.png)
---
![img_29.png](img_29.png)
---
![img_30.png](img_30.png)
---
![img_31.png](img_31.png)
---
![img_32.png](img_32.png)
---
![img_33.png](img_33.png)
---
![img_34.png](img_34.png)
---
![img_35.png](img_35.png)
---

### ✅ Code (Recursive Approach)

```java
import java.util.ArrayList;

class Node {
    int data;
    Node left, right;
    Node(int value)
    {
        data = value;
        left = null;
        right = null;
    }
}

public class GfG {
    void levelOrderRec(Node root, int level, ArrayList<ArrayList<Integer>> res) {
        // Base case
        if (root == null)
            return;

        // Add a new level to the result if needed
        if (res.size() <= level)
            res.add(new ArrayList<>());

        // Add current node's data to its corresponding level
        res.get(level).add(root.data);

        // Recur for left and right children
        levelOrderRec(root.left, level + 1, res);
        levelOrderRec(root.right, level + 1, res);
    }

    // Function to perform level order traversal
    ArrayList<ArrayList<Integer>> levelOrder(Node root)
    {
        ArrayList<ArrayList<Integer>> res = new ArrayList<>();
        levelOrderRec(root, 0, res);
        return res;
    }

    public static void main(String[] args)
    {
        //      5
        //     / \
        //   12   13
        //   /  \    \
        //  7    14   2
        // / \  /  \  / \
        //17 23 27 3  8  11

        Node root = new Node(5);
        root.left = new Node(12);
        root.right = new Node(13);

        root.left.left = new Node(7);
        root.left.right = new Node(14);

        root.right.right = new Node(2);

        root.left.left.left = new Node(17);
        root.left.left.right = new Node(23);

        root.left.right.left = new Node(27);
        root.left.right.right = new Node(3);

        root.right.right.left = new Node(8);
        root.right.right.right = new Node(11);

        GfG tree = new GfG();
        ArrayList<ArrayList<Integer>> res = tree.levelOrder(root);

        for (ArrayList<Integer> level : res) {
            for (int val : level) {
                System.out.print(val + " ");
            }
            System.out.println();
        }
    }
}
```

### Output (Recursive)

```
5 
12 13 
7 14 2 
17 23 27 3 8 11 
```

---

# ⭐ Approach 2 (Expected): Using a Queue (Iterative BFS)

**This is the standard BFS method and the most widely used.**

### How It Works

1. Create a queue
2. Add the root
3. While the queue is not empty:

    * Take all nodes of the current level
    * Add their children to the queue
    * Move to the next level

This guarantees level-by-level traversal.

---

### ✅ Code (Iterative Queue Approach)

```java
import java.util.ArrayList;
import java.util.LinkedList;
import java.util.Queue;

class Node {
    int data;
    Node left, right;
    Node(int value)
    {
        data = value;
        left = null;
        right = null;
    }
}

// Iterative method to perform level order traversal
public class GfG {
    public static ArrayList<ArrayList<Integer>> levelOrder(Node root)
    {
        if (root == null)
            return new ArrayList<>();

        Queue<Node> q = new LinkedList<>();
        ArrayList<ArrayList<Integer>> res = new ArrayList<>();

        q.offer(root);
        int currLevel = 0;

        while (!q.isEmpty()) {
            int len = q.size();
            res.add(new ArrayList<>());

            for (int i = 0; i < len; i++) {
                Node node = q.poll();
                res.get(currLevel).add(node.data);

                if (node.left != null)
                    q.offer(node.left);

                if (node.right != null)
                    q.offer(node.right);
            }
            currLevel++;
        }
        return res;
    }

    public static void main(String[] args)
    {
        // same tree used earlier

        Node root = new Node(5);
        root.left = new Node(12);
        root.right = new Node(13);

        root.left.left = new Node(7);
        root.left.right = new Node(14);

        root.right.right = new Node(2);

        root.left.left.left = new Node(17);
        root.left.left.right = new Node(23);

        root.left.right.left = new Node(27);
        root.left.right.right = new Node(3);

        root.right.right.left = new Node(8);
        root.right.right.right = new Node(11);

        ArrayList<ArrayList<Integer>> res = levelOrder(root);

        for (ArrayList<Integer> level : res) {
            for (int val : level) {
                System.out.print(val + " ");
            }
            System.out.println();
        }
    }
}
```

### Output (Iterative)

```
5 
12 13 
7 14 2 
17 23 27 3 8 11 
```

---

# ⭐ Time & Space Complexity

| Approach    | Time     | Space    | Notes                  |
| ----------- | -------- | -------- | ---------------------- |
| Recursion   | **O(n)** | **O(n)** | Stores nodes by levels |
| Queue (BFS) | **O(n)** | **O(n)** | Most common approach   |

Both approaches visit every node exactly once.

---

# ⭐ Key Points to Remember

* Level Order Traversal = **Breadth-First Search**
* Processes the tree **level by level**
* Very useful for:

    * Finding the **shortest path** in a tree
    * Printing tree level structures
    * Serializing/deserializing trees
* Queue method is the **expected/standard** BFS approach

---

