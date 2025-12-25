package black_red_tree.check_if_balanced;

public class TreeDemo {

    // Function to calculate the height of a tree
    static int height(Node node) {
        if (node == null)
            return 0;

        // Height = 1 + max of left height and right heights
        return 1 + Math.max(height(node.left), height(node.right));
    }

    // Function to check if the binary tree with a given root
    // is height-balanced
    static boolean isBalanced(Node root) {
        if (root == null)
            return true;

        // Get the height of left and right subtrees
        int lHeight = height(root.left);
        int rHeight = height(root.right);

        if (Math.abs(lHeight - rHeight) > 1)
            return false;

        // Recursively check the left and right subtrees
        return isBalanced(root.left) && isBalanced(root.right);
    }

    public static void main(String[] args) {
        // Representation of input BST:
        //            10
        //           / \
        //          20   30
        //         /  \
        //        40   60
        Node root = new Node(10);
        root.left = new Node(20);
        root.right = new Node(30);
        root.left.left = new Node(40);
        root.left.right = new Node(60);
        //root.left.left.left = new Node(50);

        System.out.println(isBalanced(root) ? "true" : "false");
    }
}