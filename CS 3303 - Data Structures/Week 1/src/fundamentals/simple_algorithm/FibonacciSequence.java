package fundamentals.simple_algorithm;

import java.util.ArrayList;
import java.util.List;

public class FibonacciSequence {
    private static final int SEQUENCE_LENGTH = 20;

    public static void main(String[] args) {
        // Using a loop
        List<Integer> loopSequence = generateFibonacciWithLoop(SEQUENCE_LENGTH);
        System.out.println("Fibonacci sequence (loop): " + loopSequence);

        // Using a recursive method
        List<Integer> recursiveSequence = generateFibonacci(SEQUENCE_LENGTH);
        System.out.println("Fibonacci sequence (recursion): " + recursiveSequence);
    }

    // Using loop (kept for comparison)
    public static List<Integer> generateFibonacciWithLoop(int length) {
        List<Integer> sequence = new ArrayList<>();
        if (length >= 1) sequence.add(0);
        if (length >= 2) sequence.add(1);

        int a = 0, b = 1;
        for (int i = 2; i < length; i++) {
            int next = a + b;
            sequence.add(next);
            a = b;
            b = next;
        }
        return sequence;
    }

    // Recursive method
    public static List<Integer> generateFibonacci(int length) {
        if (length <= 0) return new ArrayList<>();
        if (length == 1) return new ArrayList<>(List.of(0));
        if (length == 2) return new ArrayList<>(List.of(0, 1));

        List<Integer> sequence = generateFibonacci(length - 1);
        sequence.add(sequence.get(sequence.size() - 1) + sequence.get(sequence.size() - 2));
        return sequence;
    }
}