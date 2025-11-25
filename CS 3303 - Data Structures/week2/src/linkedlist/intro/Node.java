package linkedlist.intro;

// Define the 'Node' class
class Node {
    // Data members
    int data1;
    int data2;
    int data;

    // Reference to another Node object (self-referential)
    Node link;

    // Reference to other Node objects (multi-referential)
    Node prev_link;
    Node next_link;

    // Constructor to initialize values
    public Node(int data1, int data2) {
        this.data1 = data1;
        this.data2 = data2;
        this.link = null;
    }

    public Node(int data) {
        this.data1 = data;
        this.link = null;
    }

    // Default constructor
    public Node() {
        this.data1 = 10;
        this.data2 = 20;
        this.link = null;
    }
}


