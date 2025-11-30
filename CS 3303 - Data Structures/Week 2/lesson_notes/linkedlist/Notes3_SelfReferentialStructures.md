
---

# Self-Referential Structures

Self-referential structures are structures (or classes) that contain **one or more references/pointers to the same type of structure** as one of their members.

In other words, if a structure contains a field that references another structure of the same type, it is **self-referential**.

---

## Example

```java
// Define the 'Node' class
class Node {
    // Data members
    int data1;
    int data2;

    // Reference to another Node object (self-referential)
    Node link;

    // Constructor to initialize values
    public Node(int data1, int data2) {
        this.data1 = data1;
        this.data2 = data2;
        this.link = null;
    }

    // Default constructor
    public Node() {
        this.data1 = 0;
        this.data2 = 0;
        this.link = null;
    }
}

// Main class to demonstrate Node creation
public class Main {
    public static void main(String[] args) {
        // Create a Node using the default constructor
        Node ob = new Node();

        // Print values to verify
        System.out.println("Data1: " + ob.data1 + ", Data2: " + ob.data2);
    }
}
```

**Output**

```
Data1: 0, Data2: 0
```

In the example above, the field `link` is a reference to another object of type `Node`. Therefore, the class `Node` is a **self-referential structure**.
A very important point is that these references should be properly initialized before accessing them; otherwise, they may contain null.

---

# Types of Self-Referential Structures

1. **Self-Referential Structure with a Single Link**
2. **Self-Referential Structure with Multiple Links**

---

# 1. Self-Referential Structure with a Single Link

These structures contain **one self-pointer/reference**.
The following example shows how to link objects together and access their data.

---

## Implementation (Single Link)

```java
// Java implementation of a self-referential structure with a single link
public class Main {
    // Node structure with one link
    static class Node {
        int data1;
        int data2;
        Node link;
    }

    public static void main(String[] args) {
        Node ob1 = new Node(); // Node 1

        // Initialization
        ob1.link = null;
        ob1.data1 = 10;
        ob1.data2 = 20;

        Node ob2 = new Node(); // Node 2

        // Initialization
        ob2.link = null;
        ob2.data1 = 30;
        ob2.data2 = 40;

        // Link ob1 → ob2
        ob1.link = ob2;

        // Accessing data members of ob2 using ob1
        System.out.println(ob1.link.data1);
        System.out.println(ob1.link.data2);
    }
}
```

**Output**

```
30
40
```

---

# 2. Self-Referential Structure with Multiple Links

Such structures contain **two or more self-references**, allowing complex data structures to be created easily (e.g., doubly linked lists, trees, graphs).

The following example demonstrates a Node with both a `next_link` and a `prev_link`.

---

## Implementation (Multiple Links)

```java
public class Main {
    public static void main(String[] args) {
        // Create nodes
        Node ob1 = new Node(); // Node1
        Node ob2 = new Node(); // Node2
        Node ob3 = new Node(); // Node3

        // Initialize data
        ob1.data = 10;
        ob2.data = 20;
        ob3.data = 30;

        // Set forward links
        ob1.next_link = ob2;
        ob2.next_link = ob3;

        // Set backward links
        ob2.prev_link = ob1;
        ob3.prev_link = ob2;

        // Accessing data using ob1
        System.out.println(
            ob1.data + "\t" +
            ob1.next_link.data + "\t" +
            ob1.next_link.next_link.data
        );

        // Accessing data using ob2
        System.out.println(
            ob2.prev_link.data + "\t" +
            ob2.data + "\t" +
            ob2.next_link.data
        );

        // Accessing data using ob3
        System.out.println(
            ob3.prev_link.prev_link.data + "\t" +
            ob3.prev_link.data + "\t" +
            ob3.data
        );
    }
}

// Node structure with two self-references
class Node {
    int data;
    Node prev_link;
    Node next_link;
}
```

**Output**

```
10    20    30
10    20    30
10    20    30
```

In this example, the three Node objects (`ob1`, `ob2`, and `ob3`) are interconnected in such a way that each can access the others’ data through their forward and backward links.
This demonstrates the flexibility and power of self-referential structures.

---

# Applications of Self-Referential Structures

Self-referential structures are foundational to many complex data structures, including:

* Linked Lists
* Stacks
* Queues
* Trees
* Graphs
* And many more

They make it possible for one object to dynamically reference others, enabling flexible and powerful ways to organize data.

---

