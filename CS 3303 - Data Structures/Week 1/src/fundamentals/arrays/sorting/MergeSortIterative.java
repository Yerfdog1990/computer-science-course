package fundamentals.arrays.sorting;

import java.util.Arrays;

public class MergeSortIterative {

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

    public static int[] mergeSort(int[] arr) {
        int n = arr.length;
        int step = 1;

        while (step < n) {
            for (int i = 0; i < n; i += 2 * step) {

                int mid = Math.min(i + step, n);
                int end = Math.min(i + 2 * step, n);

                int[] left = new int[mid - i];
                int[] right = new int[end - mid];

                System.arraycopy(arr, i, left, 0, left.length);
                System.arraycopy(arr, mid, right, 0, right.length);

                int[] merged = merge(left, right);

                for (int j = 0; j < merged.length; j++) {
                    arr[i + j] = merged[j];
                }
            }

            step *= 2;
        }

        return arr;
    }

    public static void main(String[] args) {
        int[] arr = new int[1000];
        for (int i = 0; i < 1000; i++) {
            arr[i] = (int) (Math.random() * 1000);
        }
        // Unsorted array
        System.out.println("Unsorted array: " + Arrays.toString(arr));
        long startTime = System.currentTimeMillis();
        mergeSort(arr);
        long endTime = System.currentTimeMillis();
        System.out.println("Sorted array: " + Arrays.toString(arr));
        System.out.println("Time taken: " + (endTime - startTime) + " ms");
    }
}

