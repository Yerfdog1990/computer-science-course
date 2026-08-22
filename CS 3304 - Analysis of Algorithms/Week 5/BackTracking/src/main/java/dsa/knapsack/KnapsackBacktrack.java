package dsa.knapsack;

import java.util.ArrayList;
import java.util.List;

public class KnapsackBacktrack {
    static class Item {
        int weight, value;
        String name;
        public Item(String name, int weight, int value) {
            this.name = name;
            this.weight = weight;
            this.value = value;
        }
    }

    int maxProfit = 0;
    List<Item> bestSubset = new ArrayList<>();

    public void solve(List<Item> items, int capacity) {
        backtrack(items, 0, capacity, 0, new ArrayList<>());
    }

    private void backtrack(List<Item> items, int index, int remaining, int currentProfit, List<Item> currentSubset) {
        if (index == items.size() || remaining == 0) {
            if (currentProfit > maxProfit) {
                maxProfit = currentProfit;
                bestSubset = new ArrayList<>(currentSubset);
            }
            return;
        }
        Item item = items.get(index);
        if (item.weight <= remaining) {
            currentSubset.add(item);
            backtrack(items, index + 1, remaining - item.weight, currentProfit + item.value, currentSubset);
            currentSubset.remove(currentSubset.size() - 1);
        }
        backtrack(items, index + 1, remaining, currentProfit, currentSubset);
    }
}
