package fundamentals.arrays.searching;

import java.util.Arrays;

public class BinarySearch {

    public static int binarySearch(int[] arr, int targetVal) {
        int left = 0;
        int right = arr.length - 1;

        while (left <= right) {
            int mid = (left + right) / 2;

            if (arr[mid] == targetVal) {
                return mid;
            }

            if (arr[mid] < targetVal) {
                left = mid + 1;
            } else {
                right = mid - 1;
            }
        }

        return -1;
    }

    public static void main(String[] args) {
        int[] myArray = new int[1000];
        for (int i = 0; i < 1000; i++) {
            myArray[i] = (int) (Math.random() * 1000);
        }
        // Unsorted array
        int[] unsortedArray = myArray.clone();
        System.out.println("Unsorted array: " + Arrays.toString(myArray));

        // Sorted array
        Arrays.sort(myArray);
        System.out.println("Sorted array: " + Arrays.toString(myArray));
        int myTarget = 15;

        // Binary search
        long startTime = System.currentTimeMillis();
        int result = binarySearch(myArray, myTarget);
        long endTime = System.currentTimeMillis();
        System.out.println("Time taken for binary search: " + (endTime - startTime) + " ms");

        if (result != -1) {
            System.out.println("Value " + myTarget + " found at index " + result);
        } else {
            System.out.println("Target not found in array.");
        }

        // Linear search
        startTime = System.currentTimeMillis();
        result = LinearSearch.linearForwardSearch(unsortedArray, myTarget);
        endTime = System.currentTimeMillis();
        System.out.println("Time taken for linear search: " + (endTime - startTime) + " ms");

        if (result != -1) {
            System.out.println("Value " + myTarget + " found at index " + result);
        } else {
            System.out.println("Target not found in array.");
        }
    }
}