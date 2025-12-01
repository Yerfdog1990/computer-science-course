
---

# # 📘 Infix, Prefix, and Postfix Expressions/Notations

**Last Updated: 27 Aug, 2025**

Mathematical expressions often involve complex operations where **order matters**. To ensure expressions can be interpreted correctly by humans and computers, we use three primary notations:

* **Infix notation**
* **Prefix notation (Polish)**
* **Postfix notation (Reverse Polish)**

Each notation has unique advantages and disadvantages. This lesson explores them in detail.

---

# # 📑 Table of Contents

1. [Infix Expressions](#infix-expressions)
2. [Advantages of Infix Expressions](#advantages-of-infix-expressions)
3. [Disadvantages of Infix Expressions](#disadvantages-of-infix-expressions)
4. [Prefix Expressions (Polish Notation)](#prefix-expressions)
5. [Advantages of Prefix Expressions](#advantages-of-prefix-expressions)
6. [Disadvantages of Prefix Expressions](#disadvantages-of-prefix-expressions)
7. [Postfix Expressions (Reverse Polish)](#postfix-expressions)
8. [Advantages of Postfix Expressions](#advantages-of-postfix-notation)
9. [Disadvantages of Postfix Expressions](#disadvantages-of-postfix-expressions)
10. [Comparison Table](#comparison-of-infix-prefix-and-postfix-expressions)
11. [FAQs](#frequently-asked-questions)

---

# # 🔹 Infix Expressions

In **infix notation**, operators are written **between operands**.

Example:

```
2 + 3
A + B * C
(A + B) * C
```

Humans naturally use infix notation, but computers struggle because:

* Operator precedence must be respected
* Parentheses modify evaluation order
* Parsing is more complex

---

## ✔ Common Characteristics of Infix Notation

* Operators appear **between operands**
* Parentheses may indicate **explicit evaluation order**
* Operator precedence determines order when parentheses are absent

Example with parentheses:

```
(2 + 3) * 4
```

Example relying on precedence:

```
2 + 3 * 4   → interpreted as 2 + (3 * 4)
```

---

## ✔ Operator Precedence Table

```
| Operator        | Precedence |
|-----------------|------------|
| Parentheses ()  | Highest    |
| Exponents ^     | High       |
| Multiplication *| Medium     |
| Division /      | Medium     |
| Addition +      | Low        |
| Subtraction -   | Low        |
```

---

## ✔ Evaluating Infix Expressions

Evaluating infix directly is difficult for computers. Typically:

1. Convert infix → **postfix**
2. Evaluate the postfix expression using a stack

---

# # ⭐ Advantages of Infix Expressions

* Natural and human-readable
* Supported by most programming languages
* Easy for people to understand

---

# # ⚠ Disadvantages of Infix Expressions

* Requires parentheses to remove ambiguity
* Harder for computers to parse
* Must track operator precedence and associativity

---

# # 🔹 Prefix Expressions (Polish Notation)

In **prefix notation**, the operator appears **before operands**.

Example:

```
+ A B
* + A B C
```

This eliminates ambiguity because order is implied by the operator’s position.

Example:

```
Infix:  A + B
Prefix: + A B
```

---

## ✔ Evaluating Prefix Expressions

Prefix expressions are evaluated from **right to left**, using a stack.

---

# # ⭐ Advantages of Prefix Expressions

* No parentheses required
* Easy for computers to parse
* Efficient for expressions with many nested operations

---

# # ⚠ Disadvantages of Prefix Expressions

* Harder for humans to read
* Not commonly used in everyday notation

---

# # 🔹 Postfix Expressions (Reverse Polish Notation)

In **postfix notation**, the operator appears **after operands**.

Example:

```
A B +
A B C * +
```

Postfix is very popular in compilers and calculators.

Example:

```
Infix:  5 + 2
Postfix: 5 2 +
```

---

## ✔ Evaluating Postfix Expressions

Evaluation is done from **left to right** using a stack:

1. Push operands
2. When an operator appears → pop, compute, push result

---

# # ⭐ Advantages of Postfix Notation

* No parentheses needed
* Evaluation is simple and stack-friendly
* More readable than prefix in many cases

---

# # ⚠ Disadvantages of Postfix Expressions

* Requires stack-based algorithm
* Less common in human writing

---

# # 📊 Comparison of Infix, Prefix, and Postfix Expressions

```
| Aspect                       | Infix Notation            | Prefix Notation            | Postfix Notation           |
|------------------------------|---------------------------|----------------------------|----------------------------|
| Readability                  | Very readable             | Less readable              | Less readable              |
| Operator Placement           | Between operands          | Before operands            | After operands             |
| Parentheses Requirement      | Often required            | Not required               | Not required               |
| Precedence Tracking          | Required                  | Not required               | Not required               |
| Evaluation Direction         | Left-to-right             | Right-to-left              | Left-to-right              |
| Ambiguity                    | Possible                  | None                       | None                       |
| Unary Operator Handling      | Tricky                    | Simplified                 | Simplified                 |
| Computer Efficiency          | Lower                     | Higher                     | Higher                     |
| Usage                        | Everyday math             | Compilers, algorithms      | Compilers, calculators     |
```

---

# # 📚 Deep Explanation with Examples

## ✔ Why Infix Can Be Ambiguous

Expression:

```
A + B * C
```

Which operator applies first?

Because:

* `*` has higher precedence than `+`

Correct interpretation:

```
A + (B * C)
```

Parentheses show evaluation order explicitly.

---

## ✔ Fully Parenthesized Expressions

To remove ambiguity:

```
A + B * C + D
```

Becomes:

```
((A + (B * C)) + D)
```

---

## ✔ Introducing Prefix and Postfix

Take:

```
A + B
```

Move the operator before operands:

```
+ A B   (prefix)
```

Move it after operands:

```
A B +   (postfix)
```

---

# # 📌 Table 2: Basic Examples of Infix, Prefix, and Postfix

```
| Infix        | Prefix     | Postfix     |
|--------------|------------|-------------|
| A + B        | + A B      | A B +       |
| A + B * C    | + A * B C  | A B C * +   |
```

---

# # 📌 Table 3: Expressions With Parentheses

```
| Infix          | Prefix       | Postfix        |
|----------------|--------------|----------------|
| (A + B) * C    | * + A B C    | A B + C *      |
```

Note: Prefix and postfix **do not require parentheses**.

---

# # 📌 Table 4: Additional Examples

```
| Infix                  | Prefix              | Postfix              |
|------------------------|---------------------|-----------------------|
| A + B * C + D          | + + A * B C D       | A B C * + D +         |
| (A + B) * (C + D)      | * + A B + C D       | A B + C D + *         |
| A * B + C * D          | + * A B * C D       | A B * C D * +         |
| A + B + C + D          | + + + A B C D       | A B + C + D +         |
```

---

# # ❓ Frequently Asked Questions

### **1. Which notation is best for computers?**

**Prefix and postfix** — because no precedence tracking is needed.

---

### **2. Why do compilers use postfix?**

Postfix is extremely easy to evaluate using a stack.

---

### **3. Why do humans use infix?**

It matches natural language and mathematical writing conventions.

---

### **4. Can every infix expression be converted to prefix and postfix?**

Yes — and without ambiguity.

---

### **5. Why do prefix/postfix not need parentheses?**

Operator placement **already defines** the order of operations.

---
