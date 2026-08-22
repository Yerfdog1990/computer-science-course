package dsa.fibonacci;

public class FibOptimized {

    static int findFibonacciNthTerm(int n) {
        if (n <= 1) {
            return n;
        }

        int prevPrev = 0, prev = 1, curr = 1;

        for (int i = 2; i <= n; i++) {
            curr = prev + prevPrev;
            prevPrev = prev;
            prev = curr;
        }

        return curr;
    }
}