package linkedlist.intro;



public class MultipleLink {
    public static void main(String[] args) {
        // Create nodes
        Node ob1 = new Node(); // Node1
        Node ob2 = new Node(); // Node2
        Node ob3 = new Node(); // Node3

        // Initialize data
        ob1.data = 10;
        ob2.data = 20;
        ob3.data = 30;

        // Set forward links
        ob1.next_link = ob2;
        ob2.next_link = ob3;

        // Set backward links
        ob2.prev_link = ob1;
        ob3.prev_link = ob2;

        // Accessing data using ob1
        System.out.println(
                ob1.data + "\t" +
                        ob1.next_link.data + "\t" +
                        ob1.next_link.next_link.data
        );

        // Accessing data using ob2
        System.out.println(
                ob2.prev_link.data + "\t" +
                        ob2.data + "\t" +
                        ob2.next_link.data
        );

        // Accessing data using ob3
        System.out.println(
                ob3.prev_link.prev_link.data + "\t" +
                        ob3.prev_link.data + "\t" +
                        ob3.data
        );
    }
}



