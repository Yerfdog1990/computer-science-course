
---

# ⭐ Lesson Notes: Postorder Traversal of a Binary Tree

**Last Updated: 07 Oct, 2025**

## 1. What Is Postorder Traversal?

Postorder Traversal is a tree-traversal method that follows the order:

### **Left → Right → Root**

This means for every node in the binary tree:

1. **Traverse the left subtree first**
2. **Traverse the right subtree next**
3. **Visit the node itself last**

This approach is especially useful when the root must be processed **after** its subtrees.

---

## 2. Why Do We Use Postorder Traversal?

* Used to **delete a tree safely**, because children are removed before the parent.
* Used in **expression trees** to generate **postfix** notation.
* Useful whenever you need a bottom-up evaluation.

---

## 3. Example 1

**Input Tree:**

```
    1
   / \
  2   3
```

**Output:**
`[2, 3, 1]`

**Explanation:**
Postorder follows **Left → Right → Root**:

* Left → **2**
* Right → **3**
* Root → **1**

---

## 4. Example 2

```
        1
      /   \
     2     3
    / \     \
   4   5     6
```

**Output:**
`[4, 5, 2, 6, 3, 1]`

**Explanation:**
Traverse from bottom to top:

Left → 4, 5, 2
Right → 6, 3
Root → 1

---

## 5. How Does Postorder Traversal Work?

The recursive idea is:

1. Go all the way into the *left* subtree
2. Then go into the *right* subtree
3. When both children are done, **visit the node**

This gives a bottom-up order.

---
![img_22.png](img_22.png)
---
![img_23.png](img_23.png)
---
![img_24.png](img_24.png)
---
![img_25.png](img_25.png)
---
![img_26.png](img_26.png)
---
![img_27.png](img_27.png)
---

## 6. Code Example (Using the Code You Provided)

```java
import java.util.ArrayList;

// Node Structure
class Node {
    int data;
    Node left;
    Node right;

    Node(int v) {
        data = v;
        left = null;
        right = null;
    }
}

public class GFG {
    static void postOrder(Node node, ArrayList<Integer> res) {
        if (node == null)
            return;

        // First we traverse left subtree
        postOrder(node.left, res);

        // After visiting left, traverse right subtree
        postOrder(node.right, res);

        // now we visit node
        res.add(node.data);
    }

    public static void main(String[] args) {

        //Represent Tree
        //       1
        //      / \
        //     2   3
        //    / \   \
        //   4   5   6
        Node root = new Node(1);
        root.left = new Node(2);
        root.right = new Node(3);
        root.left.left = new Node(4);
        root.left.right = new Node(5);
        root.right.right = new Node(6);

        ArrayList<Integer> result = new ArrayList<>();
        postOrder(root, result);

        // Print the postorder
        for (int val : result)
            System.out.print(val + " ");
    }
}
```

### **Output**

```
4 5 2 6 3 1
```

---

## 7. Time & Space Complexity

### ⏱ Time Complexity: **O(n)**

We visit each of the `n` nodes exactly once.

### 💾 Auxiliary Space: **O(h)**

Where `h` = height of the tree.

* **Worst case:** `h = n` → skewed tree
* **Best case:** `h = log n` → complete tree

---

## 8. Key Properties to Remember

* Order is **Left → Right → Root**
* Very useful for **tree deletion** (subtrees first, then node)
* Generates **postfix expressions** from expression trees
* Processes the root **last**, making it a bottom-up traversal

---

# **Iterative Postorder Traversal (Using Two Stacks)**

## ✅ **What is Postorder Traversal?**

Postorder Traversal is one of the common ways to traverse a binary tree.
The order is:

👉 **Left → Right → Root**

This means:

1. Visit the **left subtree**
2. Visit the **right subtree**
3. Visit the **root node**

Example:

Input Tree:

```
    1
   / \
  2   3
 / \
4   5
```

Output (Postorder):
👉 **4 5 2 3 1**

---

# ⭐ Goal

We want to perform **Postorder Traversal without using recursion**, using an **Iterative** method.

To do this, we use **two stacks**.

---

# 📌 Why Two Stacks?

Postorder requires visiting children before the root, but a stack naturally works in reverse order (LIFO).

Two stacks help us:

1. **Stack 1** = used to process nodes
2. **Stack 2** = stores nodes in **reverse postorder**
3. When Stack 2 is popped, the output becomes **Left → Right → Root**

---

# 📘 Approach (Step-by-Step)

### ✔ Step 1: Push the root to Stack 1

### ✔ Step 2: While Stack 1 is not empty

* Pop a node from Stack 1
* Push it into Stack 2
* Push its **left child first**, then **right child** into Stack 1
  (This ensures left is processed before right)

### ✔ Step 3: When Stack 1 becomes empty

Pop everything from Stack 2 → this produces correct **postorder output**.

---

# 🖼 Diagram Placeholder

*(Teacher can draw the movement of nodes between the two stacks here)*

---

# 📌 Example Walkthrough

Given tree:

```
      1
     / \
    2   3
   / \
  4   5
```

### Operations:

| Step               | Stack 1 | Stack 2       |
| ------------------ | ------- | ------------- |
| Push 1             | 1       | empty         |
| Pop 1 → push to s2 | 2,3     | 1             |
| Pop 3 → s2         | 2,6,7   | 1,3           |
| Pop 7 → s2         | 2,6     | 1,3,7         |
| Pop 6 → s2         | 2       | 1,3,7,6       |
| Pop 2 → s2         | 4,5     | 1,3,7,6,2     |
| Pop 5 → s2         | 4       | 1,3,7,6,2,5   |
| Pop 4 → s2         | empty   | 1,3,7,6,2,5,4 |

Final Output → **4 5 2 6 3 1**

---

# 💻 Java Implementation

(Using your exact provided code)

```java
// Java program to find the postorder 
// traversal using 2 Stacks
import java.util.ArrayList;
import java.util.Stack;

class Node {
    int data;
    Node left, right;

    Node(int x) {
        data = x;
        left = right = null;
    }
}

class GfG {

    // Function to do post-order traversal 
    // using two stacks
    static ArrayList<Integer> postOrder(Node root) {
        ArrayList<Integer> result = new ArrayList<>();
        if (root == null) {
            return result;
        }

        // Create two stacks
        Stack<Node> stk1 = new Stack<>();
        Stack<Node> stk2 = new Stack<>();

        // Push root to first stack
        stk1.push(root);
        Node curr;

        // Run while first stack is not empty
        while (!stk1.isEmpty()) {

            // Pop from stk1 and push it to stk2
            curr = stk1.pop();
            stk2.push(curr);

            // Push left and right children of 
            // the popped node
            if (curr.left != null) {
                stk1.push(curr.left);
            }
            if (curr.right != null) {
                stk1.push(curr.right);
            }
        }

        // Collect all elements from second stack
        while (!stk2.isEmpty()) {
            curr = stk2.pop();
            result.add(curr.data);
        }

        return result;
    }

    static void printArray(ArrayList<Integer> arr) {
        for (int data : arr) {
            System.out.print(data + " ");
        }
        System.out.println();
    }

    public static void main(String[] args) {

        // Representation of input binary tree:
        //           1
        //         /   \
        //        2     3
        //      /  \
        //     4    5
        Node root = new Node(1);
        root.left = new Node(2);
        root.right = new Node(3);
        root.left.left = new Node(4);
        root.left.right = new Node(5);

        ArrayList<Integer> result = postOrder(root);

        printArray(result);
    }
}
```

### **Output**

```
4 5 2 3 1
```

---

# ⏱ Time & Space Complexity

### **Time Complexity: O(n)**

Every node is processed **twice**:

* once in stack 1
* once in stack 2

### **Auxiliary Space: O(n)**

Two stacks may store all nodes.

---

# ⭐ Key Takeaways

✔ Iterative Postorder traversal can be done using **two stacks**
✔ Stack 1 helps simulate preorder but in reverse
✔ Stack 2 stores the result in true postorder order
✔ Useful when recursion is not allowed
✔ Time: **O(n)**, Space: **O(n)**

---

Below are **complete lesson notes** on **Iterative Postorder Traversal – Set 3 (Using One Stack with States)** using *all* the information you provided.
The notes follow a clean teaching structure: concept → need → algorithm → example → code → complexity.

---

# **Iterative Postorder Traversal | Using One Stack (State-Based Method)**

Postorder traversal of a binary tree follows the order:

### **Left → Right → Root**

Traditionally, postorder traversal is performed using recursion. However, recursion consumes implicit stack space and may not be suitable for very deep trees. To avoid recursion, several iterative approaches exist:

### ✔ Postorder Traversal Variants

1. **Recursive Postorder Traversal**
2. **Iterative Postorder Traversal using Two Stacks**
3. **Iterative Postorder Traversal using One Stack**
4. **Iterative Postorder Traversal using One Stack + States**  ✅ *(This lesson)*

---

# **Why Use a Single Stack + State?**

The two-stack approach works well, but still requires two auxiliary data structures.
This method reduces the traversal to **one stack**, by associating a **processing state** with each pushed node.

Every pushed stack entry is a **(node, state)** pair.

### **State Meaning**

| State | Meaning                  |
| ----- | ------------------------ |
| **0** | Move to the left child   |
| **1** | Move to the right child  |
| **2** | Process (print) the node |

This lets us simulate recursion manually.

---

# **Algorithm (Detailed Steps)**

### **Initialization**

* Push **(root, 0)** onto the stack.
  *(0 = left subtree yet to be processed)*

### **Loop While Stack is Not Empty**

Let `(node = a, state = b)` be the pair on top of the stack.

---

### **Case 1: state = 0 → Go Left**

* Update state to 1
* Push **(node.left, 0)**
* Repeat the loop

---

### **Case 2: state = 1 → Go Right**

* Update state to 2
* Push **(node.right, 0)**
* Repeat the loop

---

### **Case 3: state = 2 → Visit Node**

* Add `node.data` to output
* Pop the node from the stack

---

# **Illustration: Small Example**

Consider this tree:

```
     a
    / \
   b   c
```

### Step-by-step:

1. Push (a,0)
   → stack: (a,0)

2. Top = (a,0)
   Push (a,1), Push (b,0)
   → (b,0), (a,1)

3. Top = (b,0)
   Push (b,1)
   → (b,1), (a,1)

4. Top = (b,1)
   Push (b,2)
   → (b,2), (a,1)

5. Top = (b,2)
   Print **b**
   → stack: (a,1)

6. Top = (a,1)
   Push (a,2), Push (c,0)

7–9. Process c similar to b
→ print **c**

10. Top = (a,2)
    Print **a**

Output: **b c a** (postorder)

---

# **Java Implementation (Using One Stack + State)**

```java
// Java program for iterative postorder traversal
// using one stack with states
import java.util.*;

class Node {
    int data;
    Node left, right;

    Node(int x) {
        data = x;
        left = right = null;
    }
}

public class GfG {

    // Function for iterative post-order traversal using a single stack
    public static ArrayList<Integer> postOrder(Node root) {
        ArrayList<Integer> result = new ArrayList<>();
        if (root == null) {
            return result;
        }

        // Stack to store pairs of (Node, state)
        Stack<Pair> stk = new Stack<>();
        stk.push(new Pair(root, 0));

        while (!stk.isEmpty()) {

            // Get the top element
            Pair topElement = stk.peek();
            Node node = topElement.node;
            int state = topElement.state;

            if (state == 0) {

                // State 0: Go left
                stk.peek().state = 1;
                if (node.left != null) {
                    stk.push(new Pair(node.left, 0));
                }
            }
            else if (state == 1) {

                // State 1: Go right
                stk.peek().state = 2;
                if (node.right != null) {
                    stk.push(new Pair(node.right, 0));
                }
            }
            else {

                // State 2: Process node
                result.add(node.data);
                stk.pop();
            }
        }
        return result;
    }

    static class Pair {
        Node node;
        int state;

        Pair(Node node, int state) {
            this.node = node;
            this.state = state;
        }
    }

    public static void printList(List<Integer> arr) {
        for (int data : arr) {
            System.out.print(data + " ");
        }
        System.out.println();
    }

    public static void main(String[] args) {

        // Example Tree:
        //           1
        //          / \
        //         2   3
        //            / \
        //           4   5
        
        Node root = new Node(1);
        root.left = new Node(2);
        root.right = new Node(3);
        root.right.left = new Node(4);
        root.right.right = new Node(5);

        List<Integer> result = postOrder(root);
        printList(result);
    }
}
```

### **Output**

```
4 5 2 3 1
```

---

# **Time & Space Complexity**

| Complexity      | Explanation                                |
| --------------- | ------------------------------------------ |
| **Time: O(n)**  | Each node is added/processed exactly once  |
| **Space: O(n)** | Stack may hold up to n (node, state) pairs |

---

# **Key Takeaways**

* Uses only **one stack** with controlled state transitions.
* Simulates recursion by tracking node processing status.
* Very systematic and avoids duplication of work.
* More memory efficient than two-stack approach.
* Works for all binary trees.

---
