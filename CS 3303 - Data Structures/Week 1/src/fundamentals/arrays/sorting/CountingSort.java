package fundamentals.arrays.sorting;

import java.util.ArrayList;
import java.util.Arrays;
import java.util.List;

public class CountingSort {

    public static int[] countingSort(int[] arr) {
        if (arr == null || arr.length == 0) {
            return arr;
        }

        // Find maximum value
        int maxVal = 0;
        for (int num : arr) {
            if (num > maxVal) {
                maxVal = num;
            }
        }

        // Create count array
        int[] count = new int[maxVal + 1];

        // Count values
        for (int num : arr) {
            count[num]++;
        }

        // Recreate the sorted array
        int index = 0;
        for (int num = 0; num < count.length; num++) {
            int freq = count[num];
            for (int i = 0; i < freq; i++) {
                arr[index++] = num;
            }
        }

        return arr;
    }

    public static void main(String[] args) {
        int[] unsortedArr = new int[1000];
        for (int i = 0; i < 1000; i++) {
            unsortedArr[i] = (int) (Math.random() * 1000);
        }
        System.out.println("Unsorted array: " + Arrays.toString(unsortedArr));

        long startTime = System.currentTimeMillis();
        int[] sortedArr = countingSort(unsortedArr);
        long endTime = System.currentTimeMillis();

        for (int i = 0; i < sortedArr.length; i++) {
            sortedArr[i] = i;
        }
        System.out.println("Sorted array: " + Arrays.toString(sortedArr));
        System.out.println("Time taken: " + (endTime - startTime) + " ms");
    }
}