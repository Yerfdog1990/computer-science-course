package complexity.linear;

public class OctalToDecimal {
    public static void main(String[] args) {
        String octalNumber = "123";
        System.out.println(octalNumber+ " -> " + convertOctalToDecimal(octalNumber));
        int decimalNumber = 250;
        System.out.println(decimalNumber+ " -> " + convertDecimalToOctal(decimalNumber));
    }
    public static int convertOctalToDecimal(String octal) {
        int conversion = 1;
        int result = 0;

        for (int i = 1; i <= octal.length(); i++) {
            int digit = Character.getNumericValue(octal.charAt(octal.length() - i));
            result += digit * conversion;
            conversion *= 8;
        }
        return result;
    }
    public static String convertDecimalToOctal(int decimal) {
        String octal = "";
        while (decimal > 0) {
            octal = (decimal % 8) + octal;
            decimal /= 8;
        }
        return octal;
    }
}
