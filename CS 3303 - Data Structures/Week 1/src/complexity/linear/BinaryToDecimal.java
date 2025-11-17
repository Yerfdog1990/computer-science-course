package complexity.linear;

public class BinaryToDecimal {
    public static void main(String[] args) {
        String binaryNumber = "10110";
        System.out.println(binaryNumber+ " -> " + convertToDecimal(binaryNumber));
        int decimalNumber = 250;
        System.out.println(decimalNumber+ " -> " + convertDecimalToBinary(decimalNumber));
    }
    public static int convertToDecimal(String binary) {
        int conversion = 1;
        int result = 0;
        for (int i = 1; i <= binary.length(); i++) {
            if (binary.charAt(binary.length() - i) == '1')
                result += conversion;
            conversion *= 2;
        }
        return result;
    }

    public int convertToDecimalAlt(String binary) {
        int result = 0;
        for (int i = 1; i <= binary.length(); i++) {
            if (binary.charAt(binary.length() - i) == '1')
                result += Math.pow(2, i - 1);
        }
        return result;
    }
    public static String convertDecimalToBinary(int decimal) {
        String binary = "";
        while (decimal > 0) {
            binary = (decimal % 2) + binary;
            decimal /= 2;
        }
        return binary;
    }
}
