package dsa.fibonacci;

import lombok.extern.slf4j.Slf4j;
import org.junit.jupiter.api.Test;

import static org.junit.jupiter.api.Assertions.assertEquals;

@Slf4j
public class FibOptimizedTest {

    @Test
    void givenZeroElement_whenFindNthTermInFibonacciSeries_ThenReturnZero() {
        int result = FibOptimized.findFibonacciNthTerm(0);
        log.info("Nth term for n = 0: {}", result);
        assertEquals(0, result);
    }

    @Test
    void givenOneElement_whenFindNthTermInFibonacciSeries_ThenReturnOne() {
        int result = FibOptimized.findFibonacciNthTerm(1);
        log.info("Nth term for n = 1: {}", result);
        assertEquals(1, result);
    }

    @Test
    void givenTwoElements_whenFindNthTermInFibonacciSeries_ThenReturnOne() {
        int result = FibOptimized.findFibonacciNthTerm(2);
        log.info("Nth term for n = 2: {}", result);
        assertEquals(1, result);
    }

    @Test
    void givenThreeElements_whenFindNthTermInFibonacciSeries_ThenReturnTwo() {
        int result = FibOptimized.findFibonacciNthTerm(3);
        log.info("Nth term for n = 3: {}", result);
        assertEquals(2, result);
    }

    @Test
    void givenFourElements_whenFindNthTermInFibonacciSeries_ThenReturnThree() {
        int result = FibOptimized.findFibonacciNthTerm(4);
        log.info("Nth term for n = 4: {}", result);
        assertEquals(3, result);
    }

    @Test
    void givenFiveElements_whenFindNthTermInFibonacciSeries_ThenReturnFive() {
        int result = FibOptimized.findFibonacciNthTerm(5);
        log.info("Nth term for n = 5: {}", result);
        assertEquals(5, result);
    }
}
