
---

# 📘 **Lesson Notes: Stack Using Array**

**Last Updated : 13 Sep, 2025**

A **stack** is a **linear data structure** that follows the **Last-In-First-Out (LIFO)** principle.
When a stack is implemented using an **array**, we treat the **end of the array as the top** of the stack.

---

# 📌 1. Declaration of Stack Using Array

To implement a stack using an array, we maintain:

* **`int[] arr`** → array to store stack elements
* **`capacity`** → maximum size of the stack
* **`top`** → index of the current top element (`-1` means empty)

### Java Example

```java
class myStack {

    // array to store elements
    private int[] arr;

    // maximum size of stack
    private int capacity;

    // index of top element
    private int top;

    // constructor
    public myStack(int cap) {
        capacity = cap;
        arr = new int[capacity];
        top = -1;
    }
}
```

---

# 📌 2. Operations on Stack (Array Implementation)

---

## ✅ **Push Operation**

**Purpose:** Add an item to the stack.
If the stack is full → **Overflow condition**.

### Rules:

1. Check if **top == capacity - 1** → Stack Overflow
2. Otherwise increment top
3. Insert element at `arr[top]`

### Java Code

```java
void push(int x) {

    if (top == capacity - 1) {
        System.out.println("Stack Overflow");
        return;
    }

    arr[++top] = x;
}
```

**Time Complexity:** O(1)
**Auxiliary Space:** O(1)

---

## ✅ **Pop Operation**

**Purpose:** Remove and return the top element.
If the stack is empty → **Underflow condition**.

### Rules:

1. Check if **top == -1** → Stack Underflow
2. Store the element at top
3. Decrement top
4. Return stored value

### Java Code

```java
int pop() {

    if (top == -1) {
        System.out.println("Stack Underflow");
        return -1;
    }

    return arr[top--];
}
```

**Time Complexity:** O(1)
**Auxiliary Space:** O(1)

---

## ✅ **Top/Peek Operation**

**Purpose:** Return the top element without removing it.

### Rules:

1. Check if **top == -1** → Stack is empty
2. Otherwise, return `arr[top]`

### Java Code

```java
int peek() {

    if (top == -1) {
        System.out.println("Stack is Empty");
        return -1;
    }

    return arr[top];
}
```

**Time Complexity:** O(1)
**Auxiliary Space:** O(1)

---

## ✅ **isEmpty Operation**

**Purpose:** Check if the stack is empty.

### Rules:

* If **top == -1** → empty
* Else → not empty

### Java Code

```java
boolean isEmpty() {
    return top == -1;
}
```

**Time Complexity:** O(1)
**Auxiliary Space:** O(1)

---

## ✅ **isFull Operation**

**Purpose:** Check if the stack is full.

### Rules:

* If **top == capacity - 1** → full
* Else → not full

### Java Code

```java
boolean isFull() {
    return top == capacity - 1;
}
```

**Time Complexity:** O(1)
**Auxiliary Space:** O(1)

---

# 📌 3. Full Implementation of Stack Using Array

```java
import java.util.Arrays;

class myStack {

    // array to store elements
    private int[] arr;

    // maximum size of stack
    private int capacity;

    // index of top element
    private int top;

    // constructor
    public myStack(int cap) {
        capacity = cap;
        arr = new int[capacity];
        top = -1;
    }

    // push operation
    public void push(int x) {
        if (top == capacity - 1) {
            System.out.println("Stack Overflow");
            return;
        }
        arr[++top] = x;
    }

    // pop operation
    public int pop() {
        if (top == -1) {
            System.out.println("Stack Underflow");
            return -1;
        }
        return arr[top--];
    }

    // peek (or top) operation
    public int peek() {
        if (top == -1) {
            System.out.println("Stack is Empty");
            return -1;
        }
        return arr[top];
    }

    // check if stack is empty
    public boolean isEmpty() {
        return top == -1;
    }

    // check if stack is full
    public boolean isFull() {
        return top == capacity - 1;
    }
}

public class Main {
    public static void main(String[] args) {
        myStack st = new myStack(4);

        // pushing elements
        st.push(1);
        st.push(2);
        st.push(3);
        st.push(4);

        // popping one element
        System.out.println("Popped: " + st.pop());

        // checking top element
        System.out.println("Top element: " + st.peek());

        // checking if stack is empty
        System.out.println("Is stack empty: " +
                            (st.isEmpty() ? "Yes" : "No"));

        // checking if stack is full
        System.out.println("Is stack full: " +
                            (st.isFull() ? "Yes" : "No"));
    }
}
```

### **Output**

```
Popped: 4
Top element: 3
Is stack empty: No
Is stack full: No
```

---

# 📌 4. Stack Implementation Using Dynamic Array

A fixed-size array has a maximum capacity that **cannot grow**.
To overcome this limitation, we use **dynamic arrays**, which resize automatically.

* C++ → `vector`
* **Java → `ArrayList` (used below)**
* Python → `list`
* C# → `List`
* JavaScript → `Array`

---

## Java Example (Dynamic Array)

```java
import java.util.ArrayList;

class myStack {
    ArrayList<Integer> arr = new ArrayList<>();

    // push operation
    void push(int x) {
        arr.add(x);
    }

    // pop operation
    int pop() {
        if (arr.isEmpty()) {
            System.out.println("Stack Underflow");
            return -1;
        }
        int val = arr.get(arr.size() - 1);
        arr.remove(arr.size() - 1);
        return val;
    }

    // peek operation
    int peek() {
        if (arr.isEmpty()) {
            System.out.println("Stack is Empty");
            return -1;
        }
        return arr.get(arr.size() - 1);
    }

    // check if stack is empty
    boolean isEmpty() {
        return arr.isEmpty();
    }

    // current size
    int size() {
        return arr.size();
    }

    public static void main(String[] args) {
        myStack st = new myStack();

        // pushing elements
        st.push(1);
        st.push(2);
        st.push(3);
        st.push(4);

        // popping one element
        System.out.println("Popped: " + st.pop());

        // checking top element
        System.out.println("Top element: " + st.peek());

        // checking if stack is empty
        System.out.println("Is stack empty: " +
                            (st.isEmpty() ? "Yes" : "No"));

        // checking current size
        System.out.println("Current size: " + st.size());
    }
}
```

### Output

```
Popped: 4
Top element: 3
Is stack empty: No
Current size: 3
```

---

# 📌 5. Fixed Size Stack vs Dynamic Size Stack

| Feature      | Fixed Size (Array)               | Dynamic Size (ArrayList)     |
| ------------ | -------------------------------- | ---------------------------- |
| Capacity     | Fixed                            | Grows automatically          |
| Overflow     | Possible                         | Impossible                   |
| Memory usage | Predictable                      | Expands as needed            |
| Speed        | Very fast                        | Slight overhead for resizing |
| Use-case     | Embedded systems, limited memory | Modern applications          |

---

