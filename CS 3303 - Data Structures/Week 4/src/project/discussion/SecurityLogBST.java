package project.discussion;

public class SecurityLogBST {

    LogNode root;

    // Helper: compare timestamps as strings (ISO format)
    private int compareTimestamps(String t1, String t2) {
        return t1.compareTo(t2);
    }

    // Insert a new log entry
    public LogNode insert(LogNode node, String timestamp, String eventType, String severity) {
        if (node == null) {
            return new LogNode(timestamp, eventType, severity);
        }

        if (compareTimestamps(timestamp, node.timestamp) < 0) {
            node.left = insert(node.left, timestamp, eventType, severity);
        } else if (compareTimestamps(timestamp, node.timestamp) > 0) {
            node.right = insert(node.right, timestamp, eventType, severity);
        }
        return node;
    }

    // Delete a log entry by timestamp
    public LogNode delete(LogNode node, String timestamp) {
        if (node == null) return null;

        if (compareTimestamps(timestamp, node.timestamp) < 0) {
            node.left = delete(node.left, timestamp);
        } else if (compareTimestamps(timestamp, node.timestamp) > 0) {
            node.right = delete(node.right, timestamp);
        } else {
            // Node found
            if (node.left == null) return node.right;
            if (node.right == null) return node.left;

            // Node with two children: get inorder successor
            LogNode successor = minValueNode(node.right);
            node.timestamp = successor.timestamp;
            node.eventType = successor.eventType;
            node.severity = successor.severity;
            node.right = delete(node.right, successor.timestamp);
        }
        return node;
    }

    // Find minimum value node (used for deletion)
    private LogNode minValueNode(LogNode node) {
        while (node.left != null) node = node.left;
        return node;
    }

    // Inorder traversal (for display)
    public void inorder(LogNode node) {
        if (node != null) {
            inorder(node.left);
            System.out.println(node.timestamp + " | " + node.eventType + " | " + node.severity);
            inorder(node.right);
        }
    }

    // Demo usage
    public static void main(String[] args) {
        SecurityLogBST bst = new SecurityLogBST();

        // Insert log entries (example from context)
        bst.root = bst.insert(bst.root, "2025-08-10 03:14:15", "Unauthorized Access", "Critical");
        bst.root = bst.insert(bst.root, "2025-08-09 22:45:00", "System Scan", "Low");
        bst.root = bst.insert(bst.root, "2025-08-10 04:01:22", "Malware Detection", "High");
        bst.root = bst.insert(bst.root, "2025-08-10 04:30:05", "Data Exfiltration Attempt", "Critical");

        System.out.println("Inorder traversal after insertion:");
        bst.inorder(bst.root);

        // Delete a log entry ("System Scan")
        bst.root = bst.delete(bst.root, "2025-08-09 22:45:00");

        System.out.println("\nInorder traversal after deletion:");
        bst.inorder(bst.root);
    }
}
