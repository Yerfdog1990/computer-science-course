package pattern_matching;

public class NaiveApproach {
    // Function to perform naive pattern searching
    static void search(String txt, String pat) {

        int n = txt.length();
        int m = pat.length();

        // Loop to slide the pattern over text
        for (int i = 0; i <= n - m; i++) {

            int j;

            // Check for pattern match at position i
            for (j = 0; j < m; j++) {
                if (txt.charAt(i + j) != pat.charAt(j)) {
                    break;
                }
            }

            // If pattern matches completely
            if (j == m) {
                System.out.println("Pattern found at index " + i);
            }
        }
    }

    // Driver code
    public static void main(String[] args) {

        String txt = "AABAACAADAABAABA";
        String pat = "AABA";

        search(txt, pat);
    }
}