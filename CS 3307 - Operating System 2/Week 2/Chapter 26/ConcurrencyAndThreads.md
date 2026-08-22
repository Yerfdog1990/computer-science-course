# Concurrency and Threads 

*(Based on OSTEP Chapter 26: "Concurrency: An Introduction")*

---

## Table of Contents

1. Background & Motivation
2. The Thread Abstraction
3. Thread State vs. Process State
4. Threads and the Address Space (The Stack Problem)
5. Why Use Threads? (Two Major Reasons)
6. Threads vs. Multiple Processes
7. Example: Basic Thread Creation
8. Why It Gets Worse: Shared Data
9. The Heart of the Problem: Uncontrolled Scheduling
10. Detailed Trace of the Race Condition
11. Key Concurrency Terms (Critical Section, Race Condition, Indeterminate, Mutual Exclusion)
12. The Wish for Atomicity
13. One More Problem: Waiting For Another
14. Why Study This in an OS Class?
15. Glossary of Key Terms
16. Summary Table
17. Worked Homework Questions & Solutions (Conceptual)
18. Annotated Reference List
19. Big-Picture Takeaways

---

## 1. Background & Motivation

- So far, the OS has given us two major abstractions:
    1. **The process** — turns a single physical CPU into the illusion of many virtual CPUs (via time-sharing / scheduling).
    2. **The address space** — turns physical memory (and disk) into the illusion of a large, private virtual memory for each process.
- This chapter introduces a **third major abstraction**, but this time it operates **within** a single running process: the **thread**.
- **Key conceptual shift:** Instead of a process having a **single point of execution** (one Program Counter, one register set), a **multi-threaded** process has **multiple points of execution** — multiple PCs, each being fetched from and executed independently.
- **Intuition:** A thread is a lot like a separate process — **except** that threads of the same process **share the same address space**, and therefore can access the same data directly.

---

## 2. The Thread Abstraction

- A thread is essentially "a separate process, minus its own private address space."
- Multiple threads belonging to one process **run concurrently**, and (on a multiprocessor) potentially **simultaneously** — truly at the same time on different cores.
- This is the foundation for the whole "concurrency" section of the OS course: once you have multiple independent streams of execution that can share data, you open the door to a whole new category of bugs and challenges that don't exist in single-threaded programs.

---

## 3. Thread State vs. Process State

| Aspect | Process | Thread |
|---|---|---|
| **Program Counter (PC)** | Has one | Has its own (tracks where *this* thread is fetching instructions) |
| **Registers** | Has its own set | Has its own **private** set |
| **Address space** | Has its own, private | **Shared** with all other threads in the same process |
| **Control block** | Stored in a **Process Control Block (PCB)** | Stored in a **Thread Control Block (TCB)** |
| **Context switch cost** | Must switch address space (e.g., page table) | **No need to switch address space** — cheaper in that respect |

### 3.1 Context Switching Between Threads
- Just like switching between processes, switching between two threads (T1 and T2) requires saving T1's register state and restoring T2's register state — this is a genuine **context switch**.
- **Crucial difference from process context switches:** the **address space remains the same**. There is no need to switch which page table is active, because both threads belong to the same process and thus share memory. This makes thread context switches **cheaper** than process context switches in that specific respect.

---

## 4. Threads and the Address Space (The Stack Problem)

### 4.1 Single-Threaded Address Space (Classic Process)
In a traditional single-threaded process, the address space is laid out simply:
```
16KB  ┌───────────────┐
      │     Stack     │   ← grows negatively (downward)
      │       ↓       │
15KB  │   (free space)│
      │       ↑       │
 2KB  │      Heap     │   ← grows positively (upward)
 1KB  │  Program Code │   ← where instructions live
 0KB  └───────────────┘
```
- **Program Code**: where instructions live (fixed, doesn't grow).
- **Heap**: contains dynamically allocated data (e.g., via `malloc`); grows **positively** (upward).
- **Stack**: contains local variables, routine arguments, return values, etc.; grows **negatively** (downward, toward the heap).
- Trouble only arises when the heap and stack **collide** — i.e., you run out of free space in between.

### 4.2 Multi-Threaded Address Space
- In a multi-threaded process, **each thread needs its own stack**, since each thread independently calls into routines, has its own local variables, arguments, and return addresses.
- Instead of **one** stack at the bottom of the address space, there are now **multiple stacks spread throughout** the address space — one per thread:
```
16KB  ┌───────────────┐
      │   Stack (2)   │
      │  (free space) │
      │   Stack (1)   │
      │  (free space) │
 2KB  │      Heap     │
 1KB  │  Program Code │
 0KB  └───────────────┘
```
- Any stack-allocated variable (locals, arguments, return values) is placed in what's called **thread-local storage** — i.e., specifically in *that* thread's own stack region.

### 4.3 The Downside: A Less Elegant Layout
- This "ruins" the beautifully simple layout of the single-threaded address space.
- Previously, the heap and stack could each grow independently, and the only worry was running out of total room.
- With multiple stacks interspersed in the address space, this clean separation is lost.
- **Fortunately**, this usually isn't a huge practical problem, since **stacks typically don't need to be very large** — the main exception being programs that rely heavily on **recursion**, which can consume stack space quickly.

---

## 5. Why Use Threads? (Two Major Reasons)

### 5.1 Reason 1 — Parallelism

- **Scenario:** You're writing a program that performs operations on very large arrays — e.g., adding two large arrays together, or incrementing every element by some amount.
- **Single processor:** the task is straightforward — just do each operation sequentially.
- **Multiple processors:** You can potentially **speed things up considerably** by splitting the work across processors, each handling a portion of the array.
- The task of transforming a single-threaded program into one that exploits multiple CPUs is called **parallelization**.
- **Using one thread per CPU** is a natural, typical strategy to make programs run faster on modern (multi-core) hardware.

### 5.2 Reason 2 — Avoiding Blocking Due to Slow I/O

- **Scenario:** A program performs I/O of various kinds: waiting to send/receive a network message, waiting for a disk I/O to complete, or even (implicitly) waiting for a **page fault** to be resolved.
- Rather than the *entire program* sitting idle while waiting, it may be preferable to **do something else** in the meantime — e.g., perform other computation, or issue additional I/O requests.
- **Threads let you avoid getting stuck:** while one thread blocks waiting for I/O, the CPU scheduler can switch to another **ready** thread within the *same* program and make progress there instead.
- This is directly analogous to how **multiprogramming** allows the OS to overlap I/O and computation **across different programs** — except here, it's happening **within a single program**, via threads.
- **Real-world relevance:** Many modern **server applications** — web servers, database management systems, etc. — make heavy use of threads for exactly this reason: to overlap I/O waits with useful computation, maximizing throughput and responsiveness.

---

## 6. Threads vs. Multiple Processes

- In both scenarios above (parallelism, and overlapping I/O), you *could* technically use **multiple processes** instead of threads.
- **But threads have a key advantage:** they **share an address space**, making it easy to **share data** between them directly (no need for explicit inter-process communication mechanisms like pipes or shared memory segments).
- **Rule of thumb:**
    - **Threads** → natural choice when tasks need to **share data structures** heavily and operate on **common state**.
    - **Processes** → more appropriate for **logically separate tasks** where **little to no sharing** of in-memory data structures is needed (e.g., running unrelated applications).

---

## 7. Example: Basic Thread Creation

### 7.1 The Code

The example program creates two threads that each print a letter ("A" or "B"):

```c
#include <stdio.h>
#include <assert.h>
#include <pthread.h>
#include "common.h"
#include "common_threads.h"

void *mythread(void *arg) {
    printf("%s\n", (char *) arg);
    return NULL;
}

int main(int argc, char *argv[]) {
    pthread_t p1, p2;
    int rc;
    printf("main: begin\n");
    Pthread_create(&p1, NULL, mythread, "A");
    Pthread_create(&p2, NULL, mythread, "B");
    // join waits for the threads to finish
    Pthread_join(p1, NULL);
    Pthread_join(p2, NULL);
    printf("main: end\n");
    return 0;
}
```

### 7.2 What's Happening
- **`Pthread_create()`** spawns a new thread that runs the function `mythread()`, passing it a different argument ("A" or "B") for each thread.
- Once created, a thread **may start running immediately**, or it may sit in a **"ready" but not "running"** state — this depends entirely on the **whims of the OS scheduler**.
- On a **multiprocessor**, the two threads could even genuinely run **at the exact same time**, on different cores.
- **`Pthread_join()`** blocks the calling thread (here, `main`) until the specified thread finishes. By calling `join` twice (once for each of T1 and T2), `main` ensures **both threads complete** before it prints `"main: end"` and exits.
- **Three threads total** are involved in this simple program: the **main thread**, **Thread 1 (T1)**, and **Thread 2 (T2)**.

### 7.3 Multiple Possible Orderings — The Crux of Concurrency

Because the scheduler determines when each thread actually runs, there is **no single guaranteed execution order**. Several different valid orderings are possible for the *exact same code*:

**Trace 1 — Threads run only after being explicitly created and both joined in sequence:**
```
main                    Thread 1        Thread 2
starts running
prints "main: begin"
creates Thread 1
creates Thread 2
waits for T1
                        runs
                        prints "A"
                        returns
waits for T2
                                        runs
                                        prints "B"
                                        returns
prints "main: end"
```

**Trace 2 — Each thread runs immediately upon creation, before the next create() call:**
```
main                    Thread 1        Thread 2
starts running
prints "main: begin"
creates Thread 1
                        runs
                        prints "A"
                        returns
creates Thread 2
                                        runs
                                        prints "B"
                                        returns
waits for T1
returns immediately; T1 is done
waits for T2
returns immediately; T2 is done
prints "main: end"
```

**Trace 3 — Thread 2 happens to run before Thread 1, even though created after it:**
```
main                    Thread 1        Thread 2
starts running
prints "main: begin"
creates Thread 1
creates Thread 2
                                        runs
                                        prints "B"
                                        returns
waits for T1
                        runs
                        prints "A"
                        returns
waits for T2
returns immediately; T2 is done
prints "main: end"
```

### 7.4 Key Insight
- There is **no guarantee** that a thread created first will run first — "B" could print before "A" even though Thread 1 (which prints "A") was created before Thread 2.
- **Thread creation is a bit like a function call**, but instead of executing the function and *then* returning to the caller, the system creates a **new, independent thread of execution** for the called routine. It may run before the `create()` call even returns, or much later — entirely up to the scheduler.
- **Takeaway:** Threads make program behavior **harder to predict**. It's already difficult to reason about what runs when in a single-threaded system; concurrency makes this "simply... much worse."

---

## 8. Why It Gets Worse: Shared Data

The thread-creation example above didn't show any **data sharing** between threads. Now we examine what happens when threads **update a shared variable**.

### 8.1 The Code

```c
#include <stdio.h>
#include <pthread.h>
#include "common.h"
#include "common_threads.h"

static volatile int counter = 0;

// mythread()
//
// Simply adds 1 to counter repeatedly, in a loop
// No, this is not how you would add 10,000,000 to
// a counter, but it shows the problem nicely.
//
void *mythread(void *arg) {
    printf("%s: begin\n", (char *) arg);
    int i;
    for (i = 0; i < 1e7; i++) {
        counter = counter + 1;
    }
    printf("%s: done\n", (char *) arg);
    return NULL;
}

int main(int argc, char *argv[]) {
    pthread_t p1, p2;
    printf("main: begin (counter = %d)\n", counter);
    Pthread_create(&p1, NULL, mythread, "A");
    Pthread_create(&p2, NULL, mythread, "B");

    // join waits for the threads to finish
    Pthread_join(p1, NULL);
    Pthread_join(p2, NULL);
    printf("main: done with both (counter = %d)\n", counter);
    return 0;
}
```

### 8.2 Implementation Notes
- Following Stevens' convention [SR05], the wrapper functions `Pthread_create()` and `Pthread_join()` simply **check the return code and exit on failure** — a minimal but useful way to catch errors in simple programs.
- Both threads run the **same function body** (`mythread`), differentiated only by the string argument passed in ("A" or "B") — a clean way to reuse code for multiple, near-identical worker threads.
- **The core task:** each thread adds 1 to the shared global variable `counter`, in a loop, **10 million (1e7) times**. Since two threads each do this, the **expected final result is 20,000,000**.

### 8.3 The Surprising (Buggy) Behavior

Sometimes, running the program gives the **expected, correct result**:
```
prompt> gcc -o main main.c -Wall -pthread; ./main
main: begin (counter = 0)
A: begin
B: begin
A: done
B: done
main: done with both (counter = 20000000)
```

But **sometimes** — even on a **single processor** — it doesn't:
```
prompt> ./main
main: begin (counter = 0)
A: begin
B: begin
A: done
B: done
main: done with both (counter = 19345221)
```

And running it **again** gives yet **another different (wrong) result**:
```
prompt> ./main
main: begin (counter = 0)
A: begin
B: begin
A: done
B: done
main: done with both (counter = 19221041)
```

- **Not only is the result wrong, it's a *different* wrong result each time** — a striking violation of the deterministic behavior we normally expect from computers.

> **Tip — Know and Use Your Tools:** The book recommends learning tools like **`objdump`** (a disassembler, showing the assembly instructions that make up a compiled program), along with debuggers (**`gdb`**) and memory profilers (**`valgrind`**, **`purify`**). Understanding what your code *actually* compiles down to is essential for debugging concurrency bugs like this one.

---

## 9. The Heart of the Problem: Uncontrolled Scheduling

### 9.1 What the Compiler Actually Generates

The single C statement:
```c
counter = counter + 1;
```
compiles down (in x86 assembly) to **three separate instructions**:
```asm
mov 0x8049a1c, %eax   ; load counter's value from memory into register eax
add $0x1, %eax        ; add 1 to the register
mov %eax, 0x8049a1c   ; store the updated register value back into memory
```
(Here, `counter` is assumed to live at memory address `0x8049a1c`.)

- **Crucially, this is NOT a single atomic operation** — it's three distinct steps, and a **context switch (interrupt) can occur between any of them**.

### 9.2 Narrative Walkthrough of the Bug

1. **Thread 1** enters this code region to increment `counter` (currently, say, = 50).
    - Loads `counter` into its own `eax` register → `T1's eax = 50`.
    - Adds 1 → `T1's eax = 51`.
2. **A timer interrupt occurs** right at this point. The OS saves T1's full state (PC, registers including `eax=51`) into T1's **TCB**.
3. **Thread 2** is scheduled to run next, and it **also** enters this same code region.
    - It loads `counter` (still 50, since T1 hasn't written back yet!) into **its own, separate** `eax` register → `T2's eax = 50`.
    - Adds 1 → `T2's eax = 51`.
    - Stores `eax` back to memory → `counter` is now set to **51**.
4. **Another context switch occurs**, and **Thread 1 resumes** exactly where it left off — about to execute its final `mov` instruction, with its own saved `eax = 51` restored.
    - It stores `eax` (= 51) back into `counter` → `counter` is set to **51** — again!

**Net result:** the increment code ran **twice**, but `counter` only increased **by 1** (from 50 to 51), instead of the expected +2 (to 52). One entire increment was **silently lost**.

### 9.3 Why Each Thread's Register Values Are Independent
- Each thread, while running, has its **own private set of registers** — the registers are **virtualized** by the context-switch code, which saves and restores them separately for each thread.
- This is exactly *why* Thread 2 can independently load `counter=50` into its own `eax`, completely unaware that Thread 1 already has a "stale" copy of the same value sitting in *its* saved `eax`.

---

## 10. Detailed Trace of the Race Condition

Assume the three instructions are loaded starting at memory address **100**:
```
100  mov 0x8049a1c, %eax
105  add $0x1, %eax
108  mov %eax, 0x8049a1c
```
*(Note: x86 has variable-length instructions — the `mov` here is 5 bytes, the `add` is only 3 bytes, hence addresses 100 → 105 → 108.)*

Assume `counter` starts at **50**. Here is the exact interleaved trace:

| OS Action | Thread 1 | Thread 2 | PC (after instr.) | eax | counter |
|---|---|---|---|---|---|
| *(before critical section)* | | | 100 | 0 | 50 |
| | `mov 8049a1c,%eax` | | 105 | 50 | 50 |
| | `add $0x1,%eax` | | 108 | 51 | 50 |
| **interrupt**, save T1 | | | | | |
| restore T2 | | *(resumes at 100)* | 100 | 0 | 50 |
| | | `mov 8049a1c,%eax` | 105 | 50 | 50 |
| | | `add $0x1,%eax` | 108 | 51 | 50 |
| | | `mov %eax,8049a1c` | 113 | 51 | **51** |
| **interrupt**, save T2 | | | | | |
| restore T1 | *(resumes at 108)* | | 108 | 51 | 51 |
| | `mov %eax,8049a1c` | | 113 | 51 | **51** |

- **Final value of `counter` = 51**, instead of the correct **52**.
- This precise **timing-dependent** outcome — where the result depends on exactly *when* the context switches happen to occur — is the essence of the bug.

### 10.1 Naming the Phenomenon
- This is called a **race condition** (or, more specifically, a **data race**): the result **depends on the timing** of the threads' execution.
- With unlucky timing (context switches occurring at inconvenient points), we get an **incorrect** result — and because the exact timing varies from run to run, we may get a **different incorrect result** each time.
- We call such a program's behavior **indeterminate** — the opposite of the deterministic behavior we normally expect from computers.
- The specific piece of code that accesses the shared variable and **must not be executed concurrently by more than one thread** is called a **critical section**.
- What we actually want is **mutual exclusion**: a guarantee that **if one thread is executing within the critical section, all others are prevented from doing so** at the same time.

### 10.2 Historical Note
- Virtually all of these terms — critical section, race condition, mutual exclusion — were **coined by Edsger Dijkstra**, a foundational pioneer of the field (and Turing Award winner, in part for this work).
- His 1968 paper, **"Cooperating Sequential Processes"** [D68], is cited as an exceptionally clear early description of this exact problem.

---

## 11. Key Concurrency Terms (Critical Section, Race Condition, Indeterminate, Mutual Exclusion)

These four terms are so foundational to concurrent programming that they deserve to be called out explicitly, together:

| Term | Definition |
|---|---|
| **Critical Section** | A piece of code that accesses a shared resource, usually a variable or data structure. |
| **Race Condition** (or **data race**) | Arises when multiple threads enter a critical section at roughly the same time, both attempting to update shared data, leading to a surprising (and often undesirable) outcome. |
| **Indeterminate Program** | A program containing one or more race conditions; its output varies from run to run depending on which threads ran when — the opposite of the determinism we usually expect. |
| **Mutual Exclusion** | A guarantee, provided by proper synchronization primitives, that only a single thread can ever be inside a critical section at once — avoiding races and producing deterministic outputs. |

> See Dijkstra's early papers [D65, D68] for the foundational treatment of these ideas.

---

## 12. The Wish for Atomicity

### 12.1 The Idea of a "Super Instruction"

One conceptual fix: what if the hardware provided a **single, more powerful instruction** that performed the entire increment in one atomic step? For example, a hypothetical instruction:
```asm
memory-add 0x8049a1c, $0x1
```
- If this instruction is **guaranteed by the hardware to execute atomically**, it **cannot be interrupted mid-instruction** — when an interrupt occurs, either the instruction **hasn't run at all**, or it has **run to completion**. There is no observable in-between state.

### 12.2 What "Atomic" Means
- **Atomically** = "as a unit," often summarized as **"all or none."**
- We would ideally like the entire three-instruction sequence:
```asm
mov 0x8049a1c, %eax
add $0x1, %eax
mov %eax, 0x8049a1c
```
to execute **as if it were one indivisible instruction**.

### 12.3 Why a Single "Do Anything Atomically" Instruction Isn't Realistic
- If we had such an instruction for this specific case, we could just issue it directly.
- But in the **general case**, we won't have a specific hardware instruction for every possible operation we might want to make atomic.
- **Example:** if building a concurrent B-tree, would we want the hardware to offer an "atomic update of B-tree" instruction? Almost certainly **not** — that's far too specific for a general-purpose instruction set.

### 12.4 The Real Solution — Build General Synchronization Primitives
- Instead, we ask the **hardware** for a small set of **useful, general-purpose atomic instructions**.
- Using these hardware primitives, combined with help from the **operating system**, we can build **synchronization primitives** (locks, semaphores, condition variables, etc. — covered in later chapters).
- These primitives let us construct multi-threaded code that accesses critical sections in a **controlled, synchronized manner**, reliably producing correct results despite the fundamentally unpredictable nature of concurrent scheduling.

> **Tip — Use Atomic Operations:** Atomic operations are one of the most powerful underlying techniques across computer systems — from computer architecture, to concurrent code, to file systems (e.g., **journaling**, **copy-on-write**), to database systems, to distributed systems [L+93]. Grouping a set of actions into a single atomic unit is sometimes called a **transaction** — a concept explored in great depth in database systems and transaction processing [GR92].

---

## 13. One More Problem: Waiting For Another

- So far, this chapter has framed concurrency as being **only** about controlling access to shared variables (i.e., building atomic critical sections).
- But there's a **second, distinct kind of interaction** between threads: sometimes, **one thread must wait for another thread to complete some action** before it can proceed.
- **Example:** a process performs a disk I/O and is put to sleep; when the I/O completes, the process must be **roused ("woken up")** so it can continue running.
- This "sleeping/waking" interaction is **different** from the mutual-exclusion problem — it requires its own set of mechanisms, covered in the later chapter on **condition variables**.
- The book explicitly foreshadows this: synchronization support is needed not just for **atomicity** of critical sections, but also for this **orchestrated waiting/waking** pattern between threads.

---

## 14. Why Study This in an OS Class?

- **One-word answer: "History."**
- The **operating system itself was the first concurrent program ever written** — many of the foundational concurrency techniques were developed specifically *for use within the OS*.
- Later, once **multi-threaded user-level processes** became common, application programmers had to grapple with the exact same problems.

### 14.1 A Concrete OS Example — Concurrent File Appends
- Imagine **two processes**, both calling `write()` to **append** data to the **same file** (i.e., add data to the end, increasing the file's length).
- To do this correctly, each process must:
    1. Allocate a new disk block.
    2. Record, in the file's **inode**, where this new block lives.
    3. Update the file's recorded **size** to reflect the new, larger length.
- Because an **interrupt can occur at any point** during this sequence, the code that updates these shared kernel structures (e.g., a **free-space bitmap**, or the file's **inode**) constitutes a **critical section**.
- **Conclusion:** OS designers, from the very introduction of interrupts, have had to carefully consider how the OS updates its internal structures — an untimely interrupt in unsynchronized code causes exactly the kind of bugs described above.
- **Virtually every kernel data structure** — page tables, process lists, file system structures, etc. — must be accessed with **proper synchronization primitives** to behave correctly under concurrency.

---

## 15. Glossary of Key Terms

| Term | Definition |
|---|---|
| **Thread** | An independent point of execution within a process, sharing the process's address space but with its own PC, registers, and stack. |
| **Thread Control Block (TCB)** | Data structure storing the saved state (PC, registers, etc.) of a single thread, analogous to a PCB for processes. |
| **Thread-Local Storage** | Data (e.g., stack-allocated variables) that belongs uniquely to a specific thread, stored on that thread's own stack. |
| **Parallelization** | Transforming a single-threaded program into one that uses multiple threads/CPUs to complete work faster. |
| **Critical Section** | Code that accesses shared data/resources and must not run concurrently across threads. |
| **Race Condition / Data Race** | A bug where program outcome depends on the precise timing/interleaving of concurrent thread execution. |
| **Indeterminate Program** | A program whose output can vary from run to run due to unresolved race conditions. |
| **Mutual Exclusion** | A guarantee that only one thread can execute a critical section at a time. |
| **Atomicity** | The "all or nothing" property — a sequence of actions appears to happen as a single, indivisible unit. |
| **Synchronization Primitive** | A tool (built from hardware + OS support) — e.g., locks — used to enforce mutual exclusion or ordering between threads. |
| **Transaction** | A grouping of multiple actions into a single atomic unit, a concept central to databases. |
| **Condition Variable** | A synchronization mechanism (covered in later chapters) for the "waiting for another thread" problem, beyond simple mutual exclusion. |

---

## 16. Summary Table

| Concept | Process | Thread |
|---|---|---|
| **Address space** | Private | Shared with sibling threads |
| **Stack** | One, at bottom of address space | One per thread, scattered through address space |
| **Register state** | Saved in PCB | Saved in TCB |
| **Context switch cost** | Higher (must switch address space/page table) | Lower (no address space switch needed) |
| **Data sharing** | Requires explicit IPC mechanisms | Trivial — shared memory by default |
| **Best suited for** | Logically separate tasks, little data sharing | Tasks needing heavy data sharing, or exploiting parallel CPUs / overlapping I/O |

---

## 17. Worked Homework Questions & Solutions (Conceptual)

> These correspond to the `x86.py` simulator homework. Since the simulator itself isn't available here, the **conceptual reasoning** behind expected outcomes is given below.

### Q1–Q3 (`loop.s`, single/multiple threads, fixed/random interrupts, register `%dx`)
- With a **single thread** (Q1), there is **no concurrency at all**, so `%dx`'s value changes **deterministically and predictably** with each instruction — no race is possible.
- With **two threads** (Q2), each initialized with `dx=3`, since **registers are private per-thread**, there is **no shared state being contested** in this particular program — so even though execution is interleaved, there is **no race condition**, because nothing is shared. The interleaving affects *when* things happen but not *what* the final values are, since each thread's `%dx` is entirely its own.
- Making the interrupt interval **small/random** (Q3) changes **when** context switches happen and thus the exact interleaving trace, but since there's no shared state at risk (per above), it **does not change the correctness of the final outcome** — only the order in which output/trace events appear.

### Q4–Q8 (`looping-race-nolock.s`, shared memory address 2000, called `value`)
- With a **single thread** (Q4), `value` increases **predictably and correctly** with each loop iteration — no race is possible, since there's no concurrent access.
- With **two threads**, each looping (e.g.) 3 times (Q5), **both threads share** the same memory location (`2000`). This **is** a shared/critical section scenario — the *expected* correct final value assumes no interleaving problems (e.g., 2 threads × 3 increments = 6 total, if starting from 0), but this is **only guaranteed if operations don't overlap badly**.
- With **random interrupt intervals** (Q6), the final value of `value` becomes **unpredictable** — **you cannot determine the final result just by eyeballing the interleaving**, precisely because the underlying `load–increment–store` sequence is **not atomic**. The **critical section** is exactly the span from the `load` of `value` to the final `store` back to memory — an interrupt landing **inside** this window can cause a lost update (exactly as shown in Section 10's detailed trace). An interrupt landing **outside** this window (i.e., between fully-completed increments) is always safe.
- With **fixed interrupt intervals** (Q7), some interrupt intervals will "accidentally" avoid ever interrupting mid-update (e.g., if the interval never lines up with the 3-instruction critical section), yielding a **correct** result; other interval values will **reliably or intermittently** interrupt mid-critical-section, causing **lost updates** and an **incorrect** final value. Only intervals that never coincide with the critical section boundary (or that are much larger/smaller than it, depending on alignment) produce consistently correct results.
- With **more loop iterations** (Q8, e.g., `bx=100`), the **surface area for a race increases** — more updates means more opportunities for an interrupt to fall inside a critical section, so **more interrupt intervals** will produce **incorrect** results compared to the smaller-loop-count case; certain "lucky" intervals may still coincidentally avoid triggering any race.

### Q9–Q10 (`wait-for-me.s`, threads coordinating via a value at address 2000, register `%ax`)
- This program demonstrates the **"waiting for another thread"** pattern (Section 13), rather than pure mutual exclusion.
- With `ax=1,ax=0` (Q9): thread 0 is signaled to proceed/do something (`ax=1`), while thread 1 starts with `ax=0`, likely **waiting/spinning** until thread 0 sets the shared memory location to indicate it's done, at which point thread 1 proceeds. The final value at location 2000 reflects **whichever "done" signal state** the coordinating threads agree upon (e.g., set to 1 once thread 0 has finished its part).
- With the inputs **switched** (`ax=0,ax=1`, Q10): now thread 0 is the one starting at `ax=0` — likely the one **waiting/spinning** for thread 1 to signal completion. This flips which thread is "producer" and which is "consumer" of the shared signal.
    - **Changing the interrupt interval** (e.g., `-i 1000`, or random) affects **how often the waiting thread re-checks the shared value** — with a **spin-wait** (busy-waiting) implementation, a **waiting thread with a very long interrupt interval could waste enormous amounts of CPU time** just spinning and re-checking the flag, rather than yielding the CPU productively. This is generally an **inefficient use of the CPU** — a key motivation for the later chapters' more efficient sleep/wake mechanisms (e.g., condition variables) instead of naive busy-waiting.

---

## 18. Annotated Reference List

| Citation | Work | Relevance |
|---|---|---|
| **[D65]** | "Solution of a problem in concurrent programming control" — E. W. Dijkstra (1965) | First paper outlining the mutual exclusion problem and an early solution (not widely used in practice; needs more hardware/OS support). |
| **[D68]** | "Cooperating Sequential Processes" — Edsger W. Dijkstra (1968) | Foundational, exceptionally clear early description of the entire concurrent-programming problem space; developed while working on the "THE" operating system. |
| **[GR92]** | "Transaction Processing: Concepts and Techniques" — Jim Gray & Andreas Reuter (1992) | The definitive reference on transaction processing and atomicity in database systems. |
| **[L+93]** | "Atomic Transactions" — Lynch, Merritt, Weihl, Fekete (1993) | Theoretical and practical treatment of atomic transactions in distributed systems. |
| **[NM92]** | "What Are Race Conditions? Some Issues and Formalizations" — Netzer & Miller (1992) | Detailed discussion distinguishing different types of races in concurrent programs (this chapter focuses specifically on **data races**). |
| **[SR05]** | "Advanced Programming in the UNIX Environment" — Stevens & Rago (2005) | Source of the convention for simple error-checking wrapper functions (e.g., `Pthread_create`) used in the example code. |

---

## 19. Big-Picture Takeaways

1. **Threads let a single process have multiple independent points of execution**, sharing the same address space — this makes data sharing trivial but introduces serious correctness challenges.
2. **Two major motivations for threads:** exploiting **parallelism** across multiple CPUs, and **overlapping slow I/O** with useful computation within a single program.
3. **Thread creation does not guarantee any particular execution order** — the OS scheduler decides, and reasoning about "what runs when" becomes fundamentally harder with concurrency.
4. **Shared data is the root of the danger.** Simple-looking code like `counter = counter + 1` is actually a multi-instruction sequence at the hardware level, and **is not atomic** — an interrupt landing in the middle can silently corrupt shared state.
5. This produces a **race condition**, leading to **indeterminate** program behavior — the same program can produce **different, incorrect results** on different runs.
6. The code region where this danger lives is called a **critical section**, and the fix we need is **mutual exclusion** — ensuring only one thread executes the critical section at a time.
7. Real systems achieve this via **hardware-supported atomic instructions**, combined with OS-level **synchronization primitives** (locks, etc.) — the subject of the chapters that follow.
8. Beyond mutual exclusion, there's a **second core concurrency problem**: threads sometimes need to **wait for each other** (e.g., waiting for I/O completion) — handled via mechanisms like **condition variables**, covered later.
9. **Historically**, the OS itself was the **first concurrent program**, and virtually every kernel data structure (page tables, process lists, file system metadata) requires careful synchronization — concurrency isn't an optional advanced topic, it's foundational to how operating systems work at all.