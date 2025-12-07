
---

# ⭐ Lesson Notes: Inorder Traversal of a Binary Tree

**Last Updated: 07 Oct, 2025**

## 1. What Is Inorder Traversal?

Inorder Traversal is a method of visiting all the nodes in a **binary tree** in a specific order.
The order is very simple:

### **Left → Root → Right**

This means:

>1. First, **traverse the left subtree**
>2. Then, **visit the root node**
>3. Finally, **traverse the right subtree**

This rule is applied **recursively** to every node.

---

## 2. Why Do We Use Inorder Traversal?

* It helps us process nodes in a **hierarchical sequence**
* It is very useful for **Binary Search Trees (BSTs)** because:

  👉 **Inorder traversal of a BST gives the elements in sorted order**

It is also used in:
>* Expression trees
>* Tree-based algorithms
>* Anytime you need ordered processing of data stored in a tree

---

## 3. Example 1

**Input:**
Tree:

```
    1
   / \
  2   3
```

**Output:**
`[2, 1, 3]`

**Explanation:**
Follow Left → Root → Right

* Visit left node → **2**
* Visit root → **1**
* Visit right node → **3**

---

## 4. Example 2

**Input Tree:**

```
        1
      /   \
     2     3
    / \     \
   4   5     6
```

**Output:**
`[4, 2, 5, 1, 3, 6]`

**Explanation (Left → Root → Right):**
Visit in this order: **4 → 2 → 5 → 1 → 3 → 6**

This matches exactly with the required inorder output.

---
## 5. How Does Inorder Traversal Work?

### The recursive idea:

1. If the current node is `null`, just return
2. Recursively traverse the **left subtree**
3. Visit (process) the **current node**
4. Recursively traverse the **right subtree**
---
![img_10.png](img_10.png)
---
![img_11.png](img_11.png)
---
![img_12.png](img_12.png)
---
![img_13.png](img_13.png)
---
![img_14.png](img_14.png)
---
![img_15.png](img_15.png)
---

## 6. Code Example (Using the Provided Code)

```java
import java.util.ArrayList;

//Node Structure
class Node {
    int data;
    Node left;
    Node right;

    Node(int x) {
        data = x;
        left = right = null;
    }
}

public class GFG {

    static void inOrder(Node node, ArrayList<Integer> res) {
        if (node == null)
            return;
        
        // Traverse the left subtree first
        inOrder(node.left, res);
        
        // Visit the current node
        res.add(node.data);
        
        // Traverse the right subtree last
        inOrder(node.right, res);
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

        ArrayList<Integer> res = new ArrayList<>();
        inOrder(root, res);

        for(int node : res)
            System.out.print(node + " ");
    }
}
```

### **Output**

```
4 2 5 1 3 6
```

---

## 7. Time & Space Complexity

### ⏱ Time Complexity: **O(n)**

Because you must visit all `n` nodes exactly once.

### 💾 Auxiliary Space: **O(h)**

`h` = height of the tree

* **Worst case:** h = n (skewed tree)
* **Best case:** h = log n (complete tree)

---

## 8. Key Properties to Remember

* Visits nodes in **Left → Root → Right** order
* For a **Binary Search Tree**, the output is **sorted**
* Uses **recursion** naturally
* Useful for **expression trees, BST operations, and sequential processing**

---
Below are **beginner-friendly lesson notes** on **Inorder Tree Traversal without Recursion**, using **every piece of information you provided**, rewritten clearly and in order.

---

# 🌳 **Inorder Tree Traversal Without Recursion**
---

## ✅ **What is Inorder Traversal?**

Inorder traversal of a binary tree means visiting nodes in the order:

👉 **Left → Root → Right**

Example:
For this tree:

```
      1
    /   \
   2     3
  / \
 4   5
```

The **inorder output** is:

➡️ **4 2 5 1 3**

---

## ⭐ Why Do We Need Inorder Traversal *Without* Recursion?

Usually, inorder traversal is done using recursion. But recursion uses the system stack.

Sometimes we want to:

* Avoid recursion
* Control the traversal manually
* Use **iterative** solutions for efficiency

There are **two main techniques**:

---

# 🟦 **1. Naive Approach — Using a Stack (O(n) Time, O(h) Space)**

This approach simulates recursion using your own **stack**.

### 🔍 **How it Works**

1. Start from the root
2. Keep pushing nodes while moving to the **left**
3. When you reach NULL:

    * Pop a node
    * Print it
    * Move to its **right child**
4. Repeat until the stack is empty and current = null

---

## 📘 **Illustration of Steps**

Given the tree:

```
      1
    /   \
   2     3
  / \
 4   5
```

Let’s walk through it:

* Start at root → push **1**
* Move left → push **2**
* Move left → push **4**
* Left of 4 is NULL → pop → print **4**
* Back to node 2 → pop → print **2**
* Move right → node **5** → push → pop → print **5**
* Back to 1 → pop → print **1**
* Move right → node **3** → push → pop → print **3**

Final Output:
➡️ **4 2 5 1 3**

---

## 🧪 **Java Code (Stack Approach)**

```java
// Java program to print inorder traversal using stack.
import java.util.ArrayList;
import java.util.Stack;

class Node {
    int data;
    Node left, right;

    Node(int x) {
        data = x;
        left = null;
        right = null;
    }
}

class GfG {

    // Iterative function for inorder tree traversal
    static ArrayList<Integer> inOrder(Node root) {
        ArrayList<Integer> ans = new ArrayList<>();
        Stack<Node> s = new Stack<>();
        Node curr = root;

        while (curr != null || !s.isEmpty()) {

            // Reach the left most Node of the curr Node
            while (curr != null) {

                // Place pointer to a tree node on
                // the stack before traversing
                // the node's left subtree
                s.push(curr);
                curr = curr.left;
            }

            // Current must be NULL at this point
            curr = s.pop();
            ans.add(curr.data);

            // we have visited the node and its
            // left subtree. Now, it's right
            // subtree's turn
            curr = curr.right;
        }

        return ans;
    }

    static void printList(ArrayList<Integer> v) {
        for (int i : v) {
            System.out.print(i + " ");
        }
        System.out.println();
    }

    public static void main(String[] args) {
        
        // Constructed binary tree is
        //          1
        //        /   \
        //      2      3
        //    /  \
        //  4     5
        Node root = new Node(1);
        root.left = new Node(2);
        root.right = new Node(3);
        root.left.left = new Node(4);
        root.left.right = new Node(5);

        ArrayList<Integer> res = inOrder(root);
        printList(res);
    }
}
```

### ✔ Output

```
4 2 5 1 3
```

---

# 🟩 **2. Expected Approach — Using Morris Traversal (O(n) Time, O(1) Space)**

This algorithm performs inorder traversal **without stack and without recursion**.

### 🍃 Key Ideas:

* Uses the concept of **Threaded Binary Trees**
* Temporarily creates links to the **inorder successor**
* Restores the tree after traversal
* Only constant space is used → **O(1)**

### ✔ You do NOT need to memorize internals yet.

Just know it avoids recursion and avoids using a stack.

You can read the full code (not included in this section) from:
➡️ *Inorder Tree Traversal without recursion and without stack!*

---

# 📊 **Complexity Summary**

| Approach                    | Time Complexity | Space Complexity | Notes                                          |
| --------------------------- | --------------- | ---------------- | ---------------------------------------------- |
| Stack (Naive)               | O(n)            | O(h)             | Easy to understand, beginner friendly          |
| Morris Traversal (Expected) | O(n)            | O(1)             | Most efficient, modifies tree during traversal |

---

# 📝 **Final Notes**

* For beginners → **start with the stack approach**
* For interviews → **know Morris traversal**
* Inorder is always → **Left → Root → Right**


