package project.assignment;

public class FraudDetectionDemo {
    public static void main(String[] args) {
        // Create the BST
        SecurityLogBST bst = new SecurityLogBST();

        // Insert transactions
        bst.insert(new Transaction("T-994A", "Standard Transfer", 200.00, 50, false));
        bst.insert(new Transaction("T-102B", "ATM Withdrawal", 60.00, 25, false));
        bst.insert(new Transaction("T-772X", "Rapid Transfer (1/3)", 4500.00, 85, false));
        bst.insert(new Transaction("T-331C", "Debit POS", 12.50, 10, false));
        bst.insert(new Transaction("T-555D", "Bill Pay", 150.00, 35, false));
        bst.insert(new Transaction("T-999Z", "Unauth. Location IP", 15000.00, 98, true));

        System.out.println("=== Transaction Tree (Level Order) ===");
        bst.levelOrder();

        System.out.println("\n=== Transactions Sorted by Risk Score ===");
        bst.inorder();

        // Search for a transaction
        String searchId = "T-555D";
        System.out.println("\n=== Searching for Transaction " + searchId + " ===");
        Transaction found = bst.search(searchId);
        System.out.println(found != null ? "Found: " + found : "Transaction not found");

        // Delete a transaction
        System.out.println("\n=== Deleting Transaction T-102B ===");
        bst.delete("T-102B");
        System.out.println("After deletion:");
        bst.inorder();
    }
}
