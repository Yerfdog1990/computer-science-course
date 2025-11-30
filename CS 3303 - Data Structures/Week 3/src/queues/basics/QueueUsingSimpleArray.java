package queues.basics;

public class QueueUsingSimpleArray {

    private int[] arr;
    private int capacity;
    private int size;

    // Constructor
    public QueueUsingSimpleArray(int capacity) {

        this.capacity = capacity;       // Maximum items queue can store
        arr = new int[capacity];        // Array to store queue items
        size = 0;                       // Initially queue is empty
    }

    // Check if queue is empty
    public boolean isEmpty() {
        return size == 0;
    }

    // Check if queue is full
    public boolean isFull() {
        return size == capacity;
    }

    // Enqueue operation
    public void enqueue(int x) {
        if (isFull()) {
            System.out.println("Queue Overflow");
            return;
        }
        arr[size] = x;      // Insert at the end
        size++;
    }

    // Dequeue operation
    public void dequeue() {
        if (isEmpty()) {
            System.out.println("Queue Underflow");
            return;
        }

        // Shift all items to the left
        for (int i = 1; i < size; i++) {
            arr[i - 1] = arr[i];
        }
        size--;
    }

    // Get the first element
    public int getFront() {
        if (isEmpty()) {
            System.out.println("Queue is empty!");
            return -1;
        }
        return arr[0];
    }

    // Get the last element
    public int getRear() {
        if (isEmpty()) {
            System.out.println("Queue is empty!");
            return -1;
        }
        return arr[size - 1];
    }

    public static void main(String[] args) {

        QueueUsingSimpleArray q = new QueueUsingSimpleArray(3);

        q.enqueue(10);
        q.enqueue(20);
        q.enqueue(30);

        System.out.println("Front: " + q.getFront());
        q.dequeue();

        System.out.println("Front: " + q.getFront());
        System.out.println("Rear: " + q.getRear());

        q.enqueue(40);
    }
}



