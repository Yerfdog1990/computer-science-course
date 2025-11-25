package linkedlist.types.circular_linkedlist.doubled;


public class CircularDoublyLinkedListDemo {
    public static void main(String[] args) {
        CDNode node1 = new CDNode(3);
        CDNode node2 = new CDNode(5);
        CDNode node3 = new CDNode(13);
        CDNode node4 = new CDNode(2);

        node1.next = node2;
        node1.prev = node4;   // Line 13: Makes circular

        node2.prev = node1;
        node2.next = node3;

        node3.prev = node2;
        node3.next = node4;

        node4.prev = node3;
        node4.next = node1;   // Line 22: Makes circular

        System.out.println("\nTraversing forward:");
        CDNode currentNode = node1;
        CDNode startNode = node1;

        System.out.print(currentNode.data + " -> ");
        currentNode = currentNode.next;

        while (currentNode != startNode) { // Line 26: ensures single full loop
            System.out.print(currentNode.data + " -> ");
            currentNode = currentNode.next;
        }
        System.out.println("...");

        System.out.println("\nTraversing backward:");
        currentNode = node4;
        startNode = node4;

        System.out.print(currentNode.data + " -> ");
        currentNode = currentNode.prev;

        while (currentNode != startNode) {
            System.out.print(currentNode.data + " -> ");
            currentNode = currentNode.prev;
        }
        System.out.println("...");
    }
}