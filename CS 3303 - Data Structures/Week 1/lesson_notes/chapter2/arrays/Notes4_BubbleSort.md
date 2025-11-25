
---

# **Bubble Sort**

## **Introduction to Bubble Sort**

**Bubble Sort** is one of the simplest sorting algorithms. It sorts an array from the **lowest value to the highest value** by repeatedly comparing and swapping adjacent elements.

The name **"Bubble"** comes from how the **largest values gradually "bubble up"** to the end of the array after each full pass.

---

## **How Bubble Sort Works**

Bubble Sort follows these steps:

1. Go through the array, one element at a time.
2. For each element, compare it with the next element.
3. If the current element is greater than the next, **swap them**.
4. Repeat this process as many times as there are elements in the array.

By the end of each pass, the **largest unsorted value moves to its correct position** at the right end of the array.

---

# **Manual Walkthrough**

Let’s manually walk through Bubble Sort for one pass of this array:

```
[7, 12, 9, 11, 3]
```

### **Step-by-step**

**Step 1:** Look at 7 and 12 → correct order → no swap

```
[7, 12, 9, 11, 3]
```

**Step 2:** Compare 12 and 9 → wrong order → swap

```
[7, 9, 12, 11, 3]
```

**Step 3:** Compare 12 and 11 → swap

```
[7, 9, 11, 12, 3]
```

**Step 4:** Compare 12 and 3 → swap

```
[7, 9, 11, 3, 12]
```

After one run-through, **12 has bubbled to the end**, but the rest of the array is still unsorted.

---

## **Full Manual Sort Illustration**

Sorting continues until all values are in correct order:

```
[3, 7, 9, 11, 12]
```

A 5-element array requires **4 full passes**, because each pass places one more element in its final position.

---

# **Bubble Sort Implementation in Java**

To implement Bubble Sort in Java, we need:

* An array
* An **outer loop** running `n - 1` times
* An **inner loop** that compares adjacent elements, decreasing in length each time
* A **swap** operation

---

## **Java Implementation (Basic Bubble Sort)**

```java
public class BubbleSortBasic {
    public static void main(String[] args) {
        int[] arr = {64, 34, 25, 12, 22, 11, 90, 5};
        
        bubbleSort(arr);

        System.out.print("Sorted array: ");
        for (int num : arr) {
            System.out.print(num + " ");
        }
    }

    public static void bubbleSort(int[] arr) {
        int n = arr.length;

        for (int i = 0; i < n - 1; i++) {
            for (int j = 0; j < n - i - 1; j++) {

                if (arr[j] > arr[j + 1]) {
                    // Swap
                    int temp = arr[j];
                    arr[j] = arr[j + 1];
                    arr[j + 1] = temp;
                }
            }
        }
    }
}
```

---

# **Improved Bubble Sort**

Bubble Sort can be optimized.
If in one full pass **no swaps happen**, the array is already sorted → We can stop early.

Useful when the array is nearly sorted, such as:

```
int[] arr = {7, 3, 9, 12, 11};
```

---

## **Java Implementation (Optimized Bubble Sort)**

```java
public class BubbleSortOptimized {
    public static void main(String[] args) {
        int[] arr = {7, 3, 9, 12, 11};
        
        bubbleSort(arr);

        System.out.print("Sorted array: ");
        for (int num : arr) {
            System.out.print(num + " ");
        }
    }

    public static void bubbleSort(int[] arr) {
        int n = arr.length;

        for (int i = 0; i < n - 1; i++) {
            boolean swapped = false;

            for (int j = 0; j < n - i - 1; j++) {

                if (arr[j] > arr[j + 1]) {
                    // Swap
                    int temp = arr[j];
                    arr[j] = arr[j + 1];
                    arr[j + 1] = temp;
                    swapped = true;
                }
            }

            // If no elements were swapped, stop
            if (!swapped) {
                break;
            }
        }
    }
}
```

---

# **Bubble Sort Time Complexity**

Bubble Sort performs:

* **n comparisons** per pass
* **n passes** in total

Total operations:

[
n \cdot n = n^2
]

Therefore, the time complexity is:

[
O(n^2)
]

---

### **Time Complexity Behavior**

* Very slow for large arrays
* Time grows *quadratically* as input size increases
* Much slower compared to advanced sorting algorithms like **Quicksort**, which operates in:

[
O(n \log n)
]

---

# **Exercise (Java Version)**

Rewrite the following logic into a **recursive Bubble Sort** method.

### **Starting Point (Python-like logic)**

```
print(0)
print(1)
count = 2

def fibonacci(prev1, prev2):
    global count
    if count <= 19:
        newFibo = prev1 + prev2
        print(newFibo)
        prev2 = prev1
        prev1 = newFibo
        count += 1
        
(prev1, prev2)
    else:
        return

fibonacci(1,0)
```

---

## **Exercise (Java)**

**Task:**
Convert the logic above into a **recursive Java method** that prints the first 20 Fibonacci numbers.

### **Starter Template**

```java
public class FibonacciExercise {

    static int count = 2; // Already printed 0 and 1

    public static void main(String[] args) {
        System.out.println(0);
        System.out.println(1);

        fibonacci(1, 0); // prev1 = 1, prev2 = 0
    }

    public static void fibonacci(int prev1, int prev2) {
        // TODO: Implement recursion here
    }
}
```

### **Requirements**

* Use recursion
* Stop after 20 numbers
* Print each Fibonacci number
* Update values just like in the Python logic

---
