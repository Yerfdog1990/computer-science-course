package queues.basics;

// Queue class definition
public class QueueUsingLinkedList {

    private Node front;
    private Node rear;
    private int currSize;

    public QueueUsingLinkedList() {
        currSize = 0;
        front = rear = null;
    }

    // Check if the queue is empty
    public boolean isEmpty() {
        return front == null;
    }

    // Enqueue operation
    public void enqueue(int new_data) {
        Node new_node = new Node(new_data);

        if (isEmpty()) {
            front = rear = new_node;
        } else {
            rear.next = new_node;
            rear = new_node;
        }

        currSize++;
    }

    // Dequeue operation
    public int dequeue() {
        if (isEmpty()) {
            System.out.println("Queue Underflow");
            return -1;
        }

        Node temp = front;
        int removedData = temp.data;

        front = front.next;

        if (front == null)
            rear = null;

        currSize--;
        return removedData;
    }

    // Return front element
    public int getfront() {
        if (isEmpty()) {
            System.out.println("Queue is empty");
            return -1;
        }
        return front.data;
    }

    // Return size of queue
    public int size() {
        return currSize;
    }
    public static void main(String[] args) {
        QueueUsingLinkedList q = new QueueUsingLinkedList();

        q.enqueue(10);
        q.enqueue(20);

        System.out.println("Dequeue: " + q.dequeue());

        q.enqueue(30);

        System.out.println("Front: " + q.getfront());
        System.out.println("Size: " + q.size());
    }
}