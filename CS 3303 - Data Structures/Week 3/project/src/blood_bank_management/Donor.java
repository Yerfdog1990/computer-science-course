package blood_bank_management;

import java.time.LocalDate;
import java.util.Stack;

/*
Donor Management:
Tracks donor information and donation history
Enforces eligibility rules using recursion
Maintains donation history using a Stack
 */
public class Donor {
    private String id;
    private String name;
    private String bloodType;
    private LocalDate lastDonationDate;
    private Stack<LocalDate> donationHistory;
    private boolean isEligible;
    private static final int MIN_DONATION_GAP_DAYS = 56; // 8 weeks

    public Donor(String id, String name, String bloodType) {
        this.id = id;
        this.name = name;
        this.bloodType = bloodType.toUpperCase();
        this.donationHistory = new Stack<>();
        this.isEligible = true;
    }

    public boolean canDonate() {
        if (donationHistory.isEmpty()) {
            return true;
        }
        LocalDate lastDonation = donationHistory.peek();
        LocalDate nextEligibleDate = lastDonation.plusDays(MIN_DONATION_GAP_DAYS);
        return LocalDate.now().isAfter(nextEligibleDate);
    }

    public void recordDonation() {
        if (canDonate()) {
            donationHistory.push(LocalDate.now());
            this.lastDonationDate = LocalDate.now();
            this.isEligible = false;
        } else {
            throw new IllegalStateException("Donor is not eligible to donate yet.");
        }
    }

    // Recursively check if donor has donated within the last year
    public boolean hasDonatedInLastYear(Stack<LocalDate> history, LocalDate currentDate) {
        if (history.isEmpty()) {
            return false;
        }
        LocalDate lastDonation = history.pop();
        if (lastDonation.isAfter(currentDate.minusYears(1))) {
            return true;
        }
        return hasDonatedInLastYear(history, currentDate);
    }

    // Getters and setters
    public String getId() { return id; }
    public String getName() { return name; }
    public String getBloodType() { return bloodType; }
    public LocalDate getLastDonationDate() { return lastDonationDate; }
    public boolean isEligible() { return isEligible; }

    @Override
    public String toString() {
        return String.format("Donor{id='%s', name='%s', bloodType='%s', lastDonation=%s, eligible=%b}",
                id, name, bloodType, lastDonationDate, isEligible);
    }
}
