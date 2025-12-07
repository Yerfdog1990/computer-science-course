package operations.transverse.preorder;

import java.util.ArrayList;

public class PreOrderTransversalDemo {

    public static void preOrder(Node node, ArrayList<Integer> res) {
        if (node == null)
            return;

        // Visit the current node first
        res.add(node.data);

        // Traverse the left subtree
        preOrder(node.left, res);

        // Traverse the right subtree
        preOrder(node.right, res);
    }

    public static void main(String[] args) {
        // Create a binary tree
        //       1
        //      /  \
        //    2     3
        //   / \     \
        //  4   5     6

        Node root = new Node(1);
        root.left = new Node(2);
        root.right = new Node(3);
        root.left.left = new Node(4);
        root.left.right = new Node(5);
        root.right.right = new Node(6);

        ArrayList<Integer> result = new ArrayList<>();
        preOrder(root, result);

        for (int val : result) {
            System.out.print(val + " ");
        }
    }
}