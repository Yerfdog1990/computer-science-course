package hashing;

import java.util.Arrays;

public class MultiplicationHashing {

    static int[] table;
    static int tableSize;
    static final double A = 0.6180339887; // Knuth's constant

    // Constructor
    MultiplicationHashing(int size) {
        tableSize = size;
        table = new int[tableSize];
        Arrays.fill(table, -1);
    }

    // Multiplication hash function
    static int hash(int key) {
        double product = key * A;
        double fractionalPart = product - Math.floor(product);
        return (int) Math.floor(tableSize * fractionalPart);
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
        MultiplicationHashing ht = new MultiplicationHashing(16); // power of 2

        int[] keys = {22, 30, 50, 45, 61};
        for (int key : keys) {
            insert(key);
        }

        display();
    }
}