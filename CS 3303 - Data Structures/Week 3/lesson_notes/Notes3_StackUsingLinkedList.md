
---

# **Lesson Notes: Stack Using Linked List**

**Last Updated: 13 Sep, 2025**

A **stack** is a linear data structure that follows the **Last-In-First-Out (LIFO)** principle. The element inserted last is the first to be removed. Unlike array-based stacks (which have fixed capacity), a **stack implemented using a linked list** grows dynamically—allowing efficient push and pop operations without worrying about overflow (unless memory is exhausted).

In a linked list implementation, **each stack element is stored inside a node**, and the **head of the linked list acts as the top of the stack**.

---

## **1. Stack Representation Using Linked List**

To implement a stack using a linked list, we maintain:

### **Node Structure**

Each node contains:

* `data` → value to store
* `next` → reference to the next node

### **Top Pointer**

* Points to the **current top of the stack**
* Initially, `top = null`, meaning the stack is empty

### **Node Class Example**

```java
/* Node structure */
class Node {
    public int data;
    public Node next;

    public Node(int x) {
        data = x;
        next = null;
    }
}
```

### **Stack Class Skeleton**

```java
/* Stack class */
class MyStack {

    // pointer to top node
    private Node top;

    public MyStack() {
        // initially stack is empty
        top = null;
    }
}
```

---

# **2. Operations on Stack Using Linked List**

---

## **A. Push Operation (Insert at Top)**

The push operation inserts a new element at the top of the stack.

### **Steps**

1. Create a new node with the given value
2. Set `newNode.next = top`
3. Update `top` to point to this new node

Because linked lists grow dynamically, **no fixed capacity** exists. Overflow happens only if **memory is exhausted**.

### **Code: push()**

```java
void push(int x) {
    Node temp = new Node(x);
    temp.next = top;
    top = temp;
}
```

**Time Complexity:** O(1)
**Auxiliary Space:** O(1)

---

## **B. Pop Operation (Remove the Top Element)**

Removes and returns the top element.

### **Steps**

1. Check if stack is empty → Underflow
2. Store top node temporarily
3. Move `top` to `top.next`
4. Delete the old top node
5. Return its value

### **Code: pop()**

```java
public int pop() {
  
    if (top == null) {
        System.out.println("Stack Underflow");
        return -1;
    }

    Node temp = top;
    top = top.next;
    int val = temp.data;

    temp = null; 
    return val;
}
```

**Time Complexity:** O(1)
**Auxiliary Space:** O(1)

---

## **C. Peek (Top) Operation**

Returns the top element without removing it.

### **Steps**

* If stack is empty → print message
* Otherwise return `top.data`

### **Code: peek()**

```java
int peek() {
   
    if (top == null) {
        System.out.println("Stack is Empty");
        return -1;
    }
   
    return top.data;
}
```

**Time Complexity:** O(1)
**Auxiliary Space:** O(1)

---

## **D. isEmpty Operation**

Checks if the stack is empty.

### **Logic**

* If `top == null` → stack is empty

### **Code: isEmpty()**

```java
boolean isEmpty() {
    return top == null;
}
```

**Time Complexity:** O(1)
**Auxiliary Space:** O(1)

---

# **3. Full Stack Implementation Using Linked List**

This implementation also includes:

* A `count` variable to track current size
* Methods: push, pop, peek, isEmpty, size

---

### **Complete Java Code**

```java
// Node structure
class Node {
    int data;
    Node next;

    Node(int x) {
        data = x;
        next = null;
    }
}

// Stack implementation using linked list
class myStack {
    Node top;

    // To Store current size of stack
    int count;

    myStack() {
        // initially stack is empty
        top = null;
        count = 0;
    }

    // push operation
    void push(int x) {
        Node temp = new Node(x);
        temp.next = top;
        top = temp;

        count++;
    }

    // pop operation
    int pop() {
        if (top == null) {
            System.out.println("Stack Underflow");
            return -1;
        }
        Node temp = top;
        top = top.next;
        int val = temp.data;

        count--;
        return val;
    }

    // peek operation
    int peek() {
        if (top == null) {
            System.out.println("Stack is Empty");
            return -1;
        }
        return top.data;
    }

    // check if stack is empty
    boolean isEmpty() {
        return top == null;
    }

    // size of stack
    int size() {
        return count;
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
        System.out.println("Is stack empty: " + (st.isEmpty() ? "Yes" : "No"));

        // checking current size
        System.out.println("Current size: " + st.size());
    }
}
```

### **Output**

```
Popped: 4
Top element: 3
Is stack empty: No
Current size: 3
```

---

# **4. Advantages of Stack Using Linked List**

- ✔ **No fixed size** → grows dynamically
- ✔ **Efficient operations** (push/pop in O(1))
- ✔ No overflow unless memory is full
- ✔ All elements stored in non-contiguous memory (flexible structure)

---

# **Summary**

Using a linked list to implement a stack offers flexibility, dynamic memory usage, and constant time operations. It avoids the limitations of fixed-size arrays and is often used in interview questions and system design scenarios.

---

