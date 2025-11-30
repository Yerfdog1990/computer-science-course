
---

# **DSA Linked Lists Operations**

## **Linked List Operations**

Basic things we can do with linked lists are:

* **Traversal**
* **Remove a node**
* **Insert a node**
* **Sort**

For simplicity, **singly linked lists** will be used to explain these operations below.

---

# **Traversal of a Linked List**

Traversing a linked list means to go through the linked list by following the links from one node to the next.

![Screenshot 2568-11-26 at 06.03.20.png](Screenshot%202568-11-26%20at%2006.03.20.png)

Traversal is typically done to:

* search for a specific node
* read or modify a node’s content
* remove a node
* insert a node before or after a specific node

To traverse a singly linked list, start with the **head node**, then follow each node’s `next` pointer until the next address is `null`.

### **Animation Concept**

```
Head
7 -> 11 -> 3 -> 2 -> 9 -> null
Traverse →
```

### **Example — Traversal of a singly linked list in Java**

```java
class Node {
    int data;
    Node next;

    Node(int data) {
        this.data = data;
        this.next = null;
    }
}

public class LinkedListTraversal {
    public static void traverseAndPrint(Node head) {
        Node currentNode = head;
        while (currentNode != null) {
            System.out.print(currentNode.data + " -> ");
            currentNode = currentNode.next;
        }
        System.out.println("null");
    }

    public static void main(String[] args) {
        Node node1 = new Node(7);
        Node node2 = new Node(11);
        Node node3 = new Node(3);
        Node node4 = new Node(2);
        Node node5 = new Node(9);

        node1.next = node2;
        node2.next = node3;
        node3.next = node4;
        node4.next = node5;

        traverseAndPrint(node1);
    }
}
```

---

# **Find the Lowest Value in a Linked List**

To find the lowest value, we must **traverse the list** and compare each node’s value to the current lowest value found.

Same principle as finding the lowest value in an array, but here we must **follow the next link** manually.

![Screenshot 2568-11-26 at 06.03.41.png](Screenshot%202568-11-26%20at%2006.03.41.png)

### **Animation Concept**

```
Head
7 -> 11 -> 3 -> 2 -> 9 -> null
Lowest value: 2
```

We start with the value in the head node as the initial lowest value.

### **Example — Finding the lowest value in Java**

```java
class Node {
    int data;
    Node next;

    Node(int data) {
        this.data = data;
        this.next = null;
    }
}

public class FindLowestValue {
    public static int findLowestValue(Node head) {
        int minValue = head.data;          // initial lowest value
        Node currentNode = head.next;

        while (currentNode != null) {
            if (currentNode.data < minValue) {
                minValue = currentNode.data;
            }
            currentNode = currentNode.next;
        }
        return minValue;
    }

    public static void main(String[] args) {
        Node node1 = new Node(7);
        Node node2 = new Node(11);
        Node node3 = new Node(3);
        Node node4 = new Node(2);
        Node node5 = new Node(9);

        node1.next = node2;
        node2.next = node3;
        node3.next = node4;
        node4.next = node5;

        System.out.println("The lowest value in the linked list is: " +
                           findLowestValue(node1));
    }
}
```

**Explanation:**
The initial lowest value is set to the value of the first node. Then, if a lower value is found, the lowest value variable is updated.

---

# **Delete a Node in a Linked List**

Here we have a reference to the specific node we want to delete.

![Screenshot 2568-11-26 at 06.04.12.png](Screenshot%202568-11-26%20at%2006.04.12.png)

Before deleting the node, we must:

1. Traverse the list to find the **previous node**
2. Connect the previous node to the node **after** the one being deleted
3. Delete the node

Because singly linked lists have **no backward links**, we must start from the head to find the previous node.

### **Animation Concept**

```
Head
7 -> 11 -> 3 -> 2 -> 9 -> null
Delete "2"
```

### **Example — Deleting a specific node in Java**

```java
class Node {
    int data;
    Node next;

    Node(int data) {
        this.data = data;
        this.next = null;
    }
}

public class DeleteNode {
    public static void traverseAndPrint(Node head) {
        Node currentNode = head;
        while (currentNode != null) {
            System.out.print(currentNode.data + " -> ");
            currentNode = currentNode.next;
        }
        System.out.println("null");
    }

    public static Node deleteSpecificNode(Node head, Node nodeToDelete) {
        if (head == nodeToDelete) {
            return head.next;
        }

        Node currentNode = head;
        while (currentNode.next != null && currentNode.next != nodeToDelete) {
            currentNode = currentNode.next;
        }

        if (currentNode.next == null) {
            return head;
        }

        currentNode.next = currentNode.next.next; // connect around the deleted node

        return head;
    }

    public static void main(String[] args) {
        Node node1 = new Node(7);
        Node node2 = new Node(11);
        Node node3 = new Node(3);
        Node node4 = new Node(2);
        Node node5 = new Node(9);

        node1.next = node2;
        node2.next = node3;
        node3.next = node4;
        node4.next = node5;

        System.out.println("Before deletion:");
        traverseAndPrint(node1);

        node1 = deleteSpecificNode(node1, node4); // delete node 2

        System.out.println("\nAfter deletion:");
        traverseAndPrint(node1);
    }
}
```

**Explanation:**
If the node to delete is the head, the new head becomes the next node. Otherwise, we link around the deleted node.

---

# **Insert a Node in a Linked List**

Inserting a node is similar to deleting a node because we must update the next pointers carefully.

![Screenshot 2568-11-26 at 06.04.34.png](Screenshot%202568-11-26%20at%2006.04.34.png)

To insert a node:

1. Create the new node
2. Traverse to the desired position
3. Adjust pointers

    * previous node → new node
    * new node → next node

### **Animation Concept**

```
Head
7 -> 97 -> 3 -> 2 -> 9 -> null

Insert 97 at position 2:
1. New node created
2. Node 7 links to new node
3. New node links to node 3
```

### **Example — Inserting a node in Java**

```java
class Node {
    int data;
    Node next;

    Node(int data) {
        this.data = data;
        this.next = null;
    }
}

public class InsertNode {
    public static void traverseAndPrint(Node head) {
        Node currentNode = head;
        while (currentNode != null) {
            System.out.print(currentNode.data + " -> ");
            currentNode = currentNode.next;
        }
        System.out.println("null");
    }

    public static Node insertNodeAtPosition(Node head, Node newNode, int position) {
        if (position == 1) {
            newNode.next = head;
            return newNode;
        }

        Node currentNode = head;
        for (int i = 0; i < position - 2; i++) {
            if (currentNode == null) {
                break;
            }
            currentNode = currentNode.next;
        }

        newNode.next = currentNode.next;
        currentNode.next = newNode;

        return head;
    }

    public static void main(String[] args) {
        Node node1 = new Node(7);
        Node node2 = new Node(3);
        Node node3 = new Node(2);
        Node node4 = new Node(9);

        node1.next = node2;
        node2.next = node3;
        node3.next = node4;

        System.out.println("Original list:");
        traverseAndPrint(node1);

        Node newNode = new Node(97);
        node1 = insertNodeAtPosition(node1, newNode, 2);

        System.out.println("\nAfter insertion:");
        traverseAndPrint(node1);
    }
}
```

**Explanation:**
The return value is the new head. If the node is inserted at the start, the new node becomes the head.

---

# **Other Linked List Operations**

We have covered:

* traversal
* deletion
* insertion

Other operations include sorting.

Sorting algorithms like **Selection Sort** can be applied because they do not depend on array indexing.

But algorithms like:

* Counting Sort
* Radix Sort
* Quicksort

cannot be used directly because linked lists cannot access elements by index.

---

# **Linked Lists vs Arrays**

Key differences:

* Linked lists are **not fixed-size**; arrays are.
* Linked list nodes are not stored **contiguously** in memory.
* Linked lists require **extra memory** for pointers.
* Linked list operations require more code.
* Arrays allow direct access: `arr[5]`, linked lists require traversal.

---

# **Time Complexity of Linked List Operations**

* Traversal: **O(n)**
* Search: **O(n)**
* Insertion at a known location: **O(1)**
* Deletion at a known location: **O(1)**
* Sorting: same time complexities as arrays (e.g., selection sort O(n²))

Binary search **cannot** be used because we cannot jump to the middle of a linked list.

---

# **DSA Exercise**

**Complete the code for the Linked List traversal function.**

```python
def traverseAndPrint(head):
    currentNode = 

    while currentNode:
        print(currentNode.data, end=" -> ")
        currentNode = currentNode.

    print("null")
```

If you want, I can also convert the **exercise** into Java format.

---

