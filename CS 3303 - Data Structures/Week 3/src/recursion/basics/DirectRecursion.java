package recursion.basics;

public class DirectRecursion {
    
    // Direct recursion example: A method that calls itself directly
    public static void countDown(int n) {
        if (n <= 0) {
            System.out.println("Blast off!");
            return;
        }
        System.out.println(n);
        countDown(n - 1); // Direct recursive call
    }

    public static void countUp(int n) {
        if (n >= 10) {
            System.out.println("Blast off!");
            return;
        }
        System.out.println(n);
        countUp(n + 1);
    }
    
    public static void main(String[] args) {
        System.out.println("Countdown from 5:");
        countDown(5);

        System.out.println("Countup to 10:");
        countUp(0);
    }
}
