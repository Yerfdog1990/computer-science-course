package blood_bank_management;

/*
Blood Inventory:
Uses a circular queue to ensure FIFO usage of blood units
Tracks blood type and expiration dates
Prevents blood wastage by using oldest units first
 */
public class BloodInventory {
    private static final int MAX_INVENTORY_SIZE = 100;
    private BloodUnit[] inventory;
    private int front, rear, size;

    public BloodInventory() {
        inventory = new BloodUnit[MAX_INVENTORY_SIZE];
        front = 0;
        rear = -1;
        size = 0;
    }

    public void addUnit(BloodUnit unit) {
        if (isFull()) {
            throw new IllegalStateException("Inventory is full");
        }
        rear = (rear + 1) % MAX_INVENTORY_SIZE;
        inventory[rear] = unit;
        size++;
    }

    public BloodUnit getOldestUnit() {
        if (isEmpty()) {
            return null;
        }
        BloodUnit unit = inventory[front];
        front = (front + 1) % MAX_INVENTORY_SIZE;
        size--;
        return unit;
    }

    public BloodUnit peekOldestUnit() {
        if (isEmpty()) {
            return null;
        }
        return inventory[front];
    }

    public boolean isEmpty() {
        return size == 0;
    }

    public boolean isFull() {
        return size == MAX_INVENTORY_SIZE;
    }

    public int getSize() {
        return size;
    }
}
