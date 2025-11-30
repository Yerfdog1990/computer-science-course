
---

# ⭐ **Lesson Notes: Queue Using Simple Array**

---

## 📘 **1. Introduction to Queues**

A **Queue** is a linear data structure that follows the **FIFO (First In, First Out)** principle.

➡ **FIFO meaning:**

* The **first element inserted** is the **first element removed**.
* Just like people standing in a line: the person who comes first gets served first.

### ✔ Real-life examples:

* Queue at a bank
* Ticket counters
* Printers processing tasks in order
* Call center line-up

---

## 📌 **2. Queue Characteristics**

* Insertions happen at the **rear** end.
* Deletions happen at the **front** end.
* Maintains strict order of processing.

### 📌 Difference Between Stack and Queue

| Feature    | Stack (LIFO)                        | Queue (FIFO)                          |
| ---------- | ----------------------------------- | ------------------------------------- |
| Removal    | Last inserted element removed first | First inserted element removed first  |
| Ends used  | One end only                        | Two ends (front & rear)               |
| Common use | Undo operations, recursion          | Scheduling, buffering, CPU task queue |

---

---

# ⭐ **3. Queue Data Structure**

A queue maintains two pointers:

* **front** → index of first element
* **size** → number of elements currently in queue
* **rear** is derived from:

  ```
  rear = front + size – 1
  ```

---

# ⭐ **4. Simple Array Implementation of Queue**

This is the *basic* array-based queue where:

* Elements are added at the **end**
* Elements are removed from the **front**
* **Shifting of elements** is required after each dequeue → **O(n)**

---

## 📌 **4.1 Working Mechanism**

### Enqueue (Insert)

* Insert at end:
  `arr[size] = element`
* Increment size
* Time: **O(1)**

### Dequeue (Delete)

* Remove from the beginning
* Shift all elements one index to the left
* Time: **O(n)** → **costly operation**

This is the major limitation of simple array queue.

---

## 📌 **4.2 Operation Complexities**

| Operation     | Time Complexity | Description              |
|---------------|-----------------|--------------------------|
| **enqueue()** | O(1)            | Insert at end            |
| **dequeue()** | O(n)            | Shift elements left      |
| **getFront()**| O(1)            | Return `arr[0]`          |
| **getRear()** | O(1)            | Return `arr[size - 1]`   |
| **isFull()**  | O(1)            | Check `size == capacity` |
| **isEmpty()** | O(1)            | Check `size == 0`        |

---

# ⭐ **5. Queue Using a Fixed-Size Array**

We store:

* `arr[]` → stores queue elements
* `capacity` → max possible elements
* `size` → current number of elements

### 📌 Queue State Diagram

Initially:

```
arr: [ _  _  _  _ ]
front = 0
size = 0
```

After enqueue 10, 20, 30:

```
arr: [10, 20, 30, _]
front = 0
size = 3
rear = front + size - 1 = 2
```

After dequeue (removing 10):

```
arr: [20, 30, _ , _]
front = 0
size = 2
```

---

# ⭐ **6. Complete Java Code: Simple Array Queue**

```java
class myQueue {
    private int[] arr;
    private int capacity;
    private int size;

    // Constructor
    public myQueue(int capacity) {

        this.capacity = capacity;       // Maximum items queue can store
        arr = new int[capacity];        // Array to store queue items
        size = 0;                       // Initially queue is empty
    }

    // Check if queue is empty
    public boolean isEmpty() {
        return size == 0;
    }

    // Check if queue is full
    public boolean isFull() {
        return size == capacity;
    }

    // Enqueue operation
    public void enqueue(int x) {
        if (isFull()) {
            System.out.println("Queue Overflow");
            return;
        }
        arr[size] = x;      // Insert at the end
        size++;
    }

    // Dequeue operation
    public void dequeue() {
        if (isEmpty()) {
            System.out.println("Queue Underflow");
            return;
        }

        // Shift all items to the left
        for (int i = 1; i < size; i++) {
            arr[i - 1] = arr[i];
        }
        size--;
    }

    // Get first element
    public int getFront() {
        if (isEmpty()) {
            System.out.println("Queue is empty!");
            return -1;
        }
        return arr[0];
    }

    // Get last element
    public int getRear() {
        if (isEmpty()) {
            System.out.println("Queue is empty!");
            return -1;
        }
        return arr[size - 1];
    }
}

public class Main {
    public static void main(String[] args) {

        myQueue q = new myQueue(3);

        q.enqueue(10);
        q.enqueue(20);
        q.enqueue(30);

        System.out.println("Front: " + q.getFront()); 
        q.dequeue();

        System.out.println("Front: " + q.getFront());
        System.out.println("Rear: " + q.getRear());

        q.enqueue(40);
    }
}
```

### 📌 **Output**

```
Front: 10
Front: 20
Rear: 30
```

---

# ⭐ **7. Issues With Simple Array Queue**

### ❌ **Major Problem:** dequeue() is **O(n)** due to shifting

This is inefficient for large data structures.

### ❌ More Limitations:

* Wasted time in shifting
* Fixed size
* Not suitable for production-level queue systems

---

# ⭐ **8. Infinite (Dynamic) Array Queue Concept**

This implementation assumes:

* Array is conceptually infinite
* Uses **only front pointer**
* No shifting
* Enqueue at arr[next index]
* Dequeue by incrementing front

### ✔ Both Enqueue and Dequeue become **O(1)**

But memory waste becomes a problem → unused space before `front`.

---

# ⭐ **9. Circular Array Queue (Efficient O(1) Queue)**

To solve all problems:

* Use a **circular buffer**
* Apply **modular arithmetic** to move front/rear
* All operations become **O(1)**

This is the recommended array-based queue implementation.

✔ No shifting
✔ No wasted space
✔ Efficient

But **your current lesson focuses on Simple Array Queue**.
Circular version can be written if needed.

---

# ⭐ **10. Summary Table: Simple vs Circular Array Queue**

| Feature      | Simple Array Queue     | Circular Array Queue |
| ------------ | ---------------------- | -------------------- |
| Enqueue      | O(1)                   | O(1)                 |
| Dequeue      | O(n) ❌                 | O(1) ✔               |
| Memory use   | Poor (shifting waste)  | Excellent            |
| Recommended? | No (for learning only) | Yes                  |

---



