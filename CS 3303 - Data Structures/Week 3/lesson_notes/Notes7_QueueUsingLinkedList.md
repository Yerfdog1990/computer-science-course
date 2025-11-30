
# 📘 **Queue – Linked List Implementation**

**Last Updated : 20 Sep, 2025**

A **Queue** is a *linear*, *FIFO (First-In-First-Out)* data structure.
The first element inserted is the first one removed.

When implemented using a **Linked List**, each node acts as an element of the queue, making enqueue and dequeue operations efficient and always **O(1)**.

---

# 🧱 **Why Implement Queue Using Linked List?**

### ✔ No fixed capacity

Unlike arrays, linked list queues grow dynamically.

### ✔ No shifting of elements

Dequeue does not require shifting elements (unlike array queue).

### ✔ Both operations are O(1)

* Enqueue inserts at tail
* Dequeue removes from head

---

# 🧩 **Queue Structure Using Linked List**

To implement a queue, we need:

### ✅ A `Node` class:

| Field  | Purpose              |
| ------ | -------------------- |
| `data` | Stores element value |
| `next` | Points to next node  |

### ✅ Two references:

| Pointer | Meaning                    |
| ------- | -------------------------- |
| `front` | First node / head of queue |
| `rear`  | Last node / tail of queue  |

---

# 🧱 **Node Class**

```java
class Node {
    int data;
    Node next;

    Node(int new_data) {
        data = new_data;
        next = null;
    }
}
```

---

# 🏗️ **Queue Class Structure**

```java
class myQueue {
    private Node front;  // head
    private Node rear;   // tail
    private int currSize;

    public myQueue() {
        front = rear = null;
        currSize = 0;
    }
}
```

---

# 🔄 **Operations on Queue Using Linked List**

---

# 1️⃣ **Enqueue Operation**

**Adds an element at the rear (tail).**

### Steps:

1. Create a new node
2. If queue is empty → `front = rear = newNode`
3. Else → attach node at end and update `rear`
4. Increase size

### Time Complexity:

```
O(1)
```

### Java Code:

```java
public void enqueue(int new_data) {
    Node new_node = new Node(new_data);

    if (isEmpty()) {
        front = rear = new_node;
    } else {
        rear.next = new_node;
        rear = new_node;
    }

    currSize++;
}
```

---

# 2️⃣ **Dequeue Operation**

**Removes and returns the front element.**

### Steps:

1. If queue is empty → Underflow
2. Store front node
3. Move front to next
4. If front becomes null → queue is empty → set `rear = null`
5. Decrease size

### Time Complexity:

```
O(1)
```

### Java Code:

```java
public int dequeue() {
    if (isEmpty()) {
        System.out.println("Queue Underflow");
        return -1;
    }

    Node temp = front;
    int removedData = temp.data;

    front = front.next;

    if (front == null)
        rear = null;

    currSize--;
    return removedData;
}
```

---

# 3️⃣ **isEmpty Operation**

Returns `true` if no elements exist.

### Time Complexity:

```
O(1)
```

### Java Code:

```java
public boolean isEmpty() {
    return front == null;
}
```

---

# 4️⃣ **Front Operation**

Returns (but does not remove) the front value.

### Time Complexity:

```
O(1)
```

### Java Code:

```java
public int getfront() {
    if (isEmpty()) {
        System.out.println("Queue is empty");
        return -1;
    }
    return front.data;
}
```

---

# 5️⃣ **Size Operation**

Returns number of elements in queue.

### Time Complexity:

```
O(1)
```

### Java Code:

```java
public int size() {
    return currSize;
}
```

---

# ✅ **Full Implementation: Queue Using Linked List**

```java
// Node class definition
class Node {
    int data;
    Node next;

    Node(int new_data) {
        data = new_data;
        next = null;
    }
}

// Queue class definition
class myQueue {
    private Node front;
    private Node rear;
    private int currSize;

    public myQueue() {
        currSize = 0;
        front = rear = null;
    }

    // Check if the queue is empty
    public boolean isEmpty() {
        return front == null;
    }

    // Enqueue operation
    public void enqueue(int new_data) {
        Node new_node = new Node(new_data);

        if (isEmpty()) {
            front = rear = new_node;
        } else {
            rear.next = new_node;
            rear = new_node;
        }

        currSize++;
    }

    // Dequeue operation
    public int dequeue() {
        if (isEmpty()) {
            System.out.println("Queue Underflow");
            return -1;
        }

        Node temp = front;
        int removedData = temp.data;

        front = front.next;

        if (front == null)
            rear = null;

        currSize--;
        return removedData;
    }

    // Return front element
    public int getfront() {
        if (isEmpty()) {
            System.out.println("Queue is empty");
            return -1;
        }
        return front.data;
    }

    // Return size of queue
    public int size() {
        return currSize;
    }
}

class GFG {
    public static void main(String[] args) {
        myQueue q = new myQueue();

        q.enqueue(10);
        q.enqueue(20);

        System.out.println("Dequeue: " + q.dequeue());

        q.enqueue(30);

        System.out.println("Front: " + q.getfront());
        System.out.println("Size: " + q.size());
    }
}
```

---

# ▶️ **Output**

```
Dequeue: 10
Front: 20
Size: 2
```

---

# 🎯 **Key Takeaways**

* Queue follows **FIFO**.
* Linked list implementation avoids capacity limits.
* Both **enqueue** and **dequeue** run in **O(1)**.
* `front` = remove end, `rear` = insert end.
* Ideal when number of elements is unknown/dynamic.

---

