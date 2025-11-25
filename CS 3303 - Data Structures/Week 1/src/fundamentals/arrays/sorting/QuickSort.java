package fundamentals.arrays.sorting;

import java.util.Arrays;

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

    public static void main(String[] args) {
        int[] array = new int[1000];
        for (int i = 0; i < 1000; i++) {
            array[i] = (int) (Math.random() * 1000);
        }
        // unsorted array
        System.out.println("Unsorted array: " + Arrays.toString(array));
        long startTime = System.currentTimeMillis();
        quickSort(array, 0, array.length - 1);
        long endTime = System.currentTimeMillis();
        for (int j : array) {
            array[j] = j;
        }
        System.out.println("Quick sort: " +Arrays.toString(array));
        System.out.println("Time taken: " + (endTime - startTime) + " ms");
    }
}