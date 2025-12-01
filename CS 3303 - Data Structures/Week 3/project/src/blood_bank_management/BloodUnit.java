package blood_bank_management;

import java.time.LocalDate;


/*
Blood Unit:
Represents a single unit of blood with attributes like ID, type, donation date, expiry date, and donor ID.
Provides methods to check if the unit has expired and to retrieve its details.
 */
public class BloodUnit {
    private String unitId;
    private String bloodType;
    private LocalDate donationDate;
    private LocalDate expiryDate;
    private String donorId;

    public BloodUnit(String unitId, String bloodType, String donorId) {
        this.unitId = unitId;
        this.bloodType = bloodType.toUpperCase();
        this.donorId = donorId;
        this.donationDate = LocalDate.now();
        this.expiryDate = donationDate.plusDays(42); // 6 weeks shelf life
    }

    public boolean isExpired() {
        return LocalDate.now().isAfter(expiryDate);
    }

    // Getters
    public String getUnitId() { return unitId; }
    public String getBloodType() { return bloodType; }
    public LocalDate getDonationDate() { return donationDate; }
    public LocalDate getExpiryDate() { return expiryDate; }
    public String getDonorId() { return donorId; }

    @Override
    public String toString() {
        return String.format("BloodUnit{id='%s', type='%s', donated=%s, expires=%s, donorId='%s'}",
                unitId, bloodType, donationDate, expiryDate, donorId);
    }
}
