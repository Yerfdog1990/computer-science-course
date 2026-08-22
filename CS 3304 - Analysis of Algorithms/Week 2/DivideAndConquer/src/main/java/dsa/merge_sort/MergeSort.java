package dsa.merge_sort;

public class MergeSort {

    /**
     * Merges two sorted subarrays into one sorted array.
     *
     * Left subarray:  arr[left...mid]
     * Right subarray: arr[mid+1...right]
     */
    public static void merge(int[] arr, int left, int mid, int right) {

        // Calculate the sizes of the two subarrays
        int leftSize = mid - left + 1;
        int rightSize = right - mid;

        // Create temporary arrays
        int[] leftArray = new int[leftSize];
        int[] rightArray = new int[rightSize];

        // Copy data into the temporary left array
        for (int i = 0; i < leftSize; i++) {
            leftArray[i] = arr[left + i];
        }

        // Copy data into the temporary right array
        for (int j = 0; j < rightSize; j++) {
            rightArray[j] = arr[mid + 1 + j];
        }

        // Index for leftArray
        int leftIndex = 0;

        // Index for rightArray
        int rightIndex = 0;

        // Index for merged array
        int mergedIndex = left;

        // Compare elements from both arrays and copy the smaller one
        while (leftIndex < leftSize && rightIndex < rightSize) {

            if (leftArray[leftIndex] <= rightArray[rightIndex]) {
                arr[mergedIndex] = leftArray[leftIndex];
                leftIndex++;
            } else {
                arr[mergedIndex] = rightArray[rightIndex];
                rightIndex++;
            }

            mergedIndex++;
        }

        // Copy any remaining elements from the left array
        while (leftIndex < leftSize) {
            arr[mergedIndex] = leftArray[leftIndex];
            leftIndex++;
            mergedIndex++;
        }

        // Copy any remaining elements from the right array
        while (rightIndex < rightSize) {
            arr[mergedIndex] = rightArray[rightIndex];
            rightIndex++;
            mergedIndex++;
        }
    }

    /**
     * Recursively divides the array into halves,
     * sorts each half, then merges them.
     */
    public static int[] mergeSort(int[] arr, int left, int right) {

        // Base case: stop when the subarray has one or zero elements
        if (left >= right) {
            return arr;
        }

        // Find the middle index
        int mid = left + (right - left) / 2;

        // Sort the left half
        mergeSort(arr, left, mid);

        // Sort the right half
        mergeSort(arr, mid + 1, right);

        // Merge the two sorted halves
        merge(arr, left, mid, right);

        return arr;
    }

    /**
     * Prints an array.
     */
    public static void printArray(int[] arr) {
        for (int value : arr) {
            System.out.print(value + " ");
        }
        System.out.println();
    }

    public static void main(String[] args) {

        int[] arr = {38, 27, 43, 10};

        System.out.print("Before sorting: ");
        printArray(arr);

        mergeSort(arr, 0, arr.length - 1);

        System.out.print("After sorting:  ");
        printArray(arr);
    }
}