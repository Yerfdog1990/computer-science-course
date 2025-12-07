package operations.transverse.inorder;

import java.util.ArrayList;

public class InOrderTransversalDemo {

    static void inOrder(Node node, ArrayList<Integer> res) {
        if (node == null)
            return;

        // Traverse the left subtree first
        inOrder(node.left, res);

        // Visit the current node
        res.add(node.data);

        // Traverse the right subtree last
        inOrder(node.right, res);
    }

    public static void main(String[] args) {
        // Create a binary tree
        //        1
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

        ArrayList<Integer> res = new ArrayList<>();
        inOrder(root, res);

        for(int node : res)
            System.out.print(node + " ");
    }
}
