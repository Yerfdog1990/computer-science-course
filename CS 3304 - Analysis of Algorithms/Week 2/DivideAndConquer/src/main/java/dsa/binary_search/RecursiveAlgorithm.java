package dsa.binary_search;

public class RecursiveAlgorithm {

    static int binarySearch(int[] arr, int low, int high, int target) {
        if (high >= low) {
            int mid = low + (high - low) / 2;

            // Element is present at the middle
            if (arr[mid] == target)
                return mid;

            // Element is smaller — search left subarray
            if (arr[mid] > target)
                return binarySearch(arr, low, mid - 1, target);

            // Element is larger — search right subarray
            return binarySearch(arr, mid + 1, high, target);
        }

        // Element not present
        return -1;
    }
}
