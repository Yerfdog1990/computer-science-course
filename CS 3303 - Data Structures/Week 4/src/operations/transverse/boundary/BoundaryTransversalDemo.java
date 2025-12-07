package operations.transverse.boundary;

import java.util.ArrayList;
import java.util.List;

public class BoundaryTransversalDemo {

    static boolean isLeaf(Node node) {
        return node.left == null && node.right == null;
    }

    static void collectLeft(Node root, ArrayList<Integer> res) {
        if (root == null || isLeaf(root)) return;
        res.add(root.data);
        if (root.left != null) collectLeft(root.left, res);
        else collectLeft(root.right, res);
    }

    static void collectLeaves(Node root, ArrayList<Integer> res) {
        if (root == null) return;
        if (isLeaf(root)) { res.add(root.data); return; }
        collectLeaves(root.left, res);
        collectLeaves(root.right, res);
    }

    static void collectRight(Node root, ArrayList<Integer> res) {
        if (root == null || isLeaf(root)) return;
        if (root.right != null) collectRight(root.right, res);
        else collectRight(root.left, res);
        res.add(root.data); // bottom-up
    }

    static ArrayList<Integer> boundaryTraversal(Node root) {
        ArrayList<Integer> res = new ArrayList<>();
        if (root == null) return res;
        if (!isLeaf(root)) res.add(root.data);
        collectLeft(root.left, res);
        collectLeaves(root, res);
        collectRight(root.right, res);
        return res;
    }

    public static void main(String[] args) {
        Node root = new Node(1);
        root.left = new Node(2);
        root.right = new Node(3);
        root.left.left = new Node(4);
        root.left.right = new Node(5);
        root.right.left = new Node(6);
        root.right.right = new Node(7);
        root.left.right.left = new Node(8);
        root.left.right.right = new Node(9);

        List<Integer> boundary = boundaryTraversal(root);
        for (int x : boundary) System.out.print(x + " ");
    }
}