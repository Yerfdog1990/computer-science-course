package queues.basics;

public class QueueUsingCircularArray {


    private int[] arr;
    private int front;
    private int size;
    private int capacity;

    public QueueUsingCircularArray(int cap) {
        capacity = cap;
        arr = new int[capacity];
        front = 0;
        size = 0;
    }

    // Insert an element at the rear
    public void enqueue(int x) {
        if (size == capacity) {
            System.out.println("Queue is full!");
            return;
        }
        int rear = (front + size) % capacity;
        arr[rear] = x;
        size++;
    }

    // Remove an element from the front
    public int dequeue() {
        if (size == 0) {
            System.out.println("Queue is empty!");
            return -1;
        }
        int res = arr[front];
        front = (front + 1) % capacity;
        size--;
        return res;
    }

    // Get the front element
    public int getFront() {
        if (size == 0) return -1;
        return arr[front];
    }

    // Get the rear element
    public int getRear() {
        if (size == 0) return -1;
        int rear = (front + size - 1) % capacity;
        return arr[rear];
    }

    public static void main(String[] args) {
        QueueUsingCircularArray q = new QueueUsingCircularArray(5);
        q.enqueue(10);
        q.enqueue(20);
        q.enqueue(30);

        System.out.println(q.getFront() + " " + q.getRear());

        q.dequeue();
        System.out.println(q.getFront() + " " + q.getRear());

        q.enqueue(40);
        System.out.println(q.getFront() + " " + q.getRear());
    }
}
