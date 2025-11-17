package complexity.exponential;

import java.util.ArrayList;
import java.util.List;

public class PrimeFactors {

    public static List<Integer> findPrimeFactors(int number) {
        List<Integer> primeFactors = new ArrayList<>();
        int divisor = 2;

        while (number > 1) {
            if (number % divisor == 0) {
                primeFactors.add(divisor);
                number /= divisor;
            } else {
                divisor++;
            }
        }

        return primeFactors;
    }
    public static void main(String[] args) {
        System.out.println(findPrimeFactors(12));
    }
}
