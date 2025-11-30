package queues.basics;


import java.util.Arrays;

public class PriorityQueue {
    private int[] heap;
    private int size;
    private static final int DEFAULT_CAPACITY = 10;

    public PriorityQueue() {
        this.heap = new int[DEFAULT_CAPACITY];
        this.size = 0;
    }

    public PriorityQueue(int initialCapacity) {
        this.heap = new int[initialCapacity];
        this.size = 0;
    }

    // Insert a new element into the priority queue
    public void enqueue(int item) {
        if (size == heap.length) {
            resize();
        }

        // Add the new item to the end
        heap[size] = item;

        // Heapify up to maintain the heap property
        heapifyUp(size);
        size++;
    }

    // Remove and return the element with the highest priority (smallest number)
    public int dequeue() {
        if (isEmpty()) {
            throw new IllegalStateException("Priority queue is empty");
        }

        int min = heap[0];

        // Replace the root with the last element
        heap[0] = heap[size - 1];
        size--;

        // Heapify down to maintain the heap property
        if (!isEmpty()) {
            heapifyDown(0);
        }

        return min;
    }

    // Get the element with the highest priority without removing it
    public int peek() {
        if (isEmpty()) {
            throw new IllegalStateException("Priority queue is empty");
        }
        return heap[0];
    }

    public boolean isEmpty() {
        return size == 0;
    }

    public int size() {
        return size;
    }

    // Helper method to maintain the heap property (upward)
    private void heapifyUp(int index) {
        int parentIndex = (index - 1) / 2;

        while (index > 0 && heap[index] < heap[parentIndex]) {
            // Swap with parent
            swap(index, parentIndex);
            index = parentIndex;
            parentIndex = (index - 1) / 2;
        }
    }

    // Helper method to maintain the heap property (downward)
    private void heapifyDown(int index) {
        int leftChild = 2 * index + 1;
        int rightChild = 2 * index + 2;
        int smallest = index;

        // Find the smallest among the current node and its children
        if (leftChild < size && heap[leftChild] < heap[smallest]) {
            smallest = leftChild;
        }
        if (rightChild < size && heap[rightChild] < heap[smallest]) {
            smallest = rightChild;
        }

        // If the smallest is not the current node, swap and continue heapifying down
        if (smallest != index) {
            swap(index, smallest);
            heapifyDown(smallest);
        }
    }

    // Helper method to swap two elements in the heap
    private void swap(int i, int j) {
        int temp = heap[i];
        heap[i] = heap[j];
        heap[j] = temp;
    }

    // Helper method to resize the heap when it's full
    private void resize() {
        int newCapacity = heap.length * 2;
        heap = Arrays.copyOf(heap, newCapacity);
    }

    @Override
    public String toString() {
        StringBuilder sb = new StringBuilder();
        sb.append("[");
        for (int i = 0; i < size; i++) {
            sb.append(heap[i]);
            if (i < size - 1) {
                sb.append(", ");
            }
        }
        sb.append("]");
        return sb.toString();
    }

    public static void main(String[] args) {
        // Create a priority queue
        PriorityQueue pq = new PriorityQueue();

        // Enqueue elements with different priorities
        System.out.println("Enqueuing elements: 5, 3, 8, 1, 4, 7");
        pq.enqueue(5);
        pq.enqueue(3);
        pq.enqueue(8);
        pq.enqueue(1);
        pq.enqueue(4);
        pq.enqueue(7);

        System.out.println("Priority Queue: " + pq);
        System.out.println("Size: " + pq.size());
        System.out.println("Peek: " + pq.peek());

        // Dequeue elements
        System.out.println("\nDequeuing elements:");
        while (!pq.isEmpty()) {
            System.out.println("Dequeued: " + pq.dequeue() + " | Remaining: " + pq);
        }
    }
}
