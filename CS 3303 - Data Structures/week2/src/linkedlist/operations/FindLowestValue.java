package linkedlist.operations;


public class FindLowestValue {
    public static int findLowestValue(Node head) {
        int minValue = head.data;          // initial lowest value
        Node currentNode = head.next;

        while (currentNode != null) {
            if (currentNode.data < minValue) {
                minValue = currentNode.data;
            }
            currentNode = currentNode.next;
        }
        return minValue;
    }

    public static void main(String[] args) {
        Node node1 = new Node(7);
        Node node2 = new Node(11);
        Node node3 = new Node(3);
        Node node4 = new Node(2);
        Node node5 = new Node(9);

        node1.next = node2;
        node2.next = node3;
        node3.next = node4;
        node4.next = node5;

        System.out.println("The lowest value in the linked list is: " +
                findLowestValue(node1));
    }
}
