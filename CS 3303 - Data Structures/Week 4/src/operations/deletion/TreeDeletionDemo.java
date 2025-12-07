package operations.deletion;


import java.util.LinkedList;
import java.util.Queue;

// Java program to delete a specific 
// element in a binary tree
public class TreeDeletionDemo {

    // Function to delete the deepest node in a binary tree
    static void deleteDeepest(Node root, Node dNode) {
        Queue<Node> q = new LinkedList<>();
        q.add(root);

        Node curr;
        while (!q.isEmpty()) {
            curr = q.poll();

            // If the current node is the deepest node, delete it
            if (curr == dNode) {
                curr = null;
                dNode = null;
                return;
            }

            // Check right child
            if (curr.right != null) {
                if (curr.right == dNode) {
                    curr.right = null;
                    dNode = null;
                    return;
                }
                q.add(curr.right);
            }

            // Check left child
            if (curr.left != null) {
                if (curr.left == dNode) {
                    curr.left = null;
                    dNode = null;
                    return;
                }
                q.add(curr.left);
            }
        }
    }

    // Function to delete the node with the given key
    static Node deletion(Node root, int key) {

        if (root == null)
            return null;

        // Tree has only one node
        if (root.left == null && root.right == null) {
            if (root.data == key)
                return null;
            else
                return root;
        }

        Queue<Node> q = new LinkedList<>();
        q.add(root);

        Node curr = null;
        Node keyNode = null;

        // BFS to find the deepest node and key node
        while (!q.isEmpty()) {
            curr = q.poll();

            if (curr.data == key)
                keyNode = curr;

            if (curr.left != null)
                q.add(curr.left);

            if (curr.right != null)
                q.add(curr.right);
        }

        // Replace key node with deepest node
        if (keyNode != null) {
            int x = curr.data;   // deepest node data
            keyNode.data = x;    // replace
            deleteDeepest(root, curr);  // delete deepest node
        }

        return root;
    }

    // Inorder traversal
    static void inorder(Node curr) {
        if (curr == null)
            return;

        inorder(curr.left);
        System.out.print(curr.data + " ");
        inorder(curr.right);
    }

    public static void main(String[] args) {

        // Construct the binary tree
        //       10         
        //      /  \       
        //    11    9
        //   / \   / \     
        //  7  12 15  8   
        Node root = new Node(10);
        root.left = new Node(11);
        root.right = new Node(9);
        root.left.left = new Node(7);
        root.left.right = new Node(12);
        root.right.left = new Node(15);
        root.right.right = new Node(8);

        int key = 11;
        root = deletion(root, key);
        inorder(root);
    }
}