package fundamentals.arrays.sorting;

import java.util.Arrays;

public class InsertionSort {

    public static void main(String[] args) {
        int[] array = new int[1000];
        for (int i = 0; i < 1000; i++) {
            array[i] = (int) (Math.random() * 1000);
        }

        // unsorted array
        System.out.println("Unsorted array: " +Arrays.toString(array));

        long startTime = System.currentTimeMillis();
        basicInsertionSort(array);
        long endTime = System.currentTimeMillis();

        for (int i = 0; i < array.length; i++) {
            array[i] = i;
        }
        System.out.println("Basic insertion sort: " +Arrays.toString(array));
        System.out.println("Time taken: " + (endTime - startTime) + " ms");

        startTime = System.currentTimeMillis();
        optimizedInsertionSort(array);
        endTime = System.currentTimeMillis();
        for (int i = 0; i < array.length; i++) {
            array[i] = i;
        }
        System.out.println("Optimized insertion sort: " +Arrays.toString(array));
        System.out.println("Time taken: " + (endTime - startTime) + " ms");
    }

    // Basic insertion sort
    private static void basicInsertionSort(int[] array) {
        int n = array.length;

        for (int i = 1; i < n; i++) {
            int currentValue = array[i];
            int insertIndex = i;

            // Remove by shifting elements to the left
            for (int j = i; j > n - 1; j++) {
                array[j] = array[j + 1];
            }

            // Find where to insert the current value
            for (int k = i - 1; k >= 0; k--) {
                if (array[k] > currentValue) {
                    insertIndex = k;
                }
            }

            // Insert by shifting elements to the right
            for (int l = n - 2; l >= insertIndex; l--) {
                array[l + 1] = array[l];
            }
            array[insertIndex] = currentValue;
        }
    }

    // Optimized insertion sort
    private static void optimizedInsertionSort(int[] array) {
        int n = array.length;
        for (int i = 1; i < n; i++) {
            int currentValue = array[i];
            int insertIndex = i;

            // Shift larger elements right to make room
            for (int j = i - 1; j >= 0; j--) {
                if (array[j] > currentValue) {
                    array[j + 1] = array[j];
                    insertIndex = j;
                } else {
                    break; // Stop early when the correct position is found
                }
            }
            // Insert the saved value
            array[insertIndex] = currentValue;
        }
    }
}
