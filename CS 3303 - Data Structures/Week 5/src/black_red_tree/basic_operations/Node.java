package black_red_tree.basic_operations;

public class Node {
    public int data;
    public NodeColour color;
    public Node left, right, parent;

    public Node(int data) {
        this.data = data;
        this.color = NodeColour.RED; // New nodes are always red
        this.left = null;
        this.right = null;
        this.parent = null;
    }
}
