package linkedlist.operations;


public class InsertNode {
    public static void traverseAndPrint(Node head) {
        Node currentNode = head;
        while (currentNode != null) {
            System.out.print(currentNode.data + " -> ");
            currentNode = currentNode.next;
        }
        System.out.println("null");
    }

    public static Node insertNodeAtPosition(Node head, Node newNode, int position) {
        if (position == 1) {
            newNode.next = head;
            return newNode;
        }

        Node currentNode = head;
        for (int i = 0; i < position - 2; i++) {
            if (currentNode == null) {
                break;
            }
            currentNode = currentNode.next;
        }

        newNode.next = currentNode.next;
        currentNode.next = newNode;

        return head;
    }

    public static void main(String[] args) {
        Node node1 = new Node(7);
        Node node2 = new Node(3);
        Node node3 = new Node(2);
        Node node4 = new Node(9);

        node1.next = node2;
        node2.next = node3;
        node3.next = node4;

        System.out.println("Original list:");
        traverseAndPrint(node1);

        Node newNode = new Node(97);
        node1 = insertNodeAtPosition(node1, newNode, 2);

        System.out.println("\nAfter insertion:");
        traverseAndPrint(node1);
    }
}