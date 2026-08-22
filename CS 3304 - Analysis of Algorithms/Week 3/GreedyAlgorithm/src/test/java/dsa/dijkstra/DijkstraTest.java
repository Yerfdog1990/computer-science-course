package dsa.dijkstra;

import lombok.extern.slf4j.Slf4j;
import org.junit.jupiter.api.Test;
import static org.junit.jupiter.api.Assertions.assertEquals;
import java.util.Random;

@Slf4j
public class DijkstraTest {

    // Helper to create a random weighted adjacency matrix
    private int[][] generateRandomGraph(int nodes, int maxWeight) {
        Random rand = new Random();
        int[][] graph = new int[nodes][nodes];
        for (int i = 0; i < nodes; i++) {
            for (int j = 0; j < nodes; j++) {
                if (i != j && rand.nextBoolean()) {
                    graph[i][j] = rand.nextInt(maxWeight) + 1;
                }
            }
        }
        return graph;
    }

    @Test
    void givenGraphsOfVariousSizes_whenRunningDijkstra_thenReturnExecutionTime() {
        int[][] graph10 = generateRandomGraph(10, 20);
        int[][] graph50 = generateRandomGraph(50, 100);
        int[][] graph100 = generateRandomGraph(100, 200);

        long start = System.nanoTime();
        int[] distances10 = Dijkstra.dijkstra(graph10, 0);
        long end = System.nanoTime();
        log.info("Dijkstra execution time for 10 nodes: " + (end - start) + " ns");

        start = System.nanoTime();
        int[] distances50 = Dijkstra.dijkstra(graph50, 0);
        end = System.nanoTime();
        log.info("Dijkstra execution time for 50 nodes: " + (end - start) + " ns");

        start = System.nanoTime();
        int[] distances100 = Dijkstra.dijkstra(graph100, 0);
        end = System.nanoTime();
        log.info("Dijkstra execution time for 100 nodes: " + (end - start) + " ns");

        // For correctness, ensure the source node distance is zero
        assertEquals(0, distances10[0]);
        assertEquals(0, distances50[0]);
        assertEquals(0, distances100[0]);
    }
}
