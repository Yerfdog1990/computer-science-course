# Memory Management 

*(Based on Section 6.4: "Memory Management")*

---

## Table of Contents

1. Learning Objectives
2. Memory Multiplexing — The Big Picture
3. Isolation Mechanisms
4. Time Slicing (Worked Example)
5. Sharing
6. Virtualization (Full Virtualization vs. Guest Modification)
7. Fragmentation (Internal vs. External)
8. Linkers and Dynamic Linking
9. Dynamic Storage Management (Stack vs. Heap Allocation)
10. Virtual Memory and Address Translation
11. The Translation Lookaside Buffer (TLB)
12. Demand Paging
13. Page Faults and Page Fetching/Prefetching
14. Page Replacement and Thrashing
15. **[Added]** Memory Optimization Criteria and the Memory Hierarchy
16. **[Added]** Contiguous vs. Non-Contiguous Memory Allocation
17. **[Added]** Fixed vs. Variable Sized Partitioning
18. **[Added]** First, Best, and Worst Fit Allocation Algorithms (Worked Examples)
19. **[Added]** Address Translation for Contiguous Memory Allocation
20. **[Added]** Paging Mechanics — Pages, Frames, and the Indexed Page Table
21. Glossary of Key Terms
22. Summary Tables
23. Concepts in Practice — Explained
24. Big-Picture Takeaways
25. **[Added]** What Was Missing — Gap Summary

---

## 1. Learning Objectives

By the end of this material, you should be able to:
- Discuss **key concepts related to memory**.
- Evaluate **dynamic storage management solutions**.
- Discuss the **differences between virtual and physical memory**.

---

## 2. Memory Multiplexing — The Big Picture

### 2.1 Why Multiplexing Is Necessary
- **Different processes and threads share the same hardware** — the same CPU, the same physical memory, the same disks/devices.
- Therefore, the OS must **multiplex**:
    - The **CPU** → **temporal** execution (time-sharing, covered in scheduling).
    - **Memory** → **spatial** access (the focus here).
    - **Disks and devices** → shared access over time.
- **Recall:** the complete "working state" of a process (or kernel) is defined by its **data** — memory contents, register values, and disk state.

### 2.2 The Safety/Security Motivation
- For **safety, security, and reliability**, processes should be **barred from accessing each other's memory**.
- **Memory multiplexing** = dividing the capacity of the memory "communication channel" into **multiple logical channels**, one (or more) per process.

### 2.3 Four Critical Concepts in Memory Multiplexing

```
        Memory Multiplexing
        /      |      |      \
  Isolation Sharing Virtual-  Utilization
                     ization
```

1. **Isolation** — preventing processes from interfering with each other's execution/data.
2. **Sharing** — allowing controlled, deliberate overlap when desired.
3. **Virtualization** — giving each process the illusion of its own private memory.
4. **Utilization** — making optimal use of the limited physical resources available.

The rest of this section explores each of these (plus fragmentation, linking, storage management, virtual memory, and paging) in depth.

---

## 3. Isolation Mechanisms

### 3.1 Why Isolation Matters
- Multiple programs run **concurrently** on the same CPU and memory — isolation ensures they operate **independently**, without interfering with each other's execution or data.
- **In memory multiplexing specifically**, isolation is achieved via technologies that prevent **distinct process states from colliding in physical memory** due to unintended overlap ("**overlap control**").
- **Concrete goals:**
    - Prevent process **P1 from spying on** process **P2**.
    - If **P1 has a bug**, ensure that bug **doesn't impact P2**.

### 3.2 Four Key Isolation Mechanisms

#### (a) User/Kernel Mode Flag
- A **register** representing the CPU's current mode: **user mode** or **kernel mode**.
- **Boot sequence:** the CPU **boots in kernel mode**, marking the flag accordingly.
- **When the user starts an application:** the CPU marks the flag as **user mode**.

#### (b) Address Space Boundaries
- **Protect the kernel and address-space programs from each other** — i.e., enforce that a user program's address space cannot reach into kernel memory (or another process's memory) without going through a controlled mechanism.

#### (c) System Call Interface
- The **programming interface** application code uses to request kernel-mode services.
- **Mechanism:** a system call is **executed in user mode** to **request** that **kernel mode** perform a specific action (e.g., via a `syscall()` function).

```
   User Mode                    Kernel Mode
   [Application]                [OS Kernel]
        │                             │
        │──── syscall() request ─────▶│
        │                             │  (performs requested action)
        │◀──── result returned ───────│
```

- **Purpose:** the system call interface acts as an **isolation mechanism** — it prevents user-mode processes and kernel-mode code from **overlapping or colliding** directly; all cross-boundary interaction is mediated through this controlled interface.

#### (d) Time Slicing
- (Detailed separately in Section 4, since it's substantial enough to warrant its own worked example.)
- **In brief:** provides each process a bounded **time frame** to run under **preemptive multitasking**, ensuring every process gets a turn.

---

## 4. Time Slicing (Worked Example)

### 4.1 The Mechanism
- Under **preemptive multitasking**, each process is given a **time slice** — a bounded window in which it may run.
- **If the process finishes its job before the time slice ends:** it **releases the CPU voluntarily** and does **not** need to be swapped out.
- **If the time slice ends and the process hasn't finished:** the CPU **shifts it to the end of the process queue**, to be resumed later.

### 4.2 Worked Example

**Setup:**
- **P1:** execution time = 3 ms.
- **P2:** execution time = 4 ms.
- **P3:** execution time = 2 ms.
- **Time slice = 2 ms.**

**Walkthrough (round-robin with 2ms slices):**

| Time Slot | Process Running | Remaining Time After Slot |
|---|---|---|
| 0–2ms | P1 | P1: 3−2 = 1ms left |
| 2–4ms | P2 | P2: 4−2 = 2ms left |
| 4–6ms | P3 | P3: 2−2 = 0ms left → **P3 completes** |
| 6–7ms | P1 (remaining 1ms) | P1: 1−1 = 0ms left → **P1 completes** |
| 7–9ms | P2 (remaining 2ms) | P2: 2−2 = 0ms left → **P2 completes** |

- **P3 finishes first** (at t=6ms) — it needed only 2ms, so it completes in exactly **one** time slice.
- **P1 finishes next** (at t=7ms) — it needed 3ms total: one full 2ms slice, then a partial 1ms slice to finish (releasing the CPU early once done, since it didn't need the full second slice).
- **P2 finishes last** (at t=9ms) — it needed 4ms total, exactly **two full 2ms time slices**.

```
Queue-based visualization (■ = active process, □ = empty/completed slot):

Slot 1 (0-2ms):  [P1][P2][P3]  → P1 runs
Slot 2 (2-4ms):  [P2][P3][P1*] → P2 runs (P1* = P1 re-queued with 1ms left)
Slot 3 (4-6ms):  [P3][P1*][P2*]→ P3 runs, COMPLETES, removed from queue
Slot 4 (6-7ms):  [P1*][P2*]    → P1 runs its remaining 1ms, COMPLETES
Slot 5 (7-9ms):  [P2*]         → P2 runs its remaining 2ms, COMPLETES
```

- This illustrates the **general round-robin time-slicing pattern**: processes cycle through the queue, each getting bounded turns, with unfinished processes re-queued at the end until they complete.

---

## 5. Sharing

### 5.1 Definition
- **Sharing** occurs when **multiple processes can use the same piece of data concurrently**.

### 5.2 Why Allow Sharing?
- The option to **overlap** processes' access to data should be available **when desired**, for two reasons:
    1. **Efficiency.**
    2. **Communication** (processes explicitly cooperating need a way to exchange data).

### 5.3 The Performance Benefit
- **Memory sharing improves system performance** because:
    - Data is **not copied** from one address space to another.
    - **Memory allocation happens only once**, rather than being duplicated per process.

---

## 6. Virtualization

### 6.1 Definition (Memory Virtualization)
- **Virtualization** (with respect to memory) gives an application the **impression** that it has its **own logical memory**, **independent** of the actual physical memory available.
- **Core need:** create the **illusion** of more resources than actually exist in the underlying physical system.

### 6.2 Two Approaches to Memory Virtualization

#### (a) Full Virtualization
- **Definition:** multiple **operating systems** run **concurrently** on a **single physical machine**, **fully isolated** from each other, by **emulating hardware resources** through a **hypervisor**.
- **Key requirement:** in full virtualization, **all guest OSes expect contiguous physical memory starting from physical address 0** — the hypervisor must maintain this illusion for each guest OS independently.

#### (b) Guest Modification
- **Definition:** altering the **guest OS** (or its configuration) to improve **compatibility, performance, or integration** with the virtualization environment/hypervisor.
- **Specific technique:** modifying the guest OS to **avoid using instructions that virtualize inefficiently** — i.e., adapting the guest's own code to play more nicely with virtualization, rather than relying solely on the hypervisor to paper over inefficiencies.
- **Underlying goal:** ensure **optimal use of limited resources**, guaranteeing a **high level of utilization**.

```
              Full Virtualization              Guest Modification
   ┌─────────────────────────────┐    ┌─────────────────────────────┐
   │  Guest OS A  │  Guest OS B  │    │  Guest OS (MODIFIED to      │
   │  (unmodified)│ (unmodified) │    │  avoid inefficient virt.    │
   ├─────────────────────────────┤    │  instructions)              │
   │      Hypervisor             │    ├─────────────────────────────┤
   │  (emulates hardware for     │    │      Hypervisor             │
   │   each guest independently) │    └─────────────────────────────┘
   ├─────────────────────────────┤
   │    Physical Hardware        │
   └─────────────────────────────┘
```

---

## 7. Fragmentation

### 7.1 The Underlying Problem
- Processes use **different amounts of memory**, and their memory needs **change over time**.
- **Fragmentation** occurs when memory blocks **cannot be allocated to processes** due to their small size — the blocks remain **unused**.

### 7.2 Internal Fragmentation
- **Definition:** occurs when a process is **allocated a block larger than it actually needs** — the **leftover portion** of that block remains **allocated but unused**.
- **Key characteristic:** the wasted space is **inside** an allocated block, "trapped" and unusable by any other process.

### 7.3 External Fragmentation
- **Definition:** occurs when the **total space needed** for a process **is available** somewhere in memory, but **cannot be used** because it is **not contiguous** (i.e., scattered across multiple small, non-adjacent free regions).
- **Key characteristic:** enough **total** free memory exists, but no **single contiguous block** is large enough.

```
Internal Fragmentation:
  [ Allocated Block (100 KB) ]
  [ Process uses: 70 KB ][ WASTED: 30 KB, unused but reserved ]

External Fragmentation:
  [Used][ Free: 10KB ][Used][ Free: 15KB ][Used][ Free: 8KB ]
  → Process needs 25KB contiguous — NONE of the individual free
    chunks (10, 15, 8 KB) is big enough, even though 10+15+8=33KB
    total free space exists somewhere in memory.
```

---

## 8. Linkers and Dynamic Linking

### 8.1 What Is a Linker?
- A **linker** is a software tool the OS uses to **combine object files into an executable file**.
- **Key function — name resolution:** matching the **name** of a variable or function in an application to the **virtual memory address** it will have when loaded and run.
- **Overall job:**
    1. Combine many **separate pieces** of a program.
    2. **Reorganize storage allocation** so all pieces fit together.
    3. **"Touch up" addresses** so the program can run correctly under its new, combined memory organization.

```
  Object files ──┐
                  ├──▶ [ LINKER ] ──▶ .exe (executable)
  Libraries ──────┘         │
                             ├──▶ Library (shared)
                             └──▶ .dll (dynamic-link library)
```

- After the linker combines multiple **compiler-generated object files** into a **single executable**, that executable can then be **loaded and executed** by the OS.

### 8.2 Runtime Memory Layout (Linux Example)
- When a process **starts running**, the allocation process begins by dividing memory into smaller parts called **segments**.
- **Linux's memory layout convention:**
    - **Code** starts at location **0**.
    - **Data** starts **immediately above the code**, and **grows upward**.
    - **Stack** starts at the **highest address**, and **grows downward**.

```
Highest address   ┌──────────────┐
                  │    Stack     │  ← grows DOWNWARD
                  │      ↓       │
                  │  (free space)│
                  │      ↑       │
                  │     Data     │  ← grows UPWARD
Location 0        │     Code     │
                  └──────────────┘
```

- **At process start:** the OS **loads the file into memory**, with the option to **share memory with other processes** where applicable.
- **At runtime:** the OS **facilitates dynamic memory sizing** — adding more assigned memory as needed while the process runs.

### 8.3 Dynamic Linking

- **Definition:** the mechanism that allows a program to **associate external code symbols with addresses at runtime** (rather than at compile/link time).
- **In dynamic linking:** the code is **located and loaded when the program is first run**, not baked permanently into the executable ahead of time.
- **Historical context:** since the **late 1980s**, most systems have supported **shared libraries and dynamic linking** — keeping only a **single copy** of common library packages in memory, **shared by all processes** that need them.
- **Key implication:** the system **doesn't know where the library is loaded until runtime**, and must **resolve references dynamically** as the program executes.

### 8.4 Why Dynamic Linking Matters
- **Memory efficiency:** rather than every process embedding its own private copy of a common library (e.g., the C standard library), **one shared copy** in memory serves **all** processes using it — significant memory savings at scale.
- **Trade-off:** requires runtime address resolution, adding some complexity/overhead compared to fully static linking (where all addresses are fixed at link time).

---

## 9. Dynamic Storage Management

### 9.1 The Two Basic Operations
Dynamic storage management exists to satisfy varying memory needs via two fundamental operations:
1. **Allocate** a block with a given number of bytes.
2. **Free** a previously allocated block.

### 9.2 Two General Approaches

| | **Stack Allocation** | **Heap Allocation** |
|---|---|---|
| **Structure** | Hierarchical, restricted (LIFO) | General tree-based structure |
| **Simplicity** | Simple and efficient | More difficult to implement, less efficient |
| **Predictability requirement** | Works when allocation/freeing is **partially predictable** | Used when allocation/release is **NOT predictable** |

### 9.3 Stack Allocation

- **Definition:** a **linear data structure** following **LIFO order** (Last In, First Out).
- **Key property:** memory is **freed in the opposite order from allocation**.
- **Classic example:** if procedure **X calls Y**, then **Y will certainly return before X returns** — this is exactly the nesting pattern stacks are built to exploit.
- **Use case:** stacks store the **state of the current procedure call** — taking advantage of this predictable "nested call/return" programming pattern.

```
   Push →  ┌──────────┐  ← top of stack
           │  Data 3  │
           ├──────────┤
           │  Data 2  │
           ├──────────┤
           │  Data 1  │
           └──────────┘
   Pop  ←  (removes from the top, LIFO order)
```

### 9.4 Heap Allocation

- **Definition:** allocating data using a **tree-based data structure** called a **heap**.
- **Structural form:** a heap is represented by a **complete binary tree**.
- **Two types of heap structures:**
    - **Max heap:** the **root node** holds the **greatest value**; this property holds recursively for every subtree.
    - **Min heap:** the **root node** holds the **minimum value**; likewise recursive for every subtree.

```
        Max Heap                    Min Heap
           9                            1
         /   \                        /   \
        7     8                      3     2
       / \   / \                    / \   / \
      3   5 6   4                  7   5 8   4
```

### 9.5 An Important Clarification: What Memory Managers ACTUALLY Do
> **Memory managers, such as those used in C and C++, do NOT actually store available memory in a heap data structure** (in the tree sense above).

- Instead, they manipulate a **doubly linked list of blocks**, which they **confusingly** also refer to as a **"heap"** (this is a well-known terminology collision — "heap" the tree data structure vs. "heap" the memory-management region are **different concepts** sharing the same name!).
- These memory managers attempt to **optimize memory usage** via a **"best fit"** method (choosing the free block that most closely matches the requested size, to minimize wasted space).

### 9.6 When Is the (Linked-List) "Heap" Approach Used?
- Used when the **allocation and release of memory are NOT predictable** — i.e., it's **not clear in advance** how much memory will be needed until the program actually runs.
- **Typical use case:** storing **data structures that change size over time**, based on how many elements are added or removed.
- **Structure of heap memory:** consists of **allocated areas** and **free areas** (sometimes called **"holes"**).

```
Heap Memory Layout (linked-list style):
[Allocated][ Hole ][Allocated][Allocated][ Hole ][Allocated][ Hole ]
```

---

## 10. Virtual Memory

### 10.1 Definition and Purpose
- **Virtual memory** is the key OS component that **ensures process isolation** by guaranteeing that **each process gets its own view of memory**.
- **The illusion provided:** a running program (process) has a **seemingly infinite view of memory**, and can access **any region** without worrying about **other concurrently running programs**.

### 10.2 Address Translation
- The OS **seamlessly translates** each process's memory request into a **separate region of physical hardware memory** — this is **address translation**.
- **When does address translation occur?** Whenever the system needs to **find a physical address** in memory that **matches** a given **virtual address**.
- **Key privacy/abstraction property:** the running process **only ever deals with virtual addresses** — it **never sees the physical address** directly.

### 10.3 Pages
- **Virtual memory is mapped to physical memory in units called "pages."**

```
Process's view:              Actual physical memory:
┌──────────────────┐         ┌──────────────────┐
│ Virtual Address  │         │                  │
│      Space       │ ──────▶ │  Physical Memory │
│ (seemingly       │ address │  (finite, shared │
│  infinite,       │ transl- │   among all      │
│  private)        │ ation   │   processes)     │
└──────────────────┘         └──────────────────┘
       (in units of pages, mapped via address translation)
```

---

## 11. The Translation Lookaside Buffer (TLB)

### 11.1 The Performance Problem
- There is a **time cost** associated with performing **virtual-to-physical address translation**.
- This cost **adds up significantly**, since **most programs constantly need memory access** to store/retrieve data.

### 11.2 The Solution: TLB Caching
- To **speed up address translation**, the CPU has **dedicated hardware** for **caching recent address translations** — the **Translation Lookaside Buffer (TLB)**.
- **Definition:** a TLB is a **memory cache** that stores **recent virtual-to-physical memory translations**.
- **Benefit:** TLBs help the CPU **avoid making multiple round trips to main memory** just to resolve a **single** virtual memory access — reducing it to **only one round trip** (in the common case).

### 11.3 How the TLB Works — Hits and Misses

- A TLB contains **page table entries that have been most recently used**.
- **Given a virtual address**, the processor **examines the TLB**:
    - **If the page table entry IS present** → this is a **"hit."** The **frame number is retrieved**, and the **real (physical) address is formed** directly.
    - **If the page table entry is NOT found** → this is a **"miss."** In this case:
        1. The **page number is used as an index** while processing the **full page table** (a slower lookup).
        2. The TLB **checks if the page is already in memory**.
        3. **If it's not in memory** → a **page fault** is issued (see Section 13).
        4. The TLB is then **updated** to include the **new page entry** (so future accesses to that page will be a "hit").

```
             Virtual Address
                   │
                   ▼
          ┌─────────────────┐
          │   Check TLB     │
          └─────────────────┘
               │        │
            HIT│        │MISS
               ▼        ▼
      [Retrieve frame  [Index full page table]
       number directly,        │
       form real address]      ▼
                        [Page in memory?]
                          │        │
                       YES│        │NO
                          ▼        ▼
                   [Form real   [PAGE FAULT —
                    address]     see Section 13]
                                    │
                                    ▼
                          [Update TLB with
                           new page entry]
```

---

## 12. Demand Paging

### 12.1 Paging — General Definition
- **Paging** is the storage mechanism that uses **pages** to retrieve a process from **secondary (virtual) memory** into **main memory**.

### 12.2 The Problem Virtual Memory Doesn't Solve By Itself
- Virtual memory gives the **illusion** of near-infinite memory — but what happens when the OS **actually runs out of free physical memory**?
- **Modern OS backup plan:** when **DRAM runs out**, **virtual memory can be mapped to disk** to meet demand.

### 12.3 Demand Paging — Definition
> **Demand paging** is the storage mechanism in which **pages are only allocated in memory if required** by the executing process.

- I.e., pages are loaded **lazily**, on an as-needed basis, rather than all upfront.

### 12.4 Swap In / Swap Out

```
        Virtual Memory (disk/backing store)
              │              ▲
     swap in  │              │  swap out
    (demand)  ▼              │
              Main Memory (DRAM)
                    │
                    ▼
                   CPU
```

- **Swap in:** the CPU **demands** pages from virtual memory, bringing them **into** main memory.
- **Swap out:** pages are **released** from main memory, sent **back to** virtual memory (disk), typically to free up space for incoming pages.

### 12.5 Working Set Size (WSS)

- **Definition:** the **total amount of memory** a process requires during a **specific period of activity**, measured as the **set of pages/data blocks** the process actually accesses.
- **How it's measured:** by tracking the **unique pages** a process references over a **fixed interval of time**.
- **Why it matters:** WSS provides an **estimate of a process's active memory footprint**, helping inform memory-management decisions like **paging and swapping**, to **optimize performance and resource allocation**.

---

## 13. Page Faults and Page Fetching/Prefetching

### 13.1 What Is a Page Fault?
> A **page fault** occurs when the **CPU demands a page** and that page is **NOT present in main memory** — specifically, when a process references a page that is (instead) in the **backing store** (disk).

### 13.2 Handling a Page Fault — Step by Step

1. **Control transfers** from the running program to the **OS** (to handle the fault).
2. The OS **finds a free page frame** in memory.
3. The OS **loads the page** from the backing store **into main memory**.
4. The OS **resumes execution** of the thread.

- **Hardware support:** the CPU has **special hardware** to assist in **resuming execution correctly** after a page fault (ensuring the interrupted instruction can be properly restarted or completed).

```
  Process references a page NOT in main memory
                    │
                    ▼
          [PAGE FAULT triggered]
                    │
                    ▼
     Control transfers to the OS
                    │
                    ▼
     OS finds a free page frame in memory
                    │
                    ▼
     OS loads the page from backing store → main memory
                    │
                    ▼
     Execution of the thread RESUMES
```

### 13.3 Page Fetching
- **Definition:** the process of **bringing pages into memory** (i.e., demand paging in action) is called **page fetching**.
- **Typical modern OS behavior:** start the process with **NO pages loaded**, and do **not** load a page into memory **until it is referenced** — a strictly **lazy/on-demand** approach.

### 13.4 Prefetching
- Since **disk access is much slower than DRAM access**, OSes are often designed to **predictively swap** in-use pages into DRAM and out-of-use pages to disk.
- **Prefetching:** the act of trying to **predict when pages will be needed** and **loading them ahead of time**, in order to **avoid page faults** before they happen.
- **Contrast with pure demand paging:** demand paging is strictly reactive (load only when referenced); prefetching adds a **proactive, predictive** element on top, to reduce the number of costly page faults that actually occur.

---

## 14. Page Replacement and Thrashing

### 14.1 Page Replacement
- **When is it necessary?** If **all memory is in use**, the OS must **throw out one page** every time there's a **page fault**, to make room for the newly needed page.
- **Mechanism:** one page in the (full) DRAM is **swapped to disk**, while the **requested page** is brought **into** DRAM — a direct swap.

### 14.2 The Problem: Thrashing

> **Thrashing** occurs when a computer's OS becomes **overwhelmed** by the number of processes requesting memory, leading to a cycle where the system spends **more time moving data** between physical memory and disk (**paging/swapping**) than **executing actual processes**.

- **Mechanism of the vicious cycle:** each page fault causes one of the **active** pages to be moved to disk — but this "evicted" page may soon be needed again, causing **another page fault shortly after** — and the cycle **repeats and compounds**.
- **Restaurant analogy (from the text):** *"It's like a busy restaurant where the staff spends more time rearranging tables than serving food."*
- **Primary cause:** **too many programs running simultaneously**, collectively demanding more memory than is **physically available**, forcing the system to **constantly shuffle data** to/from disk just to make room for new requests.

### 14.3 Strategies to Prevent Thrashing

1. **Limit the number of simultaneously running programs** — to avoid **memory overcommitment**.
2. **Optimize how memory is allocated** to processes.
3. **Increase the system's physical memory** (more DRAM = less pressure to swap).

- **Net effect of managing memory efficiently:** avoids slowdown **and** can **significantly improve overall system performance**.

### 14.4 The Extreme Case
- In **severe thrashing**, the OS can spend **ALL its time** fetching and replacing pages, getting **almost no actual work done**.
- **This is precisely why devices can "slow to a halt"** when they run out of memory — every thread ends up **waiting on requested pages**, rather than executing.

---

## 15. Memory Optimization Criteria and the Memory Hierarchy

> *This entire section was missing from the original notes — it covers foundational material on why memory hierarchy exists at all, which underpins everything else in this document.*

### 15.1 The Two Core Components
- Every computer's capabilities are fundamentally defined by **two main components**: the **CPU** and **Memory**.
- Having covered CPU topics (scheduling, deadlocks) elsewhere, this material focuses on **Memory** — its problems, issues, and solutions.

### 15.2 Three Competing Optimization Criteria
To optimize computational performance, three criteria matter:

1. **Size** — larger memory is desirable, so it can handle/run larger programs.
2. **Access Time** — smaller access time is desirable, so the machine performs faster.
3. **Per-Unit Cost** — lower cost is desirable.

### 15.3 Why You Can't Have All Three at Once
> **These three desires directly contradict one another.**

- You **cannot** have a memory that is simultaneously **large**, **fast (low access time)**, **and** **cheap**.
- **The core trade-off:** if memory size is large, there is more "search space," so it takes **more time** to locate specific data.
- **Rule of thumb:** *"Increasing the size of the memory will increase the access time, and if we decrease the access time, then we need to reduce the memory size."*
- **Conclusion:** it is **practically impossible** to have a single large memory that is also low-cost and fast.

### 15.4 The Memory Hierarchy — Resolving the Trade-off

- To achieve **all three** traits (large size, small access time, low cost) **simultaneously** — not in one memory, but across a **system** of memories — the **memory hierarchy** concept is introduced.

```
        CPU
         │
         ▼
   Cache Memory     (small, very fast, expensive per byte)
         │
         ▼
   Main Memory       (medium size/speed/cost)
         │
         ▼
  Secondary Memory   (large, slow, cheap per byte)
```

### 15.5 Locality of Reference — The Key Enabling Concept

- The memory hierarchy exploits **"locality of reference"** to deliver all three optimization traits together.

**Illustrative example:**
- A program has **1000 instructions**, mainly stored on **Secondary Memory**.
- The CPU is currently executing instruction **I200**.
- The system will **pre-fetch** the next few instructions (e.g., **I201–I220**) into **Cache Memory**, and some more (e.g., **I221–I250**) as well — **reducing access time** for the CPU.
- **Result:** the system does **NOT** need to access Secondary Memory to get instructions for the process **currently executing** — because nearby instructions (in time and in address) have already been staged closer to the CPU.
- **This achieves BOTH small access time AND large storage** simultaneously, by exploiting the fact that recently/soon-to-be-used data tends to cluster.

### 15.6 Calculating Overhead Access Time via Hit Ratio

- We can calculate the CPU's **overhead access time** using the **hit ratio** of the main memory.

**Worked Example:**
- **Hit ratio** = 90% (data found in Main Memory 90% of the time).
- **Main memory access time** = 10 ms.
- **Secondary memory access time** = 100 ms.

**Formula and calculation:**
$$
\text{Average Access Time} = 0.9 \times (10) + 0.1 \times (10 + 100)
$$
$$
= 9 + 0.1 \times 110 = 9 + 11 = 20 \text{ ms}
$$

- **Interpretation:** 90% of the time, the CPU only pays the **main memory** access cost (10ms). The other 10% of the time (a **miss**), the CPU must **first** check main memory (10ms), **then** go to secondary memory (100ms) — hence the **10 + 100** term for the miss case.
- **Result:** the effective average access time (20ms) is **much closer to the fast main-memory time (10ms)** than to the slow secondary-memory time (100ms) — precisely because **most** accesses hit the faster tier.

### 15.7 The Two Central Questions This Material Answers

1. **How do processes/instructions move from Secondary Memory to Main Memory?** — This is the **memory allocation** problem (contiguous or non-contiguous), covered in Sections 16–18 below.
2. **How does the CPU's generated address get converted for use by Main Memory?** — The CPU generates a **Logical Address (LA)**, understandable in the context of secondary-memory-relative addressing, but Main Memory only understands **Physical Address (PA)**. Since the CPU mostly interacts with Main Memory (which needs PA), **address translation** is required — covered in Sections 19–20 below.

---

## 16. Contiguous vs. Non-Contiguous Memory Allocation

> *This section, and its explicit CMA/N-CMA terminology, was entirely missing from the original notes.*

### 16.1 Contiguous Memory Allocation (CMA)

- **Definition:** move **all** the instructions/instances of a process and place them **together**, in one contiguous block.
- **Classic example:** declaring an **array** — array elements are allocated in a contiguous fashion (all together).
- **Benefit:** contiguous memory offers **smaller access time (faster)** than competing schemes, since elements are organized in sequence and any element can be **accessed directly**.

#### The Problem: External Fragmentation
- CMA **creates external fragmentation**, where memory becomes saturated in a way that prevents new instructions/processes from being stored, **even though enough total free space exists**.

**Worked Example:**
- Total memory = **10 KB**.
- Process **P2** already occupies **2 KB**.
- Remaining free memory = **8 KB**, but split into **two separate 4 KB chunks** (not contiguous).
- **New process P1 = 5 KB** needs to be stored — even though **8 KB** total is free, **no single contiguous chunk** is large enough (each chunk is only 4 KB) to hold the 5 KB requirement.

```
10 KB total:
[ 4 KB free ][ 2 KB: P2 ][ 4 KB free ]
       ↑                        ↑
   Neither 4KB chunk can hold P1 (5KB) — even though 8KB total is free!
```

- **Conclusion:** CMA **always suffers from** external fragmentation.

### 16.2 Non-Contiguous Memory Allocation (N-CMA)

- **Definition:** break process instructions into **small chunks**, which can be placed at **different (scattered) locations** in Main Memory.
- **Classic example:** the **linked list** — nodes are interconnected via **pointers**; the first node knows the address of the second, the second knows the address of the third, and so on.

```
[First Node] → [N_i] → [N_i+1] → ... → [Last Node (N_n)]
   (each node holds a pointer to the next node's address)
```

#### Trade-off: Slower Access, But No External Fragmentation
- **Downside:** to access the **last node (N_n)**, you **cannot** access it directly — you must traverse the list **linearly**, making the system **somewhat slower**.
- **Upside:** because process instructions can be placed **non-contiguously**, there will be **NO external fragmentation**.

### 16.3 Summary Comparison

| | CMA | N-CMA |
|---|---|---|
| **Placement** | All instructions together, contiguous | Instructions scattered in chunks |
| **Access speed** | Fast (direct access) | Slower (often requires traversal/lookup) |
| **External fragmentation?** | YES — always a risk | NO |
| **Classic example** | Array | Linked list |

---

## 17. Fixed vs. Variable Sized Partitioning

> *This section — covering the two specific ways to implement CMA — was missing from the original notes.*

There are **two methods** to implement Contiguous Memory Allocation:

### 17.1 Fixed Sized Partitioning

- **Definition:** Main Memory is divided into a set of **fixed-sized partitions**. (Note: the partitions themselves are **not required to be of equal size** to each other — just fixed **once established**.)
- A process moving from Secondary Memory to Main Memory occupies an **entire memory slice/partition**, with all its instructions **collocated**.

#### The Problem: Internal Fragmentation (in addition to external)
- **Worked Example:** Memory = 10 KB, divided into **four fixed partitions**: 5 KB, 2 KB, 2 KB, 1 KB.
- If we want to store **Process P1 = 4 KB**, it must be allocated to a partition **large enough** to hold it — e.g., the **5 KB** partition.
- **Problem:** the **entire 5 KB partition** is now reserved for P1, even though P1 only needs 4 KB — the remaining **1 KB cannot be reused** by any other process (since only **one process per partition** is allowed).
- **This wasted, unusable space WITHIN an allocated partition is internal fragmentation.**

```
Fixed Partitions:  [ 5 KB ][ 2 KB ][ 2 KB ][ 1 KB ]
P1 (4 KB) → placed in the 5 KB partition:
                   [ P1: 4KB used | 1KB WASTED (internal frag) ][ 2 KB ][ 2 KB ][ 1 KB ]
```

- **Root cause:** internal fragmentation occurs because **the partition cannot be reused** (shared) — it's dedicated to just **one process**, regardless of how much of it that process actually uses.

### 17.2 Variable Sized Partitioning

- **Definition:** there is **no pre-defined partitioning** — a process can occupy space based on **exactly** its own requirement.
- **Key benefit:** variable sized partitioning **can NEVER suffer from internal fragmentation** (since each allocation is sized exactly to the process's need).
- **However:** **external fragmentation can still occur** (as in general CMA).

**Worked Example:**
- Memory = 10 KB (variable sized).
- Two processes: **P1 = 4 KB**, **P2 = 3 KB**.

```
[ P1: 4KB ][ P2: 3KB ][ free: 3KB ]
(no internal fragmentation — each process gets exactly what it needs)
```

### 17.3 Summary Comparison

| | Fixed Sized Partitioning | Variable Sized Partitioning |
|---|---|---|
| **Partition sizes** | Pre-defined, fixed | Determined by actual process need |
| **Internal fragmentation?** | YES — possible | NEVER |
| **External fragmentation?** | YES — possible | YES — possible |
| **One process per partition?** | Yes | N/A (allocated to exact size) |

---

## 18. First, Best, and Worst Fit Allocation Algorithms (Worked Examples)

> *This entire section — the three classic contiguous-allocation algorithms, applied to BOTH variable and fixed sized partitioning, with fully worked numeric examples — was missing from the original notes.*

### 18.1 The Three Algorithms — Basic Definitions

1. **First Fit ("First Algorithm"):** the system starts searching from the **first available partition**; once it finds a partition **big enough**, the process is stored there immediately (no further searching).
2. **Best Fit ("Best Algorithm"):** the **entire memory is scanned**, and the system chooses the partition that is the **closest (smallest) match** in size to the process being placed.
3. **Worst Fit ("Worst Algorithm"):** the entire memory is scanned, and the system chooses the **largest available** partition (the "worst," i.e., most oversized, match) for the process.

#### Quick Illustration
- Partitions: Pr1 = 100 KB, Pr2 = 150 KB, Pr3 = 75 KB. Process P1 = 70 KB.
    - **First Fit** → placed in **Pr1** (first partition encountered that fits, scanning in order).
    - **Best Fit** → placed in **Pr3** (75 KB is the closest-fitting size ≥ 70 KB).
    - **Worst Fit** → placed in **Pr2** (150 KB is the largest available partition).

### 18.2 Worked Example — Variable Sized Partitioning

**Setup:** Four processes: P1=300 KB, P2=25 KB, P3=125 KB, P4=50 KB.
**Initial free memory blocks:** 150 KB, 300 KB, 350 KB, 600 KB (in that left-to-right order).

#### First Fit Result
- **P1 (300 KB)** → placed in the **first** block big enough: the **350 KB** block (150 KB and 300 KB blocks are skipped/considered in order — 350 KB is the first that fits 300 KB, per the example).
- **P2 and P3** → placed into the **150 KB** block (in sequence, as they fit).
- **P4** → placed into the **remaining space** of the 350 KB block (after P1 was placed there).

#### Best Fit Result
- **P1 and P2** → placed into the **600 KB** block (best remaining fit among available options in this scan).
- **P3** → **best fits** with the **150 KB** block.
- **P4** → **NO** contiguous space remains that fits — **external fragmentation of 50 KB** results.

#### Worst Fit Result
- **P1** → placed into the **600 KB** block (the "worst"/largest fit — deliberately oversized).
- **P2 and P3** → placed into the **350 KB** block.
- **P4** → placed into the **remaining** space of the 600 KB block.
- **Result: NO external fragmentation** in this particular example.

#### Key Takeaway from This Example
> In this specific example, **Worst Fit** avoids external fragmentation, while **Best Fit** produces **50 KB** of external fragmentation. This illustrates that **"best" fit is not always actually best** in terms of avoiding fragmentation — the outcome is highly **workload/scenario dependent**.

### 18.3 Worked Example — Fixed Sized Partitioning

**Setup:** Four processes: P1=357 KB, P2=210 KB, P3=468 KB, P4=491 KB.
**Fixed partitions:** 200 KB, 400 KB, 600 KB, 500 KB, 300 KB, 250 KB.

#### First Fit Analysis
- **Internal Fragmentation:** 43 + 390 + 32 = **465 KB**.
- **External Fragmentation:** **491 KB** (P4 cannot be stored — no single partition of 491 KB or more remains available in contiguous form).

#### Best Fit Analysis
- **Internal Fragmentation:** 43 + 109 + 32 + 40 = **224 KB**.
- **External Fragmentation:** **0 KB**.

#### Worst Fit Analysis
- **Internal Fragmentation:** 243 + 290 = **533 KB**.
- **External Fragmentation:** 468 + 491 = **959 KB**.

#### Conclusion
> **Best Fit proves to be the best algorithm** in this **fixed-sized partitioning** scenario — it achieves the **lowest internal fragmentation** (224 KB, vs. 465 KB for First Fit and 533 KB for Worst Fit) **and zero external fragmentation** (vs. 491 KB for First Fit and a massive 959 KB for Worst Fit).

### 18.4 Summary — Algorithm Performance Comparison

| Algorithm | Variable Sized: External Frag. | Fixed Sized: Internal Frag. | Fixed Sized: External Frag. |
|---|---|---|---|
| **First Fit** | (scenario dependent) | 465 KB | 491 KB |
| **Best Fit** | 50 KB (in worked example) | **224 KB** (lowest) | **0 KB** (lowest) |
| **Worst Fit** | 0 KB (in worked example) | 533 KB | 959 KB |

> **Important nuance:** these results are **example-specific**, not universal laws — as shown, Best Fit performed *worse* than Worst Fit for external fragmentation in the **variable-sized** example, but performed **best overall** in the **fixed-sized** example. Algorithm choice should always be validated against the **actual expected workload**.

---

## 19. Address Translation for Contiguous Memory Allocation

> *This entire section — covering the limit register, relocation register, and the specific PA = LA + RR formula with a worked legitimacy-check example — was missing from the original notes.*

### 19.1 The Core Problem
- The CPU generates a **Logical Address (LA)** to fetch instructions.
- Main Memory only understands **Physical Address (PA)**.
- Therefore, the LA must be **converted/translated** to a PA.

### 19.2 The Mechanism — Limit Register and Relocation Register

- **In Contiguous Memory Allocation, address translation is extremely easy** — it requires a small data structure holding two key values:
    1. **Limit Register:** the **maximum number** of addressable instructions/registers in Main Memory **for that specific process** (i.e., an upper bound / legitimacy check).
    2. **Relocation Register:** the **starting address** of the process in Main Memory (i.e., the base address to which the logical offset is added).

### 19.3 The Translation Process

```
                          ┌──────────────┐
Logical Address ────────▶ │  Compare to  │
                          │Limit Register│
                          └──────┬───────┘
                       LA ≤ LR?  │
                    ┌────YES─────┴─────NO────┐
                    ▼                         ▼
         Physical Address =              TRAP
         LA + Relocation Register    (illegal/out-of-bounds
                    │                  request rejected)
                    ▼
              Sent to Main Memory
```

**Formula:**
$$
\text{Physical Address (PA)} = \text{Logical Address (LA)} + \text{Relocation Register (RR)}
$$

- **Legitimacy check:** if the requested LA is **≤** the Limit Register, the request is **legitimate**, and the Relocation Register is **added** to compute the PA.
- **If LA > Limit Register:** the request is **illegal** — it is **rejected** by a **trap** (the CPU is interrupted/signaled that an out-of-bounds access was attempted).

### 19.4 Worked Example

**Setup:**
- Process **P1** is running; CPU is currently executing instruction at **Logical Address = 20**, and wants to fetch the next instruction at **Logical Address = 21**.
- **Limit Register (max instructions for this process) = 100.**
- **Relocation Register (starting address of P1 in Main Memory) = 500.**

**Step 1 — Legitimacy check:**
$$
21 \leq 100 \quad \text{✓ (legitimate request)}
$$

**Step 2 — Compute Physical Address:**
$$
PA = LA + RR = 21 + 500 = 521
$$

**Contrasting case — an illegal request:**
- If the CPU instead generates **Logical Address = 110**:
  $$
  110 \geq 100 \quad \text{(exceeds the Limit Register!)}
  $$
- This request is **turned down** by the limit register check, and a **TRAP** is issued — the CPU does **not** proceed to compute a physical address for this out-of-bounds request.

### 19.5 Why This Matters
- This limit-register/relocation-register mechanism is precisely **how contiguous memory allocation enforces protection** — it prevents a process from accidentally (or maliciously) accessing memory **outside its own allocated region**, since any LA beyond the Limit Register is **rejected before** any physical address is ever computed.

---

## 20. Paging Mechanics — Pages, Frames, and the Indexed Page Table

> *This section adds concrete terminology (page vs. frame), the linked-list address-translation approach, and the indexed page table mechanism with its worked example — details that were missing from (or only partially covered in) the original notes' more abstract virtual-memory discussion.*

### 20.1 Paging as an Implementation of N-CMA

- **Non-Contiguous Memory Allocation** can be implemented via **paging** or **segmentation**; this material focuses on **paging**.
- **To avoid external fragmentation**, N-CMA does **not** require that all instructions of a process be placed together.

### 20.2 Setting Up Paging — Key Rules

1. **Both Secondary Memory and Main Memory are partitioned.**
2. **Each partition/block is of a FIXED size** — since block size is fixed, there **will be some internal fragmentation**, but **NO external fragmentation**.
3. **The partition sizes in Secondary Memory and Main Memory must be EQUAL.**

### 20.3 Terminology: Page vs. Frame

| Term | Definition |
|---|---|
| **Page** | A partition of **Secondary Memory**. |
| **Frame** | A partition of **Main Memory**. |

- **Pages and frames are the SAME size**, specifically to **avoid misconfiguration** when instructions move from secondary to main memory.
- A process's instructions can be stored **contiguously OR non-contiguously** across pages in Secondary Memory (contiguous page layout is not mandatory).

### 20.4 Why N-CMA Address Translation Is Harder Than CMA's

- **In CMA:** you only need the **Base Address (BA)** of the process; **PA = BA + LA** works because everything is sequential/collocated.
- **In N-CMA:** pages (containing the process's instructions) are stored at **scattered, effectively random** locations in Main Memory — so the simple "base address + offset" trick **doesn't work**. A **different algorithmic approach** is needed to locate the correct page/frame.

### 20.5 The Logical Address Structure for Paging

- In N-CMA with paging, the **Logical Address** is divided into **two parts**:
    1. **P (Page Number)** — which page the desired instruction belongs to.
    2. **D (Instruction Offset / Displacement)** — the specific position **within** that page.

```
Logical Address = [ P (Page Number) | D (Offset within page) ]
```

### 20.6 Approach 1 — Linked List Address Translation

- Pages belonging to the **same process** are **interlinked** via address pointers: P1 knows the address of P2, P2 knows the address of P3, and so on.
- All that's needed is the **Base Address (BA) of the FIRST page** — the linked list then **resolves** the LA to a PA by traversal.
- **Major drawback:** this method is **extremely slow**, resulting in **long overall access time** (since finding a page deep in the list requires traversing all preceding pointers).

### 20.7 Approach 2 — Indexed Table (Page Table)

#### The Data Structure
- Maintain a data structure called the **Page Table (PT)**.
- **Number of entries in the PT** = number of pages the specific process has in Secondary Memory.
- **Each process has its OWN Page Table**, containing **indexes** (i.e., the **base/frame address** in Main Memory corresponding to each page).

#### How It Works — Worked Example

**Setup:**
- Process **X** is stored over **three pages**: P1, P2, P3, in Secondary Memory.
- The CPU is currently running process X, and **all three pages** have been moved from Secondary Memory to Main Memory, **non-contiguously**:
    - **P1 → Frame F4**
    - **P2 → Frame F2**
    - **P3 → Frame F5**
- The **OS creates a Page Table** for process X, recording these frame mappings.

**Translation process:**
- Whenever the CPU generates a Logical Address, the **Page Table (PT)** is consulted to **find the frame number**, and the **Physical Address is then computed**.
- **Specific request:** CPU wants to fetch **instruction 24 (I24)**, which is stored on **Page 2 (P2)**.
    - Logical Address = **[P2, D24]** (Page number 2, offset/displacement 24).
    - **Page Table lookup:** P2 → **Frame F2**.
    - **Physical Address = F2 (base) + D24 (offset)** — directly computed via the indexed table, **no traversal needed**.

```
                Page Table for Process X
  Page Number  │  Frame Number
  ─────────────┼───────────────
       P1      │      F4
       P2      │      F2
       P3      │      F5

Logical Address [P2, D24]
        │
        ▼
  Look up P2 in Page Table → Frame F2
        │
        ▼
  Physical Address = F2 + D24  (direct computation, no linked-list traversal!)
```

#### Advantage of the Indexed Table (Page Table) Approach
> **Fast Access** — unlike the linked-list approach (which requires slow, sequential traversal), the indexed Page Table allows the **frame number for any page to be looked up directly** (like a simple array/table lookup), dramatically **reducing access time** while still preserving the fragmentation benefits of non-contiguous allocation.

### 20.8 Summary — Linked List vs. Indexed Table (Page Table) for N-CMA

| | Linked List Approach | Indexed Table (Page Table) Approach |
|---|---|---|
| **How pages are located** | Traverse pointers from page to page | Direct lookup via a per-process table |
| **Speed** | Slow (sequential traversal) | Fast (direct/indexed access) |
| **Data needed** | Base address of first page only | Full table of page→frame mappings, per process |
| **Real-world relevance** | Largely a teaching/historical illustration | Reflects how real page tables work in modern OSes |

---

## 21. Glossary of Key Terms

| Term | Definition |
|---|---|
| **Memory multiplexing** | Dividing memory's communication channel capacity into multiple logical channels, one per process. |
| **Isolation** | Ensuring concurrently running programs don't interfere with each other's execution or data. |
| **User/kernel mode flag** | A CPU register indicating whether the processor is currently in user mode or kernel mode. |
| **Address space boundaries** | Protections that keep kernel and user-program address spaces separate. |
| **System call interface** | The controlled programming interface through which user-mode code requests kernel-mode actions. |
| **Time slicing** | Giving each process a bounded time window to run under preemptive multitasking. |
| **Sharing** | Multiple processes using the same piece of data concurrently, avoiding redundant copies. |
| **Virtualization (memory)** | Giving an application the illusion of its own independent logical memory. |
| **Full virtualization** | Multiple OSes run concurrently, fully isolated, via hardware emulation through a hypervisor. |
| **Guest modification** | Altering a guest OS to avoid inefficient virtualization instructions, improving performance/compatibility. |
| **Fragmentation** | The general problem of memory blocks going unused because they can't be allocated to processes. |
| **Internal fragmentation** | Wasted space WITHIN an allocated block that exceeds what the process actually needs. |
| **External fragmentation** | Enough total free memory exists, but not in one contiguous block large enough for the request. |
| **Linker** | A tool combining object files into a single executable, performing name resolution and address fix-up. |
| **Dynamic linking** | Associating external code symbols with addresses at RUNTIME, rather than at link time. |
| **Shared library** | A single in-memory copy of common library code, shared across multiple processes. |
| **Stack allocation** | LIFO-based memory allocation, simple/efficient, used when allocation/freeing is predictable. |
| **Heap allocation** | Tree-based (conceptually) memory allocation, used when allocation/freeing is unpredictable. |
| **Max heap / Min heap** | Complete binary trees where the root (and every subtree root) holds the greatest / least value, respectively. |
| **"Heap" (memory manager sense)** | In practice, a doubly linked list of memory blocks (NOT a tree), managed via "best fit" allocation — distinct from the heap data structure. |
| **Virtual memory** | The OS abstraction giving each process its own private, seemingly infinite view of memory. |
| **Address translation** | Converting a virtual address into its corresponding physical address. |
| **Page** | The unit in which virtual memory is mapped to physical memory. |
| **Translation Lookaside Buffer (TLB)** | A hardware cache storing recent virtual-to-physical address translations, speeding up access. |
| **TLB hit / miss** | Whether a requested page's translation is (hit) or isn't (miss) currently present in the TLB. |
| **Demand paging** | Allocating pages into memory only when actually required by the executing process. |
| **Swap in / Swap out** | Bringing a page from disk into main memory / sending a page from main memory back to disk. |
| **Working Set Size (WSS)** | The set of unique pages a process references over a given interval — its active memory footprint. |
| **Page fault** | An event triggered when a referenced page is not present in main memory. |
| **Page fetching** | The act of bringing a needed page into memory in response to a page fault (reactive). |
| **Prefetching** | Proactively loading pages before they're referenced, predicting future need to avoid page faults. |
| **Page replacement** | Evicting one page from full memory to make room for an incoming requested page. |
| **Thrashing** | A state where the system spends more time paging/swapping than executing actual process work. |
| **Cache Memory** *(added)* | The fastest, smallest, most expensive tier of the memory hierarchy, sitting closest to the CPU. |
| **Secondary Memory** *(added)* | The largest, slowest, cheapest tier of the memory hierarchy (e.g., disk), holding the bulk of a process's instructions/data. |
| **Locality of Reference** *(added)* | The tendency of programs to access nearby/recently-used instructions or data again soon, enabling effective caching. |
| **Hit Ratio** *(added)* | The fraction of memory accesses successfully found at a faster tier (e.g., Main Memory) without needing the slower tier. |
| **Logical Address (LA)** *(added)* | The address generated by the CPU to reference an instruction/data item, relative to the process's own view of memory. |
| **Physical Address (PA)** *(added)* | The actual address understood by Main Memory hardware, to which a Logical Address must be translated. |
| **Contiguous Memory Allocation (CMA)** *(added)* | Allocating all of a process's instructions together, in one contiguous memory block. |
| **Non-Contiguous Memory Allocation (N-CMA)** *(added)* | Allocating a process's instructions in separate chunks, scattered across memory. |
| **Fixed Sized Partitioning** *(added)* | Dividing Main Memory into a set of pre-defined, fixed-size partitions, one process per partition. |
| **Variable Sized Partitioning** *(added)* | Allocating each process exactly the amount of contiguous memory it requires, with no pre-defined partition sizes. |
| **First Fit** *(added)* | Allocation algorithm: place the process in the first partition/block encountered that is large enough. |
| **Best Fit** *(added)* | Allocation algorithm: place the process in the smallest available partition/block that is still large enough (closest match). |
| **Worst Fit** *(added)* | Allocation algorithm: place the process in the largest available partition/block (deliberately oversized). |
| **Limit Register** *(added)* | Holds the maximum legitimate logical address (instruction count) for a process, used to validate memory requests. |
| **Relocation Register** *(added)* | Holds the starting physical address of a process in Main Memory, added to the Logical Address to compute the Physical Address. |
| **Trap** *(added)* | An interrupt/rejection issued when a Logical Address exceeds the Limit Register (an illegal, out-of-bounds request). |
| **Page** *(added, CMA/paging-specific sense)* | A fixed-size partition of Secondary Memory holding part of a process's instructions. |
| **Frame** *(added, paging-specific sense)* | A fixed-size partition of Main Memory, matching the page size, that can hold one page. |
| **Page Table (PT)** *(added)* | A per-process indexed data structure recording which frame (in Main Memory) holds each of the process's pages. |
| **Instruction Offset / Displacement (D)** *(added)* | The portion of a Logical Address specifying the position of an instruction within its page. |

---

## 22. Summary Tables

### 22.1 Internal vs. External Fragmentation

| | Internal Fragmentation | External Fragmentation |
|---|---|---|
| **Cause** | Allocated block is LARGER than needed | Free space is scattered, not contiguous |
| **Where's the waste?** | Inside an allocated block | Between allocated blocks, in small free chunks |
| **Total free memory sufficient?** | N/A (space is allocated, just unused within) | YES in total, but no single chunk is big enough |

### 22.2 Stack Allocation vs. Heap Allocation

| | Stack Allocation | Heap Allocation |
|---|---|---|
| **Order** | LIFO (last in, first out) | No fixed order |
| **Predictability needed** | Allocation/freeing partially predictable | Allocation/freeing unpredictable |
| **Simplicity** | Simple, efficient | More complex, less efficient |
| **Typical use** | Procedure call state (nested calls/returns) | Data structures that grow/shrink dynamically |
| **Real implementation** | True LIFO stack structure | Doubly linked list of blocks ("best fit"), NOT a literal tree |

### 22.3 Full Virtualization vs. Guest Modification

| | Full Virtualization | Guest Modification |
|---|---|---|
| **Guest OS changes needed?** | No — OS runs unmodified | Yes — guest OS is altered |
| **Isolation mechanism** | Hypervisor emulates hardware per guest | Guest cooperates by avoiding inefficient instructions |
| **Memory expectation** | Each guest expects contiguous physical memory from address 0 | N/A — focused on instruction-level efficiency |

### 22.4 Page Fault Handling — Step Summary

| Step | Action |
|---|---|
| 1 | Process references a page not in main memory → page fault triggered |
| 2 | Control transfers from program to OS |
| 3 | OS finds a free page frame |
| 4 | OS loads the page from backing store into main memory |
| 5 | OS resumes thread execution (with hardware support) |

---

## 23. Concepts in Practice — Explained

> *The source material poses a relatable scenario: your laptop juggling a slide presentation, email, a social media photo upload, a shared playlist, a video, and a spreadsheet all at once — until everything suddenly freezes, or the "spinning pinwheel" never stops.*

**What's actually happening inside the CPU, connecting back to this material:**
- Each of those applications is a separate **process**, each with its own **virtual memory** view, all being **multiplexed** across the same limited **physical DRAM**.
- As you open **more and more applications simultaneously**, their combined **working set size (WSS)** may exceed the available **physical memory**.
- The OS must increasingly **swap pages in and out** between DRAM and disk to keep everyone's active data available — and if the combined demand is too high, the system tips into **thrashing**: it spends **more time paging/swapping** than actually running your applications' code.
- **The freeze/spinning pinwheel** is the **visible symptom** of thrashing — the OS is so busy **fetching and replacing pages** (because too many processes are competing for too little physical memory) that it has **little to no time left** to actually execute your requested actions (clicking, typing, uploading).
- **The fix**, per the chapter's own strategies: **close some applications** (reduce the number of concurrently competing processes), or **add more physical RAM** to your machine — both directly address the root cause of overcommitted memory driving the system into thrashing.

---

## 24. Big-Picture Takeaways

1. **Memory multiplexing rests on four pillars — isolation, sharing, virtualization, and utilization** — each addressing a different aspect of safely and efficiently letting multiple processes share the same physical memory hardware.
2. **Isolation is enforced through a stack of cooperating mechanisms**: the user/kernel mode flag, address space boundaries, the system call interface, and time slicing — together ensuring processes can't accidentally (or maliciously) interfere with one another.
3. **Sharing and virtualization are two sides of a trade-off**: sharing avoids costly duplication when processes deliberately want to cooperate, while virtualization creates the illusion of private, infinite memory even though the underlying hardware is finite and shared.
4. **Fragmentation (internal and external) is an unavoidable consequence of allocating variable-sized memory requests over time** — understanding the distinction (wasted space inside a block vs. scattered, non-contiguous free space) is key to understanding why allocators are designed the way they are.
5. **Linkers and dynamic linking solve the problem of turning many separate compiled pieces into one coherent, correctly-addressed executable** — and dynamic linking specifically enables massive memory savings by sharing common library code across many processes.
6. **Dynamic storage management boils down to a trade-off between the simple, efficient but restrictive stack, and the flexible but more complex "heap"** (which, confusingly, in real memory managers is actually a linked list, not a tree) — the right choice depends on how predictable your allocation/deallocation pattern is.
7. **Virtual memory is the cornerstone abstraction enabling process isolation and the "infinite memory" illusion**, achieved through address translation — and the TLB exists purely to make that translation fast enough to be practical, given how frequently memory is accessed.
8. **Demand paging extends virtual memory's illusion even when physical memory runs out**, by treating disk as an extension of DRAM — but this comes with real performance costs (page faults) that the system tries to minimize via prefetching, and which can spiral into system-crippling thrashing if too many processes compete for too little physical memory at once.
9. **The everyday experience of a frozen, unresponsive computer is a direct, visible symptom of thrashing** — a satisfying real-world connection between this chapter's abstract mechanisms and a frustration nearly every computer user has personally experienced.

---

## 25. What Was Missing — Gap Summary

Comparing the original notes (based on the OpenStax-style Section 6.4 excerpt) against the newer, more detailed source material, the following topics were **entirely absent** from the original notes and have now been added (Sections 15–20 above):

1. **The three competing memory-optimization criteria** (size, access time, per-unit cost) and the fundamental impossibility of maximizing all three at once.
2. **The full memory hierarchy diagram** (Cache → Main → Secondary) and the **locality of reference** concept explaining *why* it works, including the concrete "I200/I201–I220" pre-fetching illustration.
3. **The hit-ratio-based average access time formula**, with the fully worked 90%-hit-ratio numeric example (yielding 20ms average access time).
4. **Explicit CMA (Contiguous Memory Allocation) and N-CMA (Non-Contiguous Memory Allocation) terminology**, including the array vs. linked-list analogies and a fully worked external-fragmentation numeric example (10KB memory, 5KB process, two 4KB free chunks).
5. **Fixed Sized Partitioning vs. Variable Sized Partitioning** as the two specific implementations of CMA, including a worked internal-fragmentation example (5/2/2/1 KB partitions).
6. **The First Fit, Best Fit, and Worst Fit allocation algorithms**, with two complete worked numerical examples — one for variable sized partitioning (four processes, four free blocks) and one for fixed sized partitioning (four processes, six fixed partitions) — including full internal/external fragmentation tallies for each algorithm.
7. **The specific address-translation mechanism for CMA**: the Limit Register, Relocation Register, the `PA = LA + RR` formula, the legitimacy check, the **trap** mechanism for illegal addresses, and a fully worked numeric example (LA=21, Limit=100, Relocation=500 → PA=521).
8. **Concrete page/frame terminology and mechanics for paging as an N-CMA implementation**: the requirement that Secondary and Main Memory partitions be equally sized, the P (page number) + D (offset) logical address structure, the linked-list address-translation approach (and its slowness), and the **indexed table (Page Table)** approach with a fully worked example (Process X across P1/P2/P3 → frames F4/F2/F5, resolving instruction I24 on P2).

These additions sit alongside (and complement, rather than replace) the original notes' coverage of memory multiplexing, isolation mechanisms, sharing, virtualization, linkers/dynamic linking, stack/heap dynamic storage management, virtual memory/TLB, demand paging, and thrashing — the two source documents describe **different but overlapping views** of the same overall Memory Management topic, and this file now reflects **both**.