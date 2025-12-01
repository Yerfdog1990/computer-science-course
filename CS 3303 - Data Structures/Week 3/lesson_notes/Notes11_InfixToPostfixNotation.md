
---

# 🧠 **Lesson Notes: Infix to Postfix Expression**

### 📌 **Definition**

An **infix expression** is written in the form:

```
operand1 operator operand2
```

Example:

```
a + b * c
```

A **postfix (Reverse Polish notation)** expression is written as:

```
operand1 operand2 operator
```

Example:

```
abc*+
```

---

## 🎯 **Goal**

Convert a given infix expression into its postfix (RPN) equivalent **while respecting operator precedence and associativity**.

---

# 🔥 **Operator Precedence & Associativity**

| Operator | Precedence  | Associativity |
| -------- | ----------- | ------------- |
| `^`      | Highest (3) | Right → Left  |
| `* /`    | Medium (2)  | Left → Right  |
| `+ -`    | Lowest (1)  | Left → Right  |

---

# 🧩 **Example Walkthrough**

### Example 1

**Input:**

```
a*(b+c)/d
```

**Steps:**

* Convert `(b+c)` → `bc+`
* Multiply `a` with result → `abc+*`
* Divide by `d` → `abc+*d/`

**Output:**

```
abc+*d/
```

---

### Example 2

**Input:**

```
a+b*c+d
```

**Steps:**

* `b*c` → `bc*`
* Add `a` → `abc*+`
* Add `d` → `abc*+d+`

**Output:**

```
abc*+d+
```

---

# 🧱 **Approach: Using Stack — O(n) Time, O(n) Space**

We scan the expression **from left to right**.

### ✔️ **Rules Used**

1. **If the character is an operand → add to the result.**
2. **If '(' → push to stack.**
3. **If ')' → pop until '(' is found.**
4. **If operator →**

    * Pop from stack while:

      ```
      top has higher precedence
      OR same precedence AND operator is left-associative
      ```
    * Then push current operator.
5. **Pop remaining operators.**

This ensures correctness based on precedence and associativity.

---

# 🧮 **Java Code: Infix to Postfix Conversion**

```java
import java.util.Stack;

public class GfG {

    // Function to return precedence of operators
    static int prec(char c) {
        if (c == '^')
            return 3;
        else if (c == '/' || c == '*')
            return 2;
        else if (c == '+' || c == '-')
            return 1;
        else
            return -1;
    }

    // Check if operator is right-associative
    // (^ is right associative)
    static boolean isRightAssociative(char c) {
        return c == '^';
    }

    public static String infixToPostfix(String s) {
        Stack<Character> st = new Stack<>();
        StringBuilder res = new StringBuilder();

        for (int i = 0; i < s.length(); i++) {
            char c = s.charAt(i);

            // If operand → add to result
            if (Character.isLetterOrDigit(c)) {
                res.append(c);
            }
            // If '(' → push
            else if (c == '(') {
                st.push('(');
            }
            // If ')' → pop until '(' is found
            else if (c == ')') {
                while (!st.isEmpty() && st.peek() != '(') {
                    res.append(st.pop());
                }
                st.pop(); // pop '('
            }
            // If operator
            else {
                while (!st.isEmpty() && st.peek() != '(' &&
                        (prec(st.peek()) > prec(c) ||
                        (prec(st.peek()) == prec(c) && !isRightAssociative(c)))) {
                    res.append(st.pop());
                }
                st.push(c);
            }
        }

        // Pop remaining operators
        while (!st.isEmpty()) {
            res.append(st.pop());
        }

        return res.toString();
    }

    public static void main(String[] args) {
        String exp = "a*(b+c)/d";
        System.out.println(infixToPostfix(exp));  // Output: abc+*d/
    }
}
```

---

# 🧪 **Output**

```
abc+*d/
```

---

# 📝 **Summary Table (Copy-Friendly)**

```markdown
| Character Type | Action |
|----------------|--------|
| Operand        | Append to result |
| '('            | Push to stack |
| ')'            | Pop until '(' |
| Operator       | Pop while top has higher or equal precedence (except right-associative '^') |
| End of Input   | Pop all operators |
```

---

# 💡 Memory Trick

### **“OL-POP-OPOP” Rule**

**O** → Operand → **L**and in result
**P** → '(' → **O**pen and push
**P** → ')' → **P**op until '('
**O** → Operator → **P**op based on precedence

Easy to recall:
👉 **Operands → output**
👉 **Parentheses control popping**
👉 **Operators compare before pushing**

---

