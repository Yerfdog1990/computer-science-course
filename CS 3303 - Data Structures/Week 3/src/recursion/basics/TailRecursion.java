package recursion.basics;

public class TailRecursion {
    
    // Tail recursion example: The recursive call is the last operation
    public static int sum(int n, int accumulator) {
        if (n == 0) {
            return accumulator;
        }
        // The recursive call is the last operation (tail position)
        return sum(n - 1, accumulator + n);
    }
    
    // Wrapper method for easier use
    public static int sum(int n) {
        return sum(n, 0);
    }
    
    public static void main(String[] args) {
        int result = sum(5); // 5 + 4 + 3 + 2 + 1 = 15
        System.out.println("Sum of numbers from 1 to 5: " + result);
    }
}
