package dsa.subset;

import java.util.ArrayList;
import java.util.List;

public class SubsetSumBacktrack {
    List<List<Integer>> results = new ArrayList<>();

    public void findSubsets(int[] arr, int target) {
        backtrack(arr, 0, target, new ArrayList<>());
    }

    private void backtrack(int[] arr, int index, int remaining, List<Integer> current) {
        if (remaining == 0) {
            results.add(new ArrayList<>(current));
            return;
        }
        if (index == arr.length || remaining < 0) {
            return;
        }
        current.add(arr[index]);
        backtrack(arr, index + 1, remaining - arr[index], current);
        current.remove(current.size() - 1);
        backtrack(arr, index + 1, remaining, current);
    }

}
