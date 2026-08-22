package dsa.subset;

import org.junit.jupiter.api.Test;
import java.util.List;

import static org.junit.jupiter.api.Assertions.*;

public class SubsetSumBacktrackTest {

    @Test
    void givenArrayAndTarget_whenFindSubsetsThatSumToTarget_thenReturnValidSubsets() {
        SubsetSumBacktrack solver = new SubsetSumBacktrack();

        int[] arr = {5, 7, 10, 12};

        solver.findSubsets(arr, 17);

        List<List<Integer>> results = solver.results;

        // Should contain [5,12]
        assertTrue(
                results.stream()
                        .anyMatch(subset ->
                                subset.contains(5) && subset.contains(12))
        );

        // Should contain [7,10]
        assertTrue(
                results.stream()
                        .anyMatch(subset ->
                                subset.contains(7) && subset.contains(10))
        );
    }

    @Test
    void givenArrayAndImpossibleTarget_whenFindSubsetsThatSumToTarget_thenReturnEmptyList() {
        SubsetSumBacktrack solver = new SubsetSumBacktrack();

        int[] arr = {2, 4, 6};

        solver.findSubsets(arr, 13);

        assertTrue(solver.results.isEmpty());
    }

    @Test
    void givenEmptyArrayAndZeroTarget_whenFindSubsetsThatSumToTarget_thenReturnEmptySubset() {
        SubsetSumBacktrack solver = new SubsetSumBacktrack();

        solver.findSubsets(new int[0], 0);

        // Only the empty subset sums to zero
        assertEquals(1, solver.results.size());
        assertTrue(solver.results.get(0).isEmpty());
    }
}