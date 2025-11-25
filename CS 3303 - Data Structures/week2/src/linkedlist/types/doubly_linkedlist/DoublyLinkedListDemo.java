package linkedlist.types.doubly_linkedlist;

public class DoublyLinkedListDemo {
    public static void main(String[] args) {
        DNode node1 = new DNode(3);
        DNode node2 = new DNode(5);
        DNode node3 = new DNode(13);
        DNode node4 = new DNode(2);

        node1.next = node2;

        node2.prev = node1;
        node2.next = node3;

        node3.prev = node2;
        node3.next = node4;

        node4.prev = node3;

        System.out.println("\nTraversing forward:");
        DNode currentNode = node1;
        while (currentNode != null) {
            System.out.print(currentNode.data + " -> ");
            currentNode = currentNode.next;
        }
        System.out.println("null");

        System.out.println("\nTraversing backward:");
        currentNode = node4;
        while (currentNode != null) {
            System.out.print(currentNode.data + " -> ");
            currentNode = currentNode.prev;
        }
        System.out.println("null");
    }
}
