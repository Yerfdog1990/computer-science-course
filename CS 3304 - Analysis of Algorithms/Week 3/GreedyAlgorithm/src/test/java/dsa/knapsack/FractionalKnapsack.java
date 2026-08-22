package dsa.knapsack;

import java.util.Arrays;
import java.util.Comparator;

public class FractionalKnapsack {

    // Represents a single item with weight and profit
    static class Item {
        double weight;
        double profit;
        double ratio; // profit-to-weight ratio

        Item(double weight, double profit) {
            this.weight = weight;
            this.profit = profit;
            this.ratio = profit / weight;
        }
    }

    static double solveFractionalKnapsack(double capacity, Item[] items) {
        // Step 1: sort items by profit/weight ratio in descending order
        Arrays.sort(items, Comparator.comparingDouble((Item i) -> i.ratio).reversed());

        double remainingCapacity = capacity;
        double totalProfit = 0.0;

        // Step 2: greedily pick items
        for (Item item : items) {
            if (remainingCapacity <= 0) break;

            if (item.weight <= remainingCapacity) {
                // Take the entire item
                remainingCapacity -= item.weight;
                totalProfit += item.profit;
            } else {
                // Take only the fraction that fits
                double fraction = remainingCapacity / item.weight;
                totalProfit += item.profit * fraction;
                remainingCapacity = 0; // knapsack is now full
            }
        }
        return totalProfit;
    }

    public static void main(String[] args) {
        // Example from Section 2.3: profits and weights of 7 items
        Item[] items = {
                new Item(2, 10),
                new Item(3, 5),
                new Item(5, 15),
                new Item(7, 7),
                new Item(1, 6),
                new Item(4, 18),
                new Item(1, 3)
        };

        double capacity = 15;
        double maxProfit = solveFractionalKnapsack(capacity, items);
        System.out.println("Maximum profit: " + maxProfit); // ~54.6
    }
}