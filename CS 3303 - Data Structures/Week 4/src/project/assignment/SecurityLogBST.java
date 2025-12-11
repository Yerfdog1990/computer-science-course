package project.assignment;

import java.util.LinkedList;
import java.util.Queue;

public class SecurityLogBST {
    private Node root;

    // Insert a new transaction into the BST based on risk score
    public void insert(Transaction transaction) {
        root = insertRec(root, transaction);
    }

    private Node insertRec(Node node, Transaction transaction) {
        if (node == null) {
            return new Node(transaction);
        }

        // Insert based on risk score (lower scores to the left, higher to the right)
        if (transaction.getRiskScore() < node.transaction.getRiskScore()) {
            node.left = insertRec(node.left, transaction);
        } else if (transaction.getRiskScore() > node.transaction.getRiskScore()) {
            node.right = insertRec(node.right, transaction);
        } else {
            node.right = insertRec(node.right, transaction);
        }

        return node;
    }

    // Search for a transaction by ID
    public Transaction search(String transactionId) {
        return searchRec(root, transactionId);
    }

    private Transaction searchRec(Node node, String transactionId) {
        if (node == null) {
            return null;
        }

        // Check current node
        if (node.transaction.getTransactionId().equals(transactionId)) {
            return node.transaction;
        }

        // Search in left and right subtrees
        Transaction leftResult = searchRec(node.left, transactionId);
        if (leftResult != null) {
            return leftResult;
        }

        return searchRec(node.right, transactionId);
    }

    // Delete a node by transaction ID
    public void delete(String transactionId) {
        // Find the node to delete
        Node nodeToDelete = findNode(root, transactionId);
        if (nodeToDelete != null) {
            root = deleteNode(root, nodeToDelete.transaction.getRiskScore(), transactionId);
        }
    }

    private Node deleteNode(Node root, int riskScore, String transactionId) {
        if (root == null) {
            return null;
        }

        // Find the node to delete
        if (riskScore < root.transaction.getRiskScore()) {
            root.left = deleteNode(root.left, riskScore, transactionId);
        } else if (riskScore > root.transaction.getRiskScore()) {
            root.right = deleteNode(root.right, riskScore, transactionId);
        } else {
            // Found node with same risk score, check transaction ID
            if (root.transaction.getTransactionId().equals(transactionId)) {
                // Node with only one child or no child
                if (root.left == null) {
                    return root.right;
                } else if (root.right == null) {
                    return root.left;
                }

                // Node with two children: Get the inorder successor
                root.transaction = minValue(root.right).transaction;

                // Delete the inorder successor
                root.right = deleteNode(root.right, root.transaction.getRiskScore(), root.transaction.getTransactionId());
            } else {
                // Same risk score but different transaction ID, search in right subtree
                root.right = deleteNode(root.right, riskScore, transactionId);
            }
        }

        return root;
    }

    private Node findNode(Node node, String transactionId) {
        if (node == null) {
            return null;
        }

        if (node.transaction.getTransactionId().equals(transactionId)) {
            return node;
        }

        Node leftResult = findNode(node.left, transactionId);
        if (leftResult != null) {
            return leftResult;
        }

        return findNode(node.right, transactionId);
    }

    private Node minValue(Node node) {
        Node current = node;
        while (current.left != null) {
            current = current.left;
        }
        return current;
    }

    // Inorder traversal (sorted by risk score)
    public void inorder() {
        inorderRec(root);
    }

    private void inorderRec(Node node) {
        if (node != null) {
            inorderRec(node.left);
            System.out.println(node.transaction);
            inorderRec(node.right);
        }
    }

    // Level order traversal
    public void levelOrder() {
        if (root == null) {
            return;
        }

        Queue<Node> queue = new LinkedList<>();
        queue.add(root);

        while (!queue.isEmpty()) {
            int levelSize = queue.size();
            for (int i = 0; i < levelSize; i++) {
                Node node = queue.poll();
                System.out.print(node.transaction.getTransactionId() +
                        "(" + node.transaction.getRiskScore() + ") ");

                if (node.left != null) {
                    queue.add(node.left);
                }
                if (node.right != null) {
                    queue.add(node.right);
                }
            }
            System.out.println();
        }
    }
}