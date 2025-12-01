package blood_bank_management;


import java.util.HashMap;
import java.util.Map;

public class BloodBankSystem {
    private Map<String, Donor> donors;
    private Map<String, BloodInventory> bloodInventories;
    private BloodRequestQueue requestQueue;
    private int nextUnitId = 1;

    public BloodBankSystem() {
        this.donors = new HashMap<>();
        this.bloodInventories = new HashMap<>();
        this.requestQueue = new BloodRequestQueue();
        initializeBloodInventories();
    }

    private void initializeBloodInventories() {
        String[] bloodTypes = {"A+", "A-", "B+", "B-", "AB+", "AB-", "O+", "O-"};
        for (String type : bloodTypes) {
            bloodInventories.put(type, new BloodInventory());
        }
    }

    public void registerDonor(String id, String name, String bloodType) {
        if (donors.containsKey(id)) {
            throw new IllegalArgumentException("Donor with ID " + id + " already exists");
        }
        donors.put(id, new Donor(id, name, bloodType));
    }

    public void recordDonation(String donorId) {
        Donor donor = donors.get(donorId);
        if (donor == null) {
            throw new IllegalArgumentException("Donor not found");
        }

        if (donor.canDonate()) {
            donor.recordDonation();
            String unitId = "UNIT-" + nextUnitId++;
            BloodUnit unit = new BloodUnit(unitId, donor.getBloodType(), donorId);
            bloodInventories.get(donor.getBloodType()).addUnit(unit);
            System.out.println("Donation recorded: " + unit);
        } else {
            System.out.println("Donor is not eligible to donate yet");
        }
    }

    public void processBloodRequests() {
        while (requestQueue.hasPendingRequests()) {
            BloodRequest request = requestQueue.getNextRequest();
            System.out.println("\nProcessing request: " + request);

            BloodInventory inventory = bloodInventories.get(request.getBloodType());
            if (inventory == null || inventory.isEmpty()) {
                System.out.println("No inventory available for blood type: " + request.getBloodType());
                continue;
            }

            int unitsProcessed = 0;
            while (unitsProcessed < request.getUnitsNeeded() && !inventory.isEmpty()) {
                BloodUnit unit = inventory.getOldestUnit();
                if (!unit.isExpired()) {
                    System.out.println("  Dispensing unit: " + unit);
                    unitsProcessed++;
                } else {
                    System.out.println("  Discarding expired unit: " + unit);
                }
            }

            if (unitsProcessed < request.getUnitsNeeded()) {
                System.out.printf("Warning: Only %d of %d units available for request %s%n",
                        unitsProcessed, request.getUnitsNeeded(), request.getRequestId());
            }
        }
    }

    public void addBloodRequest(BloodRequest request) {
        requestQueue.addRequest(request);
        System.out.println("Added request to queue: " + request);
    }

    // Helper method to demonstrate the system
    public static void main(String[] args) {
        BloodBankSystem system = new BloodBankSystem();

        // Register some donors
        system.registerDonor("D001", "John Doe", "A+");
        system.registerDonor("D002", "Jane Smith", "O-");
        system.registerDonor("D003", "Bob Johnson", "B+");

        // Record some donations
        system.recordDonation("D001");
        system.recordDonation("D002");
        system.recordDonation("D002"); // This should fail (not enough time passed)
        system.recordDonation("D003");

        // Add some blood requests
        system.addBloodRequest(new BloodRequest("R001", "HOSP1", "A+", 2,
                BloodRequest.Priority.EMERGENCY));
        system.addBloodRequest(new BloodRequest("R002", "HOSP2", "O-", 1,
                BloodRequest.Priority.ROUTINE));
        system.addBloodRequest(new BloodRequest("R003", "HOSP3", "B+", 3,
                BloodRequest.Priority.URGENT));

        // Process all requests
        system.processBloodRequests();
    }
}