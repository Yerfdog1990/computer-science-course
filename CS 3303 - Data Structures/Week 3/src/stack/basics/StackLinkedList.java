package stack.basics;

public class StackLinkedList {
    private Node top;
    private int count;

    public StackLinkedList() {
        this.top = null;
        this.count = 0;
    }

    // push operation
    public void push(int item) {
        Node temp = new Node(item);
        temp.next = top;
        top = temp;

        count++;
    }

    // pop operation
    public int pop() {
        if (top == null) throw new IllegalStateException("Stack is empty");

        Node temp = top;
        top = top.next;
        count--;

        return temp.data;
    }

    // peek operation
    public int peek() {
        if (top == null) throw new IllegalStateException("Stack is empty");

        return top.data;
    }

    // isEmpty operation
    public boolean isEmpty() {
        return top == null;
    }

    // isFull operation
    public boolean isFull() {
        return false;
    }


    // size of stack
    public int size() {
        return count;
    }

    // Main method
    public static void main(String[] args) {
        StackLinkedList stack = new StackLinkedList();

        // pushing elements
        stack.push(1);
        stack.push(2);
        stack.push(3);
        stack.push(4);
        stack.push(5);
        stack.push(6);
        stack.push(7);
        stack.push(8);
        stack.push(9);
        stack.push(10);
        stack.push(11);
        stack.push(12);
        stack.push(13);

        // popping one element
        System.out.println("Popped: " + stack.pop());

        // checking top element
        System.out.println("Top element: " + stack.peek());

        // checking if the stack is empty
        System.out.println("Is stack empty: " + (stack.isEmpty() ? "Yes" : "No"));

        // checking current size
        System.out.println("Current size: " + stack.size());
    }
}
