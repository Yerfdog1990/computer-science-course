

# **DSA Linked Lists — Detailed Notes**

## **Introduction to Linked Lists**

A **Linked List** is, as the word implies, a list where the **nodes are linked together**.
Each node contains **data** and a **pointer**, and the way they are linked together is that **each node points to where in the memory the next node is placed**.

In other words, a linked list consists of **nodes with some sort of data**, and a **pointer or link** to the next node.
This is the fundamental structure behind **a singly linked list**.

---

## **Benefits of Linked Lists**

One big benefit of using linked lists is that **nodes are stored wherever there is free space in memory**.
Unlike arrays, nodes **do not need to be stored contiguously right after each other**.
This flexibility allows linked lists to grow dynamically without requiring a large block of consecutive memory.

Another nice advantage is that when **adding or removing nodes**, the **rest of the nodes do not need to be shifted**.
Only the pointers need to be updated, making insertion and deletion operations efficient.

---

## **Linked Lists vs Arrays**

The easiest way to understand linked lists is perhaps by **comparing linked lists with arrays**:

* **Linked lists** consist of nodes and form a **linear data structure that we build ourselves**.
* **Arrays**, on the other hand, are an **existing data structure** provided by the programming language.
* Nodes in a linked list **store links to other nodes**, while **array elements do not store links**.
* How linked lists and arrays are **stored in memory** will be explained in more detail on the next page.

---

## **Comparison Table: Arrays vs Linked Lists**

| Feature                                                                               | Arrays | Linked Lists |
| ------------------------------------------------------------------------------------- | ------ | ------------ |
| An existing data structure in the programming language                                | Yes    | No           |
| Fixed size in memory                                                                  | Yes    | No           |
| Elements/nodes stored contiguously in memory                                          | Yes    | No           |
| Memory usage is low (each node only contains data, no links)                          | Yes    | No           |
| Elements/nodes can be accessed directly (random access)                               | Yes    | No           |
| Elements/nodes can be inserted or deleted in constant time, with no shifting required | No     | Yes          |

This table helps give a clearer understanding of what linked lists are and how they differ from arrays.
To further explain these differences, the next page will focus specifically on **how linked lists and arrays are stored in memory**.

---

## **DSA Exercises**

### **Test Yourself With Exercises**

**Exercise:**
*What is a node in a Linked List?*

Each node in a Linked List **contains data**, and a **pointer** to where the next node **is placed in memory**.

Start the Exercise

---

