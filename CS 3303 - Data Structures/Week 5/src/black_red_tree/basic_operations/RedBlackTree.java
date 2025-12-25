package black_red_tree.basic_operations;

public class RedBlackTree {
    private final Node NIL;   // Sentinel NIL node
    private Node root;

    // Constructor
    public RedBlackTree() {
        NIL = new Node(0);
        NIL.color = NodeColour.BLACK;
        NIL.left = NIL.right = NIL;
        root = NIL;
    }

    /* ---------------- ROTATIONS ---------------- */
    private void leftRotate(Node x) {
        Node y = x.right;
        x.right = y.left;

        if (y.left != NIL) {
            y.left.parent = x;
        }

        y.parent = x.parent;

        if (x.parent == null) {
            root = y;
        } else if (x == x.parent.left) {
            x.parent.left = y;
        } else {
            x.parent.right = y;
        }

        y.left = x;
        x.parent = y;
    }

    private void rightRotate(Node x) {
        Node y = x.left;
        x.left = y.right;

        if (y.right != NIL) {
            y.right.parent = x;
        }

        y.parent = x.parent;

        if (x.parent == null) {
            root = y;
        } else if (x == x.parent.right) {
            x.parent.right = y;
        } else {
            x.parent.left = y;
        }

        y.right = x;
        x.parent = y;
    }

    /* ---------------- INSERTION ---------------- */
    public void insert(int data) {
        Node newNode = new Node(data);
        newNode.left = NIL;
        newNode.right = NIL;

        Node y = null;
        Node x = root;

        // Standard BST insert
        while (x != NIL) {
            y = x;
            if (newNode.data < x.data) {
                x = x.left;
            } else {
                x = x.right;
            }
        }

        newNode.parent = y;
        if (y == null) {
            root = newNode;
        } else if (newNode.data < y.data) {
            y.left = newNode;
        } else {
            y.right = newNode;
        }

        // Fix any violations
        fixInsert(newNode);
    }

    private void fixInsert(Node k) {
        while (k.parent != null && k.parent.color == NodeColour.RED) {
            if (k.parent == k.parent.parent.left) {
                Node y = k.parent.parent.right;
                if (y != NIL && y.color == NodeColour.RED) {
                    k.parent.color = NodeColour.BLACK;
                    y.color = NodeColour.BLACK;
                    k.parent.parent.color = NodeColour.RED;
                    k = k.parent.parent;
                } else {
                    if (k == k.parent.right) {
                        k = k.parent;
                        leftRotate(k);
                    }
                    k.parent.color = NodeColour.BLACK;
                    k.parent.parent.color = NodeColour.RED;
                    rightRotate(k.parent.parent);
                }
            } else {
                Node y = k.parent.parent.left;
                if (y != NIL && y.color == NodeColour.RED) {
                    k.parent.color = NodeColour.BLACK;
                    y.color = NodeColour.BLACK;
                    k.parent.parent.color = NodeColour.RED;
                    k = k.parent.parent;
                } else {
                    if (k == k.parent.left) {
                        k = k.parent;
                        rightRotate(k);
                    }
                    k.parent.color = NodeColour.BLACK;
                    k.parent.parent.color = NodeColour.RED;
                    leftRotate(k.parent.parent);
                }
            }
            if (k == root) {
                break;
            }
        }
        root.color = NodeColour.BLACK;
    }

    /* ---------------- SEARCH ---------------- */
    public boolean search(int key) {
        return searchHelper(root, key) != NIL;
    }

    private Node searchHelper(Node node, int key) {
        if (node == NIL || key == node.data) {
            return node;
        }
        if (key < node.data) {
            return searchHelper(node.left, key);
        }
        return searchHelper(node.right, key);
    }

    /* ---------------- DELETION ---------------- */
    public void delete(int data) {
        Node z = searchHelper(root, data);
        if (z == NIL) return;

        Node y = z;
        NodeColour yOriginalColor = y.color;
        Node x;

        if (z.left == NIL) {
            x = z.right;
            transplant(z, z.right);
        } else if (z.right == NIL) {
            x = z.left;
            transplant(z, z.left);
        } else {
            y = minimum(z.right);
            yOriginalColor = y.color;
            x = y.right;
            if (y.parent == z) {
                x.parent = y;
            } else {
                transplant(y, y.right);
                y.right = z.right;
                y.right.parent = y;
            }
            transplant(z, y);
            y.left = z.left;
            y.left.parent = y;
            y.color = z.color;
        }
        if (yOriginalColor == NodeColour.BLACK) {
            fixDelete(x);
        }
    }

    private void fixDelete(Node x) {
        while (x != root && x.color == NodeColour.BLACK) {
            if (x == x.parent.left) {
                Node w = x.parent.right;
                if (w.color == NodeColour.RED) {
                    w.color = NodeColour.BLACK;
                    x.parent.color = NodeColour.RED;
                    leftRotate(x.parent);
                    w = x.parent.right;
                }
                if (w.left.color == NodeColour.BLACK && w.right.color == NodeColour.BLACK) {
                    w.color = NodeColour.RED;
                    x = x.parent;
                } else {
                    if (w.right.color == NodeColour.BLACK) {
                        w.left.color = NodeColour.BLACK;
                        w.color = NodeColour.RED;
                        rightRotate(w);
                        w = x.parent.right;
                    }
                    w.color = x.parent.color;
                    x.parent.color = NodeColour.BLACK;
                    w.right.color = NodeColour.BLACK;
                    leftRotate(x.parent);
                    x = root;
                }
            } else {
                Node w = x.parent.left;
                if (w.color == NodeColour.RED) {
                    w.color = NodeColour.BLACK;
                    x.parent.color = NodeColour.RED;
                    rightRotate(x.parent);
                    w = x.parent.left;
                }
                if (w.right.color == NodeColour.BLACK && w.left.color == NodeColour.BLACK) {
                    w.color = NodeColour.RED;
                    x = x.parent;
                } else {
                    if (w.left.color == NodeColour.BLACK) {
                        w.right.color = NodeColour.BLACK;
                        w.color = NodeColour.RED;
                        leftRotate(w);
                        w = x.parent.left;
                    }
                    w.color = x.parent.color;
                    x.parent.color = NodeColour.BLACK;
                    w.left.color = NodeColour.BLACK;
                    rightRotate(x.parent);
                    x = root;
                }
            }
        }
        x.color = NodeColour.BLACK;
    }

    /* ---------------- HELPER METHODS ---------------- */
    private void transplant(Node u, Node v) {
        if (u.parent == null) {
            root = v;
        } else if (u == u.parent.left) {
            u.parent.left = v;
        } else {
            u.parent.right = v;
        }
        v.parent = u.parent;
    }

    private Node minimum(Node node) {
        while (node.left != NIL) {
            node = node.left;
        }
        return node;
    }

    /* ---------------- TRAVERSALS ---------------- */
    public void inOrder() {
        inOrderHelper(root);
        System.out.println();
    }

    private void inOrderHelper(Node node) {
        if (node != NIL) {
            inOrderHelper(node.left);
            System.out.print(node.data + "(" + (node.color == NodeColour.RED ? "R" : "B") + ") ");
            inOrderHelper(node.right);
        }
    }

    /* ---------------- MAIN METHOD ---------------- */
    public static void main(String[] args) {
        RedBlackTree tree = new RedBlackTree();

        // Insertion
        System.out.println("Inserting values: 10, 20, 30, 15, 25");
        tree.insert(10);
        tree.insert(20);
        tree.insert(30);
        tree.insert(15);
        tree.insert(25);

        System.out.print("In-order traversal: ");
        tree.inOrder();

        // Search
        System.out.println("\nSearch operations:");
        System.out.println("Search 15: " + tree.search(15));
        System.out.println("Search 25: " + tree.search(25));
        System.out.println("Search 99: " + tree.search(99));

        // Deletion
        System.out.println("\nDeleting 15");
        tree.delete(15);
        System.out.print("In-order traversal after deletion: ");
        tree.inOrder();

        System.out.println("\nDeleting 20");
        tree.delete(20);
        System.out.print("In-order traversal after deletion: ");
        tree.inOrder();
    }
}