package hashing;

import java.util.Arrays;

public class DivisionHashing {

    static int[] table;
    static int tableSize;

    // Constructor
    DivisionHashing(int size) {
        tableSize = size;
        table = new int[tableSize];
        Arrays.fill(table, -1);
    }

    // Division hash function
    static int hash(int key) {
        return key % tableSize;
    }

    // Insert using linear probing
    static void insert(int key) {
        int index = hash(key);

        // Resolve collision using linear probing
        while (table[index] != -1) {
            index = (index + 1) % tableSize;
        }
        table[index] = key;
    }

    // Display hash table
    static void display() {
        for (int i = 0; i < tableSize; i++) {
            System.out.println("Index " + i + " : " + table[i]);
        }
    }

    public static void main(String[] args) {
        DivisionHashing ht = new DivisionHashing(7); // prime table size

        int[] keys = {22, 30, 50, 45, 61};
        for (int key : keys) {
            insert(key);
        }

        display();
    }
}