# Threads 

*(Based on Chapter 2: "Threads")*

---

## Table of Contents

1. Introduction — Programs vs. Threads
2. Concurrency and Thread Lifetimes
3. Example of Multithreaded Programs (Java & C/pthreads)
4. Reasons for Using Concurrent Threads
5. Switching Between Threads (Cooperative Multitasking)
6. The Real Linux i386 Context-Switch Code
7. Generalizing: `current`, `chooseNextThread()`, and `yield()`
8. Preemptive Multitasking
9. Security and Threads
10. Glossary of Key Terms
11. Summary Tables
12. Worked Exercise Solutions
13. Programming & Exploration Projects — Discussion
14. Annotated Notes / Historical References
15. Big-Picture Takeaways

---

## 1. Introduction — Programs vs. Threads

### 1.1 The Core Distinction
- A **program** consists of **instructions** — static text describing computational steps.
- A **thread** is a **sequence of computational steps strung together one after another** — i.e., the actual **execution** of those instructions over time.
- **This distinction matters even for single-threaded programs:**
    - A very **short program** (e.g., one containing a loop) can give rise to a **very long thread** of execution.
    - Running the **same program ten times** produces **ten distinct threads**, all executing the same underlying instructions.

> **Key mental model:** A program is like a musical score; a thread is like one particular performance of it. You can have many performances (threads) of the same score (program), and a short score can produce a long performance if there's a repeat sign (a loop).

### 1.2 How Threads Arise
Threads can come into existence in **multiple ways**:
1. **Single-threaded program** → produces a single thread.
2. **Multiple single-threaded programs** (running separately) → produces multiple threads, one per program.
3. **Multi-threaded program** → a single program **spawns** additional threads (e.g., Thread A spawns Thread B).
4. **Multiple runs of one single-threaded program** → running the same program repeatedly, each run producing its own thread.

```
Single-threaded program  →  Thread

Multiple single-threaded  →  Thread A     Thread B
programs                     (separate)   (separate)

Multi-threaded program   →   Thread A ──spawn──▶ Thread B

Multiple runs of one     →   Thread A     Thread B
single-threaded program      (same program, run twice)
```

---

## 2. Concurrency and Thread Lifetimes

### 2.1 Lifetime
- Each thread has a **lifetime**: the span from its **first instruction execution** to its **last instruction execution**.

### 2.2 Definition of Concurrency
- Two threads are **concurrent** if their **lifetimes overlap**.
- **One of the most fundamental goals of an OS** is to let multiple threads run concurrently on the same computer — rather than forcing one thread to completely finish before the next can even begin.

### 2.3 Three Illustrative Cases

```
(a) Sequential threads (no overlap — NOT concurrent):
    Thread A: |----------|
    Thread B:              |----------|

(b) Concurrent threads running simultaneously on two processors:
    Thread A: |----------------------|   (Processor 1)
    Thread B:     |------------------|   (Processor 2)

(c) Concurrent threads interleaved on one processor (gaps in execution):
    Thread A: |--|    |--|      |--|
    Thread B:     |--|    |--|     |--|
```

- **Case (b):** true simultaneous execution — requires **multiple physical processors**, one thread genuinely running per processor at the same instant.
- **Case (c):** **interleaved** execution on a **single processor** — from a big-picture view the two threads' lifetimes still overlap (making them "concurrent"), even though at any *given instant* only one is actually executing. This is the case the chapter focuses on primarily.

### 2.4 Single-Processor Focus (With Multi-Processor Notes)
- The chapter (and by extension, these notes) primarily focuses on the case where **all threads run on a single processor**, since this is the simplest setting in which to understand the fundamental mechanisms.
- Multi-processor-specific issues are called out explicitly wherever they arise (e.g., in later sections about `current` being tracked **per-processor**).
- **Why run more threads than processors?** Even on a multi-processor machine, users often want to run **more concurrent threads than there are physical processors** — the OS must then divide each processor's attention among multiple threads (see Section 4 for the underlying reasons why this is desirable).

---

## 3. Example of Multithreaded Programs (Java & C/pthreads)

### 3.1 Setup
- When a program starts, it runs in a **single thread**. To become multi-threaded, the original ("parent") thread must, at some point, **spawn a child thread** to do some work while the parent continues doing other work.
- For **more than two threads**, this thread-creation step can simply be repeated.
- **Example goal:** demonstrate the **independence** of two threads by having both respond to a timer:
    - **Child thread:** sleeps **3 seconds**, then prints a message.
    - **Parent thread:** sleeps **5 seconds**, then prints a message.
- Because the threads run **concurrently**, the child's message should appear **~2 seconds before** the parent's message — not because of ordering logic, but simply because 3 seconds elapses before 5 seconds does, and both clocks start at roughly the same time.

### 3.2 Java Version

```java
public class Simple2Threads {
    public static void main(String args[]) {
        Thread childThread = new Thread(new Runnable() {
            public void run() {
                sleep(3000);
                System.out.println("Child is done sleeping 3 seconds.");
            }
        });
        childThread.start();

        sleep(5000);
        System.out.println("Parent is done sleeping 5 seconds.");
    }

    private static void sleep(int milliseconds) {
        try {
            Thread.sleep(milliseconds);
        } catch (InterruptedException e) {
            // ignore this exception; it won't happen anyhow
        }
    }
}
```

**Walkthrough:**
1. The main program creates a `Thread` object, `childThread`, wrapping a `Runnable` whose `run()` method sleeps 3000ms (3 seconds) and then prints a message.
2. The `run()` method **does not execute yet** — it only starts running once `childThread.start()` is invoked.
3. Because `run()` executes in a **separate thread**, the **main thread continues immediately** to its own next steps: sleeping 5000ms (5 seconds), then printing its own message.
4. **Key Java-specific detail:** thread **creation** (`new Thread(...)`) and thread **starting** (`.start()`) are **two distinct steps** in Java.

### 3.3 C (pthreads) Version

```c
#include <pthread.h>
#include <unistd.h>
#include <stdio.h>

static void *child(void *ignored) {
    sleep(3);
    printf("Child is done sleeping 3 seconds.\n");
    return NULL;
}

int main(int argc, char *argv[]) {
    pthread_t child_thread;
    int code;

    code = pthread_create(&child_thread, NULL, child, NULL);
    if (code) {
        fprintf(stderr, "pthread_create failed with code %d\n", code);
    }

    sleep(5);
    printf("Parent is done sleeping 5 seconds.\n");
    return 0;
}
```

**Walkthrough:**
1. The `child` procedure sleeps 3 seconds and prints a message — conceptually equivalent to the Java `Runnable`'s `run()` method.
2. `main()` calls `pthread_create(&child_thread, NULL, child, NULL)`, which creates a thread control structure (`child_thread`) **and immediately starts it running** the `child` procedure.
3. `main()` (the parent) then sleeps 5 seconds and prints its own message.

### 3.4 Key API Difference: One Step vs. Two Steps

> **The most significant difference between the two APIs:** `pthread_create()` **both creates the child thread and starts it running** in a **single call**, whereas in Java, thread **creation** (`new Thread(...)`) and thread **starting** (`.start()`) are **two separate steps**.

| Step | Java | C/pthreads |
|---|---|---|
| **Create thread object/structure** | `new Thread(runnable)` | *(combined into next step)* |
| **Start thread running** | `childThread.start()` | `pthread_create(&child_thread, NULL, child, NULL)` |

### 3.5 Other, Non-Portable APIs
- Beyond portable APIs like **Java's** and **pthreads**, many systems also expose their **own, non-portable APIs**.
- **Example:** Microsoft Windows provides the **Win32 API**, with procedures such as `CreateThread` and `Sleep` — functionally analogous to `pthread_create` and `sleep`, but specific to Windows.

---

## 4. Reasons for Using Concurrent Threads

Fundamentally, most uses of concurrent threads serve **one of two goals** (plus a third, less fundamental motivation):

### 4.1 Goal 1 — Responsiveness
> Allowing the computer system to **respond quickly** to something **external** to the system — such as a human user, or another computer system — even if one thread is in the middle of a long computation.

- The **timer example** in Section 3 illustrates this: both parent and child threads independently respond to their own timers, without either blocking the other.

#### Example A — Web Server Responsiveness
- A web server serving many clients must **read incoming request bytes** from each client's network connection (typically via a loop reading until the end of the request is detected).
- **Problem scenario:** if one client has a very slow connection (e.g., an old dial-up modem), the server might read the first part of that client's request and then have to **wait a long time** for the rest.
- **Unacceptable outcome (single-threaded server):** the *entire* website grinds to a halt for *all* clients, just because of one slow client.
- **Solution:** use **one thread per client connection** — so a thread blocked waiting on a slow client doesn't prevent other threads from continuing to serve other clients.

```
Single-threaded web server:
   Slow client ──▶ [Server, BLOCKED] ──✕──▶ Other clients (also blocked!)

Multi-threaded web server:
   Slow client ──▶ [Thread 1, blocked]
   Other clients ─▶ [Thread 2, 3, ... continue serving normally]
```

#### Example B — Web Browser Responsiveness
- Loading a very large web page can take a long time. Would you want the **entire computer to freeze** until the download finishes? No.
- Users expect to be able to, **while downloading**:
    - Work on a spreadsheet in a different window.
    - Scroll through the portion of the page **already downloaded**.
    - Click **Stop** to cancel the time-consuming download.
- **Solution:** one thread handles the network download, while **another thread** remains responsive to keyboard/mouse input.

#### Foreshadowing: Two Different Kinds of Thread Relationships
This web-browser scenario illustrates **two distinct categories** of concurrent-thread relationships:

1. **Nearly independent threads** (e.g., web browser download thread vs. a completely separate spreadsheet program): the OS's job is mostly to **isolate** them from each other (so a browser bug can't corrupt your spreadsheet's memory). This isolation is typically achieved via separate **processes** (discussed in later chapters), each with its own **protection environment**.
2. **Closely related threads within a single application** (e.g., the browser's download thread and its own UI thread): these need **not** be isolated from each other by the OS, but they **do** need coordination — e.g., ensuring the UI only lets you scroll through data that has **actually** been downloaded so far, no further. This coordination between threads is called **synchronization** (the subject of later chapters).

### 4.2 Goal 2 — Resource Utilization
> Keeping most of the hardware resources **busy most of the time**. If one thread has no need for a particular piece of hardware, another thread may be able to use it productively.

#### Example A — Multi-Processor Utilization
- On a **dual-processor** machine, running only one thread at a time would waste **half** the available processing capacity.
- **OS housekeeping example:** when allocating memory for an application, the OS typically **zeros out** that memory first (for security — so a program can't see leftover data from whatever previously used that memory). Rather than doing this **zeroing synchronously** (which would slow down every memory allocation request), the OS can run a **dedicated background thread** that proactively zeroes out unused memory ahead of time, so it's ready when needed.
- **Key insight:** this example shows that **not all threads originate from user programs** — a thread can be part of the **operating system itself**.

#### Example B — Single-Processor, Multiple Hardware Resources (Disk + CPU)
- Even on a **single-processor** system, other hardware resources exist besides the CPU — e.g., **disk drives**.
- **Scenario:** you want to (1) scan all files on disk for viruses, and (2) render a complex photo-realistic 3D scene (including shadows on partially transparent smoke). Each task alone takes about **1 hour**.
    - **Sequential execution:** 1 + 1 = **2 hours total**.
    - **Concurrent execution:** Because the virus scanner is **mostly disk-bound** (waiting on disk reads, with only brief bursts of CPU work) while the renderer is **mostly CPU-bound** (with minimal disk activity), running them **concurrently** lets the CPU work on rendering while the disk is busy fetching files for the scanner — potentially finishing both in only **~1.5 hours total**, not 2.

```
Sequential execution (2 hours total):
Processor: |---virus scan CPU bursts---||-----graphics rendering-----|
Disk:      |---virus scan disk reads---||------------idle------------|

Concurrent execution (~1.5 hours total):
Processor: |---rendering + occasional virus-scan CPU bursts----------|
Disk:      |---virus scan disk reads (overlapped with rendering)-----|
```

- **Caveat:** this savings **assumes the OS scheduler is smart enough** to give the virus scanner brief processor attention whenever a disk request completes, rather than making it wait behind the rendering program — a scheduling policy question addressed in the chapter on scheduling.

### 4.3 A Third (Less Fundamental) Reason — Modularization
- Some programmers use concurrent threads purely as a **modularization tool**: decomposing a complex system into a group of **interacting threads**, even if pure performance/responsiveness isn't the primary driver.

### 4.4 Summary of Thread Sources and Roles
Threads can:
- Be **internal to the operating system** (e.g., the memory-zeroing thread).
- Be **part of user application software**, either:
    - Dividing up work **within** a single multithreaded process (e.g., web server, web browser), or
    - Coming from **multiple independent processes** (e.g., browser in one window, spreadsheet in another).
- Regardless of source, the **typical underlying reasons** for running them concurrently remain: **responsiveness** or **improved resource utilization** — and the **basic mechanism** for dividing processor attention among threads remains the same across all these cases.

---

## 5. Switching Between Threads (Cooperative Multitasking)

### 5.1 The Core Requirement
To run more than one thread "at a time" on a single processor, the OS needs a mechanism to:
1. **Leave off** in the middle of one thread's instruction sequence.
2. Work on **other threads** for a while.
3. **Pick back up** in the original thread exactly where it left off.

### 5.2 Simplifying Assumption (For Now): Cooperative / Explicit Switching
- To explain this as simply as possible, we first assume each thread's code contains **explicit instructions** to voluntarily switch to another thread at certain points. (Later, in Section 8, this assumption is relaxed to cover automatic, **preemptive** switching.)

### 5.3 Notation and Example Execution Sequence
- Let threads **A** and **B** have instruction-execution steps named A1, A2, A3, ... and B1, B2, B3, ....
- When thread A executes `switchFromTo(A, B)`, the computer begins executing instructions from thread B.

```
Thread A     Thread B
A1
A2
A3
switchFromTo(A,B)
             B1
             B2
             B3
             switchFromTo(B,A)
A4
A5
switchFromTo(A,B)
             B4
             B5
             B6
             B7
             switchFromTo(B,A)
A6
A7
A8
switchFromTo(A,B)
             B8
             B9
```

### 5.4 The Coherence Goal
- **Goal:** from thread A's own perspective, its execution should look essentially the same as if A1 through A8 executed **consecutively, without interruption** — and likewise for B's steps B1 through B9.
- **Concrete illustration:** suppose A1–A2 load two values from memory into registers, A3 adds them (storing the sum in a register), and A4 doubles that register's contents. We need to guarantee that **A4 really doubles the sum computed by A1–A3**, and not some **other value** that thread B's steps (B1–B3) happened to store in that same physical register while A was paused.
- **Conclusion:** thread switching **cannot simply be a jump instruction**. At minimum, we must also **save registers to memory** (on switch-out) and **restore them from memory** (on switch-back-in), so each thread's own register values are correctly back in place whenever it resumes.

### 5.5 Thread Control Blocks (TCBs)
- To support switching, the OS needs to keep, **for each thread**, information about **where that thread should resume execution**.
- This information is stored in a **block of memory per thread**, called a **Thread Control Block** (or **Task Control Block**), abbreviated **TCB**.
- We can then use the **address of a thread's TCB** as a way to **refer to that thread** — i.e., "pointers to thread control blocks" effectively serve as thread identifiers/handles.

### 5.6 The `switchFromTo` Procedure

- **Signature (conceptually):** `switchFromTo(outgoing, next)` — takes two TCB pointers: the thread being switched **out of**, and the thread being switched **into**.
- In the A/B example, `A` and `B` are pointer variables to the two threads' TCBs, used alternately as "outgoing" and "next" depending on the direction of the switch.
- **Assumption for now (relaxed later):** each thread's code must know **both its own identity and the identity of the thread to switch to** — this is what allows thread A's code (after A5) to call `switchFromTo(A, B)`, and thread B's code (after B3) to call `switchFromTo(B, A)`.

### 5.7 What Needs to Be Saved: Registers, PC, and the Stack

- **Program Counter (PC)** / **Instruction Pointer (IP)**: tracks the current position in the program.
- **General registers**: hold values mid-computation.
- **The stack** (and its associated **stack pointer** register): a portion of memory used by most higher-level-language-compiled programs to store local variables, arguments, and return addresses.

#### Why Each Thread Needs Its Own Stack
- When a thread resumes, it must find its stack **exactly as it left it**.
    - Example: if thread A pushes two items onto the stack, then pauses while B runs (possibly pushing its **own** items), when A resumes it should still find **its own two items** at the top of the stack — regardless of what B did on its own (separate) stack in the meantime.
- **Solution:** give each thread its **own separate stack**, in its own dedicated region of memory.
    - While thread A runs, its stack pointer (SP) moves within A's stack region.
    - Upon switching to B, we must **save A's SP** (along with other registers) and **load B's SP**, so that while B runs, its SP correctly moves within **B's own** stack region.

### 5.8 Simplifying Register Saves: Push Onto the Stack

- Rather than saving every register into some separate structure, we can simply **push all registers onto the outgoing thread's own stack** before switching, and **pop them back off** after switching back — since we're already dealing with a per-thread stack anyway.

**General pseudocode pattern:**
```
push each register on the (outgoing thread's) stack
store the stack pointer into outgoing->SP
load the stack pointer from next->SP
store label L's address into outgoing->IP
load in next->IP and jump to that address
L:
pop each register from the (resumed outgoing thread's) stack
```

- **Before label L:** executed at the moment of switching **away** from the outgoing thread.
- **After label L:** executed **later**, when some other thread eventually switches back to this one.
- Because the same code handles both switching *out* and (later) resuming, and because the correct stack pointer will have been restored by the time control reaches label L, popping registers at L correctly retrieves values from **this thread's own stack** — matching the earlier pushes.

```
TCB Structure (conceptual):
┌──────────────────────┐        ┌────────────────┐
│  A's TCB             │        │  A's stack     │
│  ┌────────────────┐  │        │  A's saved     │
│  │ IP (resumption)│──┼───────▶│  registers     │
│  │ SP             │──┼───────▶│  A's data      │
│  └────────────────┘  │        └────────────────┘
└──────────────────────┘

┌──────────────────────┐        ┌────────────────┐
│  B's TCB             │        │  B's stack     │
│  ┌────────────────┐  │        │  B's saved     │
│  │ IP (resumption)│──┼───────▶│  registers     │
│  │ SP             │──┼───────▶│  B's data      │
│  └────────────────┘  │        └────────────────┘
└──────────────────────┘
```

---

## 6. The Real Linux i386 Context-Switch Code

To ground the abstract pattern in reality, the chapter walks through **actual Linux kernel code** (extracted from version 2.6.0-test1, i386/x86/IA-32 architecture — used in Intel Core/Xeon/Atom and AMD FX/Opteron families).

### 6.1 Setup
- `%esp` = the stack pointer register.
- `%ebx` = holds the **outgoing** thread's TCB pointer when this code begins.
- `%esi` = holds the **next** thread's TCB pointer.
- Within each TCB: **offset 812** stores the instruction pointer; **offset 816** stores the stack pointer.
- Only the **flags register** and **`%ebp`** need explicit saving here — other registers are handled by surrounding code not shown.

### 6.2 The Code, Annotated

```asm
pushfl               # pushes the flags on outgoing's stack
pushl %ebp           # pushes %ebp on outgoing's stack
movl %esp,816(%ebx)  # stores outgoing's stack pointer
movl 816(%esi),%esp  # loads next's stack pointer
movl $1f,812(%ebx)   # stores label 1's address,
                     # where outgoing will resume
pushl 812(%esi)      # pushes the instruction address
                     # where next resumes
ret                  # pops and jumps to that address
1: popl %ebp         # upon later resuming outgoing,
                     # restores %ebp
   popfl             # and restores the flags
```

### 6.3 Mapping This Back to the General Pattern

| General Pattern Step | Linux i386 Code |
|---|---|
| Push registers onto outgoing's stack | `pushfl`, `pushl %ebp` |
| Store outgoing's SP | `movl %esp,816(%ebx)` |
| Load next's SP | `movl 816(%esi),%esp` |
| Store resumption address (label L) into outgoing's TCB | `movl $1f,812(%ebx)` |
| Load next's IP and jump there | `pushl 812(%esi)` followed by `ret` (a clever trick: pushing the target address then using `ret` to "return" to it, effectively acting as an indirect jump) |
| Label L: pop registers | `1: popl %ebp`, `popfl` |

- **Notable implementation trick:** rather than an explicit jump instruction, the Linux code **pushes** the target address onto the (now-switched) stack and then uses `ret` (return) to "pop and jump" to it — a compact way of performing an indirect jump using an instruction normally used for returning from a subroutine call.

---

## 7. Generalizing: `current`, `chooseNextThread()`, and `yield()`

### 7.1 Removing the "Explicit Names" Assumption
So far, we assumed each thread's code explicitly names both itself and its target (e.g., `switchFromTo(A, B)`). We can eliminate this unrealistic assumption:

1. **Track the currently running thread** at all times via a **global variable**, `current`, holding a pointer to its TCB. (On a multiprocessor, `current` must be tracked **per-processor**.)
2. **Track all threads** in some OS-maintained data structure (e.g., a list).
3. Provide a procedure, **`chooseNextThread()`**, which consults that data structure and — using some **scheduling policy** (the subject of the *next* chapter; treated here as a "black box") — decides which thread should run next.

### 7.2 The `yield()` Procedure

```
outgoing = current;
next = chooseNextThread();
current = next;              // so the global variable will be right
switchFromTo(outgoing, next);
```

- Any thread that wants to **voluntarily** pause and let other threads run can simply call **`yield()`**.
- This is essentially the approach used by **real systems, such as Linux**.
- **Multiprocessor complication:** `current` must be recorded **per-processor**, since different processors may simultaneously be running different "current" threads.

### 7.3 Terminology: "Context Switching"
- Thread switching is often called **context switching**, since it switches from the **execution context** of one thread to another.
- **Ambiguity warning:** many authors use "context switching" to instead mean switching between **processes** (with their protection contexts) — a different topic covered in later chapters on protection boundaries.
- **Recommendation (if the distinction matters):** avoid the ambiguous term "context switching" in favor of the more specific **thread switching** or **process switching**.
- **Dispatching:** thread switching is the most common way a thread gets **dispatched** (i.e., caused to execute on a processor). The **only** way a thread can be dispatched **without** a thread switch is if the processor was previously **idle**.

---

## 8. Preemptive Multitasking

### 8.1 Cooperative vs. Preemptive Multitasking

| | Cooperative Multitasking | Preemptive Multitasking |
|---|---|---|
| **Switch points** | Only where the program **explicitly** includes a thread-switch call | Can occur **automatically**, at (almost) any time |
| **Risk from buggy code** | A bug (e.g., accidental infinite loop) with no switch point inside can **hog the processor forever**, blocking all other threads | Even a buggy infinite loop gets **periodically interrupted**, letting other threads make progress |
| **Switch timing control** | Limited to programmer-chosen points | The **OS** can choose to switch **exactly when it best serves responsiveness/resource-utilization goals** — e.g., right when input arrives for a waiting thread, or when a device becomes idle |

### 8.2 Why Prefer Preemptive Multitasking?

1. **Robustness against buggy code:** Consider a loop **expected** to iterate only a few times — in a cooperative system, it might seem "safe" to only place switch points **before and after** the loop, not inside its body. But a bug could turn it into an **infinite loop**, permanently hogging the processor. With preemptive multitasking, even a buggy infinite loop will periodically be paused, letting other threads still progress.
2. **Better-timed switches:** Preemptive multitasking allows the OS to switch threads **exactly when doing so best achieves responsiveness and resource utilization** — e.g., preempting a thread the moment input arrives for a different, waiting thread, or when a hardware device becomes idle.

### 8.3 `yield()` Still Exists, Even Under Preemption
- Even in preemptive systems, it can still be useful for a thread to **voluntarily** yield to others rather than run as long as it's technically allowed.
- **API naming (varies by system):**
    - pthreads: **`sched_yield()`**
    - Win32 (Microsoft Windows): **`SwitchToThread()`** (an exception to the common "yield"-containing naming pattern).

### 8.4 Hardware Support: Interrupts

- Preemptive multitasking needs **no fundamentally new switching mechanism** beyond what's already been described — it just requires adding a **hardware interrupt mechanism**.
- **Normal execution:** a processor executes consecutive instructions one after another, deviating only via explicit jump/`ret`-style instructions.
- **Interrupt mechanism:** external hardware (disk drive, network interface) — or a **hardware timer** set to fire periodically (e.g., every millisecond) — can signal that it needs attention. This is almost like a **procedure call forcibly inserted** between the currently executing instruction and the next one.
- Rather than continuing to the program's next instruction, the processor jumps to a special OS procedure: the **interrupt handler**.
- The interrupt handler:
    1. Deals with the hardware device's immediate needs.
    2. Executes a **"return from interrupt"** instruction, jumping back to the instruction that was about to execute when the interrupt occurred.
    3. **Must save all registers at the start, and restore them before returning**, so the interrupted program's execution can continue exactly as if nothing happened (from that program's perspective) — unless the OS decides to preempt it (see below).

### 8.5 How Preemption Actually Happens, Step by Step

1. An **interrupt occurs** (timer, or I/O device).
2. The **interrupt handler** saves the registers to the **current thread's stack**, and handles the immediate hardware need (e.g., accepting network data, or updating the system clock by one millisecond).
3. Rather than **automatically** just restoring registers and returning, the interrupt handler now **checks whether this is a good time to preempt** the current thread and switch to another — this decision is a **scheduling policy** question (next chapter).
    - **Example trigger:** the interrupt signaled arrival of data a **long-waiting thread** needed → probably a good time to switch to that thread.
    - **Example trigger:** the interrupt was from the **timer**, and the current thread has been running a **long time** → probably a good time to give another thread a turn.
4. If the OS **decides to preempt**, the interrupt handler calls a mechanism like **`switchFromTo`** — this includes switching to the **new thread's stack**, so that when registers are restored before returning from the interrupt, they'll be the **new thread's** registers, not the old one's.
5. The **previously running thread's register values remain safely on its own stack** until it is eventually resumed again later.

```
Timeline of a Preemptive Context Switch:

Thread A running
      │
      ▼
 [Timer/I/O interrupt fires]
      │
      ▼
 Interrupt handler saves A's registers onto A's stack
      │
      ▼
 Interrupt handler services the device / updates clock
      │
      ▼
 Interrupt handler decides: "preempt A, switch to B?"
      │
      ├── No  ──▶ restore A's registers, "return from interrupt" ──▶ Thread A resumes
      │
      └── Yes ──▶ switchFromTo(A, B): save A's SP/IP into A's TCB,
                  load B's SP/IP from B's TCB
                      │
                      ▼
                  restore B's registers (from B's stack)
                      │
                      ▼
                  "return from interrupt" ──▶ Thread B resumes
```

---

## 9. Security and Threads

### 9.1 Two Main Categories of Security Issues

1. **Monopolization / starvation:** some threads are **unable to execute** because others are **hogging** the computer's attention.
    - Addressed in the **security section of the scheduling chapter** (Chapter 3 in the source text).
2. **Unwanted interactions between threads:**
    - A thread **writing into storage** that another thread is currently trying to use.
    - A thread **reading from storage** that another thread considers **confidential**.
    - Addressed in later chapters covering **synchronization** and **protection** (Chapters 4, 5, and 7 in the source text).

### 9.2 The Underlying Risk Factor
- These problems are **most likely to arise** when the programmer has a **difficult time understanding** how threads may interact with one another.
- **Mitigation strategy emphasized throughout the source text:** favor **design approaches that make thread interactions easy to understand**, since this directly **minimizes the risk** stemming from incomplete understanding — prevention through clarity, not just reactive fixes.

---

## 10. Glossary of Key Terms

| Term | Definition |
|---|---|
| **Program** | Static instructions describing computational steps. |
| **Thread** | A sequence of computational steps strung together — the actual, dynamic execution of (part of) a program's instructions. |
| **Lifetime (of a thread)** | The span from a thread's first instruction execution to its last. |
| **Concurrent threads** | Two or more threads whose lifetimes overlap. |
| **Spawn** | The act of a parent thread creating a new child thread. |
| **Responsiveness** | The ability of a system to react promptly to external events (user input, network data) even while busy with other work. |
| **Resource utilization** | Keeping hardware resources (CPU, disk, etc.) busy as much as possible by overlapping work that stresses different resources. |
| **Process** | A protection environment encapsulating one or more threads, isolating them from other processes (detailed in later chapters). |
| **Synchronization** | Coordination mechanisms that regulate how closely-related threads interact (e.g., ensuring a UI thread doesn't scroll past downloaded data). |
| **Thread Control Block (TCB)** / **Task Control Block** | A per-thread block of memory storing the information (saved IP, SP, etc.) needed to resume that thread later. |
| **`switchFromTo(outgoing, next)`** | The fundamental thread-switching procedure: saves the outgoing thread's state and restores/resumes the next thread's state. |
| **Stack pointer (SP)** | A register indicating the current "top" position within a thread's own private stack region. |
| **Instruction pointer (IP)** / **Program Counter (PC)** | A register/value indicating the next instruction to execute. |
| **`current`** | A global (or per-processor) variable holding a pointer to the currently running thread's TCB. |
| **`chooseNextThread()`** | A scheduler procedure that selects which thread should run next, based on some scheduling policy. |
| **`yield()`** | A procedure a thread calls to voluntarily pause and let another thread run. |
| **Context switching** | Switching from one thread's (or, ambiguously, one process's) execution context to another's; prefer "thread switching" or "process switching" if precision matters. |
| **Dispatching** | Causing a processor to execute a particular thread; normally accomplished via a thread switch (except when the processor was previously idle). |
| **Cooperative multitasking** | A scheme where thread switches occur only at explicit, programmer-inserted points in the code. |
| **Preemptive multitasking** | A scheme where thread switches can occur automatically (via interrupts), without requiring explicit switch points in the program's own code. |
| **Interrupt** | A hardware-triggered event (timer, I/O device) that forces the processor to jump to a special interrupt handler, almost like an inserted procedure call. |
| **Interrupt handler** | OS code that responds to an interrupt — services the immediate hardware need, then decides whether to preempt the current thread. |
| **Fiber** / **User-level thread** | A thread implemented entirely outside the OS's protection boundary (terminology clarified in later chapters on protection). |

---

## 11. Summary Tables

### 11.1 Java vs. C/pthreads Thread Creation

| Step | Java | C (pthreads) |
|---|---|---|
| Create thread object | `new Thread(runnable)` | *(part of `pthread_create`)* |
| Start thread execution | `.start()` | `pthread_create(&t, NULL, func, arg)` — **creates AND starts in one call** |
| Sleep | `Thread.sleep(ms)` (throws checked `InterruptedException`) | `sleep(seconds)` (POSIX; takes **seconds**, not ms, in this basic form) |
| Yield | *(not shown in this chapter's examples; broader Java concurrency API has equivalents)* | `sched_yield()` |

### 11.2 Cooperative vs. Preemptive Multitasking

| Aspect | Cooperative | Preemptive |
|---|---|---|
| Switch trigger | Explicit code (e.g., `yield()`) | Hardware interrupt (timer or I/O) |
| Handles buggy infinite loops? | No — can hang the whole system | Yes — other threads still get turns |
| Switch timing flexibility | Limited to code's chosen points | OS can switch at optimal moments |
| Underlying mechanism | `switchFromTo()` | Same `switchFromTo()`, invoked from within an interrupt handler |

### 11.3 Reasons for Concurrency

| Goal | Description | Example(s) |
|---|---|---|
| **Responsiveness** | React quickly to external events despite other ongoing work | Web server (per-client thread), web browser (download thread + UI thread) |
| **Resource utilization** | Keep hardware busy by overlapping differently-bottlenecked work | Memory-zeroing OS thread on a spare CPU; concurrent virus scan (disk-bound) + rendering (CPU-bound) |
| **Modularization** (secondary) | Decompose a complex system into interacting threads | General software design technique |

---

## 12. Worked Exercise Solutions

### Exercise 2.1 — `sleep` (POSIX) vs. `Thread.sleep` (Java)
- **Key differences:**
    - POSIX's `sleep()` (as used in the example) takes an argument in **whole seconds**; Java's `Thread.sleep()` takes an argument in **milliseconds**, allowing finer-grained timing.
    - Java's `Thread.sleep()` can throw a **checked exception**, `InterruptedException`, which **must** be caught or declared — reflecting Java's built-in support for **interrupting** a sleeping thread cooperatively. POSIX's `sleep()` doesn't have this same checked-exception mechanism (though a sleeping thread/process can still be interrupted by signals, handled very differently in C).

### Exercise 2.2 — More Examples of Running More Threads Than Processors
Beyond the text's examples, additional cases where it's useful to run **more concurrent threads than physical processors**:
1. **A word processor with autosave:** one thread handles user typing/editing (responsiveness), another periodically autosaves the document in the background (resource utilization — using idle disk I/O time without blocking the user).
2. **A music/media player:** one thread decodes/streams audio data continuously, another manages the UI (responsiveness — so clicking pause/skip feels instant even while decoding is ongoing).
3. **A chat/messaging application:** one thread listens for incoming network messages (responsiveness — so messages appear immediately), another handles the user composing and sending a new message, and perhaps a third periodically checks for software updates (resource utilization, low-priority background work).
- In every case, these threads' **actual, momentary CPU needs** are small/bursty, so far more threads than processors can comfortably interleave without meaningfully competing for CPU time — the value comes from **responsiveness** (not blocking on I/O or long-running background tasks) rather than genuine parallel CPU-bound computation.

### Exercise 2.3 — Elapsed Time With and Without Overlap

**Setup:** Thread A loops 100 times, each iteration: (i) a 10ms disk I/O (during which A cannot use the processor), then (ii) 1ms of computation. Thread B needs 1 second (1000ms) of pure processor time, no I/O. Switching threads costs 1ms of processor time each time it happens.

**(a) Sequential — run A fully, then B fully:**
- Thread A's total time: 100 × (10ms disk + 1ms compute) = 100 × 11ms = **1100ms**.
    - *(Note: since A alone is running, the disk and compute phases for A are sequential from A's own perspective — no overlap opportunity exists when A runs alone.)*
- Then Thread B runs: **1000ms** of pure processor time.
- **Total elapsed time = 1100ms + 1000ms = 2100ms (2.1 seconds).**

**(b) Overlapped — switch to B during each of A's disk operations, switch back when the disk completes:**
- Each of A's 100 iterations: A computes for 1ms, then issues a 10ms disk operation.
    - During each 10ms disk wait, the processor switches to B (paying a 1ms switch cost), runs B for the *available* window, then switches back (another 1ms switch cost) when A's disk operation completes.
    - Switch cost per disk operation: 1ms (switch to B) + 1ms (switch back to A) = **2ms total switching overhead per iteration**.
    - Of A's 10ms disk-wait window, **2ms** is consumed by switching overhead, leaving **8ms** of actual useful processor time for B during that window.
- Over 100 iterations: A's own compute time = 100 × 1ms = **100ms**; total switching overhead = 100 × 2ms = **200ms**; useful B time obtained "for free" during A's I/O waits = 100 × 8ms = **800ms**.
- **B needs 1000ms total; 800ms of that is obtained "for free" during A's disk waits, leaving 200ms of B's work still needing to run after A finishes** (since A's disk operations end once A's loop completes).
- **Total elapsed time** = A's full loop duration (100 × (1ms compute + 10ms disk) = 1100ms, during which B gets 800ms of processor time interleaved) **+ remaining 200ms of B's work after A finishes** = **1100ms + 200ms = 1300ms (1.3 seconds)**.
- **Savings vs. sequential: 2100ms − 1300ms = 800ms (0.8 seconds) saved** by overlapping A's disk waits with B's CPU-bound work.

> *(Note: exact accounting of switch-cost placement can vary slightly depending on convention, but the core insight — substantial time savings from overlapping disk-bound and CPU-bound work, minus modest switching overhead — is the key takeaway.)*

### Exercise 2.4 — Response Time With New Threads Per Input

**(a) Single input source:** arrives every 1 second; each triggers a thread needing 600ms to run, plus 10ms to create/dispatch.
- Since inputs arrive **every 1000ms**, and each one only needs 10ms (create) + 600ms (run) = **610ms** total, there's no queuing — each input's thread fully completes (610ms) well before the next input arrives (1000ms later).
- **Average response time = 10ms (dispatch) + 600ms (run) = 610ms** for every input (no waiting on prior work, since 610ms < 1000ms gap).

**(b) Add a second input source:** arriving at 0.1s, 1.1s, 2.1s, etc. (i.e., 0.1 seconds after each first-class input), needing 10ms dispatch + 100ms run = **110ms** total, but **not created/dispatched until the processor is idle** (i.e., queued behind whatever's currently running).
- At time 0.1s, the processor is still busy with the **first-class input's thread** (which started at t=0, running until t=610ms). So the second-class input arriving at t=100ms must **wait** until t=610ms before it can even be created/dispatched.
- **Response time for this specific second-class input** = (610ms − 100ms arrival delay already waited) + 10ms dispatch + 100ms run = 510ms (wait) + 110ms (dispatch+run) = **620ms**.
- By symmetry, this same pattern (first-class thread occupies the processor first, second-class thread waits, then runs) repeats every cycle, so:
    - **Average response time for second-class inputs ≈ 620ms** (waiting for the long first-class thread to finish, then running).
    - **Average response time for first-class inputs = 610ms** (as in part a — unaffected, since they always run first upon arrival, before the second-class input's later arrival).
- **Combined average response time** = (610ms + 620ms) / 2 = **615ms**.

**(c) Second-class input now preempts immediately, with a 1ms switching delay to later resume the preempted thread:**
- Now, when a second-class input arrives at t=100ms, it **immediately preempts** the running first-class thread (rather than waiting).
- **Second-class input's response time** = 10ms (dispatch) + 100ms (run) = **110ms** — essentially unaffected by the other thread, since it's dispatched immediately.
- **First-class input's response time** = original 610ms **plus** the extra delay from being preempted: it's paused (after running for 100ms of its own time, from t=0 to t=100ms) for the second-class thread's 110ms of work, plus a **1ms switching delay** to resume, **plus** presumably another small delay to switch back out when the second thread completes. Accounting for a 1ms switch-out and 1ms switch-back-in (2ms total switching overhead) plus the full 110ms the second-class thread runs:
    - First-class thread's total elapsed time = 100ms (initial run) + 110ms (second-class thread's full dispatch+run) + ~2ms (switching overhead) + remaining 500ms (rest of first-class thread's own work) + 10ms (its own original dispatch) ≈ **610ms (original work) + 110ms (interruption) + 2ms (switch overhead) = 722ms**.
- **Average for first-class inputs ≈ 722ms; average for second-class inputs ≈ 110ms.**
- **Combined average** = (722ms + 110ms) / 2 = **416ms**.
- **Key takeaway:** preemption **dramatically improves** the short/interactive (second-class) input's response time (620ms → 110ms), at the cost of **somewhat worsening** the long-running (first-class) input's response time (610ms → 722ms) — directly illustrating the same **responsiveness vs. throughput/turnaround trade-off** seen elsewhere in OS scheduling design.

### Exercise 2.5 — Thread Switching vs. Subroutine Call/Return

- **Superficial similarity:** in both cases, execution "picks back up where it left off" after some other code has run in between (another thread, or a called subroutine).
- **Essential difference:** a **subroutine call/return** happens **within a single thread's own control flow** — the calling procedure **knows** it's calling a subroutine and **expects** control to return to exactly that call site once the subroutine finishes; the whole sequence is **synchronous and nested** (like a stack of nested function calls, always unwound in strict last-in-first-out order relative to that one thread).
    - Because it's all one thread, a **single shared stack** naturally handles this: the subroutine's local variables and return address simply get pushed on top of the same stack the caller was already using, and popped off when it returns — there's no need for a **separate** stack, since there's only ever one active "chain" of nested calls belonging to that one thread at any moment.
- **Thread switching**, by contrast, involves **suspending one independent, unrelated sequence of execution** (which may have **nothing to do** with the thread being switched to) and later resuming it — potentially **much later**, and the switch is often **not** synchronously "expected" by the outgoing thread's own code in the same tightly nested way a subroutine return is.
    - Since the two threads have **entirely independent, potentially deeply nested call histories of their own**, each needs **its own separate stack** — mixing them into one shared stack would completely scramble each thread's local variables and return addresses, since thread A's "nesting" has nothing to do with thread B's.
- **In short:** subroutine call/return is **intra-thread** control flow with a single, naturally shared stack; thread switching is **inter-thread** control flow, requiring **separate stacks** because the two threads' execution histories are logically independent of one another.

---

## 13. Programming & Exploration Projects — Discussion

*(These are hands-on projects from the original chapter; discussed conceptually here since they require an actual runtime environment to complete.)*

- **Project 2.1/2.2 (C `pthread_cancel` / Java `Thread.stop`):** Both explore **forcibly terminating** another thread from the outside. The chapter deliberately has students explore this **before** teaching proper synchronization tools, to let them **empirically observe** how a sleeping thread and a main thread reading keyboard input can interleave unpredictably — motivating the need for the more disciplined tools introduced in later chapters (this is also why Java's `Thread.stop()` is flagged as deprecated: killing threads abruptly, without a chance to release locks or clean up state properly, is inherently unsafe in general).
- **Project 2.3 (another language):** Reinforces that the **conceptual pattern** (spawn a thread, sleep, print) generalizes across languages/APIs, even though exact syntax and creation/start semantics (one-step vs. two-step, as highlighted in Section 3.4) vary.
- **Project 2.4 (Win32 API):** Reinforces that **non-portable, OS-specific APIs** (like `CreateThread`/`Sleep`) exist alongside portable ones and can be used interchangeably for the same conceptual task.
- **Exploration 2.1 (disk vs. processor-intensive concurrency):** A hands-on empirical version of the virus-scan + rendering example from Section 4.2, meant to let students **measure** real overlap savings on their own hardware.
- **Exploration 2.2 (history of cooperative → preemptive multitasking transitions in Windows/Mac OS):** Connects the chapter's conceptual cooperative-vs-preemptive discussion (Section 8) to **real historical OS design decisions**.
- **Exploration 2.3 (`vmstat` on Linux):** A concrete way to **empirically observe** how frequently real systems actually perform thread/context switches per second — grounding the abstract mechanism (Sections 5–8) in observable system behavior.

---

## 14. Annotated Notes / Historical References

| Reference | Relevance |
|---|---|
| **Codd et al., 1959 article [34]** | One of the earliest known discussions of concurrent execution (though not yet using the word "thread"), explicitly distinguishing "local" and "nonlocal" parallelism, and already identifying **both** motivations emphasized in this chapter: **resource utilization** ("more balanced loading of the facilities") and **responsiveness** ("a specified real-time response... [for] messages, transactions, etc... processed on-line"). |
| **Russinovich & Solomon's book [126]** | Cited as the source for details on the **zero page thread** in Microsoft Windows — the real-world OS example used for the "OS thread proactively zeroing memory" illustration in Section 4.2. |
| **Linux kernel v2.6.0-test1 source** | Source of the actual i386 context-switching assembly code presented in Section 6; the code was extracted from `include/asm-i386/system.h` (as included into `kernel/sched.c`), run through `gcc` to obtain pure assembly. The `ret` instruction shown is a simplification of the actual kernel code, which jumps to a small block ending in `ret`. |
| **POSIX / Java official documentation** | The chapter's own descriptions of the POSIX and Java threading APIs are explicitly stated to be **illustrative only**, not a replacement for the official documentation (available at opengroup.org/unix and Oracle's Java documentation site, respectively). |

---

## 15. Big-Picture Takeaways

1. **A thread is the execution of a program's instructions over time — not the instructions themselves.** This distinction explains why one short program can produce a very long thread (via loops), and why running the same program multiple times produces multiple distinct threads.
2. **Concurrency means overlapping lifetimes**, whether achieved via true simultaneous execution on multiple processors, or interleaved execution on a single processor.
3. **The core APIs (Java's `Thread`/`Runnable`, POSIX's `pthread_create`) look similar in spirit but differ in a key detail:** Java separates thread **creation** from thread **starting**; pthreads combines both into a single `pthread_create()` call.
4. **Threads exist to serve two fundamental goals:** **responsiveness** (react promptly to external events despite other ongoing work) and **resource utilization** (keep hardware busy by overlapping differently-bottlenecked work) — with **modularization** as a secondary, tooling-oriented motivation.
5. **The fundamental mechanism enabling thread switching is `switchFromTo()`,** which must carefully save and restore not just the program counter, but **all registers and the stack pointer** — requiring **each thread to have its own private stack** in memory, tracked via a per-thread **Thread Control Block (TCB)**.
6. **Real systems (like Linux) implement this exact pattern in raw assembly**, saving/restoring registers on each thread's own stack and swapping stack pointers directly.
7. **Cooperative multitasking (explicit `yield()`-style switching) is simple but fragile** — a single buggy infinite loop with no switch point can hang the entire system. **Preemptive multitasking**, built on the **same underlying switching mechanism** but triggered automatically via **hardware interrupts**, solves this and also lets the OS choose **optimal** moments to switch threads for better responsiveness and utilization.
8. **Security risks from threading fall into two broad buckets:** threads being **starved** of CPU time, and threads having **unwanted (unsynchronized) interactions** with each other's shared data — both are significantly reduced by **designing thread interactions to be as simple and understandable as possible**.