package stack.basics;

import java.util.Arrays;

public class StackUsingArrays {
    private int capacity;
    private int[] stackArray;
    private int top;

    // Constructor

    public StackUsingArrays(int capacity) {
        this.capacity = capacity;
        this.stackArray = new int[capacity];
        this.top = -1;
    }

    // push operation
    public void push(int item) {
        if (top == capacity - 1) throw new StackOverflowError();
        stackArray[++top] = item;
    }

    // pop operation
    public int pop() {
        if (top == -1) throw new IllegalStateException("Stack is empty");
        return stackArray[top--];
    }

    // peek operation
    public int peek() {
        if (top == -1) throw new IllegalStateException("Stack is empty");
        return stackArray[top];
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
        StackUsingArrays stack = new StackUsingArrays(10);
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
        //stack.push(110); // Throws StackOverflowError

        // printing stack
        System.out.println("Stack: " + Arrays.toString(stack.stackArray));

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
