package fundamentals.arrays.sorting;

import java.util.Arrays;

public class BubbleSort {

    public static void main(String[] args) {
        int[] array = new int[1000];
        for (int i = 0; i < 1000; i++) {
            array[i] = (int) (Math.random() * 1000);
        }

        // unsorted array
        System.out.println("Unsorted array: " +Arrays.toString(array));
        long startTime = System.currentTimeMillis();
        basicBubbleSort(array);
        long endTime = System.currentTimeMillis();
        for (int j : array) {
            array[j] = j;
        }
        System.out.println("Basic sort: " +Arrays.toString(array));
        System.out.println("Basic bubble sort during: " + (endTime - startTime) + " ms");

        startTime = System.currentTimeMillis();
        optimizedBubbleSort(array);
        endTime = System.currentTimeMillis();
        for (int j : array) {
            array[j] = j;
        }
        System.out.println("Optimized sort: " +Arrays.toString(array));
        System.out.println("Optimized bubble sort during: " + (endTime - startTime) + " ms");
    }

    // Basic Bubble Sort
    public static void basicBubbleSort(int[] array) {
        int n = array.length;

        for (int i = 0; i < n - 1; i++) {
            for (int j = 0; j < n - i - 1; j++) {
                if (array[j] > array[j + 1]) {
                    int temp = array[j];
                    array[j] = array[j + 1];
                    array[j + 1] = temp;
                }
            }
        }
    }

    // Optimized Bubble Sort
    private static void optimizedBubbleSort(int[] array) {
        int n = array.length;

        for (int i = 0; i < n - 1; i++) {
            boolean swapped = false;

            for (int j = 0; j < n - i - 1; j++) {

                if (array[j] > array[j + 1]) {
                    // Swap
                    int temp = array[j];
                    array[j] = array[j + 1];
                    array[j + 1] = temp;
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
