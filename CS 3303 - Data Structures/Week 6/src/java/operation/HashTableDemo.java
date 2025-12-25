package operation;

import java.util.ArrayList;

public class HashTableDemo {

    // Number of buckets
    private final int bucket;

    // HashTable table
    private final ArrayList<Integer>[] table;

    public HashTableDemo(int bucket) {
        this.bucket = bucket;
        this.table = new ArrayList[bucket];

        for (int i = 0; i < bucket; i++) {
            table[i] = new ArrayList<>();
        }
    }

    // Hash function
    public int hashFunction(int key) {
        return key % bucket;
    }

    // Insert key
    public void insertItem(int key) {
        int index = hashFunction(key);
        table[index].add(key);
    }

    // Delete key
    public void deleteItem(int key) {
        int index = hashFunction(key);
        table[index].remove(Integer.valueOf(key));
    }

    // Display hash table
    public void displayHash() {
        for (int i = 0; i < bucket; i++) {
            System.out.print(i);
            for (int x : table[i]) {
                System.out.print(" --> " + x);
            }
            System.out.println();
        }
    }

    public static void main(String[] args) {
        int[] keys = {15, 11, 27, 8, 12};

        HashTableDemo h = new HashTableDemo(7);

        for (int key : keys) {
            h.insertItem(key);
        }

        // Before deletion
        System.out.println("Before deletion: ");
        h.displayHash();

        // Delete 12
        h.deleteItem(12);

        // After deletion
        System.out.println("========================");
        System.out.println("After deletion: ");
        h.displayHash();
    }
}
