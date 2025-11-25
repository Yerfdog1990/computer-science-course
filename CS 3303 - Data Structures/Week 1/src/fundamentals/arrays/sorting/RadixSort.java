package fundamentals.arrays.sorting;

import java.util.ArrayList;
import java.util.Arrays;
import java.util.List;

public class RadixSort {

    public static void radixSort(int[] arr) {
        int maxVal = getMax(arr);
        int exp = 1;

        while (maxVal / exp > 0) {
            // Create buckets 0–9
            List<Integer>[] radixArray = new List[10];
            for (int i = 0; i < 10; i++) {
                radixArray[i] = new ArrayList<>();
            }

            // Move values into appropriate buckets (left to right)
            for (int i = 0; i < arr.length; i++) {
                int val = arr[i];
                int index = (val / exp) % 10;
                radixArray[index].add(val);
                // System.out.println(radixArray[index]); -> print buckets
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
        int[] myArray = new int[1000];
        for (int i = 0; i < 1000; i++) {
            myArray[i] = (int) (Math.random() * 1000);
        }

        // Unsorted array
        System.out.println("Unsorted array: " + Arrays.toString(myArray));

        long startTime = System.currentTimeMillis();
        radixSort(myArray);
        long endTime = System.currentTimeMillis();

        System.out.println("Sorted array: " + Arrays.toString(myArray));
        System.out.println("Time taken: " + (endTime - startTime) + " ms");
    }
}

