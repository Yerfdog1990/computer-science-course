
---

# 📘 **Lesson Notes: Circular Queue Implementation Using Array**

**Last Updated: 30 Oct, 2025**

A **Circular Queue** is a linear data structure that solves the limitations of a simple array-based queue.
In a normal queue implementation:

* `enqueue()` is **O(1)**
* `dequeue()` becomes **O(n)** because all elements must shift left — **OR** we waste empty slots when using front/rear indexes.

A **circular queue** fixes these issues by allowing the queue to *wrap around* the array using modulo arithmetic.

---

# 🚀 **Why Use a Circular Queue?**

A circular queue ensures:

| Operation     | Time Complexity |
| ------------- | --------------- |
| **enqueue()** | O(1)            |
| **dequeue()** | O(1)            |

### Benefits:

* No shifting of elements
* Efficient memory usage
* Fast constant-time operations

---

# 🧱 **Internal Representation**

A circular queue using array maintains the following:

| Variable   | Purpose                                   |
| ---------- | ----------------------------------------- |
| `arr[]`    | Stores queue elements                     |
| `capacity` | Maximum size                              |
| `front`    | Index of the front element                |
| `size`     | Number of elements currently in the queue |

---

# 🏗️ **Java Class Structure**

```java
class myQueue {

    // Array to store queue elements
    private int[] arr;

    // Index of front element
    private int front;

    // Current number of elements
    private int size;

    // Maximum capacity
    private int capacity;

    // Constructor
    public myQueue(int cap) {
        capacity = cap;
        arr = new int[capacity];
        front = 0;
        size = 0;
    }
}
```

---

# 🔄 **Operations on Circular Queue**

---

## 1️⃣ **enqueue(x)**

**Purpose:** Insert an element at the rear.

### Steps:

1. Check if full → if `size == capacity`
2. Compute rear index:

```
rear = (front + size) % capacity;
```

3. Insert `arr[rear] = x`
4. `size++`

### Complexity:

* **Time:** O(1)
* **Space:** O(1)

### Java Implementation

```java
public void enqueue(int x) {
    if (size == capacity) {
        System.out.println("Queue is full!");
        return;
    }
    int rear = (front + size) % capacity;
    arr[rear] = x;
    size++;
}
```

---

## 2️⃣ **dequeue()**

**Purpose:** Remove and return the front element.

### Steps:

1. Check if empty → if `size == 0`
2. Store result → `res = arr[front]`
3. Move front →

```
front = (front + 1) % capacity;
```

4. `size--`
5. Return removed element

### Complexity:

* **Time:** O(1)
* **Space:** O(1)

### Java Implementation

```java
public int dequeue() {
    if (size == 0) {
        System.out.println("Queue is empty!");
        return -1;
    }
    int res = arr[front];
    front = (front + 1) % capacity;
    size--;
    return res;
}
```

---

## 3️⃣ **getRear()**

**Purpose:** Return last element inserted (rear).

### Steps:

1. Check if empty
2. Compute rear index:

```
rear = (front + size - 1) % capacity
```

3. Return `arr[rear]`

### Complexity:

* **Time:** O(1)
* **Space:** O(1)

### Java Implementation

```java
public int getRear() {
    if (size == 0)
        return -1;
    int rear = (front + size - 1) % capacity;
    return arr[rear];
}
```

---

## 4️⃣ **getFront()**

**Purpose:** Return element at the front.

### Steps:

1. Check if empty
2. Return `arr[front]`

---

### Java Implementation

```java
public int getFront() {
    if (size == 0)
        return -1;
    return arr[front];
}
```

---

# 📦 **Complete Java Implementation**

```java
class myQueue {

    private int[] arr;
    private int front;
    private int size;
    private int capacity;

    public myQueue(int cap) {
        capacity = cap;
        arr = new int[capacity];
        front = 0;
        size = 0;
    }

    // Insert an element at the rear
    public void enqueue(int x) {
        if (size == capacity) {
            System.out.println("Queue is full!");
            return;
        }
        int rear = (front + size) % capacity;
        arr[rear] = x;
        size++;
    }

    // Remove an element from the front
    public int dequeue() {
        if (size == 0) {
            System.out.println("Queue is empty!");
            return -1;
        }
        int res = arr[front];
        front = (front + 1) % capacity;
        size--;
        return res;
    }

    // Get the front element
    public int getFront() {
        if (size == 0) return -1;
        return arr[front];
    }

    // Get the rear element
    public int getRear() {
        if (size == 0) return -1;
        int rear = (front + size - 1) % capacity;
        return arr[rear];
    }

    public static void main(String[] args) {
        myQueue q = new myQueue(5);
        q.enqueue(10);
        q.enqueue(20);
        q.enqueue(30);

        System.out.println(q.getFront() + " " + q.getRear());

        q.dequeue();
        System.out.println(q.getFront() + " " + q.getRear());

        q.enqueue(40);
        System.out.println(q.getFront() + " " + q.getRear());
    }
}
```

---

# 🧪 **Output**

```
10 30
20 30
20 40
```

---

# 🎯 **Key Takeaways**

* Circular queue prevents wasted array space.
* All operations run in **O(1)** time.
* Uses modulo to wrap around indices.
* Avoids costly shifting of elements.

---
