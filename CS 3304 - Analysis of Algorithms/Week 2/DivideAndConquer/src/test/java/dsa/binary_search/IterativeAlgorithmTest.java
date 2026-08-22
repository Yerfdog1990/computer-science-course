package dsa.binary_search;

import lombok.extern.slf4j.Slf4j;
import org.junit.jupiter.api.Test;

import static org.junit.jupiter.api.Assertions.assertEquals;
import static org.junit.jupiter.api.Assertions.assertTrue;

@Slf4j
public class IterativeAlgorithmTest {

    @Test
    void givenAnEmptyArray_whenSearching_thenReturnMinusOne() {
        int[] arr = {};
        int target = 1;
        int result = IterativeAlgorithm.binarySearch(arr, target);
        log.info("Result: {}", result);
        assertEquals(-1, result);
    }

    @Test
    void givenAnArrayWithOneElement_whenSearchingForExistingElement_thenReturnIndex() {
        int[] arr = {1};
        int target = 1;
        int result = IterativeAlgorithm.binarySearch(arr, target);
        log.info("Result: {}", result);
        assertEquals(0, result);
    }

    @Test
    void givenAnArrayWithOneElement_whenSearchingForNonExistingElement_thenReturnMinusOne() {
        int[] arr = {1};
        int target = 2;
        int result = IterativeAlgorithm.binarySearch(arr, target);
        log.info("Result: {}", result);
        assertEquals(-1, result);
    }

    @Test
    void givenAnArray_whenSearchingForExistingElement_thenReturnIndex() {
        int[] arr = {1, 2, 3, 4, 5};
        int target = 3;
        int result = IterativeAlgorithm.binarySearch(arr, target);
        log.info("Result: {}", result);
        assertEquals(2, result);
    }

    @Test
    void givenAnArray_whenSearchingForNonExistingElement_thenReturnMinusOne() {
        int[] arr = {1, 2, 3, 4, 5};
        int target = 6;
        int result = IterativeAlgorithm.binarySearch(arr, target);
        log.info("Result: {}", result);
        assertEquals(-1, result);
    }

    @Test
    void givenAnArray_whenSearchingForElementAtBeginning_thenReturnIndex() {
        int[] arr = {1, 2, 3, 4, 5};
        int target = 1;
        int result = IterativeAlgorithm.binarySearch(arr, target);
        log.info("Result: {}", result);
        assertEquals(0, result);
    }

    @Test
    void givenAnArray_whenSearchingForElementAtEnd_thenReturnIndex() {
        int[] arr = {1, 2, 3, 4, 5};
        int target = 5;
        int result = IterativeAlgorithm.binarySearch(arr, target);
        log.info("Result: {}", result);
        assertEquals(4, result);
    }

    @Test
    void givenAnArray_whenSearchingForElementInMiddle_thenReturnIndex() {
        int[] arr = {1, 2, 3, 4, 5};
        int target = 3;
        int result = IterativeAlgorithm.binarySearch(arr, target);
        log.info("Result: {}", result);
        assertEquals(2, result);
    }

    @Test
    void givenAnArray_whenSearchingForRepeatedElement_thenReturnAnyIndex() {
        int[] arr = {1, 2, 2, 2, 5};
        int target = 2;
        int result = IterativeAlgorithm.binarySearch(arr, target);
        log.info("Result: {}", result);
        // then return any index where 2 is found (0, 1, or 2)
        assertTrue(result >= 0 && result < arr.length);
        assertEquals(2, arr[result]);
    }

    @Test
    void givenAnArray_whenFindingFirstOccurrence_thenReturnFirstIndex() {
        int[] arr = {1, 2, 2, 2, 5};
        int target = 2;
        int result = IterativeAlgorithm.firstOccurrence(arr, target);
        log.info("Result: {}", result);
        assertEquals(1, result);
    }

    @Test
    void givenAnArray_whenFindingLastOccurrence_thenReturnLastIndex() {
        int[] arr = {1, 2, 2, 2, 5};
        int target = 2;
        int result = IterativeAlgorithm.lastOccurrence(arr, target);
        log.info("Result: {}", result);
        assertEquals(3, result);
    }

    @Test
    void givenAnArray_whenFindingFirstOccurrenceOfNonExistingElement_thenReturnMinusOne() {
        int[] arr = {1, 2, 2, 2, 5};
        int target = 3;
        int result = IterativeAlgorithm.firstOccurrence(arr, target);
        log.info("Result: {}", result);
        assertEquals(-1, result);
    }

    @Test
    void givenAnArray_whenFindingLastOccurrenceOfNonExistingElement_thenReturnMinusOne() {
        int[] arr = {1, 2, 2, 2, 5};
        int target = 3;
        int result = IterativeAlgorithm.lastOccurrence(arr, target);
        log.info("Result: {}", result);
        assertEquals(-1, result);
    }
}


