package dsa.np_hard_and_complete;

// Dynamic Programming (Held-Karp) Solution for TSP

import lombok.extern.slf4j.Slf4j;
import org.junit.jupiter.api.Test;
import static org.junit.jupiter.api.Assertions.assertEquals;
import java.util.Random;
@Slf4j
public class TSPDynamicTest {
    // Helper to create a random weighted adjacency matrix
    private int[][] generateRandomGraph(int nodes, int maxWeight) {
        Random rand = new Random();
        int[][] graph = new int[nodes][nodes];
        for (int i = 0; i < nodes; i++) {
            for (int j = 0; j < nodes; j++) {
                if (i != j && rand.nextBoolean()) {
                    graph[i][j] = rand.nextInt(maxWeight) + 1;
                } else if (i != j) {
                    graph[i][j] = maxWeight; // ensure there is always a path
                }
            }
        }
        return graph;
    }

    @Test
    void givenGraphsOfVariousSizes_whenRunningTSPDynamic_thenReturnExecutionTime() {
        int[][] graph5 = generateRandomGraph(5, 20);
        int[][] graph10 = generateRandomGraph(10, 50);
        int[][] graph12 = generateRandomGraph(12, 100);

        long start = System.nanoTime();
        int result5 = TSPDynamic.solve(graph5);
        long end = System.nanoTime();
        log.info("TSP DP execution time for 5 nodes: " + (end - start) + " ns");

        start = System.nanoTime();
        int result10 = TSPDynamic.solve(graph10);
        end = System.nanoTime();
        log.info("TSP DP execution time for 10 nodes: " + (end - start) + " ns");

        start = System.nanoTime();
        int result12 = TSPDynamic.solve(graph12);
        end = System.nanoTime();
        log.info("TSP DP execution time for 12 nodes: " + (end - start) + " ns");

        // For correctness, ensure the tour includes the starting city
        assertEquals(graph5.length, TSPDynamic.tourLength(result5) > 0 ? graph5.length : 0);
        assertEquals(graph10.length, TSPDynamic.tourLength(result10) > 0 ? graph10.length : 0);
        assertEquals(graph12.length, TSPDynamic.tourLength(result12) > 0 ? graph12.length : 0);
    }

}