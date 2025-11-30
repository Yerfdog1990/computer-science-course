
---

# #️⃣ **Lesson Notes: Queue Data Structure**

A **Queue** is a **linear data structure** that follows the **FIFO (First In, First Out)** principle.

👉 **FIFO means:**

* The element inserted **first** is the element that comes **out first**.
* Insertions happen at the **rear**, and deletions occur at the **front**.

Example analogy:
A line at a ticket counter—people leave in the same order they joined.

---

# ## ✳️ 1. Queue Characteristics

* Ordered list of elements.
* Insertion → **rear end** (enqueue)
* Deletion → **front end** (dequeue)
* Cannot insert if queue is **full** → **Overflow**
* Cannot delete if queue is **empty** → **Underflow**

---

# ## ✳️ 2. Queue Operations

### ### 🔹 **Primary Operations**

| Operation    | Meaning                                  | Time Complexity |
| ------------ | ---------------------------------------- | --------------- |
| `enqueue(x)` | Insert element `x` at the rear           | **O(1)**        |
| `dequeue()`  | Remove and return element from the front | **O(1)**        |

### ### 🔹 **Auxiliary Operations**

| Operation   | Meaning                             | Time Complexity |
| ----------- | ----------------------------------- | --------------- |
| `front()`   | View front element without removing | **O(1)**        |
| `rear()`    | View rear element without removing  | **O(1)**        |
| `isEmpty()` | Check if queue is empty             | **O(1)**        |
| `size()`    | Return number of elements           | **O(1)**        |

---

# ## ✳️ 3. Queue Types

### ### 1️⃣ **Simple (Linear) Queue**

* Basic FIFO queue
* Insert → rear
* Delete → front

### ### 2️⃣ **Circular Queue**

* Efficient array-based queue
* Last position connects back to first
* Better space utilization
* Prevents wasted empty slots

### ### 3️⃣ **Priority Queue**

* Elements are dequeued based on **priority**, not arrival time
* Priority may be:

    * Highest value first, or
    * Lowest value first

### ### 4️⃣ **Deque (Double-Ended Queue)**

* Insert/delete from **both ends**
* May not follow strict FIFO

---

# ## ✳️ 4. Queue Implementations

| Implementation                    | Description                                  |
| --------------------------------- | -------------------------------------------- |
| **Array (Sequential Allocation)** | Fixed size, simple, may waste space          |
| **Linked List Allocation**        | Dynamic size, no overflow unless memory ends |
| **Java Collections Framework**    | Built-in `Queue` and `Deque`                 |

---

# ## ✳️ 5. Applications of Queue

### **Computer Science**

* CPU scheduling
* Disk scheduling
* BFS (Breadth-First Search)
* Memory management
* Task/job scheduling

### **Networking**

* Packet buffering
* Routers/switches queues
* Message queues

### **Real-World**

* ATM lines
* Ticket counters
* Customer support call queues
* Waiting lines in general

---

# ## ✳️ 6. Advantages of Queue

* Efficient for **ordered processing**
* Easy to implement insertion/deletion
* Ideal when multiple consumers use the same resource
* Used in many OS and networking algorithms
* Fast inter-process communication

---

# ## ✳️ 7. Disadvantages of Queue

* Middle element insertion/deletion is **costly**
* Searching takes **O(n)**
* Using arrays: fixed size is a limitation
* Classical queue wastes space after many dequeues (fixed array)

---

# #️⃣ **8. Java Code Examples**

---

# ## ✔️ **Example 1: Simple Queue Using Java Collections (`LinkedList`)**

### **Enqueue, Dequeue, Front, Rear, Size**

```java
import java.util.LinkedList;
import java.util.Queue;

public class SimpleQueueExample {
    public static void main(String[] args) {

        Queue<Integer> queue = new LinkedList<>();

        // Enqueue
        queue.add(10);
        queue.add(20);
        queue.add(30);

        System.out.println("Queue: " + queue);

        // Dequeue
        int removed = queue.poll();
        System.out.println("Dequeued: " + removed);

        // Front element
        System.out.println("Front: " + queue.peek());

        // Rear element (requires LinkedList cast)
        System.out.println("Rear: " + ((LinkedList<Integer>) queue).getLast());

        // Size
        System.out.println("Size: " + queue.size());

        // Empty check
        System.out.println("Is empty? " + queue.isEmpty());
    }
}
```

### **Output**

```
Queue: [10, 20, 30]
Dequeued: 10
Front: 20
Rear: 30
Size: 2
Is empty? false
```

---

# ## ✔️ **Example 2: Queue Using Array (Manual Implementation)**

```java
class ArrayQueue {
    private int[] arr;
    private int front, rear, capacity, size;

    public ArrayQueue(int capacity) {
        this.capacity = capacity;
        arr = new int[capacity];
        front = 0;
        rear = -1;
        size = 0;
    }

    // Enqueue
    public void enqueue(int data) {
        if (size == capacity) {
            System.out.println("Overflow!");
            return;
        }
        rear = (rear + 1) % capacity;
        arr[rear] = data;
        size++;
    }

    // Dequeue
    public int dequeue() {
        if (size == 0) {
            System.out.println("Underflow!");
            return -1;
        }
        int temp = arr[front];
        front = (front + 1) % capacity;
        size--;
        return temp;
    }

    public int front() {
        return (size == 0) ? -1 : arr[front];
    }

    public int rear() {
        return (size == 0) ? -1 : arr[rear];
    }

    public int size() { return size; }
    public boolean isEmpty() { return size == 0; }
}

public class Test {
    public static void main(String[] args) {
        ArrayQueue q = new ArrayQueue(5);

        q.enqueue(10);
        q.enqueue(20);
        q.enqueue(30);

        System.out.println("Dequeued: " + q.dequeue());
        System.out.println("Front: " + q.front());
        System.out.println("Rear: " + q.rear());
        System.out.println("Size: " + q.size());
    }
}
```

---

# ## ✔️ **Example 3: Queue Using Linked List (Manual Implementation)**

```java
class Node {
    int data;
    Node next;

    Node(int value) {
        data = value;
        next = null;
    }
}

class LinkedListQueue {

    private Node front, rear;
    private int size;

    public LinkedListQueue() {
        front = rear = null;
        size = 0;
    }

    // Enqueue
    public void enqueue(int data) {
        Node newNode = new Node(data);

        if (rear == null) {
            front = rear = newNode;
        } else {
            rear.next = newNode;
            rear = newNode;
        }

        size++;
    }

    // Dequeue
    public int dequeue() {
        if (front == null) {
            System.out.println("Underflow!");
            return -1;
        }

        int value = front.data;
        front = front.next;

        if (front == null)
            rear = null;

        size--;
        return value;
    }

    public int front() {
        return (front == null) ? -1 : front.data;
    }

    public int rear() {
        return (rear == null) ? -1 : rear.data;
    }

    public int size() { return size; }
    public boolean isEmpty() { return size == 0; }
}
```

---

# ## 🎯 **Summary Table (Markdown Friendly)**

```
| Operation  | Description                   | Time Complexity  |
|------------|-------------------------------|------------------|
| enqueue(x) | Insert element at rear        | O(1)             |
| dequeue()  | Remove element from front     | O(1)             |
| front()    | View front element            | O(1)             |
| rear()     | View last element             | O(1)             |
| isEmpty()  | Check if queue is empty       | O(1)             |
| size()     | Total elements in queue       | O(1)             |
```


---

