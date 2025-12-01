
---

# 📘 **Lesson Notes: Postfix to Infix Conversion**

**Last Updated:** 27 Feb, 2025

---

## **1. Introduction**

Postfix (Reverse Polish) notation places **operators after operands**, e.g.,

```
a b op
```

Infix places operators **between operands**, e.g.,

```
a op b
```

Computers often evaluate postfix expressions directly, but humans find infix easier to read. Therefore, converting **Postfix → Infix** is a useful technique in expression parsing, compilers, and calculators.

---

## **2. Definitions**

### **Infix Expression**

Operator appears **between** operands.
Example:

```
a + b
```

### **Postfix Expression**

Operator appears **after** operands.
Example:

```
ab+
```

---

## **3. Examples**

### **Example 1**

**Input:**

```
abc++
```

**Output:**

```
(a + (b + c))
```

### **Example 2**

**Input:**

```
ab*c+
```

**Output:**

```
((a * b) + c)
```

### **Example 3**

**Input:**

```
abc+*d/
```

**Output:**

```
(((a * (b + c))) / d)
```

---

# **4. Approach: Using a Stack (O(n) Time, O(n) Space)**

We use a stack to build the infix expression step by step.

### **Algorithm**

1. **Initialize an empty stack.**
2. **Scan the postfix expression from left to right.**
3. If the current symbol is an **operand**, push it as a string.
4. If the symbol is an **operator**:

    * Pop the top two operands → `op1` and then `op2`
    * Form new infix expression:

      ```
      (op2 operator op1)
      ```
    * Push this new string back to the stack
5. When the expression ends, the stack contains **one final infix expression**.

---

# **5. Illustration Example**

Convert:

```
ab*c+
```

| Symbol | Action                             | Stack     |
| ------ | ---------------------------------- | --------- |
| a      | operand → push                     | a         |
| b      | operand → push                     | a, b      |
| *      | operator → pop b,a → (a*b)         | (a*b)     |
| c      | operand → push                     | (a*b), c  |
| +      | operator → pop c,(a*b) → ((a*b)+c) | ((a*b)+c) |

Final Answer:

```
((a*b)+c)
```

---

# **6. Java Implementation**

```java
import java.util.*;

class GFG {

    // Check if the character is an operand
    static boolean isOperand(char x) {
        return (x >= 'a' && x <= 'z') ||
               (x >= 'A' && x <= 'Z');
    }

    // Convert Postfix to Infix
    static String getInfix(String exp) {
        Stack<String> s = new Stack<>();

        for (int i = 0; i < exp.length(); i++) {

            char c = exp.charAt(i);

            // If operand, push as a string
            if (isOperand(c)) {
                s.push(c + "");
            }
            else {
                // Pop top two operands
                String op1 = s.pop();
                String op2 = s.pop();

                // Form (op2 operator op1)
                String temp = "(" + op2 + c + op1 + ")";

                // Push back to stack
                s.push(temp);
            }
        }

        // Final value in stack is the infix expression
        return s.peek();
    }

    // Driver Code
    public static void main(String args[]) {
        String exp = "ab*c+";
        System.out.println(getInfix(exp));  // Output: ((a*b)+c)
    }
}
```

---

# **7. Output**

```
((a*b)+c)
```

---

# **8. Complexity Analysis**

| Metric               | Value    | Explanation                     |
| -------------------- | -------- | ------------------------------- |
| **Time Complexity**  | **O(n)** | Each character processed once   |
| **Space Complexity** | **O(n)** | Stack may hold up to n elements |

---
