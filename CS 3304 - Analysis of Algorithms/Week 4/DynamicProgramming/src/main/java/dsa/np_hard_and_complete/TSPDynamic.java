package dsa.np_hard_and_complete;

// Dynamic Programming (Held-Karp) Solution for TSP
import java.util.Arrays;
public class TSPDynamic {
    // Solves the TSP problem for a given adjacency matrix
    public static int solve(int[][] graph) {
        int n = graph.length;
        int VISITED_ALL = (1 << n) - 1;
        int[][] dp = new int[n][1 << n];
        for (int[] row : dp) Arrays.fill(row, -1);
        return tsp(0, 1, graph, dp, VISITED_ALL);
    }

    // Helper method for recursion with memoization
    private static int tsp(int pos, int visited, int[][] graph, int[][] dp, int VISITED_ALL) {
        if (visited == VISITED_ALL) {
            return graph[pos][0]; // return to the starting city
        }
        if (dp[pos][visited] != -1) {
            return dp[pos][visited];
        }
        int min = Integer.MAX_VALUE;
        for (int city = 0; city < graph.length; city++) {
            if ((visited & (1 << city)) == 0) {
                int dist = graph[pos][city] + tsp(city, visited | (1 << city), graph, dp, VISITED_ALL);
                min = Math.min(min, dist);
            }
        }
        return dp[pos][visited] = min;
    }

    // Utility method to get the tour length (for testing)
    public static int tourLength(int result) {
        // For the TSP, the tour always covers all nodes
        return Math.max(result, 0);
    }

}
