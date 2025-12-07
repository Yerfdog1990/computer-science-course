
---

# ⭐ Lesson Notes: Preorder Traversal of a Binary Tree

**Last Updated: 07 Oct, 2025**

## 1. What Is Preorder Traversal?

Preorder Traversal is a method of visiting all the nodes in a binary tree in the order:

### **Root → Left → Right**

This means:

1. **Visit the current node first**
2. **Traverse the left subtree**
3. **Traverse the right subtree**

This order is applied **recursively** for every node in the tree.

---

## 2. Why Do We Use Preorder Traversal?

* It is commonly used to create the **prefix notation** of expression trees.
* It is useful when you need to **copy** or **rebuild** a tree because the root is always processed first.
* It gives a clear “top-down” view of the tree.

---

## 3. Example 1

**Input Tree:**

```
    1
   / \
  2   3
```

**Output:**
`[1, 2, 3]`

**Explanation:**
Preorder follows **Root → Left → Right**
So we visit:

* Root → **1**
* Left → **2**
* Right → **3**

---

## 4. Example 2

Tree:

```
        1
      /   \
     2     3
    / \     \
   4   5     6
```

**Output:**
`[1, 2, 4, 5, 3, 6]`

**Explanation:**
Apply **Root → Left → Right**:

Visit in this order:
**1 → 2 → 4 → 5 → 3 → 6**

---


## 5. How Does Preorder Traversal Work?

The recursive logic is simple:

1. If the node is `null`, stop
2. Visit the **node**
3. Traverse the **left subtree**
4. Traverse the **right subtree**

---
![img_16.png](img_16.png)
---
![img_17.png](img_17.png)
---
![img_18.png](img_18.png)
---
![img_19.png](img_19.png)
---
![img_20.png](img_20.png)
---
![img_21.png](img_21.png)
---

## 6. Code Example (Using Your Provided Code)

```java
import java.util.ArrayList;

//Node Structure
class Node {
    int data;
    Node left, right;

    Node(int v) {
        data = v;
        left = right = null;
    }
}

class GFG {
   
    public static void preOrder(Node node, ArrayList<Integer> res) {
        if (node == null)
            return;

        // Visit the current node first
        res.add(node.data);

        // Traverse the left subtree
        preOrder(node.left, res);

        // Traverse the right subtree
        preOrder(node.right, res);
    }

    public static void main(String[] args) {
        // Create binary tree
        //       1
        //      /  \
        //    2     3
        //   / \     \
        //  4   5     6

        Node root = new Node(1);
        root.left = new Node(2);
        root.right = new Node(3);
        root.left.left = new Node(4);
        root.left.right = new Node(5);
        root.right.right = new Node(6);

        ArrayList<Integer> result = new ArrayList<>();
        preOrder(root, result);

        for (int val : result) {
            System.out.print(val + " ");
        }
    }
}
```

### **Output**

```
1 2 4 5 3 6
```

---

## 7. Time & Space Complexity

### ⏱ Time Complexity: **O(n)**

You must visit all `n` nodes exactly once.

### 💾 Auxiliary Space: **O(h)**

Where `h` = height of the tree

* **Worst case:** h = n (skewed tree)
* **Best case:** h = log n (complete tree)

---

## 8. Key Properties to Remember

* The **root node is always visited first**
* Order: **Root → Left → Right**
* Used in **expression trees** for generating **prefix notation**
* Helps in **tree reconstruction** because the root appears first in the traversal

---

# ⭐**Iterative Preorder Traversal of a Binary Tree**

## 🎯 What is Preorder Traversal?

Preorder Traversal visits nodes in the following order:

### ✅ **Root → Left → Right**

This means:

1. Visit the current node (root)
2. Traverse the left subtree
3. Traverse the right subtree

---

## 🧠 Why Iterative Preorder Traversal?

Normally, preorder traversal is done using recursion.
But in some cases (like very deep trees), recursion may cause stack overflow.
So we use **an iterative approach** using **a stack**.

---

# 📌 Examples

### **Example 1**

Input:

```
       1
     /   \
    2     3
  /  \
 4    5
```

Output:

```
1 2 4 5 3
```

Explanation: Root → Left → Right

---

### **Example 2**

Input:

```
            8
          /   \
         1      5
          \    /  \
           7  10   6
            \  /
            10 6
```

Output:

```
8 1 7 10 5 10 6 6
```

---

# 📚 Table of Content

1. **Naive Approach** – Simple Iterative Preorder (Stack)
2. **Better Approach** – Using Current Pointer (Stack)
3. **Expected Approach** – Preorder Morris Traversal (No Stack, No Recursion)

---

# 🥇 1. Naive Approach – Iterative Preorder Using Stack

**Time Complexity:** O(n)
**Space Complexity:** O(n)

### 🧩 Idea

1. Create an empty stack
2. Push the root into the stack
3. While the stack is not empty:

    * Pop a node and print it
    * Push its **right child** (if exists)
    * Push its **left child** (if exists)

We push **right before left** because the stack is LIFO (last-in-first-out), so left gets processed first.

---

## ✅ Java Code: Simple Iterative Preorder

```java
import java.util.ArrayList;
import java.util.List;
import java.util.Stack;

class Node {
    int data;
    Node left, right;

    Node(int data) {
        this.data = data;
        left = right = null;
    }
}

public class GfG {
    public static List<Integer> preOrder(Node root) {
        List<Integer> res = new ArrayList<>();
        if (root == null)
            return res;

        Stack<Node> s = new Stack<>();
        s.push(root);

        while (!s.isEmpty()) {
            Node curr = s.pop();
            res.add(curr.data);

            if (curr.right != null)
                s.push(curr.right);
            if (curr.left != null)
                s.push(curr.left);
        }

        return res;
    }

    public static void main(String[] args) {
        Node root = new Node(1);
        root.left = new Node(2);
        root.right = new Node(3);
        root.left.left = new Node(4);
        root.left.right = new Node(5);

        List<Integer> preorder = preOrder(root);

        for (int val : preorder) {
            System.out.print(val + " ");
        }
        System.out.println();
    }
}
```

### ✔ Output:

```
1 2 4 5 3
```

---

# 🥈 2. Better Approach – Iterative Preorder With Current Pointer

**Time Complexity:** O(n)
**Space Complexity:** O(n)** (but fewer operations)

### 🧠 Key Insight

In the first solution, we notice that **left child is popped immediately**.
So instead of pushing left into the stack, we simply **move directly left**, and only push the **right child** into the stack.

---

## ✅ Java Code: Better Iterative Preorder

```java
import java.util.ArrayList;
import java.util.List;
import java.util.Stack;

class Node {
    int data;
    Node left, right;

    Node(int data) {
        this.data = data;
        this.left = this.right = null;
    }
}

public class GfG {
    static List<Integer> preOrder(Node root) {
        List<Integer> res = new ArrayList<>();
        if (root == null) return res;

        Stack<Node> s = new Stack<>();
        Node curr = root;

        while (!s.isEmpty() || curr != null) {
            while (curr != null) {
                res.add(curr.data);
                if (curr.right != null) s.push(curr.right);
                curr = curr.left;
            }

            if (!s.isEmpty()) {
                curr = s.pop();
            }
        }

        return res;
    }

    public static void main(String[] args) {
        Node root = new Node(1);
        root.left = new Node(2);
        root.right = new Node(3);
        root.left.left = new Node(4);
        root.left.right = new Node(5);

        List<Integer> res = preOrder(root);

        for (int x : res) {
            System.out.print(x + " ");
        }
        System.out.println();
    }
}
```

### ✔ Output:

```
1 2 4 5 3
```

---

# 🥇 3. Expected Approach – **Preorder Morris Traversal**

This is the most optimal method.

### ⭐ **Time Complexity:** O(n)

### ⭐ **Space Complexity:** O(1)** (No recursion, no stack)

### 🧠 Idea

Morris Traversal uses the idea of **Threaded Binary Trees** by temporarily modifying pointers.

### Algorithm Steps:

1. If left child is `null` → print node → go right
2. Otherwise:

    * Find inorder predecessor (rightmost node in left subtree)
    * If predecessor’s right is `null`:

        * Make it point to current node
        * Print current node
        * Go left
    * Else:

        * Remove the temporary link
        * Go right

---

## ✅ Java Code: Morris Preorder Traversal

```java
import java.util.ArrayList;
import java.util.List;

class Node {
    int data;
    Node left, right;

    Node(int item) {
        data = item;
        left = right = null;
    }
}

public class MorrisPreorder {
    public static List<Integer> morrisPreOrder(Node node) {
        List<Integer> result = new ArrayList<>();

        while (node != null) {
            if (node.left == null) {
                result.add(node.data);
                node = node.right;
            } else {
                Node current = node.left;
                while (current.right != null && current.right != node) {
                    current = current.right;
                }

                if (current.right == node) {
                    current.right = null;
                    node = node.right;
                } else {
                    result.add(node.data);
                    current.right = node;
                    node = node.left;
                }
            }
        }

        return result;
    }

    public static void main(String[] args) {
        Node root = new Node(1);
        root.left = new Node(2);
        root.right = new Node(3);
        root.left.left = new Node(4);
        root.left.right = new Node(5);

        List<Integer> morrisResult = morrisPreOrder(root);

        for (int value : morrisResult) {
            System.out.print(value + " ");
        }
        System.out.println();
    }
}
```

### ✔ Output:

```
1 2 4 5 3
```

---

# 📌 Summary Table

| Approach         | Uses Stack?  | Uses Recursion? | Time | Space  |
| ---------------- | ------------ |-----------------| ---- |--------|
| Simple Iterative | ✅ Yes       | ❌ No           | O(n) | O(n)   |
| Better Iterative | ✅ Yes (less)| ❌ No           | O(n) | O(n)   |
| Morris Traversal | ❌ No        | ❌ No           | O(n) | ⭐O(1) | 

---

# 🎯 Key Takeaways for Beginners

* Preorder = **Root → Left → Right**
* Stack helps us simulate recursion manually.
* Morris Traversal is the most efficient because it uses **no extra space**.
* All methods produce the same traversal result—only the approach differs.

---

