package fundamentals.arrays.sorting;

import java.util.Arrays;

public class MergeSortRecursive {

    public static int[] mergeSort(int[] arr) {
        if (arr.length <= 1) {
            return arr;
        }

        int mid = arr.length / 2;

        int[] left = new int[mid];
        int[] right = new int[arr.length - mid];

        System.arraycopy(arr, 0, left, 0, mid);
        System.arraycopy(arr, mid, right, 0, arr.length - mid);

        int[] sortedLeft = mergeSort(left);
        int[] sortedRight = mergeSort(right);

        return merge(sortedLeft, sortedRight);
    }

    private static int[] merge(int[] left, int[] right) {
        int[] result = new int[left.length + right.length];
        int i = 0, j = 0, k = 0;

        while (i < left.length && j < right.length) {
            if (left[i] < right[j]) {
                result[k++] = left[i++];
            } else {
                result[k++] = right[j++];
            }
        }

        while (i < left.length) {
            result[k++] = left[i++];
        }

        while (j < right.length) {
            result[k++] = right[j++];
        }

        return result;
    }

    public static void main(String[] args) {
        int[] myArray = new int[1000];
        for (int i = 0; i < 1000; i++) {
            myArray[i] = (int) (Math.random() * 1000);
        }

        // Unsorted array
        System.out.println("Unsorted array: " + Arrays.toString(myArray));

        long startTime = System.currentTimeMillis();
        int[] sortedArray = mergeSort(myArray);
        long endTime = System.currentTimeMillis();

        System.out.println("Sorted array: " + Arrays.toString(sortedArray));
        System.out.println("Time taken: " + (endTime - startTime) + " ms");
    }
}
