package dsa.knapsack;

import java.util.ArrayList;

public class JobSequencing {

    // Represents a single job with an id, deadline, and profit
    static class Job {
        String id;
        int deadline;
        int profit;

        Job(String id, int deadline, int profit) {
            this.id = id;
            this.deadline = deadline;
            this.profit = profit;
        }
    }

    static void scheduleJobs(ArrayList<Job> jobs) {
        int n = jobs.size();

        // Step 1: find the maximum deadline -> total number of time slots
        int maxDeadline = 0;
        for (Job job : jobs) {
            maxDeadline = Math.max(maxDeadline, job.deadline);
        }

        // Step 2: sort jobs in descending order of profit
        jobs.sort((a, b) -> b.profit - a.profit);

        // Step 3: allocate jobs to the latest available slot at/before their deadline
        boolean[] slotOccupied = new boolean[maxDeadline];
        String[] result = new String[maxDeadline];
        int totalProfit = 0;

        for (Job job : jobs) {
            // Search backward from the job's deadline (capped at maxDeadline) down to slot 1
            for (int t = Math.min(maxDeadline, job.deadline) - 1; t >= 0; t--) {
                if (!slotOccupied[t]) {
                    slotOccupied[t] = true;
                    result[t] = job.id;
                    totalProfit += job.profit;
                    break; // move on to the next job
                }
            }
            // If no free slot was found, the job is simply skipped (rejected)
        }

        // Step 4: print the final schedule
        System.out.println("Scheduled sequence:");
        for (int t = 0; t < maxDeadline; t++) {
            if (slotOccupied[t]) {
                System.out.println("Slot " + (t + 1) + ": " + result[t]);
            }
        }
        System.out.println("Total Maximum Profit: " + totalProfit);
    }

    public static void main(String[] args) {
        // Example from Section 6: J1..J5 with deadlines and profits
        ArrayList<Job> jobs = new ArrayList<>();
        jobs.add(new Job("J1", 2, 20));
        jobs.add(new Job("J2", 2, 60));
        jobs.add(new Job("J3", 1, 40));
        jobs.add(new Job("J4", 3, 100));
        jobs.add(new Job("J5", 4, 80));

        scheduleJobs(jobs);
        // Expected output: J3 -> J2 -> J4 -> J5, Total Maximum Profit: 280
    }
}