package fundamentals.arrays.searching;


import java.util.Arrays;

public class LinearSearch {

    public static int linearForwardSearch(int[] arr, int targetVal) {
        for (int i = 0; i < arr.length; i++) {
            if (arr[i] == targetVal) {
                return i;
            }
        }
        return -1;
    }

    public static int linearBackwardSearch(int[] arr, int targetVal) {
        for (int i = arr.length; i > 0; i--) {
            if (arr[i - 1] == targetVal) {
                return i - 1;
            }
        }
        return -1;
    }

    public static void main(String[] args) {
        int[] arr = new int[1000];
        for (int i = 0; i < 1000; i++) {
            arr[i] = (int) (Math.random() * 1000);
        }

        // Unsorted array
        System.out.println("Unsorted array: " + Arrays.toString(arr));
        int targetVal = 9;

        // Forward search
        long startTime = System.currentTimeMillis();
        int result = linearForwardSearch(arr, targetVal);
        long endTime = System.currentTimeMillis();
        System.out.println("Time taken: " + (endTime - startTime) + " ms");

        if (result != -1) {
            System.out.println("Value " + targetVal + " found at index " + result);
        } else {
            System.out.println("Value " + targetVal + " not found");
        }

        // Backward search
        startTime = System.currentTimeMillis();
        result = linearBackwardSearch(arr, targetVal);
        endTime = System.currentTimeMillis();
        System.out.println("Time taken: " + (endTime - startTime) + " ms");

        if (result != -1) {
            System.out.println("Value " + targetVal + " found at index " + result);
        } else {
            System.out.println("Value " + targetVal + " not found");
        }
    }
}
