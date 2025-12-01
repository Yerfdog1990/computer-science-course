
---

# **📘 Lesson Notes: Prefix to Postfix Conversion**

**Last Updated: 14 Feb, 2025**

---

## **1. Introduction**

When working with expressions, computers typically evaluate using **postfix (Reverse Polish)** notation because it eliminates ambiguity and does not require parentheses.
For human readability, we often see **infix**, but internally, prefix and postfix are more efficient.

Here, we want to convert **directly from Prefix → Postfix** without going through infix.

---

## **2. Review: Prefix and Postfix Notations**

### **Prefix (Polish Notation)**

Operator comes **before** the operands.

**Form:**

```
operator operand1 operand2
```

**Example:**

```
*+AB-CD
```

which in infix is:

```
(A + B) * (C - D)
```

---

### **Postfix (Reverse Polish Notation)**

Operator comes **after** the operands.

**Form:**

```
operand1 operand2 operator
```

**Example:**

```
AB+CD-*
```

which represents:

```
(A + B) * (C - D)
```

---

# **3. Problem Statement**

Given a **prefix expression**, convert it to its equivalent **postfix expression**.

---

## **4. Why Convert Prefix → Postfix Directly?**

Avoids unnecessary intermediate steps:

❌ Prefix → Infix → Postfix (more expensive)
✔ Prefix → Postfix directly (efficient and stack-friendly)

Computers evaluate postfix, so this conversion improves clarity and performance in compiler design, interpreters, and expression evaluation systems.

---

# **5. Examples**

### **Example 1**

**Input:**

```
Prefix: *+AB-CD
```

**Output:**

```
Postfix: AB+CD-*
```

**Explanation:**
Prefix → Infix: `(A+B)*(C-D)`
Infix → Postfix: `AB+CD-*`

---

### **Example 2**

**Input:**

```
Prefix: *-A/BC-/AKL
```

**Output:**

```
Postfix: ABC/-AK/L-*`
```

**Explanation:**
Prefix → Infix: `(A-(B/C))*((A/K)-L)`
Infix → Postfix: `ABC/-AK/L-*`

---

# **6. Algorithm: Prefix → Postfix**

We use a **stack** and scan the prefix expression **from right to left**:

1. Read prefix expression from **right to left**.
2. If the symbol is an **operand**, push it onto the stack.
3. If the symbol is an **operator**:

    * Pop the **first** operand → `op1`
    * Pop the **second** operand → `op2`
    * Form new postfix:

      ```
      newExp = op1 + op2 + operator
      ```
    * Push newExp back onto the stack.
4. Continue until end of expression.
5. Stack now contains exactly **one element** → final postfix expression.

---

# **7. Step-by-Step Example**

Convert:

```
Prefix: *+AB-CD
```

Scanning right-to-left:

| Symbol | Action                           | Stack     |
| ------ | -------------------------------- | --------- |
| D      | Operand → push                   | D         |
| C      | Operand → push                   | D, C      |
| -      | Operator → pop C, D → form `CD-` | CD-       |
| B      | Operand → push                   | CD-, B    |
| A      | Operand → push                   | CD-, B, A |
| +      | Operator → form `AB+`            | CD-, AB+  |
| *      | Operator → form `AB+CD-*`        | AB+CD-*   |

Final Answer:

```
AB+CD-*
```

---

# **8. Java Implementation**

```java
// Java Program to convert prefix to postfix
import java.util.*;

class GFG {

    // function to check if character is operator
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

    // Convert prefix to postfix expression
    static String preToPost(String pre_exp) {

        Stack<String> s = new Stack<>();

        int length = pre_exp.length();

        // scan prefix from right to left
        for (int i = length - 1; i >= 0; i--) {

            char c = pre_exp.charAt(i);

            // if operator
            if (isOperator(c)) {

                // pop two operands
                String op1 = s.pop();
                String op2 = s.pop();

                // create postfix: operand1 operand2 operator
                String temp = op1 + op2 + c;

                // push result back
                s.push(temp);
            }
            else {
                // operand → push as string
                s.push(c + "");
            }
        }

        // final result
        return s.peek();
    }

    // Driver Code
    public static void main(String args[]) {

        String pre_exp = "*-A/BC-/AKL";

        System.out.println("Postfix : " + preToPost(pre_exp));
    }
}
```

---

# **9. Output**

```
Postfix : ABC/-AK/L-*
```

---

# **10. Complexity Analysis**

| Metric               | Value    | Notes                             |
| -------------------- | -------- | --------------------------------- |
| **Time Complexity**  | **O(N)** | Each character is processed once  |
| **Space Complexity** | **O(N)** | Stack stores intermediate strings |

---

# **11. Summary**

✔ Use a stack
✔ Scan prefix from **right to left**
✔ Operands → push
✔ Operators → pop 2, form postfix, push
✔ End result is the final postfix expression

---

