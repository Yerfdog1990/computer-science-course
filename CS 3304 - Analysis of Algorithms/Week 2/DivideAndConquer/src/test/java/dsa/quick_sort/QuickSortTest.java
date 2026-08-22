package dsa.quick_sort;

import lombok.extern.slf4j.Slf4j;
import org.junit.jupiter.api.Test;

import java.util.Arrays;

import static org.junit.jupiter.api.Assertions.assertEquals;

@Slf4j
public class QuickSortTest {


    @Test
    void givenUnsortedArray_whenPerformQuickSort_thenReturnSortedArray() {
        int[] arr = {12, 32, 13, 4, 25};
        log.info("Original: {}", Arrays.toString(arr));
        int[] sorted = QuickSort.quickSort(arr, 0, arr.length - 1);
        log.info("Sorted: {}", Arrays.toString(sorted));
        assertEquals(4, sorted[0]);
        assertEquals(12, sorted[1]);
        assertEquals(13, sorted[2]);
        assertEquals(25, sorted[3]);
        assertEquals(32, sorted[4]);
    }

}