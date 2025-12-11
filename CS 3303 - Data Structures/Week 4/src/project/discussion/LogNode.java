package project.discussion;

// Node class for each log entry
public class LogNode {
    String timestamp;
    String eventType;
    String severity;
    LogNode left, right;

    LogNode(String timestamp, String eventType, String severity) {
        this.timestamp = timestamp;
        this.eventType = eventType;
        this.severity = severity;
        this.left = null;
        this.right = null;
    }
}
