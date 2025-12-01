package blood_bank_management;

import java.time.LocalDate;


/*
Request Processing:
Implements priority queues for emergency vs routine requests
Processes emergency requests before routine ones
Handles partial fulfillment of requests when inventory is low
 */
public class BloodRequest implements Comparable<BloodRequest> {
    public enum Priority { EMERGENCY, URGENT, ROUTINE }
    private String requestId;
    private String hospitalId;
    private String bloodType;
    private int unitsNeeded;
    private Priority priority;
    private LocalDate requestDate;

    public BloodRequest(String requestId, String hospitalId, String bloodType,
                        int unitsNeeded, Priority priority) {
        this.requestId = requestId;
        this.hospitalId = hospitalId;
        this.bloodType = bloodType.toUpperCase();
        this.unitsNeeded = unitsNeeded;
        this.priority = priority;
        this.requestDate = LocalDate.now();
    }

    @Override
    public int compareTo(BloodRequest other) {
        // Higher priority comes first
        int priorityCompare = this.priority.compareTo(other.priority);
        if (priorityCompare != 0) {
            return -priorityCompare; // Negative for descending order
        }
        // For same priority, earlier request comes first
        return this.requestDate.compareTo(other.requestDate);
    }

    // Getters
    public String getRequestId() { return requestId; }
    public String getHospitalId() { return hospitalId; }
    public String getBloodType() { return bloodType; }
    public int getUnitsNeeded() { return unitsNeeded; }
    public Priority getPriority() { return priority; }
    public LocalDate getRequestDate() { return requestDate; }

    @Override
    public String toString() {
        return String.format("BloodRequest{id='%s', hospital='%s', type='%s', " +
                        "units=%d, priority=%s, date=%s}",
                requestId, hospitalId, bloodType, unitsNeeded, priority, requestDate);
    }
}