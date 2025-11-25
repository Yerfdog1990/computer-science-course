
---

# DSA Radix Sort

## Radix Sort

The Radix Sort algorithm sorts an array by individual digits, starting with the least significant digit (the rightmost one).

The radix (or base) is the number of unique digits in a number system. In the decimal system we normally use, there are 10 different digits from 0 till 9.

Radix Sort uses the radix so that decimal values are put into 10 different buckets (or containers) corresponding to the digit that is in focus, then put back into the array before moving on to the next digit.

Radix Sort is a non-comparative algorithm that only works with non-negative integers.

The Radix Sort algorithm can be described like this:

### How it works:

1. Start with the least significant digit (rightmost digit).
2. Sort the values based on the digit in focus by first putting the values in the correct bucket based on the digit in focus, and then put them back into array in the correct order.
3. Move to the next digit, and sort again, like in the step above, until there are no digits left.

---

## Stable Sorting

Radix Sort must sort the elements in a **stable** way for the result to be sorted correctly.

A stable sorting algorithm is an algorithm that keeps the order of elements with the same value before and after the sorting. Let's say we have two elements "K" and "L", where "K" comes before "L", and they both have value "3". A sorting algorithm is considered stable if element "K" still comes before "L" after the array is sorted.

It makes little sense to talk about stable sorting algorithms for the previous algorithms we have looked at individually, because the result would be same if they are stable or not. But it is important for Radix Sort that the sorting is done in a stable way because the elements are sorted by just one digit at a time.

So after sorting the elements on the least significant digit and moving to the next digit, it is important to not destroy the sorting work that has already been done on the previous digit position, and that is why we need to take care that Radix Sort does the sorting on each digit position in a stable way.

---

## Manual Run Through

Let's try to do the sorting manually, just to get an even better understanding of how Radix Sort works before actually implementing it in a programming language.

### Step 1:

We start with an unsorted array, and an empty array to fit values with corresponding radices 0 till 9.

```
myArray = [ 33, 45, 40, 25, 17, 24]
radixArray = [ [], [], [], [], [], [], [], [], [], [] ]
```

### Step 2:

We start sorting by focusing on the least significant digit.

```
myArray = [ 33, 45, 40, 25, 17, 24]
radixArray = [ [], [], [], [], [], [], [], [], [], [] ]
```

### Step 3:

Now we move the elements into the correct positions in the radix array according to the digit in focus. Elements are taken from the start of myArray and pushed into the correct position in the radixArray.

```
myArray = [ ]
radixArray = [ [40], [], [], [33], [24], [45, 25], [], [17], [], [] ]
```

### Step 4:

We move the elements back into the initial array, and the sorting is now done for the least significant digit. Elements are taken from the end radixArray, and put into the start of myArray.

```
myArray = [ 40, 33, 24, 45, 25, 17 ]
radixArray = [ [], [], [], [], [], [], [], [], [], [] ]
```

### Step 5:

We move focus to the next digit. Notice that values 45 and 25 are still in the same order relative to each other as they were to start with, because we sort in a stable way.

```
myArray = [ 40, 33, 24, 45, 25, 17 ]
radixArray = [ [], [], [], [], [], [], [], [], [], [] ]
```

### Step 6:

We move elements into the radix array according to the focused digit.

```
myArray = [ ]
radixArray = [ [], [17], [24, 25], [33], [40, 45], [], [], [], [], [] ]
```

### Step 7:

We move elements back into the start of myArray, from the back of radixArray.

```
myArray = [ 17, 24, 25, 33, 40, 45 ]
radixArray = [ [], [], [], [], [], [], [], [], [], [] ]
```

The sorting is finished!

---

## Manual Run Through: What Happened?

We see that values are moved from the array and placed in the radix array according to the current radix in focus. And then the values are moved back into the array we want to sort.

This moving of values from the array we want to sort and back again must be done as many times as the maximum number of digits in a value. So for example if **437** is the highest number in the array that needs to be sorted, we know we must sort three times, once for each digit.

We also see that the radix array needs to be **two-dimensional** so that more than one value can be placed in the same radix (index).

And, as mentioned earlier, we must move values between the two arrays in a way that **keeps the order** of values with the same radix in focus, so the sorting is stable.

---

# Radix Sort Implementation (Java Version)

To implement the Radix Sort algorithm, we need:

* An array with non-negative integers that needs to be sorted.
* A two-dimensional array with index 0 to 9 to hold values with the current radix in focus.
* A loop that takes values from the unsorted array and places them in the correct position in the two-dimensional radix array.
* A loop that puts values back into the initial array from the radix array.
* An outer loop that runs as many times as there are digits in the highest value.

## Java Implementation of Radix Sort

```java
public class RadixSortDemo {

    public static void radixSort(int[] arr) {
        int maxVal = getMax(arr);
        int exp = 1;

        while (maxVal / exp > 0) {
            // Create buckets 0–9
            List<Integer>[] radixArray = new List[10];
            for (int i = 0; i < 10; i++) {
                radixArray[i] = new java.util.ArrayList<>();
            }

            // Move values into appropriate buckets (left to right)
            for (int i = 0; i < arr.length; i++) {
                int val = arr[i];
                int index = (val / exp) % 10;
                radixArray[index].add(val);
            }

            // Move values back into an array
            int idx = 0;
            for (List<Integer> bucket : radixArray) {
                for (int val : bucket) {
                    arr[idx++] = val;
                }
            }

            exp *= 10;
        }
    }

    private static int getMax(int[] arr) {
        int max = arr[0];
        for (int n : arr) if (n > max) max = n;
        return max;
    }

    public static void main(String[] args) {
        int[] myArray = {170, 45, 75, 90, 802, 24, 2, 66};

        System.out.println("Original array:");
        System.out.println(java.util.Arrays.toString(myArray));

        radixSort(myArray);

        System.out.println("Sorted array:");
        System.out.println(java.util.Arrays.toString(myArray));
    }
}
```

---

# Radix Sort Using Other Sorting Algorithms (Java Version)

Radix Sort can actually be implemented together with any other stable sorting algorithm.

Here is the version where **Bubble Sort** is used on each bucket:

```java
import java.util.*;

public class RadixSortWithBubbleSort {

    public static void bubbleSort(List<Integer> list) {
        int n = list.size();
        for (int i = 0; i < n; i++) {
            for (int j = 0; j < n - i - 1; j++) {
                if (list.get(j) > list.get(j + 1)) {
                    int temp = list.get(j);
                    list.set(j, list.get(j + 1));
                    list.set(j + 1, temp);
                }
            }
        }
    }

    public static void radixSortWithBubble(int[] arr) {
        int maxVal = Arrays.stream(arr).max().getAsInt();
        int exp = 1;

        while (maxVal / exp > 0) {

            List<Integer>[] radixArray = new ArrayList[10];
            for (int i = 0; i < 10; i++) {
                radixArray[i] = new ArrayList<>();
            }

            // Distribute values
            for (int num : arr) {
                int index = (num / exp) % 10;
                radixArray[index].add(num);
            }

            // Sort each bucket with a bubble sort
            for (List<Integer> bucket : radixArray) {
                bubbleSort(bucket);
            }

            // Reconstruct an array
            int idx = 0;
            for (List<Integer> bucket : radixArray) {
                for (int num : bucket) {
                    arr[idx++] = num;
                }
            }

            exp *= 10;
        }
    }

    public static void main(String[] args) {
        int[] myArray = {170, 45, 75, 90, 802, 24, 2, 66};

        System.out.println("Original array:");
        System.out.println(Arrays.toString(myArray));

        radixSortWithBubble(myArray);

        System.out.println("Sorted array:");
        System.out.println(Arrays.toString(myArray));
    }
}
```

---

# Radix Sort Time Complexity

The time complexity for Radix Sort is:

```
O(n ⋅ k)
```

This means that Radix Sort depends both on:

* the number of values `n`
* the number of digits in the highest value `k`

### Best Case

Lots of values but few digits (e.g., max = 999):

```
O(n)
```

### Worst Case

If the number of digits equals the number of values:

```
O(n²)
```

### Common Case

If digit the count grows logarithmically:

```
k(n) = log n
O(n log n)
```

---

# DSA Exercise

**Exercise:**
To sort an array with Radix Sort, what property must the sorting have for the sorting to be done properly?

Radix Sort must use a **stable** sorting algorithm.

---

