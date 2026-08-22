package dsa.binary_search;

public class IterativeAlgorithm {

    static int binarySearch(int[] arr, int target) {
        int low = 0, high = arr.length - 1;

        while (low <= high) {
            int mid = low + (high - low) / 2;

            // Check if target is present at mid
            if (arr[mid] == target)
                return mid;

            // If target is greater, ignore left half
            if (arr[mid] < target)
                low = mid + 1;

                // If target is smaller, ignore right half
            else
                high = mid - 1;
        }

        // Element was not present
        return -1;
    }

    // Finding the First Occurrence, e.g. arr = {1, 2, 2, 2, 5}, target = 2  →  returns 1
    public static int firstOccurrence(int[] arr, int target) {
        int start = 0, end = arr.length - 1;
        int answer = -1;

        while (start <= end) {
            int mid = start + (end - start) / 2;

            if (arr[mid] == target) {
                answer = mid;       // record this as a candidate
                end = mid - 1;      // keep searching left for an earlier one
            } else if (arr[mid] < target) {
                start = mid + 1;
            } else {
                end = mid - 1;
            }
        }
        return answer;
    }

    // Finding the Last Occurrence, e.g. arr = {1, 2, 2, 2, 5}, target = 2  →  returns 3
    public static int lastOccurrence(int[] arr, int target) {
        int start = 0, end = arr.length - 1;
        int answer = -1;

        while (start <= end) {
            int mid = start + (end - start) / 2;

            if (arr[mid] == target) {
                answer = mid;       // record this as a candidate
                start = mid + 1;    // keep searching right for a later one
            } else if (arr[mid] < target) {
                start = mid + 1;
            } else {
                end = mid - 1;
            }
        }
        return answer;
    }
}
