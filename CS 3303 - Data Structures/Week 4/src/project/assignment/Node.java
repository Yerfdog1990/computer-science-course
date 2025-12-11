package project.assignment;

public class Node {
    Transaction transaction;
    Node left, right;

    public Node(Transaction transaction) {
        this.transaction = transaction;
        this.left = this.right = null;
    }
}
