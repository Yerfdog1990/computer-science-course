package dsa.merge_sort;

import dsa.quick_sort.QuickSort;
import lombok.extern.slf4j.Slf4j;
import org.junit.jupiter.api.Test;

import java.util.Arrays;

import static org.junit.jupiter.api.Assertions.assertEquals;


@Slf4j
public class MergeSortTest {
    //@Test
    void givenUnsortedArray_whenPerformMergeSort_thenReturnSortedArray() {
        int[] arr = {12, 32, 13, 4, 25};
        log.info("Original: {}", Arrays.toString(arr));
        int[] sorted = MergeSort.mergeSort(arr, 0, arr.length - 1);
        log.info("Sorted: {}", Arrays.toString(sorted));
        assertEquals(4, sorted[0]);
        assertEquals(12, sorted[1]);
        assertEquals(13, sorted[2]);
        assertEquals(25, sorted[3]);
        assertEquals(32, sorted[4]);
    }

    @Test
    void givenUnsortedArrayOf10Elements_whenPerformingEmpiricalTestingOfMergeSortAndQuickSort_thenReturnSortingDuration() {
        int[] arr10A = {
                38, 27, 43, 3, 9,
                82, 10, 15, 61, 50
        };
        // Implementation for empirical testing
        long startTime = System.nanoTime();
        int[] quickSorted = QuickSort.quickSort(arr10A, 0, arr10A.length - 1);
        long endTime = System.nanoTime();
        log.info("Quick sorting duration for 10 elements: {} ns", endTime - startTime);

        int[] arr10B = {
                38, 27, 43, 3, 9,
                82, 10, 15, 61, 50
        };
        // Implementation for empirical testing
        startTime = System.nanoTime();
        int[] mergeSorted = MergeSort.mergeSort(arr10B, 0, arr10B.length - 1);
        endTime = System.nanoTime();
        log.info("Merge sorting duration for 10 elements: {} ns", endTime - startTime);

        // Confirm both that first and last elements of both sorted arrays are the same
        assertEquals(quickSorted[0], mergeSorted[0]);
        assertEquals(quickSorted[quickSorted.length - 1], mergeSorted[mergeSorted.length - 1]);
    }

    @Test
    void givenUnsortedArrayOf50Elements_whenPerformingEmpiricalTestingOfMergeSortAndQuickSort_thenReturnSortingDuration() {
        int[] arr50A = {
                91, 12, 57, 34, 78, 5, 66, 23, 89, 41,
                17, 95, 62, 8, 49, 73, 30, 54, 19, 84,
                2, 97, 46, 68, 25, 11, 99, 38, 76, 53,
                7, 64, 21, 87, 40, 14, 70, 31, 58, 93,
                4, 81, 27, 60, 16, 52, 35, 98, 44, 72
        };

        // Implementation for empirical testing
        long startTime = System.nanoTime();
        int[] quickSorted = QuickSort.quickSort(arr50A, 0, arr50A.length - 1);
        long endTime = System.nanoTime();
        log.info("Quick sorting duration for 50 elements: {} ns", endTime - startTime);


        int[] arr50B = {
                91, 12, 57, 34, 78, 5, 66, 23, 89, 41,
                17, 95, 62, 8, 49, 73, 30, 54, 19, 84,
                2, 97, 46, 68, 25, 11, 99, 38, 76, 53,
                7, 64, 21, 87, 40, 14, 70, 31, 58, 93,
                4, 81, 27, 60, 16, 52, 35, 98, 44, 72
        };
        // Implementation for empirical testing
        startTime = System.nanoTime();
        int[] mergeSorted = MergeSort.mergeSort(arr50B, 0, arr50B.length - 1);
        endTime = System.nanoTime();
        log.info("Merge sorting duration for 50 elements: {} ns", endTime - startTime);

        // Confirm both arrays have the same length
        assertEquals(quickSorted.length, mergeSorted.length);
    }

    @Test
    void givenUnsortedArrayOf100Elements_whenPerformingEmpiricalTestingOfMergeSortAndQuickSort_thenReturnSortingDuration() {
        int[] arr100A = {
                91, 12, 57, 34, 78, 5, 66, 23, 89, 41,
                17, 95, 62, 8, 49, 73, 30, 54, 19, 84,
                2, 97, 46, 68, 25, 11, 99, 38, 76, 53,
                7, 64, 21, 87, 40, 14, 70, 31, 58, 93,
                4, 81, 27, 60, 16, 52, 35, 98, 44, 72,
                13, 88, 56, 1, 75, 39, 94, 28, 63, 47,
                6, 80, 22, 69, 37, 96, 50, 15, 85, 32,
                59, 9, 90, 42, 71, 18, 65, 24, 83, 48,
                3, 92, 55, 20, 77, 36, 61, 10, 86, 29,
                74, 45, 67, 26, 82, 33, 79, 43, 51, 100
        };
        // Implementation for empirical testing
        long startTime = System.nanoTime();
        int[] quickSorted = QuickSort.quickSort(arr100A, 0, arr100A.length - 1);
        long endTime = System.nanoTime();
        log.info("Quick sorting duration for 100 elements: {} ns", endTime - startTime);

        int[] arr100B = {
                91, 12, 57, 34, 78, 5, 66, 23, 89, 41,
                17, 95, 62, 8, 49, 73, 30, 54, 19, 84,
                2, 97, 46, 68, 25, 11, 99, 38, 76, 53,
                7, 64, 21, 87, 40, 14, 70, 31, 58, 93,
                4, 81, 27, 60, 16, 52, 35, 98, 44, 72,
                13, 88, 56, 1, 75, 39, 94, 28, 63, 47,
                6, 80, 22, 69, 37, 96, 50, 15, 85, 32,
                59, 9, 90, 42, 71, 18, 65, 24, 83, 48,
                3, 92, 55, 20, 77, 36, 61, 10, 86, 29,
                74, 45, 67, 26, 82, 33, 79, 43, 51, 100
        };
        // Implementation for empirical testing
        startTime = System.nanoTime();
        int[] mergeSorted = MergeSort.mergeSort(arr100B, 0, arr100B.length - 1);
        endTime = System.nanoTime();
        log.info("Merge sorting duration for 100 elements: {} ns", endTime - startTime);

        // Confirm both arrays have the same length
        assertEquals(quickSorted.length, mergeSorted.length);
    }

    @Test
    void givenUnsortedArrayOf150Elements_whenPerformingEmpiricalTestingOfMergeSortAndQuickSort_thenReturnSortingDuration() {
        int[] arr150A = {
                91, 12, 57, 34, 78, 5, 66, 23, 89, 41,
                17, 95, 62, 8, 49, 73, 30, 54, 19, 84,
                2, 97, 46, 68, 25, 11, 99, 38, 76, 53,
                7, 64, 21, 87, 40, 14, 70, 31, 58, 93,
                4, 81, 27, 60, 16, 52, 35, 98, 44, 72,
                13, 88, 56, 1, 75, 39, 94, 28, 63, 47,
                6, 80, 22, 69, 37, 96, 50, 15, 85, 32,
                59, 9, 90, 42, 71, 18, 65, 24, 83, 48,
                3, 92, 55, 20, 77, 36, 61, 10, 86, 29,
                74, 45, 67, 26, 82, 33, 79, 43, 51, 100,
                117, 102, 145, 108, 126, 139, 111, 134, 149, 105,
                121, 150, 114, 132, 103, 147, 119, 136, 124, 141,
                106, 128, 143, 109, 130, 115, 148, 120, 137, 104,
                125, 146, 112, 133, 118, 140, 107, 129, 144, 110,
                131, 116, 135, 101, 142, 123, 127, 138, 113, 122
        };
        // Implementation for empirical testing
        long startTime = System.nanoTime();
        int[] quickSorted = QuickSort.quickSort(arr150A, 0, arr150A.length - 1);
        long endTime = System.nanoTime();
        log.info("Quick sorting duration for 150 elements: {} ns", endTime - startTime);

        int[] arr150B = {
                91, 12, 57, 34, 78, 5, 66, 23, 89, 41,
                17, 95, 62, 8, 49, 73, 30, 54, 19, 84,
                2, 97, 46, 68, 25, 11, 99, 38, 76, 53,
                7, 64, 21, 87, 40, 14, 70, 31, 58, 93,
                4, 81, 27, 60, 16, 52, 35, 98, 44, 72,
                13, 88, 56, 1, 75, 39, 94, 28, 63, 47,
                6, 80, 22, 69, 37, 96, 50, 15, 85, 32,
                59, 9, 90, 42, 71, 18, 65, 24, 83, 48,
                3, 92, 55, 20, 77, 36, 61, 10, 86, 29,
                74, 45, 67, 26, 82, 33, 79, 43, 51, 100,
                117, 102, 145, 108, 126, 139, 111, 134, 149, 105,
                121, 150, 114, 132, 103, 147, 119, 136, 124, 141,
                106, 128, 143, 109, 130, 115, 148, 120, 137, 104,
                125, 146, 112, 133, 118, 140, 107, 129, 144, 110,
                131, 116, 135, 101, 142, 123, 127, 138, 113, 122
        };
        // Implementation for empirical testing
        startTime = System.nanoTime();
        int[] mergeSorted = MergeSort.mergeSort(arr150B, 0, arr150B.length - 1);
        endTime = System.nanoTime();
        log.info("Merge sorting duration for 150 elements: {} ns", endTime - startTime);

        // Confirm both arrays have the same length
        assertEquals(quickSorted.length, mergeSorted.length);
    }

    @Test
    void givenUnsortedArrayOf200Elements_whenPerformingEmpiricalTestingOfMergeSortAndQuickSort_thenReturnSortingDuration() {
        int[] arr200A = {
                91, 12, 57, 34, 78, 5, 66, 23, 89, 41,
                17, 95, 62, 8, 49, 73, 30, 54, 19, 84,
                2, 97, 46, 68, 25, 11, 99, 38, 76, 53,
                7, 64, 21, 87, 40, 14, 70, 31, 58, 93,
                4, 81, 27, 60, 16, 52, 35, 98, 44, 72,
                13, 88, 56, 1, 75, 39, 94, 28, 63, 47,
                6, 80, 22, 69, 37, 96, 50, 15, 85, 32,
                59, 9, 90, 42, 71, 18, 65, 24, 83, 48,
                3, 92, 55, 20, 77, 36, 61, 10, 86, 29,
                74, 45, 67, 26, 82, 33, 79, 43, 51, 100,
                117, 102, 145, 108, 126, 139, 111, 134, 149, 105,
                121, 150, 114, 132, 103, 147, 119, 136, 124, 141,
                106, 128, 143, 109, 130, 115, 148, 120, 137, 104,
                125, 146, 112, 133, 118, 140, 107, 129, 144, 110,
                131, 116, 135, 101, 142, 123, 127, 138, 113, 122,
                156, 199, 174, 153, 181, 162, 195, 170, 154, 188,
                167, 200, 158, 176, 151, 193, 164, 184, 160, 197,
                155, 179, 166, 190, 152, 185, 171, 198, 157, 180,
                165, 192, 159, 177, 169, 196, 161, 183, 173, 194,
                168, 187, 163, 191, 172, 186, 175, 182, 178, 189
        };
        // Implementation for empirical testing
        long startTime = System.nanoTime();
        int[] quickSorted = QuickSort.quickSort(arr200A, 0, arr200A.length - 1);
        long endTime = System.nanoTime();
        log.info("Quick sorting duration for 200 elements: {} ns", endTime - startTime);

        int[] arr200B = {
                91, 12, 57, 34, 78, 5, 66, 23, 89, 41,
                17, 95, 62, 8, 49, 73, 30, 54, 19, 84,
                2, 97, 46, 68, 25, 11, 99, 38, 76, 53,
                7, 64, 21, 87, 40, 14, 70, 31, 58, 93,
                4, 81, 27, 60, 16, 52, 35, 98, 44, 72,
                13, 88, 56, 1, 75, 39, 94, 28, 63, 47,
                6, 80, 22, 69, 37, 96, 50, 15, 85, 32,
                59, 9, 90, 42, 71, 18, 65, 24, 83, 48,
                3, 92, 55, 20, 77, 36, 61, 10, 86, 29,
                74, 45, 67, 26, 82, 33, 79, 43, 51, 100,
                117, 102, 145, 108, 126, 139, 111, 134, 149, 105,
                121, 150, 114, 132, 103, 147, 119, 136, 124, 141,
                106, 128, 143, 109, 130, 115, 148, 120, 137, 104,
                125, 146, 112, 133, 118, 140, 107, 129, 144, 110,
                131, 116, 135, 101, 142, 123, 127, 138, 113, 122,
                156, 199, 174, 153, 181, 162, 195, 170, 154, 188,
                167, 200, 158, 176, 151, 193, 164, 184, 160, 197,
                155, 179, 166, 190, 152, 185, 171, 198, 157, 180,
                165, 192, 159, 177, 169, 196, 161, 183, 173, 194,
                168, 187, 163, 191, 172, 186, 175, 182, 178, 189
        };

        // Implementation for empirical testing
        startTime = System.nanoTime();
        int[] mergeSorted = MergeSort.mergeSort(arr200B, 0, arr200B.length - 1);
        endTime = System.nanoTime();
        log.info("Merge sorting duration for 200 elements: {} ns", endTime - startTime);

        // Confirm both arrays have the same length
        assertEquals(quickSorted.length, mergeSorted.length);
    }
}
