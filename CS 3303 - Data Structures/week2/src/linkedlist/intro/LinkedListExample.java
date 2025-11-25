package linkedlist.intro;



public class LinkedListExample {

    public static void printList(Node head) {
        Node current = head;
        while (current != null) {
            System.out.print(current.data1 + " -> ");
            current = current.link;
        }
        System.out.println("null");
    }

    public static void main(String[] args) {

        Node node1 = new Node(3);
        Node node2 = new Node(5);
        Node node3 = new Node(13);
        Node node4 = new Node(2);

        // Linking nodes
        node1.link = node2;
        node2.link = node3;
        node3.link = node4;

        printList(node1);
    }
}
