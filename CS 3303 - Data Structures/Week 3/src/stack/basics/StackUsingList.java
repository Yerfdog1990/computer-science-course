package stack.basics;

import java.util.ArrayList;
import java.util.Arrays;
import java.util.List;

public class StackUsingList {
    private List<Integer> list;
    private int top;
    private int capacity;

    // Constructor
    public StackUsingList() {
        this.list = new ArrayList<>();
        this.capacity = list.size();
        this.top = -1;
    }

    // push operation
    public void push(int item) {
        if (top == capacity - 1) {
            capacity *= 2;
        }
        list.add(item);
        top++;
    }

    // pop operation
    public int pop() {
        if (isEmpty()) throw new IllegalStateException("Stack is empty");
        int value = list.remove(top);
        top--;
        return value;
    }

    // peek operation
    public int peek() {
        if (top == -1) throw new IllegalStateException("Stack is empty");
        return list.get(top);
    }

    // isEmpty operation
    public boolean isEmpty() {
        return top == -1;
    }

    // isFull operation
    public boolean isFull() {
        return top == capacity - 1;
    }

    public static void main(String[] args) {
        StackUsingList stack = new StackUsingList();

        // Pushing elements to the stack
        stack.push(10);
        stack.push(20);
        stack.push(30);
        stack.push(40);
        stack.push(50);
        stack.push(60);
        stack.push(70);
        stack.push(80);
        stack.push(90);
        stack.push(100);
        stack.push(110);

        // printing stack
        System.out.println("Stack: " + stack.list);

        // popping one element
        System.out.println("Popped: " + stack.pop());

        // checking top element
        System.out.println("Top element: " + stack.peek());

        // checking if the stack is empty
        System.out.println("Is stack empty: " +
                (stack.isEmpty() ? "Yes" : "No"));

        // checking if the stack is full
        System.out.println("Is stack full: " +
                (stack.isFull() ? "Yes" : "No"));

    }
}
