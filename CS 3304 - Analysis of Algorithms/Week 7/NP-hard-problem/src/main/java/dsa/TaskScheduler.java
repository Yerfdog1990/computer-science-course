package dsa;

import java.util.Arrays;
import java.util.PriorityQueue;

public class TaskScheduler {
    public static int minCompletionTime(int[] tasks, int machines) {
        // Longest Processing Time (LPT) greedy algorithm
        // Sort tasks in descending order
        int[] sortedTasks = tasks.clone();
        Arrays.sort(sortedTasks);
        // Reverse to get descending order
        for (int i = 0; i < sortedTasks.length / 2; i++) {
            int temp = sortedTasks[i];
            sortedTasks[i] = sortedTasks[sortedTasks.length - 1 - i];
            sortedTasks[sortedTasks.length - 1 - i] = temp;
        }

        // Use min-heap to always assign to the machine with minimum load
        PriorityQueue<Integer> machineLoads = new PriorityQueue<>();
        for (int i = 0; i < machines; i++) {
            machineLoads.add(0);
        }

        // Assign each task to the machine with minimum current load
        for (int task : sortedTasks) {
            int minLoad = machineLoads.poll();
            machineLoads.add(minLoad + task);
        }

        // The maximum load is the answer
        int maxLoad = 0;
        while (!machineLoads.isEmpty()) {
            maxLoad = Math.max(maxLoad, machineLoads.poll());
        }
        return maxLoad;
    }
}
