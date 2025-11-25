
---

# **Quicksort (Data Structures & Algorithms — Java Version)**

Quicksort is one of the fastest and most widely used sorting algorithms. It follows the **divide-and-conquer** strategy by repeatedly partitioning an array around a *pivot* element.

The algorithm works by:

1. Choosing a pivot element.
2. Rearranging the array so elements **less than** the pivot go left, and **greater than** the pivot go right.
3. Recursively applying the same steps to the left and right sub-arrays until the entire array is sorted.

---

# **How Quicksort Works**

### **Step-by-step process**

1. **Choose a pivot**
   In this implementation, we use the **last element** as pivot.

2. **Partition the array**
   Move elements smaller than the pivot to the left, and larger elements to the right.

3. **Place the pivot into its correct sorted position**

4. **Recursively apply Quicksort** to:

    * Sub-array left of pivot
    * Sub-array right of pivot

5. Repeat until all sub-arrays have size 0 or 1.

---

# **Manual Example (Walkthrough)**

Given:

```
[11, 9, 12, 7, 3]
```

### **Step 1 — Choose pivot = 3**

All values are greater than 3 → swap pivot with first element:

```
[3, 9, 12, 7, 11]
```

### **Step 2 — Sort right side [9, 12, 7, 11]**

Pivot = 11
Move 7 left of pivot:

```
[3, 9, 7, 12, 11]
```

Swap pivot (11) with 12:

```
[3, 9, 7, 11, 12]
```

### **Step 3 — Sort sub-array [9, 7]**

Pivot = 7
Swap 9 and 7:

```
[3, 7, 9, 11, 12]
```

Array is sorted.

---

# **Java Implementation of Quicksort**

Below is a clean, complete Java implementation following the described algorithm.

```java
public class QuickSort {

    // Partition method
    public static int partition(int[] array, int low, int high) {
        int pivot = high;  // using last element as pivot index
        int pivotValue = array[pivot];
        int i = low - 1;

        for (int j = low; j < high; j++) {
            if (array[j] <= pivotValue) {
                i++;
                // swap array[i] and array[j]
                int temp = array[i];
                array[i] = array[j];
                array[j] = temp;
            }
        }

        // Place pivot in correct position
        int temp = array[i + 1];
        array[i + 1] = array[pivot];
        array[pivot] = temp;

        return i + 1; // return pivot index
    }

    // Recursive Quicksort method
    public static void quickSort(int[] array, int low, int high) {
        if (low < high) {
            int pivotIndex = partition(array, low, high);
            quickSort(array, low, pivotIndex - 1);
            quickSort(array, pivotIndex + 1, high);
        }
    }

    // Helper to print array
    public static void printArray(int[] array) {
        for (int num : array) {
            System.out.print(num + " ");
        }
        System.out.println();
    }

    public static void main(String[] args) {
        int[] myArray = {64, 34, 25, 12, 22, 11, 90, 5};

        System.out.println("Original array:");
        printArray(myArray);

        quickSort(myArray, 0, myArray.length - 1);

        System.out.println("Sorted array:");
        printArray(myArray);
    }
}
```

---

# **Time Complexity of Quicksort**

| Case             | Time Complexity | Explanation                                                                         |
| ---------------- | --------------- | ----------------------------------------------------------------------------------- |
| **Worst case**   | **O(n²)**       | Pivot divides the array very unevenly (e.g., sorted array with last-element pivot). |
| **Best case**    | **O(n log n)**  | Pivot splits array in half every time.                                              |
| **Average case** | **O(n log n)**  | Expected behavior with random data or good pivot choices.                           |

Quicksort is fast because in average cases, the pivot divides the array into fairly even halves, keeping recursion shallow.

---

# **Exercise (Java Version)**

Complete the missing parts of the Quicksort code:

```java
public class QuickSortExercise {

    public static int partition(int[] array, int low, int high) {
        int pivot = array[high];
        int i = low - 1;

        for (int j = low; j < high; j++) {
            if (array[j] <= pivot) {
                i++;
                int temp = array[i];
                array[i] = array[j];
                array[j] = temp;
            }
        }

        int temp = array[i + 1];
        array[i + 1] = array[high];
        array[high] = temp;

        return i + 1;
    }

    public static void quickSort(int[] array, int low, int high) {
        if (low < high) {
            int pivotIndex = partition(array, low, high);

            // TODO: call quickSort on left sub-array
            quickSort(array, low, pivotIndex - 1);

            // TODO: call quickSort on right sub-array
            quickSort(array, pivotIndex + 1, high);
        }
    }

    public static void main(String[] args) {
        int[] myArray = {64, 34, 25, 12, 22, 11, 90, 5};

        quickSort(myArray, 0, myArray.length - 1);

        System.out.println("Sorted array:");
        for (int num : myArray) {
            System.out.print(num + " ");
        }
    }
}
```

---


