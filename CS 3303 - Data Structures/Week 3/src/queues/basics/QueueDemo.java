package queues.basics;

import java.util.LinkedList;

public class QueueDemo {
    public static void main(String[] args) {
        LinkedList<Integer> queue = new LinkedList<>();

        // Enqueue operation
        queue.add(10);
        queue.add(20);
        queue.add(30);
        queue.add(40);
        queue.add(50);

        // print queue
        System.out.println("Queue: " + queue);

        // getFirst()/peek operation
        System.out.println("Get front element: " + queue.getFirst());

        // getRear() operation
        System.out.println("Get rear element: " + queue.getLast());

        // Dequeue operation
        System.out.println("Remove rear element: " + queue.remove());

        // isEmpty() operation
        System.out.println("Is queue empty: " + queue.isEmpty());

        // size() operation
        System.out.println("Size of queue: " + queue.size());
    }
}
