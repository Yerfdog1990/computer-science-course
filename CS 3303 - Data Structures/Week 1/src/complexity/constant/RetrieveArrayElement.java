package complexity.constant;

public class RetrieveArrayElement {
    public static int retrieveArrayElement(int[] array, int index) {
        if (index < 0 || index >= array.length) {
            throw new IndexOutOfBoundsException("Index out of bounds");
        }
        return array[index];
    }
}
