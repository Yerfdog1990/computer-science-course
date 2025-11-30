
---

# 📘 **Lesson Notes: Introduction to Stack Data Structure**

**Last Updated: 10 Sep, 2025**

---

# 📌 1. Introduction to Stack

A **Stack** is a **linear data structure** that follows the **LIFO (Last In, First Out)** principle.

✔ The **last element inserted** is the **first one removed**.
✔ All operations (push, pop) happen at **one end**, called the **top**.

---

# 📌 2. LIFO Principle Explained

**LIFO — Last In, First Out**

* New elements are always **pushed** onto the **top** of the stack.
* Removing (popping) always happens from the **top**.
* Thus, the order of operations is strictly:

```
Last In → First Out
```

### 🔍 Real-world analogies

* **Stack of plates**: The last plate placed on the pile is the first you pick up.
* **Shuttlecock tube**: You insert and remove shuttlecocks from the same end.

---

# 📌 3. Basic Stack Terminology

| Term           | Meaning                                             |
| -------------- | --------------------------------------------------- |
| **Top**        | Refers to the most recently added element.          |
| **Size**       | Number of elements currently in the stack.          |
| **Push**       | Insert element at the top.                          |
| **Pop**        | Remove and return the top element.                  |
| **Peek (Top)** | Return the current top element without removing it. |
| **isEmpty**    | Check whether the stack contains elements.          |

---

# 📌 4. Types of Stacks

## **1. Fixed-Size Stack**

* Predefined capacity.
* Cannot grow beyond that limit.
* Causes **overflow** if more elements are added.
* Causes **underflow** if popping from an empty stack.
* Usually implemented using **static arrays**.

Example:

```java
int[] stack = new int[10];
```

---

## **2. Dynamic Stack**

* Grows or shrinks automatically.
* No overflow unless memory is exhausted.
* Implemented using:

    * **Linked List**
    * **Resizable arrays (ArrayList in Java)**

🔥 Dynamic stacks are preferred in real applications.

---

# 📌 5. Common Operations on Stack

| Operation          | Description                       |
| ------------------ | --------------------------------- |
| **push(x)**        | Insert element `x` on top         |
| **pop()**          | Remove and return the top element |
| **peek() / top()** | View the top element              |
| **isEmpty()**      | Check if stack is empty           |
| **size()**         | Get number of elements            |

---

# 📌 6. Stack Implementation Methods

Stacks can be implemented using:

✔ Arrays
✔ Linked Lists
✔ Java’s built-in `Deque` (recommended in real-world Java)

---

# ⭐ 7. **Stack Implementation in Java**

---

# 📌 A. **Stack Implementation Using Array (Fixed Size)**

```java
public class ArrayStack {
    private int[] stack;
    private int top;
    private int capacity;

    public ArrayStack(int size) {
        stack = new int[size];
        capacity = size;
        top = -1;
    }

    // Push operation
    public void push(int value) {
        if (top == capacity - 1) {
            throw new RuntimeException("Stack Overflow");
        }
        stack[++top] = value;
    }

    // Pop operation
    public int pop() {
        if (isEmpty()) {
            throw new RuntimeException("Stack Underflow");
        }
        return stack[top--];
    }

    // Peek operation
    public int peek() {
        if (isEmpty()) {
            throw new RuntimeException("Stack is empty");
        }
        return stack[top];
    }

    // Check empty
    public boolean isEmpty() {
        return top == -1;
    }

    // Size of stack
    public int size() {
        return top + 1;
    }
}
```

### Example Usage

```java
public class Main {
    public static void main(String[] args) {
        ArrayStack stack = new ArrayStack(5);
        stack.push(10);
        stack.push(20);
        stack.push(30);

        System.out.println("Peek: " + stack.peek());
        System.out.println("Pop: " + stack.pop());
        System.out.println("Size: " + stack.size());
    }
}
```

---

# 📌 B. **Stack Implementation Using Linked List (Dynamic Size)**

```java
class Node {
    int value;
    Node next;

    Node(int value) {
        this.value = value;
    }
}

public class LinkedListStack {
    private Node head; // top
    private int size;

    public LinkedListStack() {
        head = null;
        size = 0;
    }

    // Push
    public void push(int value) {
        Node newNode = new Node(value);
        newNode.next = head;
        head = newNode;
        size++;
    }

    // Pop
    public int pop() {
        if (isEmpty()) {
            throw new RuntimeException("Stack is empty");
        }
        int value = head.value;
        head = head.next;
        size--;
        return value;
    }

    // Peek
    public int peek() {
        if (isEmpty()) {
            throw new RuntimeException("Stack is empty");
        }
        return head.value;
    }

    // Check empty
    public boolean isEmpty() {
        return head == null;
    }

    public int size() {
        return size;
    }
}
```

### Example Usage

```java
public class Main {
    public static void main(String[] args) {
        LinkedListStack stack = new LinkedListStack();
        stack.push(5);
        stack.push(10);
        stack.push(15);

        System.out.println("Pop: " + stack.pop());
        System.out.println("Peek: " + stack.peek());
        System.out.println("Size: " + stack.size());
    }
}
```

---

# 📌 C. **Stack Using Java’s Built-in Deque (Recommended)**

Java’s own `Stack` class is **legacy**.
Modern Java uses **Deque** for stack operations.

```java
import java.util.ArrayDeque;
import java.util.Deque;

public class DequeStackExample {
    public static void main(String[] args) {
        Deque<Integer> stack = new ArrayDeque<>();

        stack.push(100);
        stack.push(200);
        stack.push(300);

        System.out.println("Peek: " + stack.peek());
        System.out.println("Pop: " + stack.pop());
        System.out.println("Size: " + stack.size());
    }
}
```

👍 Fast
👍 No overflow
👍 Clean API
👍 Production-grade

---

# 📌 8. Applications of Stack

Stacks are widely used in:

### **1. Undo/Redo mechanisms**

* Editors (VS Code, MS Word)
* Browsers back/forward navigation

### **2. Expression evaluation**

* Infix → Postfix conversion
* Evaluating postfix expressions

### **3. Parsing**

* XML/HTML tag matching
* Syntax parsing in compilers

### **4. Algorithms**

* Depth-First Search (DFS)
* Backtracking
* Balanced parentheses checking

---

# 📌 9. Advantages & Disadvantages

### ✔ Advantages

* Simple to implement
* Efficient O(1) operations
* Useful in backtracking and recursion

### ✘ Disadvantages

* Limited direct access (only top is accessible)
* Fixed size stacks suffer from overflow

---

# 📌 10. Exercises (with Answers)

### **Exercise 1**

A stack contains the following elements:

```
Top → [ C, B, A ]
```

What does `peek()` return?

✔ **Answer: C**

---

### **Exercise 2**

What is the time complexity of push() and pop()?

✔ **O(1)**

---

### **Exercise 3**

What happens if we call pop() on an empty stack?

✔ **Underflow error / Exception**

---

### **Exercise 4**

Which data structure naturally supports recursion?

✔ **Stack**

---

