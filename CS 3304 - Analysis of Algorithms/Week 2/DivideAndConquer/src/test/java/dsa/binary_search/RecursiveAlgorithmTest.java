package dsa.binary_search;

import lombok.extern.slf4j.Slf4j;
import org.junit.jupiter.api.Test;

import static org.junit.jupiter.api.Assertions.assertEquals;

@Slf4j
public class RecursiveAlgorithmTest {

    @Test
    void givenEmptyArray_whenSearchingAnElement_thenReturnMinusOne() {
        int[] arr = {};
        int target = 5;
        int result = RecursiveAlgorithm.binarySearch(arr, 0, -1, target);
        log.info("Result: {}", result);
        assertEquals(-1, result);
    }

    @Test
    void givenSingleElementArray_whenSearchingAnElement_thenReturnIndexZero() {
        int[] arr = {5};
        int target = 5;
        int result = RecursiveAlgorithm.binarySearch(arr, 0, 0, target);
        log.info("Result: {}", result);
        assertEquals(0, result);
    }

    @Test
    void givenArrayWithOneElement_whenSearchingForNonExistingElement_thenReturnMinusOne() {
        int[] arr = {1};
        int target = 2;
        int result = RecursiveAlgorithm.binarySearch(arr, 0, 0, target);
        log.info("Result: {}", result);
        assertEquals(-1, result);
    }

    @Test
    void givenArrayWithMultipleElements_whenSearchingForExistingElement_thenReturnIndex() {
        int[] arr = {1, 2, 3, 4, 5};
        int target = 3;
        int result = RecursiveAlgorithm.binarySearch(arr, 0, 4, target);
        log.info("Result: {}", result);
        assertEquals(2, result);
    }

    @Test
    void givenArrayWithMultipleElements_whenSearchingForNonExistingElement_thenReturnMinusOne() {
        int[] arr = {1, 2, 3, 4, 5};
        int target = 6;
        int result = RecursiveAlgorithm.binarySearch(arr, 0, 4, target);
        log.info("Result: {}", result);
        assertEquals(-1, result);
    }

    @Test
    void givenArrayWithMultipleElements_whenSearchingForFirstElement_thenReturnIndexZero() {
        int[] arr = {1, 2, 3, 4, 5};
        int target = 1;
        int result = RecursiveAlgorithm.binarySearch(arr, 0, 4, target);
        log.info("Result: {}", result);
        assertEquals(0, result);
    }

    @Test
    void givenArrayWithMultipleElements_whenSearchingForLastElement_thenReturnLastIndex() {
        int[] arr = {1, 2, 3, 4, 5};
        int target = 5;
        int result = RecursiveAlgorithm.binarySearch(arr, 0, 4, target);
        log.info("Result: {}", result);
        assertEquals(4, result);
    }
}
