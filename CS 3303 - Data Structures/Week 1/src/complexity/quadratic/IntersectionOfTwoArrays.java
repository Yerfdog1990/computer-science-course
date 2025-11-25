package complexity.quadratic;

import java.util.ArrayList;
import java.util.Arrays;
import java.util.List;

public class IntersectionOfTwoArrays {

    // Using while loop and fundamentals.arrays.sorting the arrays.
    public static void intersectionFast(int[] a, int[] b) {
        Arrays.sort(a);
        Arrays.sort(b);

        List<Integer> result = new ArrayList<>();
        int i = 0, j = 0;

        while (i < a.length && j < b.length) {
            if (a[i] == b[j]) {
                // Add to the result only if it's not a duplicate
                if (result.isEmpty() || result.get(result.size() - 1) != a[i]) {
                    result.add(a[i]);
                }
                i++;
                j++;
                // Skip duplicates in both arrays
                while (i < a.length && a[i] == a[i - 1]) i++;
                while (j < b.length && b[j] == b[j - 1]) j++;
            } else if (a[i] < b[j]) {
                i++;
            } else {
                j++;
            }
        }
    }

    // Using for loop and fundamentals.arrays.sorting the arrays.
    public static void intersectionSlow(int[] a, int[] b) {
        Arrays.sort(a);
        Arrays.sort(b);

        List<Integer> result = new ArrayList<>();
        for (int i = 0; i < a.length; i++) {
            if (i > 0 && a[i] == a[i - 1]) continue;
            for (int j = 0; j < b.length; j++) {
                if (b[j] == a[i]) {
                    result.add(a[i]);
                    break;
                }
            }
        }
    }

    public static void main(String[] args) {
        int[] a = new int[100000];

        for (int i = 0; i < 100000; i++) {
            a[i] = i;
        }
        int[] b = new int[100000];

        for (int i = 0; i < 100000; i++) {
            b[i] = i;
        }

        long start1 = System.currentTimeMillis();
        intersectionFast(a, b);
        System.out.println("Time taken using while loop: " + (System.currentTimeMillis() - start1) + " ms");

        long start2 = System.currentTimeMillis();
        intersectionSlow(a, b);
        System.out.println("Time taken using for loop: " + (System.currentTimeMillis() - start2) + " ms");
    }
}
