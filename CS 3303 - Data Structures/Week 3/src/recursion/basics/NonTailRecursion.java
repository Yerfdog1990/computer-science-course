package recursion.basics;

public class NonTailRecursion {
    
    // Non-tail recursion example: Additional operations after the recursive call
    public static int sum(int n) {
        if (n == 0) {
            return 0;
        }
        // The recursive call is not the last operation
        // We still need to add 'n' after the recursive call returns
        return n + sum(n - 1);
    }
    
    public static void main(String[] args) {
        // This will print the sum of numbers from 1 to 5 (1+2+3+4+5 = 15)
        System.out.println("Sum of numbers from 1 to 5: " + sum(5));
    }
}
