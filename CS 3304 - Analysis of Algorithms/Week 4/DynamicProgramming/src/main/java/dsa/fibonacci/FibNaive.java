package dsa.fibonacci;

public class FibNaive {

    public static int findFibonacciNthTerm(int n){
        if( n<= 1){
            return n;
        } else{
            return findFibonacciNthTerm(n - 1) + findFibonacciNthTerm(n - 2);
        }
    }
}

