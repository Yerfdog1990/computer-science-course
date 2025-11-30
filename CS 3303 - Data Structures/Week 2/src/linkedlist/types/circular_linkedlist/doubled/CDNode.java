package linkedlist.types.circular_linkedlist.doubled;

class CDNode {
    int data;
    CDNode next;
    CDNode prev;

    CDNode(int data) {
        this.data = data;
        this.next = null;
        this.prev = null;
    }
}