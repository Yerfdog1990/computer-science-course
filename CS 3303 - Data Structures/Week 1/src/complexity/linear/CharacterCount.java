package complexity.linear;

public class CharacterCount {

    public static int countCharacters(String input, char target) {
        int count = 0;
        for (char c : input.toCharArray()) {
            if (c == target) {
                count++;
            }
        }
        return count;
    }
    public static void main(String[] args) {
        System.out.println(countCharacters("abcabcabc", 'a'));
    }
}
