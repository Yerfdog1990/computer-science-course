package project.assignment;

public class Transaction {
    private String transactionId;
    private String type;
    private double amount;
    private int riskScore;
    private boolean isCritical;

    public Transaction(String transactionId, String type, double amount, int riskScore, boolean isCritical) {
        this.transactionId = transactionId;
        this.type = type;
        this.amount = amount;
        this.riskScore = riskScore;
        this.isCritical = isCritical;
    }

    // Getters and setters
    public String getTransactionId() { return transactionId; }
    public String getType() { return type; }
    public double getAmount() { return amount; }
    public int getRiskScore() { return riskScore; }
    public boolean isCritical() { return isCritical; }

    @Override
    public String toString() {
        return String.format("Txn ID: %s | Type: %-20s | Amt: $%,.2f | Risk: %d%s",
                transactionId, type, amount, riskScore, isCritical ? " (CRITICAL)" : "");
    }
}
