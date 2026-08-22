package dsa.knapsack;

import org.junit.jupiter.api.Test;
import java.util.List;

import static org.junit.jupiter.api.Assertions.*;

public class KnapsackBacktrackTest {

    @Test
    void givenItemsAndCapacity_whenFindOptimalKnapsack_thenReturnMaxProfitAndBestSubset() {
        KnapsackBacktrack solver = new KnapsackBacktrack();

        List<KnapsackBacktrack.Item> items = List.of(
                new KnapsackBacktrack.Item("A", 1, 11),
                new KnapsackBacktrack.Item("B", 11, 21),
                new KnapsackBacktrack.Item("C", 21, 31)
        );

        solver.solve(items, 35);

        // Maximum profit should be 63 (Items B and C)
        assertEquals(63, solver.maxProfit);

        assertTrue(
                solver.bestSubset.stream()
                        .anyMatch(item -> item.name.equals("B"))
        );

        assertTrue(
                solver.bestSubset.stream()
                        .anyMatch(item -> item.name.equals("C"))
        );
    }

    @Test
    void givenNoItems_whenFindOptimalKnapsack_thenReturnZeroProfitAndEmptySubset() {
        KnapsackBacktrack solver = new KnapsackBacktrack();

        solver.solve(List.of(), 50);

        assertEquals(0, solver.maxProfit);
        assertTrue(solver.bestSubset.isEmpty());
    }

    @Test
    void givenItemsAndZeroCapacity_whenFindOptimalKnapsack_thenReturnZeroProfitAndEmptySubset() {
        KnapsackBacktrack solver = new KnapsackBacktrack();

        List<KnapsackBacktrack.Item> items = List.of(
                new KnapsackBacktrack.Item("A", 10, 100)
        );

        solver.solve(items, 0);

        assertEquals(0, solver.maxProfit);
        assertTrue(solver.bestSubset.isEmpty());
    }
}