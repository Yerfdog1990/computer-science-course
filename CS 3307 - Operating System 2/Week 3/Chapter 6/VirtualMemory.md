# Virtual Memory 

*(Based on Chapter 6: "Virtual Memory")*

---

## Table of Contents

1. Introduction — Virtual vs. Physical Addresses
2. The MMU and the Need for Flexible Mapping
3. The Five Defining Properties of Virtual Memory
4. Uses for Virtual Memory (§6.2, all subsections)
5. Mechanisms for Virtual Memory (§6.3)
    - Software/Hardware Interface (TLB)
    - Linear Page Tables
    - Multilevel Page Tables
    - Hashed Page Tables
    - Segmentation
6. Policies for Virtual Memory (§6.4)
    - Fetch Policy
    - Placement Policy
    - Replacement Policy
7. Security and Virtual Memory (§6.5)
8. Glossary of Key Terms
9. Summary Tables
10. Worked Exercise Solutions
11. Annotated Notes / Historical References
12. Big-Picture Takeaways

---

## 1. Introduction — Virtual vs. Physical Addresses

### 1.1 Context: A New Form of Control
- Previous chapters covered **synchronization** — controlling threads' **interactions** with **shared** storage.
- This chapter covers a **different** form of control: giving each thread its **own private storage**, rather than regulating access to shared storage.
- **Virtual memory** is the mechanism that provides this — but it turns out to be **far more general-purpose** than just providing privacy, as the chapter's extensive catalog of uses (§6.2) will show.

### 1.2 The Essential Idea
> Virtual memory **decouples** the addresses that running programs use to identify objects (**virtual addresses**) from the addresses the memory uses to identify storage locations (**physical addresses**).

### 1.3 A System WITHOUT Virtual Memory (Baseline)

```
Processor ──── address ────▶ Memory
Processor ◀──── data ──────▶ Memory
```

- The processor sends an address to memory for every load/store; data flows in the corresponding direction.
- Each load retrieves the **most recently stored value** for that address.
- **Even here**, addresses play **two subtly different roles**, depending on perspective.

### 1.4 Two Perspectives on Addresses

#### From the Processor's Perspective — Addresses as Names
- Addresses **differentiate stored objects from one another** — essentially, addresses are used as **names**.
- **Analogy:** an executive telling a clerk "file this under 'widget suppliers'" and later "get me that document we filed under 'widget suppliers'" — the label is a **name**, not inherently a physical location.
- Addresses used by executing programs to refer to objects this way are **virtual addresses**.
- **Not arbitrary:** since virtual addresses are numbers, the processor can give **related objects related names** — e.g., **arrays**: all elements stored at **consecutive** virtual addresses, so any element's address = base address + position.

#### From the Memory's Perspective — Addresses as Locations
- Addresses are **not names** — they are **spatial locations of storage cells**, used to steer data **into or out of** specific physical cells.
- Addresses used this way are **physical addresses**.

### 1.5 The Simple Case: Virtual = Physical

- In a system **without** virtual memory (Figure 6.1/6.2 style), physical addresses come **directly** from virtual addresses — they are **numerically equal**.

```
Processor's view:    0    1    2  ...
Memory's view:       0    1    2  ...
(identical — virtual address N maps directly to physical address N)
```

### 1.6 Where the Perspectives Diverge — Multiple Processes

- The divergence becomes apparent once the processor **divides its time among multiple programs** (i.e., **processes**, covered fully in Ch. 7).
- **Problem:** sometimes each process needs a **private object**, but the **natural name** to use (e.g., address 0, or "the spreadsheet") will be the **same** across multiple processes.
- **Example — multiple spreadsheet copies:** several copies of the same spreadsheet program are running. Each naturally wants to refer to "the spreadsheet" — and since they're running the **same code**, it's natural for all instances to use the **same virtual address** — yet the **actual objects must be stored separately** in physical memory.

```
                Processor                          Memory
        Process A    Process B          (physical storage locations)
        addr 0,1,2   addr 0,1,2    →    0  1  2  3  4  5
        (own "triangle" object)          [A's data][B's data]
```

- **Conclusion:** virtual addresses **can no longer equal** physical addresses.

### 1.7 Even for Different Programs, and Even for Shared Objects
- The same need for private naming arises (if less strongly) even when concurrent processes run **different** programs — coordinating globally unique addresses across all possible programs would require **unwieldy coordination**.
- **Even for deliberately SHARED objects**, addresses-as-names and addresses-as-locations diverge:
    - **Example — producer/consumer via a shared bounded buffer:** one process calls it the "output channel," the other calls it the "input channel." Each may have its **own name** (virtual address) for the object, yet the memory stores it in **just one** physical location.
- **General conclusion:** virtual and physical addresses should **not** be forced equal — it should be possible for:
    - Two processes to use the **same virtual address** to refer to **different physical addresses**, OR
    - Two processes to use **different virtual addresses** to refer to the **same physical address**.

---

## 2. The MMU and the Need for Flexible Mapping

### 2.1 The Memory Management Unit (MMU)
- General-purpose computers insert an **intermediary** between processor and memory: the **Memory Management Unit (MMU)**.
- **Program execution** within the processor works **entirely in virtual addresses**.
- **On every load/store**, the processor sends the **virtual address** to the MMU, which **translates** it into the corresponding **physical address**, sent on to memory.

```
Processor ──virtual address──▶ [ MMU ] ──physical address──▶ Memory
Processor ◀──────────────────── data ────────────────────────▶ Memory
```

### 2.2 Could the Mapping Just Be a Simple Formula?
- **Question posed:** so far, nothing rules out the mapping being as simple as "physical address = 2 × virtual address."
- **Answer: that would NOT yield the general mechanism known as virtual memory.** Real virtual memory requires several additional properties (detailed next).

---

## 3. The Five Defining Properties of Virtual Memory

1. **Table-based, not formula-based:** the virtual-to-physical mapping is represented by a **table**, not a computational rule — enabling **far more general** mappings than any fixed formula could.

2. **Page-granularity, not byte-granularity:** to keep the table's size manageable, virtual addresses are **grouped into blocks called pages**; the table records, **for each page**, the corresponding **page frame** of physical addresses (rather than one entry per individual byte address).

3. **OS-controlled contents:** the table's contents are controlled by the **operating system** — including both **incremental adjustments** (for the many uses in §6.2) and **wholesale changes** when **switching between threads** (allowing each thread/process its own private virtual address space).

4. **Sparse/undefined entries allowed → page faults:** the table **need not** contain a translation for every page — some entries can be **left blank** (illegal addresses). If the processor generates an illegal address, the **MMU interrupts the processor**, transferring control to the OS — this interrupt is a **page fault**. This mechanism:
    - **Limits usable addresses**, AND
    - Allows translations to be **inserted on demand**, only when actually needed — improving performance by moving data **only when necessary**.

5. **Fine-grained legality (permissions):** some entries may be marked legal **only in specific ways** — e.g., legal to **read** from a page but **not write** to it. **Main purpose:** enabling **trouble-free sharing** of memory between processes.

### 3.1 Summary of the Mechanism
> Virtual memory = an **OS-defined table of mappings** from virtual addresses to physical addresses (at **page granularity**), with the opportunity for **OS intervention** on accesses the table shows to be **illegal**.

### 3.2 Why This Is So Flexible
- The OS can **switch between multiple views** of physical memory.
- Parts of physical memory may be **completely invisible** in some views (no virtual address maps there).
- Other parts may be **visible in multiple views**, but at **different virtual addresses** in each.
- Mappings **need not be established in advance** — by marking pages illegal and filling them in **on first access** (via page fault), the OS provides mappings **on a demand-driven basis**.

---

## 4. Uses for Virtual Memory (§6.2)

> This section catalogs uses of virtual memory that are in **everyday use** in general-purpose OSes (not an exhaustive list of research applications).

### 4.1 Private Storage (§6.2.1)

#### Two Subgoals
1. **Independent allocation:** each computation can use whatever virtual addresses are most convenient, **without needing to avoid** collisions with other computations' addresses.
2. **Protection:** each computation's objects are protected from **accidental or malicious** access by other computations.

- **Both subgoals are achieved** by giving computations their **own virtual memory mappings** — this forms the **core of the process concept**.

#### The Process Concept (Preview of Chapter 7)
- A **process** = a group of one or more **threads** with an associated **protection context**.
- For this chapter's purposes: **a process is a group of threads that SHARE a virtual address space.**
- **Protection mechanism:** hardware + OS **collaborate** to prevent any software **outside the OS** from updating the MMU's address mapping. Each process is thus restricted to accessing **only** the physical locations the OS has allocated as page frames for **its own** pages.
- **Key subtlety:** physical memory areas for different processes need only be **disjoint at each moment in time** — processes can **take turns** using the same physical memory (e.g., after one process exits and another starts).
- This "separate virtual address spaces" model is the **mainstream approach today** (alternatives allowing shared single address spaces, while still protected, are explored in Ch. 7).

### 4.2 Controlled Sharing (§6.2.2)

#### Motivation
- Sometimes the OS **deliberately** maps a **limited portion** of memory into **more than one** process's address space — either for **communication**, or to **reduce memory consumption** and initialization time.
- **Flexibility:** shared physical memory can occupy **different virtual address ranges** in each process. **Caveat:** if this flexibility is used, the shared memory should **NOT** store pointer-based structures (e.g., linked lists), since pointers are represented as virtual addresses (which would differ between processes).

#### Sharing Program Text and DLLs
- Each process's virtual address space is typically divided into:
    - A **read-only "text" region**: machine instructions + read-only data (e.g., error message strings).
    - A **read/write region**: the rest of the process's data (often further split into stack + other data areas).
- **All processes running the same program can share the same text** — the OS maps it into each process's address space with **read-only** MMU protection bits, preventing it from **accidentally** becoming a communication channel.
- **Shared libraries / DLLs (Dynamic-Link Libraries, Microsoft's term):** large libraries of supporting code (e.g., GUI-related code) can be **shared, read-only**, across **different** programs (e.g., a web browser and a spreadsheet), just like main program text.

```
Process A (Program 1)     Process B (Program 1)     Process C (Program 2)   Process D (Program 2)
├─ Program 1 Text (SHARED across A & B)              ├─ Program 2 Text (SHARED across C & D)
├─ DLL 1 Text (SHARED across A, B, C, D)             ├─ DLL 1 Text (same shared copy)
├─ DLL 2 Text (SHARED across A & B)                  ├─ DLL 3 Text (SHARED across C & D)
└─ A's own writable storage                          └─ C's own writable storage
```

#### Sharing for Interprocess Communication (IPC)

**Simplest approach — shared writable memory:**
- Map some physical memory into **two processes' virtual address spaces** with **full read/write** permission — processes freely read/write, communicating directly.
- **Simple for the OS, but NOT simple for application programmers:** requires careful use of mutexes/reader-writer locks; without scrupulous care, **race conditions** result (difficult to debug).

```
   Process A's         Communication Area           Process B's
   private storage    (shared, writable)            private storage
```

**A more structured alternative — Message Passing:**
- Used by systems like **Mac OS X**.
- **Mechanism:** one process writes a message into a memory block, then asks the OS to **send** it to the other process; the receiving process **appears** to get a copy.
- **For SMALL messages:** the OS may literally **copy** the message.
- **For LARGE messages (efficiency):** the OS does **NOT** copy — instead, it updates the **receiver's** virtual memory map to point at the **same physical memory** as the sender's message. Both sender and receiver can access the message **without any copying**.
- **To preserve safe, race-free semantics:** the OS marks the shared page **read-only for BOTH** sender and receiver — preventing any nasty races (since the sender composed the message **before** invoking the OS, this read-only protection doesn't interfere with composition).

**Refinement — Copy on Write (COW):**
- Used by systems like **Mac OS X**.
- If **either** process tries to **write** into the shared (read-only) page, the MMU **interrupts**, transferring control to the OS.
- The OS then **copies** the page, giving sender and receiver **their own individual, writable copies**.
- The OS **resumes** the process that attempted the write, letting it now **succeed**.
- **Net effect:** the complete **illusion** that the page was copied **at the moment the message was sent** — but the **actual copying is avoided** unless a process actually writes into the page.

```
Step 1: Process A writes message into its own private memory.
Step 2: OS maps that same physical memory (read-only) into Process B's address space too.
        [Process A's msg] ←── SHARED, READ-ONLY ──→ [Process B's msg]

Step 3 (only if A or B tries to WRITE): MMU interrupts → OS copies the page →
        [A's own modifiable copy]         [B's own modifiable copy]
```

### 4.3 Flexible Memory Allocation (§6.2.3)

#### The Contiguous Allocation Problem
- **Naive approach:** if process 1 needs 8MB and process 2 needs 10MB, allocate the **first 8MB** (lowest physical addresses) to process 1, next 10MB to process 2.
- **Two difficulties with CONTIGUOUS allocation:**
    1. **Growth problem:** a process's memory needs may **grow/shrink** at runtime — if process 1 is immediately followed by process 2 in memory, what happens if process 1 needs **more** space?
    2. **External fragmentation:** processes **exit** and **new** ones (of different sizes) start. Example: 512MB total, three processes of 128MB, 256MB, 128MB running. If the **1st and 3rd** (128MB each) terminate, freeing **256MB total** — but **not contiguously** — a **new 256MB process** cannot fit, even though enough **total** free memory exists.

```
Before:  [Process A: 128MB][Process B: 256MB][Process C: 128MB]
After A, C terminate:  [free: 128MB][Process B: 256MB][free: 128MB]
New Process D needs 256MB contiguous — WHERE DOES IT GO?
(256MB is available in total, but split into two 128MB chunks — external fragmentation!)
```

#### Virtual Memory's Solution
- Because **all modern general-purpose systems have virtual memory**, this contiguous-allocation problem is a **non-issue for main memory**.
- The OS can allocate **ANY available physical page frames** to a process, **independent of their location** in physical memory.
- **Solution to the example above:** split Process D across the **two** free 128MB regions (physically non-contiguous), while D's **virtual addresses remain contiguous**.

```
[Process D, first half: 128MB][Process B: 256MB][Process D, second half: 128MB]
(physically split, but D's VIRTUAL address space is one contiguous 256MB range)
```

### 4.4 Sparse Address Spaces (§6.2.4)

- Virtual memory provides flexibility not just for the OS (allocating physical memory), but for **application programs** too, in allocating **virtual** address space.
- A process can use **whatever addresses make sense** for its data structures, even with **large gaps** between them — giving the compiler/runtime flexibility in assigning addresses.

**Example:** three data structures S1, S2, S3, each needing contiguous space AND room to grow:
```
S1    free    S2    free    S3    free
0     2       6     8       12    14      18   (addresses in MB)
```
- Here, only **1/3** of the 18MB address range is actually **occupied**. Allowing more growth room would require **even lower** occupancy percentages.
- **Real processes** commonly span **several gigabytes** of address range while using far less actual storage (often to let one region grow **up** from the bottom while another grows **down** from the top).
- **How virtual memory enables this without wasting physical memory:** the OS simply **doesn't provide physical mappings** for the virtual addresses in the **gaps** — those addresses are simply left as invalid/unmapped, costing nothing.

### 4.5 Persistence (§6.2.5)

#### The Basic Need
- Any general-purpose OS must let users **retain data** across shutdowns/restarts — typically via **files**, stored on **disk**.

#### How This Connects to Virtual Memory
- A process needing to access a file can ask the OS to **map the file into its address space**.
- The OS does **NOT** need to read the **whole file** into memory upfront — it can do so **on a demand-driven basis**:
    - First access to a given **page** of the file → **page fault**.
    - OS responds by **reading that page** into memory, **updating the mapping**, and **resuming** the process.
    - *(For efficiency, the OS might fetch **additional** pages at the same time, anticipating future need — see §6.4.1's discussion of prefetching.)*

#### Writing Back — Dirty Pages
- If the process **writes** into a page that's part of a mapped file, the OS must eventually **write that page back to disk** to achieve persistence.
- **Efficiency concern:** the OS should **NOT** write back pages that **haven't been modified** since the last write-back/read-in.
- **Therefore:** the OS needs to track which pages have been modified — these are called **dirty pages**.

#### Tracking Dirty Pages — Two Approaches

**(a) Software approach (using only mechanisms already discussed):**
- Initially mark **all pages read-only**.
- The **first** write attempt into a clean page → MMU interrupt.
- OS makes the page **writable**, adds it to a **dirty-pages list**, and lets the write proceed.
- When the OS later writes the page back to disk (making it clean again), it can **re-mark it read-only**.

**(b) Hardware approach (more efficient, more common):**
- MMU keeps a **dirty bit** per page. **Any write** into the page causes the **hardware** to set the dirty bit — **no OS intervention needed** for every single write.
- The OS periodically **reads and resets** the dirty bits.
- **Intel Itanium's compromise:** the OS sets the dirty bits, but **with hardware support** — providing software flexibility **without** the full software-approach performance cost.

### 4.6 Demand-Driven Program Loading (§6.2.6)

#### The Problem With Loading Whole Programs
- Running a program **conceptually** = read the whole executable into memory, then jump to the first instruction.
- **BUT:** many programs are **huge**, with parts that **may never be used** (e.g., error-handling routines only triggered by actual errors; optional feature modules most users never touch).
- **Reading in the whole program is inefficient.**

#### Even If the Whole Program Eventually Gets Used
- An **interactive user** might prefer **several short pauses** (for incremental disk access) over **one long initial pause**.
- Reading the **whole program upfront** → slow program **startup** (frustrating).
- Reading **incrementally** → **fast startup**, at the cost of **brief pauses during operation**.
- **Key perceptual insight:** if each such pause is only a **few tens of milliseconds**, and occurs at the time of **user interaction**, it falls **below the threshold of human perception** — essentially invisible to the user.

#### Two Reasons for Demand-Driven Program Loading
1. **Avoid reading unused portions** (efficiency).
2. **Quickly start the program's execution** (responsiveness).

- **Mechanism:** just like general persistent storage — each **page fault** causes the OS to read in **more of the program**, on demand.

#### Implication for Programmers
- Application programmers can make programs **start up faster** by **grouping all necessary startup code together** on a **few pages** — though this layout job really belongs to the **compiler and linker**, not the human programmer directly (though the programmer may guide these tools).

### 4.7 Efficient Zero Filling (§6.2.7)

#### The Security Requirement
- For **security** and **debuggability**, the OS should **never** let a process read memory containing a **leftover value** from some **other** process that previously used it.
- **Therefore:** any memory not occupied by a persistent object must be **cleared (zeroed)** before a new process accesses it.

#### The Virtual-Memory Trick
- **Naive approach:** actually write zeros across the whole region — wasteful if the region is huge and mostly unused.
- **Virtual memory's trick:** the OS can fill an **arbitrarily large virtual address range** with zeros using **only a SINGLE zeroed-out physical page frame**.
    - **How:** map **ALL** the virtual pages in that range to the **SAME** physical page frame, and mark them **read-only**.
    - This works because as long as no one **writes**, every mapped page **reads as all zeros** — with only one physical page frame actually consumed!

#### Handling Writes — A Variant of COW
- If a process **writes** into one of its (shared, read-only) zeroed pages → MMU **interrupt**.
- OS updates the mapping for **that one page** to point to a **SEPARATE, writable** page frame of zeros, then resumes the process.
- **Efficiency refinement:** rather than literally **copying** the read-only zero page (per the strict COW principle), the OS can just **write zeros directly** into the new page frame — **no copying needed**, since the source content (zeros) is trivial to produce directly.
- **Even better — proactive spare zeroed pages:** the OS can maintain a **stock of spare, pre-zeroed page frames** during **idle time**, replenishing as needed. When a write-fault occurs on a shared zero page, the OS just **hands over one of these spares** and marks it writable — **no on-demand zeroing delay** at all.

#### A Subtle Performance Detail
> When the OS **proactively** fills spare page frames with zeros during idle time, it should **bypass the processor's normal cache memory**, writing **directly into main memory**.

- **Why:** otherwise, this "housekeeping" zero-filling could **displace valuable data** from the cache, hurting the performance of whatever else is running — a performance detail directly connected to the cache/scheduling discussions in earlier chapters.

### 4.8 Substituting Disk Storage for RAM (§6.2.8)

#### The Motivating Cost/Speed Trade-off
- **Disk vs. RAM:** disk is **nonvolatile** (retains contents without power) — relevant to persistence (§4.5). But disk **also** differs from RAM in being **roughly 2 orders of magnitude cheaper per GB** — motivating a **different** use of virtual memory: **simulating lots of RAM using cheaper disk space.**
- **The catch:** disk is also **~5 orders of magnitude SLOWER** than RAM — this approach is "not without its pitfalls."

#### Why This Works — Exploiting Inactivity
- Many processes have **long periods of inactivity** — e.g., a desktop user may have a word processor, browser, mail reader, and spreadsheet open, but **focus on only one** for minutes/hours at a time.
- Even **within** a process, some parts remain inactive (e.g., a spreadsheet's online help, viewed once and not touched again for days).

#### The Core Strategy
- The system needs **enough RAM to hold the working set** — the **active** portions of all active processes. **Without this**, performance becomes **intolerably slow** due to routine disk accesses.
- The system does **NOT** need enough RAM for the **entire** storage needs of **all** processes — **inactive portions can be shuffled off to disk**, paged back in **if/when** they become active again.
- **Trade-off:** switching activity focus (e.g., from word processor to a long-unused spreadsheet) incurs **some delay** for disk access — but once the **new working set** is back in RAM, responsiveness returns to normal.

#### Historical Significance
- Much of virtual memory's **history** centers on **exactly this application**, dating back to its **invention in the early 1960s** (originally using **magnetic cores** and **magnetic drum**, rather than semiconductor RAM and magnetic disk).
- Even though this is now just **one of many** roles virtual memory plays, it deserves special attention because **many of the most interesting policy questions** (replacement policies — §6.4.3) arise **specifically** for this application: when RAM gets crowded, the OS must **guess** which pages are **unlikely to be accessed soon**.

---

## 5. Mechanisms for Virtual Memory (§6.3)

### 5.1 Basic Setup
- Address mapping needs to be **flexible yet efficient** — stored in an **explicit table**, at **page granularity**.
- **Typical page sizes have grown over the decades** (explored in Exercises 6.3/6.4); today, **4 KiB (kibibytes)** is most common.
- Each page/page frame is this size, starting at addresses that are **multiples of the page size**.

### 5.2 Basic Page Mapping Example (Figure 6.10)

- Small illustrative system: 8 virtual pages, 4 physical page frames.
- **Page 0 → Page frame 1; Page 1 → Page frame 0; Page 6 → Page frame 3.**
- **Pages 2–5 and 7 → NO mapping** (marked invalid, "X").
- **Page frame 2** is currently **unused** (not holding any page).

```
With 4-KiB pages:
  Virtual address 0    → Physical address 4096  (page 0 → frame 1)
  Virtual address 100  → Physical address 4196
  Virtual address 4092 (last word of page 0) → Physical address 8188 (last word of frame 1)
  Virtual address 4096 (start of page 1!) → Physical address 0 (page 1 → frame 0) — DIFFERENT mapping rule, new page!
```

- **Key insight:** within a single page, physical = virtual + constant offset — but **crossing a page boundary** can produce a **completely different** offset, since each page maps **independently**.

### 5.3 Software/Hardware Interface — The TLB (§6.3.1)

#### The Performance Problem
- **Every single memory access** generates a virtual address needing translation.
- **Naively:** every access would require **an extra memory access** just to look up the page table — potentially **doubling** memory accesses, and since memory performance is often the **bottleneck**, this could make programs run at **half speed**.

#### The Solution — Exploiting Locality
- Real software exhibits **both temporal and spatial locality** — addresses accessed once are likely accessed **again soon**, and **nearby** addresses are likely accessed soon too.
- **At the page level**, both forms of locality combine into: if a page is accessed, that **same page** will likely be accessed **again soon**.

#### The TLB (Translation Lookaside Buffer)
- The MMU keeps a **small, fast cache** of recently used virtual-to-physical translations — the **TLB**.
- **TLB hit:** the needed page number **IS** present → frame number retrieved directly, **no page table access needed**.
- **TLB miss:** the needed page number is **NOT** present → must consult the (slower) page table.

#### The TLB Size/Speed Conflict
- For **fast clock cycles** and good small-benchmark performance → TLB must be **very fast** (hence small).
- For performance **not to collapse** on large workloads → TLB must be **reasonably large** (hundreds of entries).
- **These two goals conflict** — chip designers can make lookup tables **large OR fast**, not both.

#### Mitigation Strategies (Hardware + OS + Application Cooperation)

1. **Separate instruction/data TLBs:** so these two access categories don't compete for the same TLB entries.
2. **TLB hierarchy** (like cache hierarchy): small/fast **L1** TLB for most accesses; larger/slower **L2** TLB catches most L1 misses without needing a full page table access. *(Example: AMD Opteron — 40-entry L1 instruction/data TLBs, 512-entry L2 instruction/data TLBs.)*
3. **Variable page sizes:** letting some TLB entries map **larger** pages reduces TLB pressure for large, contiguous structures.
4. **OS use of variable page sizes:** even if applications use small (4 KiB) pages, the **OS itself** can use larger pages; a multi-megabyte video frame buffer needn't be carved into thousands of 4-KiB chunks. **Bonus:** larger pages → smaller page tables → often faster to access too.
5. **General OS design awareness of TLB pressure:** e.g., **frequent process switching** (small scheduler time slices) increases TLB pressure and hurts performance — a direct link back to the scheduling chapter's trade-offs.
6. **Application programmer awareness:** programs with **strong locality of reference** perform much better — not just for caches, but for the TLB too. **Performance can drop off precipitously** once a program exceeds the TLB's capacity. Some data structures are inherently more TLB-friendly (e.g., a **densely occupied** table beats a **large, sparse** one) — a caution against naive "constant time per memory operation" theoretical analyses.

### 5.4 Loading the TLB — Hardware Walker vs. Software

- **Two different computer architecture approaches**, differing in **how TLB entries get loaded** on a miss:

#### (a) Hardware Page Table Walker
- MMU contains dedicated hardware (a **page table walker**) that performs the page table lookup **without software intervention**.
- **OS must maintain the page table in a FIXED FORMAT** the hardware expects.
- **Example: IA-32 (Pentium 4)** — the hardware walker **requires** a multilevel page table format; the software/hardware interface is essentially just **one register** holding the page table's starting address.
- **Complication:** if the OS updates mapping info in the page table, it must **flush obsolete TLB entries**.

#### (b) Software-Handled TLB Misses
- Hardware has **NO** specialized page-table access; on a TLB miss, it **interrupts** the processor, transferring control to the **OS**.
- OS software looks up the translation (using **whatever data structure it wants**), loads it into the TLB via a **special instruction**, and resumes.
- **Advantage:** more **flexible** (any page table format) and **simpler hardware**.
- **Disadvantage:** TLB misses become **more expensive** (full context switch to OS, losing cache locality).
- **Example:** the **MIPS processor** (used in the Sony PlayStation 2) handles TLB misses in software.

### 5.5 Handling Process Switches — Flushing vs. ASIDs

- Each process may have its **own private virtual address space**; switching processes means the virtual→physical mapping must change too.
- **Some architectures:** require **flushing ALL TLB entries** on a process switch (possible exception for **global entries** shared by all processes, which aren't flushed).
- **Other architectures:** tag TLB entries with an **Address Space Identifier (ASID)**.
    - A special register holds the **current process's ASID**.
    - **Switching processes** = just store a new ASID in this register — **no TLB flush needed**.
    - The TLB only "hits" if **both** the ASID **and** page number match — effectively **ignoring** entries belonging to other processes.
- **For hardware-walker architectures:** each process switch may **also** require updating the register pointing to the **page table** (linear/multilevel page tables are typically **per-process**). **Hashed page tables**, in contrast, can be **shared** among all processes, using ASID tags (like the TLB).

### 5.6 Linear Page Tables (§6.3.2)

#### Basic Concept
- **Conceptually simplest:** an **array**, one entry per virtual page, indexed directly by page number (like any array access: `base_address + n × entry_size`).
- Each entry contains (at minimum): a **valid bit** + a **page frame number**.
    - Valid = 0 → no corresponding frame (page frame number is unused/irrelevant).
    - Valid = 1 → page is mapped to the specified frame.
    - **Real entries** also typically include **permission bits** (e.g., write-allowed), **dirty bits**, etc.

#### The Fundamental Problem: Size
- **Example:** 32-bit address space, 4-KiB pages → 2²⁰ pages. At 4 bytes/entry → **4 MiB page table** per process!
- **Real-world impact:** the author notes running 70 processes would need a hypothetical **280 MiB** of page tables — **36% of total RAM**.
- **Gets MUCH worse for 64-bit address spaces** — a full linear page table becomes essentially **inconceivable** to store directly (explored quantitatively in Exercise 6.8).

#### The Solution: Exploit Sparsity, But Cleverly
- Virtual address spaces are generally **sparse** — only a small fraction of possible page numbers have valid translations (especially true for 64-bit systems).
- **Naive fix (store only valid entries) breaks direct array indexing** — if you "squish" out invalid entries, you lose the ability to compute an entry's location directly from its page number.
- **The actual solution requires THREE insights:**

1. **Clumping, not randomness:** valid/invalid pages tend to occur in **clumps** (software uses many consecutive pages, then a gap, then more consecutive pages). So you can decide **which chunks** of the page table are worth storing, at a **coarser granularity** than individual entries — store chunks containing **any** valid entries, skip chunks that are **entirely** invalid.

2. **Chunk size = page size:** e.g., with 4-KiB pages and 4-byte entries, each chunk holds **1024 entries**. Many chunks (with 1024 consecutive unused pages) **need no storage at all**.

3. **The recursive trick — use virtual memory to store the page table itself:** this **decouples** the **addresses** of page table entries (which stay in a nice, orderly, indexable array) from **where** (or whether) they're actually **stored**. Chunks that need storing get **page frames** allocated (anywhere convenient); chunks that don't are simply **not allocated** at all.

#### The Recursion Problem — And How It's Solved
- If you literally used the **same** linear-page-table mechanism to translate the page table's **own** virtual addresses, you'd need to look up **that** lookup's own page table entry... **infinite recursion**.
- **Real systems break this recursion** by using **TWO DIFFERENT representations**:
    - The **linear page table** → used for **application-generated** virtual addresses.
    - A **DIFFERENT** mechanism (e.g., a **multilevel page table**, accessed via **physical** addresses) → used **only** to translate the (relatively few) pages that hold the **linear page table itself**.

```
Application's virtual address
        │
        ▼
[Look up in LINEAR page table] ── requires the linear page table's OWN
        │                          virtual address to be translated too!
        ▼                                    │
Application's physical address               ▼
                                    [Translated via a DIFFERENT
                                     mechanism — e.g., multilevel
                                     page table, physically addressed]
                                    → breaks the infinite recursion
```

#### Why Bother With Linear Page Tables At All, Then?
- Since you need **another** mechanism anyway (for the page table's own pages), why not just use **that** mechanism directly for **everything**?
- **Answer: the TLB.** Repeated access to the **same** virtual page doesn't need **any** page table access (TLB hit). The page table (of either kind) is only consulted on a **new page** access. Three cases:
    1. **Same page as a recent access** → **no lookup needed at all** (TLB has it).
    2. **New page, but same CHUNK of the linear page table as a previous access** → only a **linear page table lookup** needed (the linear table's own relevant page is already TLB-cached).
    3. **New page, far from anything recently accessed** → **BOTH** kinds of lookup needed.
- Because of **spatial locality**, most non-TLB-hit accesses fall into case **(2)**, not (3) — so the (slower) secondary mechanism is used only **rarely**, even though it exists "behind the scenes." **This combination trades complexity for performance.**

### 5.7 Multilevel Page Tables (§6.3.3)

#### The Key Difference From Linear Page Tables
- **Same** first insight as linear page tables: valid entries **clump**, so page-sized chunks can often go **unstored**.
- **DIFFERENT** second insight: instead of using **recursive virtual memory** to locate stored chunks, multilevel page tables use a **TREE data structure** — no recursion needed.

#### The Two-Level Case (e.g., IA-32/Pentium/Athlon)
- IA-32: 4-KiB pages, 4-byte entries → **1024 entries per page-sized chunk**, each chunk spanning **4 MiB** of virtual address space.
- Full 32-bit address space = **4 GiB** = spanned by **1024 chunks**.
- A **second-level structure** (the **page directory**) locates these 1024 chunks: also 4 KiB, 1024 entries, 4 bytes each — but these entries point to **first-level page-table chunks**, not individual page frames.

```
                    Page Directory (1024 entries, 4KiB)
                    ┌────┬────┬────┬─── .... ───┬────┐
                    │ 0  │ 1  │ X  │            │1023│
                    └─┬──┴─┬──┴────┴────────────┴────┘
                      │    │ (X = invalid, e.g. entries for pages 1024-2047)
                      ▼    ▼
              [Page Table  [Page Table
               Chunk 0]     Chunk 1]
              1024 entries  1024 entries
              each → a      each → a
              page frame    page frame
```

#### Step-by-Step IA-32 Translation Process
1. Divide 32-bit virtual address: **20-bit page number** + **12-bit offset** within page.
2. **Check TLB** with the 20-bit page number: HIT → done (frame + offset = physical address).
3. **MISS:** subdivide the 20-bit page number into **10-bit page directory index** + **10-bit page table index**.
4. **Load the page directory entry:** address = 4 × (directory index) + page directory base (from a special register).
5. Check the directory entry's **valid bit**: 0 → **page fault** (none of these 1024 pages have a frame).
6. If **valid**, the directory entry gives a **physical base address** for a chunk of page table.
7. **Load the page table entry:** address = 4 × (page table index) + that base address.
8. Check the page table entry's **valid bit**: 0 → **page fault**.
9. If **valid**, it contains the **physical page frame number** → load TLB, complete the access.

```
Virtual Address:  [ 10-bit dir idx | 10-bit table idx | 12-bit offset ]
                          │                │                  │
                          ▼                ▼                  │
                  [Page Directory]  →  [Page Table]           │
                          │                │                  │
                          └──── gives ─────┘                  │
                            physical page frame number ───────┘
                                       │
                                       ▼
                              Physical Address
                        (frame number + unchanged offset)
```

#### IA-32 Bonus Features (Exploiting 4-MiB Directory Entry Spans)
- **Large-page shortcut:** a page directory entry can point **directly** to a single **4-MiB page frame** (controlled by a page-size bit), skipping the second level entirely — efficient for large, contiguous structures.
- **Coarse-grained permissions:** directory entries **also** carry permission bits, letting the OS mark an **entire 4-MiB region** read-only in **one** entry, rather than setting bits on each of 1024 individual 4-KiB pages.

#### Generalizing to More Levels — Tries
- This structure is a form of **trie** (digital tree / radix tree): the virtual page number is divided into consecutive **groups of bits**, each group indexing one **level** of the tree, starting from the **leftmost** group at the **top**.
- **Example — AMD64 (Opteron, Athlon 64; also "IA-32e" on Intel):** uses **FOUR-level** page tables.
    - Virtual addresses limited to **48 bits** (despite being "64-bit"); page size still 4 KiB → **12 bits** offset, **36 bits** for the page number.
    - Entries now **8 bytes** (larger physical addresses) → a 4-KiB chunk holds only **512** entries (not 1024), spanning **2 MiB**.
    - **Branching factor 512** at each level → 9 bits needed per level (2⁹=512) → **36 bits ÷ 9 = four levels**.
- **Challenge:** achieving good performance with **four** levels is hard — extending to **full** 64-bit addresses would need **even more** levels.
- **Other 64-bit designs differ:** Intel Itanium uses **linear** or **hashed** page tables; PowerPC uses **hashed** page tables (see next section).

### 5.8 Hashed Page Tables (§6.3.4)

#### The Radical Departure
- **Rejects** the "clumping" assumption entirely — assumes valid/invalid pages might be **scattered randomly** throughout the address space (more flexibility for runtime-environment designers).
- Still assumes **sparsity** overall (most entries invalid, shouldn't be stored) — but **no assumption** about clustering.

#### Consequence: Need a New Lookup Mechanism
- Since valid entries might be **anywhere**, you can't index by position within a chunk anymore — you need an **entirely different** lookup approach: a **hash table**.

#### Structure
- A **hashed page table** = an array of **hash buckets**, each a fixed-size structure holding a **small number** of page table entries.
    - **Itanium:** 1 entry per bucket.
    - **PowerPC:** 8 entries per bucket.
- **Simplest hash function:** page number **modulo** number of buckets. (E.g., 1,000,000 buckets → pages 0, 1,000,000, 2,000,000, ... all hash to bucket 0.)

#### Handling Collisions
- **Assumption for good performance:** only rarely will **multiple valid** entries land in the same bucket (a **hash collision**). Table size should **scale with** the number of valid entries (systems with lots of valid entries typically have lots of physical memory anyway, hence room for a bigger table).
- **Consequence of possible collisions:** each entry now needs an explicit **tag** recording **which** virtual page number it describes (unlike linear/multilevel tables, where the page number was **implicit** in the entry's position).
- **If a collision occurs beyond the bucket's built-in capacity** (>8 on PowerPC, or ANY collision on Itanium): the OS **chains** extra memory onto the bucket as a **linked list** — expensive but rare. Hardware walkers typically **don't** handle this case directly; if no matching tag is found in the bucket, an **interrupt** hands control to the OS to search the overflow chain.

#### Example (returning to the small Figure 6.10 scenario)
- Hashed table, 4 buckets, 1 entry each; hash = page number mod 4.
- Page 6 mod 4 = **2** → its entry (page 6 → frame 3) sits at **bucket position 2**.

```
Bucket:  0        1        2        3
Valid:   1        1        1        0
Page:    0        1        6        X
Frame:   1        0        3        X
```

#### Larger Entries Required
- Hashed page table entries must be **larger** than linear/multilevel entries, because they need: (a) the **page number tag**, and (b) potentially a **pointer to an overflow chain**.
- **Example:** Itanium uses **32-byte** hashed-page-table entries vs. **8-byte** linear-page-table entries.

#### Software TLB Variant
- Some OSes treat a hashed page table as a **"software TLB"** — holding only **selected** entries, with **no** overflow-chain handling needed (entries that don't fit are simply **omitted**).
- A **slower multilevel page table** then serves as a **comprehensive fallback** for software-TLB misses.
- **Particularly attractive** when porting an OS (e.g., Linux) originally built around multilevel page tables to a new architecture.

### 5.9 Segmentation (§6.3.5)

> **Historical note:** segmentation (in this classic sense) has **gone extinct** in modern systems — this section is presented for **historical** completeness and "can be omitted with no great loss."

#### The Core Idea
- Recall: virtual addresses = **names** for objects; physical addresses = **storage locations**.
- **Segmentation's defining property:** virtual addresses name objects at **TWO granularities simultaneously** — each address names both an **aggregate object** (a **segment** — e.g., a table or file) **and** a **specific location within it** (e.g., a table entry, or byte offset).
- **Analogy (from the text):** "Max Hailperin" identifies both the **family** (Hailperin) and the **specific person** within it (Max).

#### Structure
- Each virtual address = **segment number** + **offset within the segment**.
- **Key difference from paging:** pages are a **pure implementation detail** with **no logical meaning**; segments correspond to **actual logical objects** (files, stacks, tables) — and thus serve as **natural units for protection and sharing**.
- **BUT:** segments **can't have a fixed size** (unlike 4-KiB pages) — each segment has its **own natural size** (e.g., matching a file's actual size).

#### Pure Segmentation's Problems
- **Segment table** (analogous to a page table): specifies, per segment, a **starting physical address**, **size**, and **permissions**.
- **Each segment maps to a CONTIGUOUS range of physical memory** — meaning pure segmentation **reintroduces external fragmentation** (hard to find enough contiguous free memory for a new segment).
- **Also:** poor support for moving inactive data to disk (only an **entire segment** can be transferred at once — no fine-grained paging).

#### The Fix: Combine Segmentation With Paging
- Each process uses **two-part addresses** (segment number + offset); the MMU translates in **two stages**, using **both** a segment table **and** a page table.
- **End result:** segments can occupy **non-contiguous** page frames, and **individual pages** of a segment can be moved to disk.

#### Two Historical Implementations

**(a) IA-32 style — ONE shared page table for ALL segments:**
1. Look up the **segment number** in the segment table → get a **starting address**, length, permissions.
2. Add the segment's starting address to the **offset** → produces a **"linear address."**
3. Treat the linear address as an **ordinary paged virtual address**, translated via a **single, unified page table** shared by all segments.

```
Segment number + Offset within segment
        │
        ▼
 [Segment Table] → base address, length, permissions
        │ (add offset to base)
        ▼
   "Linear Address"
        │
        ▼
 [Unified Page Table] (same mechanism as ordinary paging)
        │
        ▼
  Physical Address
```

**(b) Multics style — a SEPARATE page table PER segment:**
1. Look up the segment number in the segment table → get length/permissions **AND** a **pointer to that segment's OWN private page table**.
2. Use this **segment-specific** page table to translate the **offset within the segment**.

```
Segment number  |  Page number  |  Offset within page
       │
       ▼
[Segment Table] → pointer to THIS segment's own page table
       │
       ▼
[Segment-Specific Page Table]
       │
       ▼
Physical Address
```

#### Comparing the Two Approaches
- **IA-32 approach LOOKS simpler** (one shared page table vs. many).
- **BUT it has a real disadvantage:** it forces each segment into a **single contiguous region** of **linear address space** — reintroducing the **complexities of contiguous allocation** (and potential external fragmentation) **at the linear-address level**, which the Multics approach **avoids** entirely (each segment gets its own independent page table, no forced contiguous linear-address allocation).

#### Why Segmentation Went Extinct
- Many of segmentation's protection/sharing benefits can be **simulated using paging alone**.
- Hardware designers concluded the **cost** (money + performance) of segmentation support **wasn't justified**.
- Since popular OSes (UNIX, Windows, Linux) are designed to be **portable** across architectures — **some of which don't support segmentation** — **none** of these OSes actually use segmentation, **even where the hardware supports it**.
- **Result — a self-reinforcing cycle of disincentives:** architecture designers have no reason to support segmentation (since OSes don't use it), and OS designers have no reason to use it (since they can't rely on hardware support existing everywhere).

#### A Modern Echo of Segmentation — Multiple ASIDs Per Process
- Modern architectures **don't** support classic segmentation, but **do** have something reminiscent of it: **multiple ASIDs per process**.
- The **top few bits** of a virtual address select **one of several** ASID registers for that process; translation then proceeds: **top bits → ASID**, then **ASID + remaining bits → page frame + offset**.
- If the OS sets multiple processes to use the **same** ASID for a shared library, they end up sharing **page frames, page table entries, AND TLB entries** — similar in spirit to sharing a segment, but **invisible at the application level** (unlike true segmentation).
- **Limited scale:** e.g., **8** ASIDs per process on Itanium, **16** on 32-bit PowerPC — nowhere near the flexibility of true segment numbers.

---

## 6. Policies for Virtual Memory (§6.4)

> Mechanisms alone aren't enough — the OS needs **policies** governing how mechanisms are actually **used**. Three key policy questions:

1. **Fetch policy:** WHEN is a page assigned a page frame?
2. **Placement policy:** WHICH page frame is assigned to a given page?
3. **Replacement policy:** WHICH page gets evicted (moved to disk) to free up a frame?

> All are **highly workload-dependent** — a policy great for one workload might be terrible for another — requiring extensive **experimentation**.

### 6.1 Fetch Policy (§6.4.1)

#### The Two Extremes
- **Extreme A — assign frames immediately** upon learning of a page's existence (e.g., all of a program's pages assigned at process start). **Conflicts** with virtual memory's own goals (e.g., fast startup for programs with large, rarely-used parts) — **discarded**.
- **Extreme B — Demand Paging:** create the mapping for each page **only** in response to an actual **page fault** when accessing it.
- **In between: Prepaging** — attempting to **anticipate** future page use, fetching **ahead of** actual demand.

#### When Demand Paging Makes Sense
1. **Limited spatial locality:** the OS can't reliably **predict** future page use — prepaging wouldn't pay off.
2. **Low page-fault cost:** even accurate predictions save little, since avoiding a cheap fault isn't worth much.

#### Linux's Actual Policy — A Hybrid
- **Zero-filled pages** (low fault cost, per §4.7's techniques): Linux uses **demand paging**.
- **Files mapped into virtual memory** (disk reads are slow): Linux **ordinarily** uses a variant of **prepaging** — **UNLESS** the application explicitly tells the OS (via `madvise`) that the file will be accessed **"randomly"**, in which case Linux switches to **demand paging** for that file's pages.

#### Clustered Paging (a.k.a. Read Around)
- The most common form of prepaging: each page fault triggers fetching a **cluster of neighboring pages**, including the faulting one.
- **"Read around"** (fetches pages **around** the fault) vs. **"read ahead"** (fetches the faulting page **and later** pages, but not earlier ones).
- **Linux specifics:** reads a **cluster of 16 pages**, aligned to a multiple of 16 — so the extra 15 pages could be **before**, **after**, or **any mix** relative to the faulting page.
- **Microsoft Windows:** uses a **smaller** cluster size, varying by whether the fault is on an **instruction** or **data** page (instructions exhibit more spatial locality → **larger** cluster for instructions).

#### Linux's Read Around — A Subtlety (Page Cache)
- On a fault, the handler fetches the **whole cluster** into RAM, but only **updates the page table entry for the faulting page**.
- The **other** fetched pages sit in RAM but **aren't yet mapped** into any virtual address space — this status is the **page cache**.
- Subsequent faults on those **already-cached** pages become **fast** (just update the page table — a **minor page fault**), rather than requiring another slow **disk read** (a **major page fault**).
- **Key insight:** read-around **doesn't reduce the total number of page faults** — it **converts** many from **major** (disk read) to **minor** (page table update only).

#### Why Prepaging's Success Rate Doesn't Need to Be High
- **Disk read ≈ 10ms**; reading 16 pages takes only **slightly** longer than reading 1.
- **Example calculation:** if extra processing per prepaged page costs 0.5ms, reading a 16-page cluster (vs. 1 page) adds **7.5ms** total extra cost. This is **more than repaid** if even **ONE** of the 15 extra pages later gets used (avoiding a full 10ms disk access for it).

### 6.2 Placement Policy (§6.4.2)

- Decides **WHICH** unused page frame a page should occupy — influencing which **physical addresses** get referenced, which can affect **cache miss rates**.

#### Beyond Cache Performance — Other Reasons Placement Matters
1. **Large-scale multiprocessor systems:** main memory is **distributed** across processing nodes — some page frames are **faster to access** from a given processor. (Microsoft Windows Server 2003 accounts for this.)
2. **Energy savings (increasingly important):** confining accesses to a **portion** of memory allows the **rest** to enter standby/low-power mode.

#### Why Placement Affects Cache Miss Rate — Cache Organization Review
- An **idealized (fully associative)** cache holds the *n* most recently accessed blocks, searchable in full each access — **infeasible** for realistically large caches.
- **Real (set-associative) caches** restrict any given memory location to a **small set of positions** — e.g., a **two-way set-associative** cache has just **two** alternative locations per memory block.
- **Many caches (beyond L1)** select a set using the **physical address**, not the virtual address — meaning **physical placement** directly affects which cache set a page's data lands in.

#### Conflict Misses vs. Capacity Misses
- If a process repeatedly accesses **three** blocks that all happen to compete for the **same set** in a two-way set-associative cache, the **miss rate will be very high** — even if the cache is **otherwise** large enough to hold far more data.
- This is called a **conflict miss** (as opposed to a **capacity miss**, from genuinely running out of cache space) — and each miss costs a slow main-memory access.

#### Historical Trend
- **Lower cache associativity** → conflict misses matter **more**. Careful placement mattered **more** when caches were **external** to the CPU chip (often low associativity). **Modern, integrated, high-associativity caches** have made placement policy **less critical** than it once was.

#### Two Placement Strategies

**(a) Page Coloring**
- **Assumption:** pages that **wouldn't** conflict without virtual memory translation **shouldn't** conflict **even with** translation.
- **Main argument in favor:** preserves any **careful, cache-conscious allocation** already done at the **virtual address** level (e.g., compilers/programmers padding array rows to avoid repeatedly hitting the same cache set — common in high-performance scientific computing like weather forecasting).

**(b) Bin Hopping**
- **Assumption:** pages mapped into frames **close together in time** are likely to be accessed with **temporal proximity** too — so give them **non-conflicting** frames.
- **Main argument in favor:** experimental evidence suggests it **outperforms** page coloring **absent** any cache-conscious data allocation — likely because bin hopping is **more flexible** (ranks **all** possible cache locations from most- to least-preferred, rather than just picking "the" preferred spot as page coloring does).

### 6.3 Replacement Policy (§6.4.3)

#### Why Evict Proactively, Not Just On-Demand?
- Rather than waiting for a page fault to trigger eviction, OSes typically maintain an **inventory of free page frames**, replenished when it drops below a **low-water mark**, until it exceeds a **high-water mark**.

**Three advantages of proactive eviction:**
1. **Avoids delaying the faulting process:** last-minute eviction (in direct response to a fault) **further delays** the very process that faulted; proactive eviction can happen **during otherwise-idle hardware time**, improving both response time and throughput.
2. **Batched disk writes:** proactively evicting **dirty** pages lets the OS **batch several page write-backs into a single disk operation** — more efficient use of the disk.
3. **Cheap "undo" for bad eviction decisions:** between being **freed** and being **reused**, a page frame can **retain a copy** of its most recently held page — letting the OS **cheaply recover** from a poor eviction choice via a **minor** page fault (remapping only) instead of a **major** one (re-reading from disk). **Especially valuable** if the MMU doesn't directly track which pages were recently referenced.

#### Microsoft Windows' Four-List Pipeline (A Concrete Example)

```
Page Table (in active use)
     │
     ├──(dirty page chosen for replacement)──▶ Modified Page List
     │                                              │
     │                                    (modified page writer
     │                                     writes to disk, eventually)
     │                                              ▼
     └──(clean page chosen for replacement)──▶ Standby Page List
                                                     │
                                          (stays unused long enough)
                                                     ▼
                                              Free Page List
                                                     │
                                        (zero page thread fills w/ zeros)
                                                     ▼
                                              Zero Page List
                                                     │
                                    (mapped back into a process on page fault)
                                                     ▼
                                              Page Table (reused!)
```

- **Modified Page List:** holds recently-evicted **dirty** pages; retains old mapping info so a **soft (minor) page fault** can recover the page cheaply if needed again soon.
- **Standby Page List:** holds evicted **clean** pages (or modified pages that have since been written to disk); also retains recovery info for soft page faults.
- **Free Page List:** pages that have sat on standby long enough without being reclaimed; feeds the **zero page thread** (which proactively zeros pages, per §4.7) and is preferred for **reading pages in from disk**.
- **Zero Page List:** pre-zeroed frames, ready to be **instantly** mapped into a process on a zero-fill page fault.

#### Balancing Eviction Rate With Fetch Rate — Local vs. Global Replacement

**Local Replacement**
- Keeps eviction/fetch rates balanced **per process, individually**. A process with **many** page faults must give up **its own** frames — it can't push **other** processes' pages out.
- Requires a **separate allocation policy** deciding how many frames each process is **allowed**.
- **Used by:** Microsoft Windows (following the lead of DEC's **VMS**, later HP's **OpenVMS**).
- **Original rationale (from VMS):** **performance isolation** — prevent one process's poor locality from hurting **other** processes. (Arguably **less** relevant for typical desktop/server workloads than for VMS's original multi-user, real-time/timesharing context.)

**Global Replacement**
- Keeps eviction/fetch rates balanced **system-wide**. A process with many faults **CAN** cause **other** processes' pages to be evicted.
- **No separate allocation policy needed** — fetch/replacement policies naturally **reallocate** frames between processes as demand shifts.
- **Used by:** the entire **UNIX family** (Linux, Mac OS X, etc.).
- **Simpler**, and **adapts more flexibly** to processes whose memory needs aren't known in advance — generally considered **more efficient** for typical workloads.

#### When Total Working Sets Exceed Available Frames
- **Local replacement symptom:** the **allocation policy** can't give each process a **reasonable** number of frames.
- **Global replacement symptom: THRASHING** — the system spends essentially all its time **paging and process-switching**, with **extremely low throughput**.

#### Swapping — The Traditional Overload Solution
- The OS picks **entire processes** to evict **completely** from memory (writing **all** their data to disk), and removes their threads from the **scheduler's runnable set** (so they stop competing for memory).
- After running the **remaining** processes a while, the OS swaps some **out** and some earlier victims **back in**.
- **Cost:** adds complexity, makes scheduling **choppier**.
- **Global-replacement systems (e.g., Linux):** often **OMIT** swapping, relying on users to **avoid** thrashing themselves.
- **Local-replacement systems (e.g., Windows):** have **little choice** but to include swapping.
- **Common terminology confusion:** people sometimes incorrectly call ordinary **paging** "swapping" (e.g., "Linux swapping," when it's really just moving **individual pages**, not entire address spaces).

#### Specific Replacement Policies

**Optimal Replacement (OPT) — the unrealistic gold standard**
- If the OS knew the **entire future sequence** of memory accesses, it could always evict the page with its **next use furthest in the future**.
- **Provably optimal** (mathematically) for minimizing the number of demand fetches — hence the name **OPT**.
- **Not achievable in practice** (requires knowing the future), but serves as a **benchmark** for evaluating realistic policies.

**Least Recently Used (LRU) — a realistic approximation**
- Uses only **PAST** information: evict the page that has gone the **longest without being accessed**.
- **Rationale:** if access probabilities shift only **slowly** over time, pages accessed frequently recently are **likely** to be accessed again soon, and vice versa.
- **More realistic than OPT**, but **still not entirely practical**: requires maintaining an ordered-by-recency list, updated on **every single memory access** — expensive.
- **Not universally optimal even among "past-only" policies:** e.g., a process **looping repeatedly** through a set of pages will make LRU perform **terribly** (it'll evict the page that's about to be reused **soonest**!). Nonetheless, LRU performs **reasonably well** in many realistic settings, so many practical policies try to **approximate** it.

**First In, First Out (FIFO) — simple but not very smart**
- Evict whichever page frame has held its **current page the LONGEST**, regardless of how recently/frequently it's been **accessed**.
- **Key difference from LRU:** FIFO looks at **when fetched**; LRU looks at **when last accessed**. A page fetched long ago but used **constantly** would be evicted by FIFO, but **protected** by LRU.
- **Performance:** early simulations showed FIFO performs comparably to **random replacement** — not very smart.
- **Belady's Anomaly:** FIFO can suffer the **counterintuitive** phenomenon where **INCREASING** the number of available page frames **INCREASES** the number of page faults (rather than decreasing, as one would expect)!

#### Worked Comparison — OPT vs. LRU vs. FIFO (Figure 6.19-style)

- On a small hypothetical 2-frame system, given some reference sequence, OPT achieves the **most hits**, LRU achieves **fewer** hits than OPT but **more** than FIFO, and FIFO performs **worst** of the three in that particular example.
- **Important caveat:** this ordering is **NOT universal** — it's possible to construct examples where **FIFO beats LRU** (Exercise 6.11), and examples where **LRU or FIFO ties OPT** (Exercise 6.12) — OPT is only guaranteed to be **at least as good**, not **always strictly better**.

#### Stack Algorithms — Immunity to Belady's Anomaly
- **Both OPT and LRU are IMMUNE** to Belady's anomaly, as is any member of the broader class of **stack algorithms**.
- **Definition:** a replacement policy is a stack algorithm if, running the **same** reference sequence on two systems (one with *n* frames, one with *n+1*), at **every point** in the sequence, the *n* pages resident on the first system are **ALSO** resident (among the *n+1*) on the second system.
- **Example (LRU):** the *n* most recently accessed pages are trivially a **subset** of the *n+1* most recently accessed pages — this property holds **automatically**.

#### Improving FIFO — The "Second Chance" Refinement
- Recall from the replacement-policy overview: evicted pages **don't** immediately vanish — they enter a **free-frame inventory**, retrievable via a **cheap minor fault** if reaccessed before actual reuse.
- **This dramatically improves FIFO's practical performance:** if FIFO evicts a **frequently-used** page, it's likely to be **faulted back in soon** (cheaply) — at which point it goes to the **END** of the FIFO list again, buying it more time before being considered for eviction again.
- **Net effect:** FIFO essentially puts pages "on probation" — pages accessed **while on probation** aren't actually replaced (permanently) — so the pages that **actually** end up evicted are approximately those **not recently used**, **approximating LRU**.
- **This FIFO-based LRU approximation is called Segmented FIFO (SFIFO).**

#### Using Reference Bits — A More Direct Approach

- Some MMUs provide a **reference bit** per page table entry: **any** address translation through that entry sets the bit to **1** (and if it's a write, also sets the **dirty bit**).
- The replacement policy can **inspect and reset** reference bits, gaining a cheaper, more direct signal of recent use — **without** needing the "evict and see if it gets faulted back in" indirection.
- **Reference bits aren't trivial to implement efficiently** (especially in multiprocessor systems) — some systems omit them.

#### Clock Replacement (Using Reference Bits)

- The OS considers page frames **cyclically**, like a clock hand sweeping around numbered positions.
- **At the hand's current position:**
    - If **reference bit = 0** → page hasn't been used recently → **choose it for replacement**.
    - If **reference bit = 1** → reset it to **0**, and **move on** to the next candidate (giving the page "a second chance" to prove its usefulness before the hand comes back around).

**Refinement incorporating the dirty bit too:**
| Reference | Dirty | Action |
|---|---|---|
| 1 | (either) | Reset reference to 0, move to next candidate |
| 0 | 0 | **Choose this page for replacement** (clean, unreferenced) |
| 0 | 1 | Start writing the page to disk, move to next candidate; once written, set dirty to 0 |

- **Usage in practice:** some versions of Microsoft Windows use **clock replacement** as the **local** replacement policy on systems where reference bits are available, and **FIFO** otherwise — showing these techniques are used **locally** (per-process) as well as globally.

---

## 7. Security and Virtual Memory (§6.5)

### 7.1 The Central Security Role
- Virtual memory is **central to security** because it provides the mechanism for equipping each process with its **own protected memory** — the **details** of this are deferred to Chapter 7 (on processes/protection).

### 7.2 One Classic Issue Discussed Here — Sensitive Data on Disk

#### The Problem
- Virtual memory's **traditional** use (§4.8) — simulating lots of RAM by moving inactive pages to disk — creates a **security risk**: a program handling **confidential data** might have that data **silently written to disk** by the virtual memory system, **without the programmer's knowledge or intent**.
- **Why this matters:** if an adversary later obtains **physical possession** of the disk, they can read **everything** on it — including data the programmer believed was **only ever in volatile RAM**.

#### The Threat Model
- Many **cryptographic systems** are explicitly designed around this exact threat: **disks are presumed subject to theft**.
- **Familiar example:** login passwords are **NOT** typically stored on disk directly — instead, systems store the result of a **one-way function** applied to the password. This suffices for **verifying** entered passwords **without** ever exposing the actual password value to disk-theft risk.
- Programs like the **login program** and **password-changing program** deliberately keep the raw password **only temporarily** in main memory.

#### The Silent Failure Mode
- **Programmers may believe** their program keeps sensitive data **only** in volatile RAM, **never** writing it to disk — and may even **carefully overwrite** that memory afterward (e.g., with zeros).
- **BUT:** if the virtual memory system happened to **write out that page** during the vulnerable window (as an ordinary, invisible background operation), a **lasting record** of the confidential data ends up on disk anyway — **completely invisible** to the application programmer, since virtual memory operates **behind the scenes** by design.

#### The Fix — `mlock` / `mlockall`
- To protect against this, you must **forbid the OS from ever writing a sensitive memory region to disk** — creating an **exception** to the normal replacement policy where certain pages are **NEVER** chosen for replacement.
- **POSIX standard API:** `mlock` and `mlockall` procedures serve exactly this purpose.
- **Restriction:** because **overuse** of these calls could **monopolize all physical memory** (starving everyone else), only **privileged** processes are allowed to use them.
- **Practical note:** programs handling sensitive info (e.g., the login program) typically **already** need elevated privileges for other reasons anyway, so this restriction isn't usually an additional obstacle for the programs that legitimately need it.

---

## 8. Glossary of Key Terms

| Term | Definition |
|---|---|
| **Virtual address** | An address used by a running program to name/identify an object; may differ from its physical storage location. |
| **Physical address** | An address used by the memory hardware to identify an actual storage location. |
| **Memory Management Unit (MMU)** | Hardware that translates virtual addresses generated by the processor into physical addresses sent to memory. |
| **Page** | A fixed-size block of virtual address space, the granularity at which virtual memory mappings are recorded. |
| **Page frame** | A fixed-size block of physical memory, matching the page size, that can hold one page's worth of data. |
| **Page table** | The OS-maintained data structure recording the virtual-page-to-physical-frame mapping. |
| **Page fault** | An interrupt triggered when the processor generates a virtual address with no (or an illegal) mapping. |
| **Process** | A group of one or more threads sharing a common virtual address space (and broader protection context). |
| **Text (segment)** | The read-only region of a process's address space holding program instructions and read-only data. |
| **DLL (Dynamic-Link Library)** | Microsoft's term for a shared library, mappable read-only into multiple processes. |
| **Copy on Write (COW)** | A technique where shared read-only pages are copied only lazily, at the moment a process actually attempts to write to them. |
| **External fragmentation** | Enough total free memory exists, but not contiguously, preventing allocation of a large-enough single block. |
| **Working set** | The active, currently-needed portion of a process's (or all processes') memory. |
| **Dirty page** | A page that has been modified since it was last written back to disk (or last read in). |
| **Dirty bit** | A hardware bit per page, set automatically on any write, used to track dirty pages efficiently. |
| **Translation Lookaside Buffer (TLB)** | A small, fast hardware cache of recently used virtual-to-physical address translations. |
| **TLB hit / miss** | Whether a needed translation is (hit) or isn't (miss) currently present in the TLB. |
| **Address Space Identifier (ASID)** | A tag on TLB entries identifying which process they belong to, avoiding the need to flush the TLB on every process switch. |
| **Page table walker** | Dedicated MMU hardware that performs page table lookups without OS software intervention. |
| **Linear page table** | A simple array-based page table, one entry per virtual page, indexed directly by page number. |
| **Multilevel (hierarchical / forward-mapped) page table** | A tree-structured page table avoiding storage of entirely-invalid chunks, without needing recursive virtual memory. |
| **Trie (digital tree / radix tree)** | The general tree structure underlying multilevel page tables, indexed by successive groups of address bits. |
| **Hashed page table** | A page table storing only valid entries in a hash table, tagged by virtual page number, without assuming clustering. |
| **Hash collision** | When multiple valid page table entries are assigned to the same hash bucket. |
| **Segment** | In classic segmentation, a logical, variable-sized object (e.g., a file or table) named by part of a virtual address. |
| **Segmentation** | A (now-extinct) virtual memory scheme naming objects at two granularities: segment and offset within segment. |
| **Fetch policy** | The policy governing WHEN a page is assigned a page frame. |
| **Demand paging** | Fetch policy: create a page's mapping only in response to an actual page fault. |
| **Prepaging** | Fetch policy: attempt to anticipate and fetch pages before they're actually needed. |
| **Clustered paging / read around** | A common prepaging technique: fetch a cluster of neighboring pages around a faulting page. |
| **Page cache** | RAM holding pages that have been fetched but not yet mapped into any process's page table. |
| **Major / minor page fault** | A page fault requiring an actual disk read (major) vs. one resolved by simply updating the page table (minor / "soft"). |
| **Placement policy** | The policy governing WHICH page frame is assigned to a given page. |
| **Conflict miss** | A cache miss caused by multiple actively-used blocks competing for the same limited set of cache positions. |
| **Capacity miss** | A cache miss caused by genuinely exceeding the cache's total storage capacity. |
| **Page coloring** | A placement policy preserving virtual-address-level cache-consciousness by avoiding new conflicts under translation. |
| **Bin hopping** | A placement policy assuming temporally-close page allocations should get non-conflicting cache-relevant frames. |
| **Replacement policy** | The policy governing WHICH page is evicted to free up a frame. |
| **Local replacement** | Balances eviction/fetch rates individually per process; a faulting process gives up only its own frames. |
| **Global replacement** | Balances eviction/fetch rates system-wide; any process's pages may be evicted regardless of who's faulting. |
| **Thrashing** | The system spending nearly all its time paging/switching, with almost no actual throughput. |
| **Swapping** | Evicting entire processes from memory (and the scheduler) to relieve severe memory overload. |
| **Optimal Replacement (OPT)** | The theoretically optimal (but unrealizable) policy: evict the page whose next use is furthest in the future. |
| **Least Recently Used (LRU)** | Evict the page that has gone the longest without being accessed. |
| **First In, First Out (FIFO)** | Evict the page frame that has held its current page the longest, regardless of recent access. |
| **Belady's Anomaly** | The counterintuitive phenomenon (possible under FIFO) where adding more page frames increases page faults. |
| **Stack algorithm** | A replacement policy class (including OPT and LRU) immune to Belady's anomaly. |
| **Segmented FIFO (SFIFO)** | A FIFO-based approximation of LRU, exploiting cheap recovery of recently-evicted, still-cached pages. |
| **Reference bit** | A hardware bit per page, set on any access, used by policies like clock replacement to approximate recency of use. |
| **Clock replacement** | A replacement policy cycling through page frames like a clock hand, using reference bits to give pages a "second chance." |
| **mlock / mlockall** | POSIX calls preventing specific memory regions from ever being written to disk, protecting sensitive data. |

---

## 9. Summary Tables

### 9.1 Page Table Structures Compared

| | Linear | Multilevel | Hashed |
|---|---|---|---|
| **Structure** | Flat array | Tree (trie) | Hash table + buckets |
| **Assumes clumping?** | Yes | Yes | No |
| **How avoids storing invalid chunks** | Recursive virtual memory | Tree branches simply omitted | Only valid entries stored at all |
| **Entry size** | Small (e.g., 4-8 bytes) | Small (e.g., 4-8 bytes) | Larger (needs tag + overflow pointer, e.g., 32 bytes on Itanium) |
| **Per-process or shared?** | Per-process | Per-process | Can be shared (with ASID tags) |
| **Real examples** | VAX, Itanium (optional) | IA-32 (2-level), AMD64 (4-level) | Itanium (optional), PowerPC |

### 9.2 The Three Virtual Memory Policies

| Policy | Question Answered | Extremes / Options |
|---|---|---|
| **Fetch** | WHEN is a page frame assigned? | Demand paging (on fault) vs. Prepaging (anticipatory) |
| **Placement** | WHICH frame is assigned? | Page coloring vs. Bin hopping |
| **Replacement** | WHICH page is evicted? | OPT, LRU, FIFO, SFIFO, Clock — Local vs. Global scope |

### 9.3 Replacement Policies Ranked by Realism/Performance (General Tendency)

| Policy | Uses Future Info? | Realistic? | Typical Relative Performance |
|---|---|---|---|
| OPT | Yes (theoretical only) | No | Best (provably optimal) |
| LRU | No (past only) | Nearly (expensive to track exactly) | Good in most realistic workloads |
| SFIFO / Clock | No | Yes (practical approximations of LRU) | Good, cheaper to implement than exact LRU |
| FIFO | No | Yes (simple) | Comparable to random; subject to Belady's anomaly |

### 9.4 Local vs. Global Replacement

| | Local Replacement | Global Replacement |
|---|---|---|
| **Balances rate...** | Per process | System-wide |
| **Faulting process affects...** | Only its own frames | Potentially any process's frames |
| **Needs separate allocation policy?** | Yes | No |
| **Used by** | Microsoft Windows (via VMS heritage) | UNIX family (Linux, Mac OS X) |
| **Overload symptom** | Allocation policy can't give reasonable frame counts | Thrashing |
| **Typically includes swapping?** | Yes (little choice) | Often omitted (e.g., Linux) |

---

## 10. Worked Exercise Solutions

### Exercise 6.1 — Extended Clerk Analogy
- **Scenario 1 (same name, different documents):** two executives, X and Y, both ask the clerk to "file this under 'Q3 Report'" for their own respective, unrelated documents. If the clerk later gets a request "fetch the Q3 Report" from **executive X**, the clerk must know **which executive is asking** to retrieve the **correct** document — i.e., the name "Q3 Report" is only meaningful **relative to** which executive is using it.
- **Scenario 2 (different names, same document):** two executives are **co-authoring** a single shared budget spreadsheet; X calls it "the budget," while Y (who received it via a different channel) calls it "the numbers file." The clerk must recognize that these **two different names**, from two different executives, actually refer to the **SAME underlying physical document**.
- **How the clerk copes:** the clerk needs a **per-executive** lookup table, mapping each executive's names to the **actual physical location** of the corresponding document, allowing the **same name** to map to different documents (Scenario 1) or **different names** to map to the same document (Scenario 2).
- **Connection to virtual memory:** this is **exactly** the role of the **page table** (per-process) — it lets **virtual addresses (names)** be interpreted **relative to which process is using them**, exactly like the clerk's per-executive lookup table, supporting both same-name-different-object and different-name-same-object scenarios (§1.6-1.7).

### Exercise 6.2 — COW for Initial Writable Data
- An executable file's writable data region has **known initial contents** (e.g., initialized global variables) — but this **initial content is only needed ONCE**, at process startup, and typically gets **modified** soon after as the program runs.
- **How COW applies:** the OS can map this initial-data region as **shared, read-only** across **multiple instances** of the same program being launched — since the **initial values** are identical for all instances, they can literally share **one physical copy** of that page. **The moment** any instance **writes** to its copy (as normal execution modifies its own data), COW triggers, giving **that instance** its own **private, writable copy** — while other, not-yet-modified instances continue sharing the original.
- **Why this is beneficial:** saves memory (one shared copy serves multiple not-yet-diverged instances) and avoids unnecessary upfront copying for instances that might modify their data quickly anyway.

### Exercise 6.3 — Factors in Page Size Trends
- **Arguments for SMALLER pages:**
    - Less **internal fragmentation** (less wasted space when a page is only partially used).
    - Finer-grained control over persistence/dirty tracking, permissions, etc.
- **Arguments for LARGER pages:**
    - **Fewer TLB entries needed** to cover a given amount of memory → **less TLB pressure**, better performance for large working sets (§5.3).
    - **Smaller page tables** (fewer total entries needed).
    - Fewer, larger disk transfers when paging (more efficient I/O).
- **What's changed over the decades:**
    - **Argument for small pages that has WEAKENED:** internal fragmentation matters proportionally **less** as typical program/data sizes and available RAM have **grown enormously** — a fixed amount of wasted space per page is a **smaller percentage** of a much larger overall memory footprint than it used to be.
    - **Argument for large pages that has STRENGTHENED:** working sets and total memory have grown **dramatically**, while TLB entry counts have grown much more **slowly** (constrained by needing to stay small/fast, per §5.3) — making TLB pressure a **bigger** relative concern over time, favoring **larger** pages to cover more memory per TLB entry.

### Exercise 6.4 — Why Page Sizes Stay Fixed for Years
- **Hardware/software ecosystem inertia:** changing the standard page size requires **coordinated changes** across CPU architecture, OS kernels, device drivers, and application assumptions — an enormous **compatibility burden**.
- **Existing software assumptions:** many programs/libraries may (even if they shouldn't) implicitly assume a particular page size (e.g., for alignment); changing it risks **breaking** things.
- **Standardization value:** a stable, widely-supported page size lets hardware and software ecosystems **optimize around a known constant**, and changing it undermines years of accumulated optimization/testing.
- *(This tension is why the "transparent huge pages" approach — mixing page sizes flexibly — represents a more recent, incremental compromise rather than an outright page-size change, as mentioned in the chapter's notes.)*

### Exercise 6.5 — Virtual/Physical Addresses for Page 6
- Given 4-KiB pages, page 6 spans virtual addresses **6×4096 = 24576** to **24576+4095 = 28671**.
- **First 4-byte word of page 6:** virtual address **24576**.
- **Last 4-byte word of page 6:** virtual address **28671 − 3 = 28668** (last 4-byte-aligned word).
- Since page 6 → page frame 3 (per Figure 6.10): physical base = 3×4096 = **12288**.
- **First word's physical address:** 12288 (i.e., 24576 − 24576 + 12288 = 12288).
- **Last word's physical address:** 12288 + (28668 − 24576) = 12288 + 4092 = **16380**.

### Exercise 6.6 — Formula for Address at Offset j in Page n
- With **k** rightmost bits as the offset:
  $$\text{address} = (n \times 2^k) + j$$
- (I.e., shift the page number left by k bits — equivalently multiply by the page size, 2^k — and add the offset j.)

### Exercise 6.7 — Formula for Physical Address Given f(n)
- Given virtual address **v**, with k rightmost bits as offset:
    - Page number: $n = \lfloor v / 2^k \rfloor$ (i.e., v right-shifted by k bits)
    - Offset: $j = v \mod 2^k$ (i.e., the rightmost k bits of v, unchanged)
    - Physical address:
      $$\text{physical address} = (f(n) \times 2^k) + (v \mod 2^k)$$

### Exercise 6.8 — Size of a Full 64-bit Linear Page Table
- 64-bit virtual addresses, 1-MiB (2²⁰ byte) pages → offset = 20 bits → page number = **64 − 20 = 44 bits** → **2⁴⁴ pages**.
- At 4 bytes/entry: total size = 2⁴⁴ × 4 = 2⁴⁶ bytes = **64 TiB** (tebibytes) — per process! (Confirming the chapter's claim that a full linear page table is "inconceivable" to store at 64-bit scale.)

### Exercise 6.9 — Calculating Page Numbers 1047552–1048575, and a Virtual Address Lookup
- The rightmost arrow of Figure 6.13 is the **1024th** (last) chunk of the page table, covering pages **1023×1024 = 1,047,552** through **1024×1024 − 1 = 1,048,575** (i.e., the 1024 pages spanned by the last of the 1024 page-table chunks).
- **Virtual address 4,290,777,130:** with 4-KiB pages (12-bit offset), page number = ⌊4,290,777,130 / 4096⌋ = **1,047,552** (approximately — this falls within the last chunk's range, i.e., **page 1047552**, the very first page of that last chunk, corresponding to page frame **100** per Figure 6.13's caption).
- **Can we tell where it's located in RAM?** Yes — per Figure 6.13, page 1,047,552 is held in **page frame 100** — so its data resides at physical address (100 × 4096) + (offset within the page).

### Exercise 6.10 — PAE Mode Calculations
**(a) Entries per chunk with 8-byte entries, 4-KiB chunks:**
$$4096 / 8 = 512 \text{ entries per chunk}$$

**(b) Virtual address range spanned by each page-table chunk:**
$$512 \text{ pages} \times 4\text{KiB/page} = 2048\text{ KiB} = 2\text{ MiB}$$

**(c) Virtual address range spanned by each page directory:**
$$512 \text{ chunks} \times 2\text{MiB/chunk} = 1024\text{ MiB} = 1\text{ GiB}$$

**(d) Number of page directories the new root points to:**
- Since PAE addresses up to **16×** as much RAM, and a single page directory now only spans 1 GiB (not the full 4 GiB it used to), and IA-32's total virtual address space is still 4 GiB: 4 GiB / 1 GiB = **4 page directories** — so the new root level has just **4 entries**.

**(e)** Diagram (described): Root (4 entries) → each points to a Page Directory (512 entries) → each points to a Page Table chunk (512 entries) → each points to a 4-KiB page frame — a **three-level** tree instead of IA-32's original two levels, with branching factors 4, 512, 512 respectively.

### Exercise 6.11 — Example Where FIFO Beats LRU
- Consider 2 page frames, reference sequence: **A, B, A, C, A** (A accessed repeatedly, interspersed).
- **FIFO** (evicts oldest-fetched): A,B loaded (misses). Ref A (hit, still same page, FIFO doesn't care about access recency). Ref C → miss, evict **A** (oldest fetched, even though just used!) → load C. Ref A → miss again... Actually let's use a clearer classic example: sequence **A,B,C,B,A** with 2 frames.
    - FIFO: A(miss,load A), B(miss,load B, evict none, frames full: A,B), C(miss, evict A [oldest], load C: frames B,C), B(HIT), A(miss, evict B [oldest now], load A) → 4 misses, 1 hit.
    - LRU: A(miss), B(miss, frames A,B), C(miss, evict A [least recently used], frames B,C), B(HIT, refresh B's recency), A(miss, evict C [now LRU since B was just touched], frames B,A) → 4 misses, 1 hit — **same** in this attempt.
- **A genuinely FIFO-favoring example:** sequence **A,B,C,A,B,D,A,B,C,D** with 3 frames is the classic Belady's-anomaly-adjacent example where FIFO can outperform LRU under specific cyclic access patterns with more frames than the simple case — constructing a minimal correct example requires careful trial; the key general principle (per the chapter) is that **cyclic access patterns longer than the frame count** can make LRU perform poorly (evicting the page about to be reused soonest) while FIFO's ignorance of recency can accidentally avoid this trap in specific sequences.

### Exercise 6.12 — Example Where LRU or FIFO Ties OPT
- **Simplest case:** any reference sequence with **NO repeated page accesses at all** (e.g., 2 frames, sequence A,B,C,D — every access is a miss regardless of policy, since nothing is ever reused) — here **OPT, LRU, and FIFO all perform identically** (every access is a miss; there's no "smart" choice that helps, since no page is ever needed again after being replaced).

### Exercise 6.13 — Example of Belady's Anomaly
- **Classic example:** reference sequence **1,2,3,4,1,2,5,1,2,3,4,5** with FIFO:
    - **3 frames:** results in **9 page faults**.
    - **4 frames:** results in **10 page faults** (MORE faults with MORE frames!) — this is the canonical textbook example of Belady's Anomaly under FIFO.

### Exercise 6.14 — Justifying OPT as a Stack Algorithm
- **Claim:** with *n* frames vs. *n+1* frames (same reference sequence, ties broken by replacing the lowest-numbered tied page), the *n* pages resident under the *n*-frame system are always a **subset** of the *n+1* pages resident under the *n+1*-frame system, at every point in time.
- **Justification (by induction on the reference sequence):** Initially (before any references), both hold 0 pages — trivially a subset. **Inductive step:** assume the property holds before processing the next reference. If it's a **hit** on both systems (or a miss on both, loaded into an already-different-but-still-consistent slot), the *n+1*-system's resident set can only **grow to still contain** the n-system's set, because: the *n+1*-system has **strictly more "room"** to keep pages around, so whenever the *n*-system is **forced** to evict a page (because it picks the one with the furthest-away next use, needing the space), the *n+1*-system either (a) still has a **free frame** available (having one **more** frame to start with) and doesn't need to evict anything, or (b) if it also must evict, it will evict the **same** page (since OPT's "furthest next use" choice, applied to a superset of pages, will still identify that same globally-furthest-next-use page as the worst one to keep, given the tie-breaking convention) — preserving the subset property.

### Exercise 6.15 — Why Reboot Between Paging Measurement Trials
- Without rebooting, **residual state** from a previous run — e.g., pages still cached in the **page cache**, TLB entries, or other OS-level caching of disk data — could make a **subsequent** run of the **same** experiment **artificially faster** (fewer major page faults, since the relevant data might already be sitting in RAM from before) — **contaminating** the measurement and making it **not representative** of a fresh, "cold" run. Rebooting ensures each trial starts from a **consistent, clean baseline**, so the measured paging behavior reflects the activity **actually being studied**, not leftover effects from a prior trial.

### Exercise 6.16 — Multiple Entries Pointing to the Same Frame (Hashed Page Tables)
- **The violated assumption:** hashed page tables rely on the assumption that **collisions are RARE** — i.e., only a small number of **valid** entries will typically hash to the same bucket, keeping overflow chains short and the table efficient.
- **If an entire 64-bit address space (2⁶⁴ pages) all mapped to ONE physical frame** (all marked read-only, all pointing to the same zeroed frame — per the "efficient zero filling" technique in §4.7), then **every single one** of these 2⁶⁴ valid virtual pages would need a **valid page table entry** — and depending on the hash function, potentially **enormous numbers of them would collide** into the same buckets (since the hash table itself is sized based on an assumption of a **modest** number of valid entries, not 2⁶⁴ of them!).
- **Problems this would cause:** the table's **hash buckets** would need **massive overflow chains** to accommodate this — completely destroying the performance assumption that lookups are typically O(1) (or close to it) — degrading to something like a giant linked-list search, defeating the entire purpose of hashing.

### Exercise 6.17 — Windows' Page Frame Source Preferences
**(a) Free list preferred over zero list, when reading a page IN from disk:**
- Because the page being read **from disk** will have its content **completely overwritten** by the disk read anyway — there's **no benefit** to using an **already-zeroed** frame (the zeroing effort would be **wasted**, immediately overwritten). Save the **precious, already-zeroed** frames (from the zero list) for situations that **actually need** zero-filled content (e.g., a fresh heap page) — don't "waste" them on a disk read that will overwrite them regardless.

**(b) Free list preferred over standby list:**
- **Standby list** pages still **retain their old content**, kept around specifically to enable a **cheap soft/minor page fault recovery** if that old content turns out to be needed again soon. **Reusing** a standby-list frame **destroys** that recovery opportunity for whatever page **used to** be there. The **free list**, by contrast, holds frames whose content is **no longer considered recoverable/useful** anyway (they've "graduated" past the point of being retrievable via a soft fault) — so reusing **those** first sacrifices **nothing** of value.

**(c) Modified list would be even worse than standby:**
- A page on the **modified** list is still **dirty** — it **hasn't yet been written back to disk**. Reusing (overwriting) such a frame **without first saving its contents** would mean **PERMANENTLY LOSING** data that hasn't been persisted anywhere else yet — an even worse outcome than losing the (already-clean, already-safely-persisted) recovery opportunity of a standby-list page. The system would need to **force an immediate, synchronous disk write** first, which is slow and defeats the purpose of proactive/batched write-back (§6.4.3's stated advantage of proactive eviction).

---

## 11. Annotated Notes / Historical References

| Reference | Relevance |
|---|---|
| **Fotheringham [59]** | One of the earliest papers on virtual memory, concerning the pioneering **Atlas** computer — established the names-vs-locations distinction underlying this whole chapter. |
| **Dennis [47]** | Made the same names-vs-locations point at greater length; seminal for **segmentation**, alongside Fotheringham's paper being seminal for **paging**. |
| **Burroughs B5000 [26]** | An early (even pre-dating Dennis's paper) commercial system that used segmentation. |
| **Denning [46]** | An influential end-of-1960s survey of the whole virtual memory field (paging and segmentation both). |
| **Denning [45]** | Developed the **working set** concept, central to §4.8's disk-substitution discussion. |
| **Huck and Hays [83]** | Described how HP's Precision Architecture merged the hash table and inverted page table into today's **hashed page table**; also introduced the **software TLB** concept. |
| **Talluri, Hill, and Khalidi [145]** | Proposed **clustered page tables** — a hashed-page-table variant storing several consecutive pages' entries per hash bucket, balancing hashed vs. linear/multilevel trade-offs. |
| **Talluri's dissertation [146] / Navarro [110]** | Research on **mixing page sizes**, motivating Linux's later "transparent huge pages" feature (from 2.6.38 onward). |
| **Kessler and Hill [88]** | Evaluated **page coloring** and **bin hopping**, and other cache-conscious page placement approaches. |
| **Belady [12], [13]** | Early comparison of replacement policies (FIFO, LRU, an early OPT variant called "MIN"); co-discovered the **anomaly** that bears his name. |
| **Mattson et al. [104]** | Refined OPT to its modern form, **proved its optimality**, introduced the **stack algorithm** concept, and proved stack algorithms are immune to Belady's anomaly. |
| **Aho, Denning, and Ullman [2]** | Analyzed replacement-policy optimality under **probabilistic models**, showing LRU approximates optimal replacement given slowly-varying access probabilities. |
| **Turner and Levy [150]** | Showed how **Segmented FIFO** can approximate LRU, in the context of VMS's local replacement. |
| **Babaoglu and Joy [9]** | Applied a similar cheap-reclamation idea **globally**, patterned on clock replacement. |

---

## 12. Big-Picture Takeaways

1. **Virtual memory's essence is decoupling names (virtual addresses) from locations (physical addresses)** — a distinction that seems abstract at first but directly explains why processes can share the same "natural" addresses without colliding, and why shared objects can have different names in different contexts.
2. **The five defining properties (table-based, page-granular, OS-controlled, sparse/fault-driven, permission-annotated) together create an extraordinarily flexible mechanism** that goes far beyond simple process isolation — enabling controlled sharing, flexible allocation, sparse address spaces, persistence, fast program startup, efficient zero-filling, and RAM/disk substitution, all from the **same underlying machinery**.
3. **Performance is preserved despite this generality almost entirely through the TLB**, which exploits temporal and spatial locality to avoid consulting the (comparatively slow) page table on the vast majority of memory accesses — making TLB design one of the most performance-critical aspects of modern CPU architecture.
4. **Page table structure (linear, multilevel, hashed) is fundamentally a trade-off between assuming clustering of valid pages (linear/multilevel) versus assuming nothing about their distribution (hashed)** — with linear tables needing a clever (if "dizzying") recursive virtual-memory trick to stay practical, multilevel tables sidestepping recursion via a tree structure, and hashed tables trading larger entries and occasional overflow chains for total flexibility.
5. **Segmentation, though historically important and conceptually elegant (naming both an object and a location within it), lost out to paging** because most of its benefits could be simulated with paging alone, and its costs weren't justified — leaving only faint echoes (like multiple ASIDs per process) in modern hardware.
6. **The three virtual memory policies — fetch, placement, and replacement — all face a common tension: proactive/anticipatory action can improve performance, but only if predictions are good enough to be worth the risk**, and the right answer is highly workload-dependent, requiring extensive real-world tuning (mirroring the "magic numbers" theme also seen in CPU scheduling).
7. **Replacement policy design centers on approximating the unrealizable OPT using only past information**, with LRU as the theoretical target, FIFO as a simple (but sometimes anomalous) baseline, and practical refinements (SFIFO, clock replacement) cleverly using cheap page-frame retention and hardware reference bits to approximate LRU's behavior without its full bookkeeping cost.
8. **Virtual memory's greatest security strength (silent, transparent operation) is also its greatest security liability for sensitive data** — because the very transparency that makes virtual memory so useful and simple to build on top of also means an application programmer can have absolutely no visibility into whether their "in-memory-only" secret has quietly been written to disk, necessitating explicit escape hatches like `mlock` for security-critical code.