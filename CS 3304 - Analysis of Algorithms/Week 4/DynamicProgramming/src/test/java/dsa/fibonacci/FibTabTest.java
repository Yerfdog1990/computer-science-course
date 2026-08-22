package dsa.fibonacci;

import lombok.extern.slf4j.Slf4j;
import org.junit.jupiter.api.Test;

import static org.junit.jupiter.api.Assertions.assertEquals;

@Slf4j
public class FibTabTest {

    @Test
    void givenZeroElement_whenFindNthTermInFibonacciSeries_ThenReturnZero() {
        int result = FibTab.findFibonacciNthTerm(0);
        log.info("Nth term for n = 0: {}", result);
        assertEquals(0, result);
    }

    @Test
    void givenOneElement_whenFindNthTermInFibonacciSeries_ThenReturnOne() {
        int result = FibTab.findFibonacciNthTerm(1);
        log.info("Nth term for n = 1: {}", result);
        assertEquals(1, result);
    }

    @Test
    void givenTwoElements_whenFindNthTermInFibonacciSeries_ThenReturnOne() {
        int result = FibTab.findFibonacciNthTerm(2);
        log.info("Nth term for n = 2: {}", result);
        assertEquals(1, result);
    }

    @Test
    void givenThreeElements_whenFindNthTermInFibonacciSeries_ThenReturnTwo() {
        int result = FibTab.findFibonacciNthTerm
                (3);
        log.info("Nth term for n = 3: {}", result);
        assertEquals(2, result);
    }

    @Test
    void givenFourElements_whenFindNthTermInFibonacciSeries_ThenReturnThree() {
        int result = FibTab.findFibonacciNthTerm(4);
        log.info("Nth term for n = 4: {}", result);
        assertEquals(3, result);
    }

    @Test
    void givenFiveElements_whenFindNthTermInFibonacciSeries_ThenReturnFive() {
        int result = FibTab.findFibonacciNthTerm(5);
        log.info("Nth term for n = 5: {}", result);
        assertEquals(5, result);
    }
}
