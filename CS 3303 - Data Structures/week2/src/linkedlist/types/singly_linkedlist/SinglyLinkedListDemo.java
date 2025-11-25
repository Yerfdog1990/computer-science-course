package linkedlist.types.singly_linkedlist;


public class SinglyLinkedListDemo {
    public static void main(String[] args) {
        SNode SNode1 = new SNode(3);
        SNode SNode2 = new SNode(5);
        SNode SNode3 = new SNode(13);
        SNode SNode4 = new SNode(2);

        SNode1.next = SNode2;
        SNode2.next = SNode3;
        SNode3.next = SNode4;

        SNode currentSNode = SNode1;
        while (currentSNode != null) {
            System.out.print(currentSNode.data + " -> ");
            currentSNode = currentSNode.next;
        }
        System.out.println("null");
    }
}