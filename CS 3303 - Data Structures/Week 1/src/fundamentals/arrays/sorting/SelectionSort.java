package fundamentals.arrays.sorting;

import java.util.Arrays;

public class SelectionSort {

    public static void main(String[] args) {
        int[] array = new int[1000];
        for (int i = 0; i < 1000; i++) {
            array[i] = (int) (Math.random() * 1000);
        }

        // unsorted array
        System.out.println("Unsorted array: " +Arrays.toString(array));

        long startTime = System.currentTimeMillis();
        basicSelectionSort(array);
        long endTime = System.currentTimeMillis();
        for (int j : array) {
            array[j] = j;
        }
        System.out.println("Basic sort: " + Arrays.toString(array));
        System.out.println("Time taken: " + (endTime - startTime) + " ms");

        startTime = System.currentTimeMillis();
        optimizedSelectionSort(array);
        endTime = System.currentTimeMillis();
        for (int j : array) {
            array[j] = j;
        }
        System.out.println("Optimized sort: " + Arrays.toString(array));
        System.out.println("Time taken: " + (endTime - startTime) + " ms");

    }

    // Basic selection sort
    private static void basicSelectionSort(int[] array) {
        int n = array.length;
        for (int i = 0; i < n - 1; i++) {
            int minIndex = i;

            for (int j = i + 1; j < n; j++) {
                if (array[j] < array[minIndex]) {
                    minIndex = j;
                }
            }

            int minValue = array[minIndex];
            for(int k = minIndex; k > i; k--) {
                array[k] = array[k - 1];
            }
            array[i] = minValue;
        }
    }

    private static void optimizedSelectionSort(int[] array) {
        int n = array.length;

        for (int i = 0; i < n - 1; i++) {
            int minIndex = i;

            for (int j = i + 1; j < n; j++) {
                if (array[j] < array[minIndex]) {
                    minIndex = j;
                }
            }

            int temp = array[i];
            array[i] = array[minIndex];
            array[minIndex] = temp;
        }
    }
}
