package complexity.quadratic;

import java.util.ArrayList;
import java.util.Arrays;
import java.util.List;

public class IntersectionOfTwoArrays {
    public List<Integer> intersectionFast(int[] a, int[] b) {
        Arrays.sort(a);
        Arrays.sort(b);

        List<Integer> result = new ArrayList<>();
        int i = 0, j = 0;

        while (i < a.length && j < b.length) {
            if (a[i] == b[j]) {
                result.add(a[i]);
                i++; j++;
            } else if (a[i] < b[j]) {
                i++;
            } else {
                j++;
            }
        }
        return result;
    }
    public static void main(String[] args) {
        IntersectionOfTwoArrays intersectionOfTwoArrays = new IntersectionOfTwoArrays();
        int[] a = {1, 2, 2, 1};
        int[] b = {2, 2};
        System.out.println(intersectionOfTwoArrays.intersectionFast(a, b));
    }
}
