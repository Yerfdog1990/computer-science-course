package linkedlist.types.circular_linkedlist.singly;

public class CircularSinglyLinkedListDemo {
    public static void main(String[] args) {
        CSNode node1 = new CSNode(3);
        CSNode node2 = new CSNode(5);
        CSNode node3 = new CSNode(13);
        CSNode node4 = new CSNode(2);

        node1.next = node2;
        node2.next = node3;
        node3.next = node4;
        node4.next = node1;  // Line 14: Makes it circular

        CSNode currentNode = node1;
        CSNode startNode = node1;

        System.out.print(currentNode.data + " -> ");
        currentNode = currentNode.next;

        while (currentNode != startNode) { // Line 17: stops after looping once
            System.out.print(currentNode.data + " -> ");
            currentNode = currentNode.next;
        }

        System.out.println("...");
    }
}