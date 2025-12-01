
---

# 🧠 **Lesson Notes: Prefix to Infix Conversion**

## 📌 1. Introduction

### **Infix Expression**

* Operators appear **between** operands.
* Format:

  ```
  operand1 operator operand2
  ```
* Example:

  ```
  (A + B) * (C - D)
  ```

### **Prefix Expression**

* Operators appear **before** operands.
* Format:

  ```
  operator operand1 operand2
  ```
* Example:

  ```
  *+AB-CD
  ```
* This is equivalent to:

  ```
  (A + B) * (C - D)
  ```

---

# 🎯 **Why Convert Prefix → Infix?**

* Computers evaluate prefix or postfix notations internally.
* However, humans understand infix expressions more naturally.
* Converting helps in:

    * interpreting expressions
    * debugging
    * compiler design
    * expression tree construction

---

# 🧪 **Examples**

### **Example 1**

Prefix:

```
*+AB-CD
```

Conversion:

```
((A + B) * (C - D))
```

---

### **Example 2**

Prefix:

```
*-A/BC-/AKL
```

Conversion:

```
((A - (B / C)) * ((A / K) - L))
```

---

# 🔥 **Algorithm (Prefix → Infix)**

This algorithm processes the prefix expression **from right to left**.

### Steps:

1. **Read the prefix expression in reverse (right → left)**
2. **If the current character is an operand:**

    * Push it to the stack.
3. **If the current character is an operator:**

    * Pop two operands from the stack:

        * operand1
        * operand2
    * Form the infix expression:

      ```
      (operand1 operator operand2)
      ```
    * Push this combined string back to the stack.
4. **Continue until the end of the expression.**
5. The final element in the stack is the **complete infix expression**.

---

# 🧱 **Stack Simulation Example**

Prefix:

```
*+AB-CD
```

| Step | Symbol | Stack content | Action       |
| ---- | ------ | ------------- | ------------ |
| 1    | D      | D             | Push         |
| 2    | C      | C, D          | Push         |
| 3    | -      | (C-D)         | Combine C−D  |
| 4    | B      | B, (C-D)      | Push         |
| 5    | A      | A, B, (C-D)   | Push         |
| 6    | +      | (A+B), (C-D)  | Combine A+B  |
| 7    | *      | ((A+B)*(C-D)) | Final result |

---

# 🧮 **Java Implementation: Prefix → Infix**

```java
// Java program to convert Prefix to Infix Expression
import java.util.Stack;

class GFG {

    // Function to check if a character is an operator
    static boolean isOperator(char x) {
        switch (x) {
            case '+':
            case '-':
            case '*':
            case '/':
            case '^':
            case '%':
                return true;
        }
        return false;
    }

    // Convert Prefix to Infix
    public static String convert(String str) {
        Stack<String> stack = new Stack<>();

        // Scan from right to left
        for (int i = str.length() - 1; i >= 0; i--) {
            char c = str.charAt(i);

            // If operator
            if (isOperator(c)) {
                String op1 = stack.pop();
                String op2 = stack.pop();

                // Combine as (operand1 operator operand2)
                String temp = "(" + op1 + c + op2 + ")";
                stack.push(temp);
            } 
            // If operand
            else {
                stack.push(c + "");   // convert char to String
            }
        }

        // Final result
        return stack.pop();
    }

    // Driver code
    public static void main(String[] args) {
        String exp = "*-A/BC-/AKL";
        System.out.println("Infix : " + convert(exp));
    }
}
```

---

# 🧾 **Output**

```
Infix : ((A-(B/C))*((A/K)-L))
```

---

# ⏱ **Complexity Analysis**

| Complexity Type      | Value                  |
| -------------------- | ---------------------- |
| **Time Complexity**  | **O(n)** — scans once  |
| **Space Complexity** | **O(n)** — stack usage |

---

# 📝 Summary (Copy-Friendly)

```markdown
Prefix to Infix Conversion Rules:

1. Read prefix from right to left.
2. If operand → push to stack.
3. If operator:
     pop operand1
     pop operand2
     combine = (operand1 operator operand2)
     push back to stack.
4. Final stack element is the complete infix expression.
```

---

