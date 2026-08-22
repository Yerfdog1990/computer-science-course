package dsa.quick_sort;

import java.util.ArrayList;
import java.util.Arrays;

public class QuickSort {

    static void swap(int[] arr, int i, int j) {
        int temp = arr[i];
        arr[i] = arr[j];
        arr[j] = temp;
    }

    // Lomuto partition — pivot is last element
    static int partition(int[] arr, int leftPointer, int rightPointer) {
        int pivot = arr[rightPointer];
        int i = leftPointer - 1;

        for (int j = leftPointer; j <= rightPointer - 1; j++) {
            if (arr[j] < pivot) {
                i++;
                swap(arr, i, j); // Move small elements to the left.
            }
        }
        swap(arr, i + 1, rightPointer); // Put the pivot between the left and right partitions.
        return i + 1;
    }

    public static int[] quickSort(int[] arr, int leftPointer, int rightPointer) {
        if (leftPointer < rightPointer) {
            int pi = partition(arr, leftPointer, rightPointer); // pivot index

            quickSort(arr, leftPointer, pi - 1);        // sort left part
            quickSort(arr, pi + 1, rightPointer);       // sort right part
        }
        return arr;
    }

    public static void main(String[] args) {
        int[] arr = {10, 7, 8, 9, 1, 2};
        System.out.println("Original array: " + Arrays.toString(arr));
        quickSort(arr, 0, arr.length - 1);

        ArrayList<Integer> sortedArray = new ArrayList<>();
        for (int val : arr) {
            sortedArray.add(val);
        }
        System.out.println("Sorted array: " + sortedArray);
        // Output: 1 5 7 8 9 10
    }
}