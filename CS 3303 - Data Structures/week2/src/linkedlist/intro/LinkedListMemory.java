package linkedlist.intro;

public class LinkedListMemory {
    public static void main(String[] args) {

        int myVal = 13;

        System.out.println("Value of integer 'myVal': " + myVal);
        System.out.println("Size of integer (Java int): 4 bytes");

        // In Java, references (addresses) are abstract.
        // But typically they are 8 bytes on a 64-bit JVM.
        Object ref = new Object();

        System.out.println("Approximate size of a reference: 8 bytes (64-bit JVM)");
        System.out.println("Reference to 'ref': " + ref);
    }
}