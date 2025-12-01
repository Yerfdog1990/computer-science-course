package blood_bank_management;

import java.util.PriorityQueue;
import java.util.Queue;
import java.util.LinkedList;

/*
Blood Request Queue:
Manages a queue of blood requests, prioritizing emergency requests over routine ones.
Supports adding requests, retrieving the next request, and checking for pending requests.
Provides methods to get the sizes of emergency and routine queues.
 */
public class BloodRequestQueue {
    private PriorityQueue<BloodRequest> emergencyQueue;
    private Queue<BloodRequest> routineQueue;

    public BloodRequestQueue() {
        emergencyQueue = new PriorityQueue<>();
        routineQueue = new LinkedList<>();
    }

    public void addRequest(BloodRequest request) {
        if (request.getPriority() == BloodRequest.Priority.ROUTINE) {
            routineQueue.add(request);
        } else {
            emergencyQueue.add(request);
        }
    }

    public BloodRequest getNextRequest() {
        if (!emergencyQueue.isEmpty()) {
            return emergencyQueue.poll(); // get the next request based on priority
        }
        return routineQueue.poll(); // get the next request based on priority.
    }

    public boolean hasPendingRequests() {
        return !(emergencyQueue.isEmpty() && routineQueue.isEmpty());
    }

    public int getEmergencyQueueSize() {
        return emergencyQueue.size();
    }

    public int getRoutineQueueSize() {
        return routineQueue.size();
    }
}