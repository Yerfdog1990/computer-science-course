package dsa;

import lombok.extern.slf4j.Slf4j;
import org.junit.jupiter.api.Test;

import java.util.Random;

import static org.junit.jupiter.api.Assertions.assertTrue;

@Slf4j
public class TaskSchedulerTest {
    // Given a list of 120 tasks with random durations from 1 to 30, and 15 machines
    @Test
    public void given120TasksAnd15Machines_whenTestingLargeDatasetScheduling_thenCompletionTimeIsBetweenLowerBoundAndSum() {
        int numTasks = 120;
        int numMachines = 15;
        int[] tasks = new int[numTasks];
        Random rand = new Random(42); // Seeded for reproducibility

        for (int i = 0; i < numTasks; i++) {
            tasks[i] = rand.nextInt(30) + 1;
        }

        // When scheduling tasks to minimize the total completion time
        int minTime = TaskScheduler.minCompletionTime(tasks, numMachines);

        // Then the total completion time should be at least as large as the largest single task
        // and not more than the sum of all tasks divided by the number of machines, rounded up
        int maxTask = 0, sum = 0;
        for (int t : tasks) {
            if (t > maxTask) maxTask = t;
            sum += t;
        }
        int theoreticalLowerBound = Math.max(maxTask, (int) Math.ceil((double) sum / numMachines));

        // Assert that the result is not less than the lower bound and not more than the total sum
        assertTrue(minTime >= theoreticalLowerBound, "Completion time is less than possible lower bound");
        assertTrue(minTime <= sum, "Completion time exceeds total sum of tasks");

        // Additional behavior check: Print the result for manual inspection
        log.info("Tested min completion time for 120 tasks and 15 machines: {}", minTime);
        log.info("Lower bound: {}", theoreticalLowerBound);
    }
}
