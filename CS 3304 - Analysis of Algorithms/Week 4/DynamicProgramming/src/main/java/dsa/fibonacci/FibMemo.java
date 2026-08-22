package dsa.fibonacci;

import java.util.Arrays;

public class FibMemo {

    static int fibRec(int n, int[] memo) {
        // Base case
        if (n <= 1) {
            return n;
        }

        // Return cached result if it exists
        if (memo[n] != -1) {
            return memo[n];
        }

        // Compute, cache, and return
        memo[n] = fibRec(n - 1, memo) + fibRec(n - 2, memo);
        return memo[n];
    }

    static int findFibonacciNthTerm(int n) {
        int[] memo = new int[n + 1];
        Arrays.fill(memo, -1);
        return fibRec(n, memo);
    }

}
