package fundamentals.simple_algorithm;

public class FibonacciRecursiveNth {
    public static void main(String[] args) {
        System.out.printf("The %dth Fibonacci number is: %d", 20, fibonacciRecursive(19));
    }
    private static int fibonacciRecursive(int n) {
        if (n <= 1) return n;
        return fibonacciRecursive(n - 1) + fibonacciRecursive(n - 2);
    }
}
