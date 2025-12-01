
---

# **📘 Lesson Notes: Postfix to Prefix Conversion**

### **Last Updated : 14 Aug, 2025**

---

## **1. Introduction**

### **Postfix Expression**

A *postfix* (Reverse Polish notation) expression places the operator **after** the operands.

**Form:**

```
operand1 operand2 operator
```

**Example:**

```
AB+ CD- *
```

Which corresponds to the infix:

```
(A + B) * (C - D)
```

---

### **Prefix Expression**

A *prefix* expression places the operator **before** the operands.

**Form:**

```
operator operand1 operand2
```

**Example:**

```
* + A B - C D
```

---

## **2. Problem Statement**

Given a **postfix** expression, convert it directly to a **prefix** expression.

We avoid unnecessary double conversion (Postfix → Infix → Prefix) and instead convert:

```
Postfix → Prefix
```

This is more efficient and works well since postfix is already stack-friendly.

---

## **3. Examples**

### **Example 1**

**Input:**

```
Postfix : AB+CD-*
```

**Output:**

```
Prefix : *+AB-CD
```

---

### **Example 2**

**Input:**

```
Postfix : ABC/-AK/L-*
```

**Output:**

```
Prefix : *-A/BC-/AKL
```

---

## **4. Algorithm: Postfix → Prefix**

We use a **stack** and scan the postfix expression from **left to right**:

1. **Read each symbol** from left to right.
2. **If operand →** push onto stack.
3. **If operator →**

    * Pop top two elements:

      ```
      op1 = top  
      op2 = next
      ```
    * Create prefix expression:

      ```
      newExp = operator + op2 + op1
      ```
    * Push the new expression back on the stack.
4. Continue until entire expression is processed.
5. Final stack element = result prefix expression.

---

## **5. Conversion Example (Step-by-Step)**

Let’s convert:

```
Postfix: AB+CD-*
```

| Symbol | Action                             | Stack     |
| ------ | ---------------------------------- | --------- |
| A      | Operand → push                     | A         |
| B      | Operand → push                     | A, B      |
| +      | Operator → pop B, A → prefix `+AB` | +AB       |
| C      | Operand → push                     | +AB, C    |
| D      | Operand → push                     | +AB, C, D |
| -      | Operator → prefix `-CD`            | +AB, -CD  |
| *      | Operator → prefix `*+AB-CD`        | *+AB-CD   |

Final Answer:

```
*+AB-CD
```

---

# **6. Java Implementation**

```java
// Java Program to convert postfix to prefix
import java.util.*;

class GFG {

    // function to check if character is an operator
    static boolean isOperator(char x) {
        switch (x) {
            case '+':
            case '-':
            case '/':
            case '*':
                return true;
        }
        return false;
    }

    // Convert Postfix to Prefix expression
    static String postToPre(String post_exp) {

        Stack<String> s = new Stack<>();

        // length of postfix expression
        int length = post_exp.length();

        // read from left to right
        for (int i = 0; i < length; i++) {

            char c = post_exp.charAt(i);

            if (isOperator(c)) {

                // pop two operands
                String op1 = s.pop();
                String op2 = s.pop();

                // build prefix string
                String temp = c + op2 + op1;

                // push back to stack
                s.push(temp);
            }
            else {
                // operand → push to stack
                s.push(c + "");
            }
        }

        // final string in stack is the result
        return s.pop();
    }

    // Driver Code
    public static void main(String args[]) {

        String post_exp = "ABC/-AK/L-*";

        System.out.println("Prefix : " + postToPre(post_exp));
    }
}
```

---

## **7. Output**

```
Prefix : *-A/BC-/AKL
```

---

## **8. Complexity Analysis**

| Metric              | Complexity               |
| ------------------- | ------------------------ |
| **Time Complexity** | **O(N)** — scanning once |
| **Auxiliary Space** | **O(N)** — stack usage   |

---

# **9. Summary**

✔ Postfix to Prefix conversion is efficient using a stack
✔ Process input left → right
✔ Operands → push
✔ Operators → pop 2 operands → build prefix → push
✔ Final stack entry is the answer

---

