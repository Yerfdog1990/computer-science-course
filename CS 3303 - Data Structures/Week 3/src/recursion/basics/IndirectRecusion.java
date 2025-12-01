package recursion.basics;

public class IndirectRecusion {
    
    // Indirect recursion example: Two methods calling each other
    public static void printEven(int n) {
        if (n <= 0) return;
        System.out.println("Even: " + n);
        printOdd(n - 1); // Call to another method
    }
    
    public static void printOdd(int n) {
        if (n <= 0) return;
        System.out.println("Odd: " + n);
        printEven(n - 1); // Call back to the first method
    }
    
    public static void main(String[] args) {
        System.out.println("Printing numbers from 5 using indirect recursion:");
        printOdd(9);
    }
}
