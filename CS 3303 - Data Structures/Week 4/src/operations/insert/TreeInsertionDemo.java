package operations.insert;

import java.util.LinkedList;
import java.util.Queue;

// Java program to insert the element (in level order)
// in Binary Tree
public class TreeInsertionDemo {

    // Function to insert the element
    // in the binary tree
    static Node InsertNode(Node root, int data) {

        // If the tree is empty, assign new node
        // address to root
        if (root == null) {
            root = new Node(data);
            return root;
        }

        // Else, do level order traversal until we find an empty
        // place, i.e. either the left child or right child of some
        // node is pointing to NULL.
        Queue<Node> q = new LinkedList<>();
        q.add(root);

        while (!q.isEmpty()) {

            // Front element in queue
            Node curr = q.poll();

            // First check left; if left is null, insert
            if (curr.left != null)
                q.add(curr.left);
            else {
                curr.left = new Node(data);
                return root;
            }

            // Then check right
            if (curr.right != null)
                q.add(curr.right);
            else {
                curr.right = new Node(data);
                return root;
            }
        }
        return root;
    }

    // Inorder traversal of a binary tree
    static void inorder(Node curr) {
        if (curr == null)
            return;
        inorder(curr.left);
        System.out.print(curr.data + " ");
        inorder(curr.right);
    }

    public static void main(String[] args) {

        // Constructing the binary tree
        //          10
        //        /    \
        //       11     9
        //      /      / \
        //     7      15   8
        Node root = new Node(10);
        root.left = new Node(11);
        root.right = new Node(9);
        root.left.left = new Node(7);
        root.right.left = new Node(15);
        root.right.right = new Node(8);

        int key = 12;
        root = InsertNode(root, key);

        // After insertion (12 added)
        //          10
        //        /    \
        //       11     9
        //      /  \   / \
        //     7   12 15  8

        inorder(root);
    }
}