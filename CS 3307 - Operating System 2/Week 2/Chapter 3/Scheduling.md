# Scheduling 

*(Based on Chapter 3: "Scheduling")*

---

## Table of Contents

1. Introduction
2. Thread States
3. Scheduling Goals (Throughput, Response Time, Urgency/Importance/Resource Allocation)
4. Fixed-Priority Scheduling
5. Dynamic-Priority Scheduling — Earliest Deadline First (EDF)
6. Dynamic-Priority Scheduling — Decay Usage Scheduling
7. Proportional-Share Scheduling (WRR, WFQ, Lottery, CFS)
8. Security and Scheduling
9. Glossary of Key Terms
10. Summary Tables
11. Worked Exercise Solutions
12. Annotated Notes / Historical References
13. Big-Picture Takeaways

---

## 1. Introduction

### 1.1 What Is a Scheduler?
- Chapter 2 established that OSes run multiple threads concurrently by **repeatedly switching each processor's attention** between threads.
- This switching requires a **scheduler**: the mechanism that decides **which thread to run at each point in time**.
- **Scope simplification:** other resources can be scheduled too (e.g., a **disk scheduler** ordering requests from multiple threads), but this chapter focuses exclusively on **processor scheduling**. Unless stated otherwise, "scheduling"/"the scheduler" means **processor** scheduling.

### 1.2 The Core Challenge: No Universally "Best" Policy
- A scheduler should make decisions that **keep users happy** — but there's **no single policy that satisfies everyone, all the time**.
- **Two distinct reasons for this:**
    1. **Conflicting user desires:** User A wants Task A done fast; User B wants Task B done fast — a direct conflict.
    2. **Context-dependent merit:** The "right" policy depends on context, not just on whom you ask. (Example: a student with several courses won't decide which assignment to work on **without considering due dates** — context matters even for a single decision-maker.)

### 1.3 Mechanism vs. Policy
- Because scheduling needs are context-dependent, OSes provide **scheduling mechanisms** that leave **subtler policy choices** up to the user.
- **Example:** an OS might provide a mechanism to "always run the highest numerically-prioritized thread," while leaving it to the **user** to actually assign those priority numbers.
- **Even so:** no single mechanism (or family of policies) suits **all** goals — hence this chapter spends significant time on (a) the **different goals** users have, and (b) the **mechanisms** used to achieve them, at least approximately. Because goals can conflict, users often must settle for **"good enough."**

### 1.4 One Universally Agreed-Upon Goal
> **A thread that can make productive use of a processor should always be preferred over one that is waiting for something** (e.g., a timer, or input arrival).

- This is the **one goal everyone agrees on**, addressed via careful thread-state tracking (Section 2), **before** diving into the more values-laden, context-dependent goals (Section 3 onward).

### 1.5 Chapter Roadmap
- §2 — **Thread states** (how schedulers avoid wasting time on non-productive threads).
- §3 — **Scheduling goals**, independent of mechanism.
- §4–§7 — **Three families of schedulers**: fixed thread priorities, dynamically adjusted priorities, and proportional-share (time-proportion-based) scheduling.
- §8 — **Scheduling and security.**

---

## 2. Thread States

### 2.1 The Problem: Threads Often Can't Usefully Run

Two illustrative examples of threads that must **wait**:

1. **Web server thread:** reads a client request from the network, reads the requested page from disk, sends it back.
    - **Waiting for network data:** if scheduled while waiting, the best it could do is **loop, checking repeatedly** whether data arrived — unproductive.
    - **Waiting for disk read:** once the request is parsed, the server issues a disk request and must again **wait** for the physical disk operation to complete.
2. **Video player thread:** displays one frame, then must **wait** a fraction of a second before the next frame (to avoid playing too fast). Between frames, all it could do is repeatedly check the real-time clock — again, unproductive.

### 2.2 Busy Waiting — Why It's Bad

- **Busy waiting:** executing a loop that continually checks for an event, without yielding the processor.
- **In a single-thread system**, this is *plausible* (there's nothing else useful to do with the CPU anyway).
- **In a multi-threaded system**, busy waiting is a **bad idea**: any processor time given to the busy-waiting thread is **wasted** — it produces no value for the waiting thread, and it's time **stolen from other threads** that could have made real progress.

### 2.3 The Alternative: Run Queues and Wait Queues

- The OS tracks which threads are **runnable** (can usefully execute) vs. **waiting**, using:
    - A **run queue**: holds runnable threads.
    - **Wait queues** (one **per reason for waiting**): hold waiting threads.
- **Note on naming:** despite being called "queues," these structures **need not be FIFO** in the strict sense.
    - Example: a wait queue for elapsing time might be kept **sorted by desired wake-up time**, not insertion order.
    - Example: another wait queue might hold threads waiting for data on a specific network channel.

### 2.4 How a Thread Actually Waits (Instead of Busy-Waiting)

- A thread wanting to wait **notifies the OS of this intention** (rather than looping).
- The OS **removes it from the run queue** and **inserts it into the appropriate wait queue**.
- Since the **scheduler only considers threads in the run queue**, it will **never** select a waiting thread — guaranteeing the scheduler only picks from threads that can **actually make progress** if given a processor.

```
   Run queue              Wait queue
   [Thread X]             [ ]
   [Thread Y] -- needs
   [Thread Z]    to wait
        |
        v  (moved)
   Run queue              Wait queue
   [Thread X]             [Thread Y]
   [Thread Z]
        |
        v (scheduler dispatches one of the remaining)
   Thread X or Z now RUNNING
```

### 2.5 How Waiting Threads Get Woken Up: The Timer Interrupt Example

- Recall from Chapter 2: a **hardware interrupt** can force the processor to (temporarily) stop running the current thread and run the OS's **interrupt handler** instead.
- **Example — real-time clock interrupt** (e.g., firing every 1/100 second):
    - The interrupt handler checks the **first thread** in the "waiting for time to elapse" wait queue.
    - Because this queue is **kept in time order**, if the first thread's wake time **hasn't arrived yet**, **no further threads need checking** (all the rest are waiting even longer).
    - If the first thread **has** waited long enough, the OS **moves it from the wait queue to the run queue**, then **checks the next thread similarly** (repeating until it finds one still waiting on a future time).

```
Wait queue (sorted by wake time), timer interrupt fires at "12:05":

  12:05    12:15    12:30    12:45
  [past]   [past]   [future] [future]
  move  ->  move  -> leave    (don't even check!)
  to RQ    to RQ
```

- This design (sorted wait queue + early-exit checking) is an efficient way to handle potentially many sleeping threads without checking every single one on every timer tick.

### 2.6 The Three (or More) Thread States

At minimum, a thread can be in one of **three distinct states**:

1. **Runnable** (but not running) — awaiting dispatch by the scheduler.
2. **Running** — actively executing on a processor.
3. **Waiting** — blocked on some event.

- Some OSes add **more states** for finer distinctions (e.g., separating different *kinds* of waiting) or special circumstances (e.g., a thread that's finished but must be kept around until another thread is notified of its completion). This chapter sticks to the **three basic states** for simplicity.

### 2.7 State Transition Diagram

```
                 Initiation
                     |
                     v
                 Runnable <----------------+
                     |                       |
                dispatch                yield or
                     |                   preemption
                     v                       |
                 Running --------------------+
                  |      |
             Termination  event wait
                  |      |
                  v      v
               (done)  Waiting
                          |
                          | (event occurs)
                          +------------------+
                                             |
                                             v
                                         (back to Runnable)
```

- **Initiation:** a new thread starts life **Runnable**, not yet running.
- **Dispatch:** the scheduler picks a Runnable thread to actually run.
- **Yield / Preemption:** a Running thread voluntarily gives up the processor, **or** is forcibly preempted by the scheduler to let another thread run — either way, it returns to **Runnable**.
- **Event wait:** a Running thread may choose to **wait** for an external event, moving to the **Waiting** state.
- **Event occurs:** a Waiting thread becomes **Runnable** again once its awaited event happens.
- **Termination:** a Running thread may finish entirely.
- *(Real systems may add more transitions — e.g., forcibly terminating a thread even while it's Waiting.)*

---

## 3. Scheduling Goals

Two overarching categories of user expectations:
1. **Performance** — maximizing system performance.
2. **Control** — allowing users to influence scheduling decisions.

```
                Scheduling goals
                /              \
        Performance            Control
        /          \          /    |    \
  Throughput   Response   Urgency Importance Resource
                 time                          allocation
```

### 3.1 Throughput

- **Definition:** the **rate** at which useful work gets done (e.g., search transactions completed per second).
- **Motivating scenario:** large-scale internet services (e.g., a search engine) need **every server** to handle as much traffic as possible, since adding more servers is expensive — so **per-server throughput** matters enormously.
- **What maximizing throughput requires:**
    1. Give every processor a runnable thread whenever possible (obvious).
    2. **Use ALL system resources efficiently** — not just the CPU, but I/O devices (disks, network interfaces) and the **memory hierarchy** (caches) too.

#### 3.1.1 I/O-Aware Throughput (Revisiting the Virus-Scan / Rendering Example)
- Recall the Chapter 2 example: a CPU-intensive rendering thread + a disk-intensive virus-scanning thread.
- **Goal:** keep **both** the processor and disk busy **simultaneously**, rather than one sitting idle while the other works.
- **Analogy:** managing a project assistant — whenever they're at risk of going idle, you must pause your own work just long enough to hand them their next assignment. Similarly, the OS must switch threads at the right moments to keep the disk continuously fed with work.

#### 3.1.2 Cache Memory Effects on Throughput

**Two distinct cache-related issues:**

**(a) Context-switching overhead (applies even on a single processor):**
- The **direct** cost of switching (saving/loading a few registers) is small.
- The **big** cost is **degraded cache performance**.
- **Mechanism:** caches hold data recently accessed (**temporal locality**) or near recently-accessed locations (**spatial locality**). When the processor switches threads, the **new thread's** favored memory locations are usually **different**, so the cache initially suffers many **misses**, running at main-memory speed until the new thread's data displaces the old thread's.
- **Danger:** if the scheduler switches back to the original thread **right as the cache finishes adapting** to the new one, throughput suffers badly — repeatedly "unadapting" the cache.

```
Processor:  [running Thread A for a while]
Cache:      [mostly holds A's data] -> high hit rate

  switch to Thread B (down arrow)

Processor:  [running Thread B]
Cache:      [still mostly A's data] -> LOW hit rate (misses go to slow main memory)
                    (over time, down arrow)
Cache:      [gradually fills with B's data] -> hit rate improves again
```

**(b) Processor affinity (multiprocessor-specific):**
- A thread runs **faster** when scheduled on the **same processor** it last ran on, because that processor's **local cache** may still hold its data.
- **Worse case if scheduled on a *different* processor:** if the needed data is sitting in **another processor's cache**, the multiprocessor's **cache coherence protocol** must transfer the data — first from the old cache **to main memory**, then from main memory **to the new cache**. This "excess coherence traffic" further hurts throughput.
- **Mitigation:** schedulers try to maintain **processor affinity** — consistently scheduling a thread on the **same processor**, absent other overriding considerations.

```
Processor 1: Thread A runs here -> Cache 1 fills with A's data
Processor 2: Thread B runs here -> Cache 2 fills with B's data

If later swapped (A->Proc 2, B->Proc 1):
  Most memory accesses MISS locally,
  requiring cache-coherence-protocol transfers between Cache 1 <-> Cache 2 <-> Main Memory
  -> Extra overhead, reduced throughput
```

### 3.2 Response Time

- **Definition:** elapsed time from a **triggering event** (keystroke, packet arrival) to the **completed response** (updated display, reply packet sent).
- **Key tension:** a system great for **throughput** may be poor for **response time**, and vice versa. **Frequent context switches** (bad for throughput) may be **necessary** for good response time.
- **Design philosophy split:**
    - **Single-user interactive systems** → typically optimize for **response time**, even sacrificing throughput.
    - **Centralized servers** → typically optimize for **throughput**, as long as response time stays **tolerable**.

#### 3.2.1 The Fundamental Trade-off — Illustrated With an Email Analogy
- Scenario: you're writing a long reply to an old friend, when a second email arrives from a close friend asking if you want to go out tonight.
    - **Option 1:** finish the long letter first, then reply "sure" to the second.
    - **Option 2:** pause the long letter, send the quick one-word reply immediately, then resume.
- **Both options extend one response's time to shorten the other's** — but they are **not symmetric in impact**: prioritizing the quick reply **helps it a lot** while **harming the long letter only a little**.

#### 3.2.2 Shortest Job First (SJF) — Minimizing Average Response Time
- **If** the OS knows how much processor time each thread needs to respond, it can apply exactly this "prioritize the quick task" logic systematically.
- **Shortest Job First (SJF):** minimizes **average response time** (provable — see Exercise 3.5).
- **Historical origin:** batch-processing systems (e.g., payroll, accounts-payable jobs) — operators, knowing typical job durations from regular use, could minimize average **turnaround time** by processing the shortest job first.
- **Why it matters today:** not for scheduling batch jobs directly, but as **conceptual background** for understanding how modern OSes improve thread responsiveness.

#### 3.2.3 The Practical Problem: The OS Usually Doesn't Know Job Length
Two solutions:

1. **Guess based on past behavior:** prioritize threads that **haven't** recently consumed large processor **bursts** (a burst = processing done between waits for external events).
2. **Hedge via frequent switching:** even without knowing which thread needs only brief processing, switching frequently among runnable threads ensures that **any** thread needing only a little time will get it relatively soon, even if others involve long computations.

#### 3.2.4 The Hedge's Limits
- **Success depends on:** (a) the length of time slices, **and** (b) the **number** of runnable threads competing.
- **Lightly loaded system:** frequent switching alone may suffice for responsiveness.
- **Heavily loaded system** (many long computations + an occasional interactive thread needing just a little time): frequent switching **alone won't help**, because even brief time slices **add up** across many competing threads. The OS **must identify and prioritize** the interactive thread specifically to avoid it waiting in line behind everyone else's turn.

### 3.3 Urgency, Importance, and Resource Allocation

- Both **throughput** and **response time** are goals a "sufficiently smart" scheduler **could** achieve entirely on its own — no user input strictly required.
- **In contrast**, some goals are precisely about giving the **user** a voice: *"This thread is high priority — work on it."*
- **Three distinct, often confusingly conflated notions** (the chapter carefully disentangles them, reserving the word "priority" for later, mechanism-specific discussion):

#### 3.3.1 Urgency
- **Definition:** a task is **urgent** if it needs to be done **soon**.
- Example: a small homework assignment due **tomorrow** is more urgent than a massive term paper due in **two days** — but that doesn't necessarily mean you *should* prioritize the homework (see Importance below).

#### 3.3.2 Importance
- **Definition:** how much is **at stake** in completing a task in a timely fashion.
- Continuing the example: you might decide the term paper is **more important**, and take a zero on the (more urgent, but less important) homework, to free up time.
- **Interaction with urgency:** if the term paper instead wasn't due for another week, you might work on the (urgent but not yet critical) homework today, planning to start the paper tomorrow. Or, if the term paper (unstarted) was due in an hour with no late papers accepted, you might recognize it as **hopeless** and redirect your time to the homework instead.
- **Key nuance:** urgency and importance are **different axes**, but **how precisely urgency is specified** shapes how a user can express importance:
    - **Hard deadlines:** importance = cost of **dropping the task entirely** (ruthless triage).
    - **Soft deadlines:** importance = how bad it is for each task to be **late**.
    - **No urgency info at all** (just "ASAP" for everything): high importance = work on it whenever possible; low importance = fill idle moments only.

#### 3.3.3 Resource Allocation
- **Definition:** controlling what **fraction** of available processing resources a thread/user/group receives.
- **Motivations:**
    - **Fairness** — e.g., splitting a shared computer's time evenly between two users.
    - **Deliberate inequity** — e.g., a web-hosting company selling proportional "shares" of a shared server (buy 2 shares, get roughly 2x the processing time).

##### Fair-Share vs. Proportional-Share Scheduling (Important Terminological Distinction!)
| | **Fair-Share Scheduling** | **Proportional-Share Scheduling** |
|---|---|---|
| **Time scale** | Long (e.g., a week) | Short (e.g., a second) |
| **Historical context** | Common when many users (e.g., university students) shared one computer | Still very much alive today (e.g., Linux's CFS) |
| **Mechanism** | Balances **cumulative** usage over a long window — heavy early-week usage leads to reduced share later in the week | Focuses only on **currently runnable** threads, allocating processor time **in proportion to specified shares** |
| **Equal shares required?** | No — administrators could grant differing allocations (e.g., advanced-course students getting more time) | No — shares can differ (e.g., 2:1:1 ratio) |
| **Status today** | Fallen out of favor with the rise of personal computers | Still widely used (e.g., Linux's CFS is largely proportional-share-based) |

##### Worked Example — Proportional-Share With a 2:1:1 Ratio
- Company A has **2 shares**; companies B and C each have **1 share**. Each runs one thread (A, B, C respectively).
- **If thread A waits an hour** (e.g., for network input) while B and C are runnable: B and C **split the processor 50/50** (since they have equal, 1:1 shares).
- **When A's input arrives and it becomes runnable again:** it does **NOT** get a full hour-long "catch-up" block. Instead, going forward, it immediately gets **half** the processor's time (reflecting its 2 shares out of the total 4), while B and C each drop to **one quarter** — preserving the **2:1:1 ratio** at all times, without ever granting "back pay" for time A wasn't runnable.

##### Individual vs. Group Proportional-Share Scheduling
- **Simplest form:** shares specified **per individual thread**.
- **More sophisticated form:** shares specified **collectively**, for all threads belonging to a user or logical group — e.g., each **user** gets an equal share regardless of how many threads they personally run; users with more threads simply **subdivide** their own share among those threads.
- **Linux's group scheduling:** flexible — threads can be grouped by user, or in other admin-defined ways.
    - **Through Linux 2.6.37:** default was **per-thread** shares (no automatic grouping).
    - **Since Linux 2.6.38:** default changed to **automatically create a group per terminal session** — so no matter how many CPU-hungry threads run from one terminal window, they can't unfairly dominate overall system performance relative to other sessions. (Technically, grouping is by **session**, not literally "terminal window," though the two usually coincide.)

#### 3.3.4 Why a Single "Priority" Number Is Often Insufficient
- Without further clarification, a statement like *"thread A is higher priority than thread B"* is **ambiguous** — it could mean:
    - **Resource allocation sense:** devote **twice** as much time to A as to B (proportional).
    - **Importance/urgency sense:** devote **almost all** time to A, running B only in A's idle moments.
- **UNIX-family "niceness"** is a classic example of this ambiguity in practice:
    - **"Nice"** = a thread prone to saying "oh, go ahead of me, I can wait" → **high niceness = low priority**.
    - **Mac OS X's interpretation:** niceness = **importance** (a very nice thread only runs when there's spare processor time).
    - **Linux's interpretation:** niceness = **resource allocation proportion** (nicer threads get proportionately **less**, but still guaranteed, processor time).
    - **Neither interpretation is "more correct"** — the real problem is that users may want to express **two different things**, but a single "niceness" knob **can't capture both simultaneously**.
- **More expressive systems exist:**
    - **Mac OS X:** lets users express **either** urgency (via a deadline) **or** importance (via niceness); urgency-specified threads are treated as **hierarchically more important** than any niceness-only thread.
    - **Linux (and similar proportional-share schedulers):** use niceness for proportion control, **but also** allow explicitly flagging threads as **low-importance**, receiving almost no processing unless the processor would otherwise sit idle.

---

## 4. Fixed-Priority Scheduling

### 4.1 The Core Mechanism
- Each thread has a **numerical priority**.
- **Rule:** higher-priority threads are **always** selected over lower-priority ones. **No thread is ever left runnable-but-not-running while a lower-priority thread runs.**
- **Simplest assignment method:** the user manually specifies each thread's priority (with a default if unspecified).
- **"Fixed-priority"** specifically means: the **OS never automatically adjusts** a thread's priority (manual user adjustment may still be possible).

### 4.2 Practical Adoption
- Fixed-priority scheduling only serves user goals well in **limited circumstances** — but it's **simple**, so many real systems offer it as an **option**.
- **Linux and Microsoft Windows** both support fixed-priority scheduling for specific threads — such threads **take precedence over all others** (which use the mechanisms of §5/§7 instead).
- **POSIX** (the international standard many OSes follow) **includes** fixed-priority scheduling as part of its specification.

### 4.3 A Numbering Convention Caveat
- **Some real systems use SMALLER numbers for MORE preferred threads** (i.e., a "higher priority" thread might have a **lower** priority *number*).
- **This chapter's convention:** "higher priority" and "lower priority" always refer to **preference**, independent of the specific numeric encoding a system uses.

### 4.4 Implementation: Array of Priority Levels
- In theory, an efficient priority queue (e.g., a **binary heap**) could manage the run queue.
- **In practice**, since most systems use only a **small range of integer priorities**, it suffices to keep a **simple array**, one entry per priority level, each holding a **list of threads** at that priority.

### 4.5 Dispatch and Preemption Rules
- **When a processor becomes idle** (thread terminated or started waiting): dispatch the **highest-priority runnable thread**.
- **When a thread becomes runnable** (newly created, or done waiting): compare its priority to the **currently running** thread's.
    - If the **newly runnable thread has higher priority** → **preempt** the running (lower-priority) thread, returning it to the run queue, and dispatch the new thread instead.

### 4.6 Handling Ties (Equal Priority)

Two strategies (both provided for by **POSIX**):

1. **FIFO (First In, First Out):** run the thread that became runnable **first**, until it waits or voluntarily yields — **only then** dispatch the next equally-high-priority thread.
2. **Round Robin (RR):** **alternate** among tied threads, each running for a small time interval (**tens or hundreds of milliseconds**), preempted via the clock interrupt handler, cycling back around eventually.

### 4.7 When Is Fixed-Priority Scheduling Appropriate?

- **NOT viable** in an **open, general-purpose environment**, where a user might (accidentally or maliciously) create a **high-priority, long-running thread** that **starves everything else**.
- **IS viable** in a **carefully quality-controlled system**, where all threads are known and vetted in advance.
- **Classic use case: hard-real-time systems** — e.g., systems controlling an **airplane's wing flaps**.

### 4.8 Hard-Real-Time Systems and Periodic Tasks

- Threads in hard-real-time systems typically perform **periodic tasks** (e.g., wake every second, make a flap adjustment, sleep the rest of the second).
- Each task has a **deadline**; missing it means the program has **failed its specification** ("hard real time").
- **Simplifying assumption used in this chapter's examples:** deadline = period (e.g., each second's adjustment must complete **within** that second).
- **Designers must have a worst-case execution-time estimate** for each thread, per period, and must **carefully verify** no deadlines will ever be missed.

### 4.9 Liu and Layland's Two Key Theorems (1973)

For a periodic hard-real-time system (periods = deadlines, no cross-thread interactions beyond CPU competition):

1. **Rate-Monotonic Scheduling (RMS) is optimal among fixed-priority assignments:** if **any** fixed-priority assignment can meet all deadlines, then the assignment that gives **shorter-period threads higher priority** will also succeed.
2. **Worst-case analysis suffices:** it's enough to check the scenario where **all threads' periods start simultaneously** (time 0) — if deadlines are met in this worst case, they'll be met in general.

- **Testing procedure:** assign priorities rate-monotonically, assume all threads start at time 0, and **plot (Gantt chart)** what happens — checking for missed deadlines.

### 4.10 Gantt Charts

- A **Gantt chart** is a bar (representing time) divided into labeled regions showing which thread runs during each interval.
- **Example notation:**
```
T1  T2  T1
0   5   15  20
```
means T1 runs 0-5 and 15-20; T2 runs 5-15.

### 4.11 Worked Example 1 — Rate-Monotonic Scheduling FAILS

- **T1:** period/deadline = 4s, worst-case execution = 2s per period.
- **T2:** period/deadline = 6s, worst-case execution = 3s per period.
- **Surface-level feasibility check:** T1 demands 2/4 = 50% of the processor; T2 demands 3/6 = 50% — totaling exactly **100%** (fully utilized, not oversubscribed) — looks *maybe* feasible.
- **Since T1 has the shorter period, it gets higher priority (rate-monotonic).** Both threads start their period at t=0.

**Resulting Gantt chart (first 6 seconds):**
```
T1  T2  T1
0   2   4   6
```

- **Walkthrough:** T1 runs first (higher priority, both runnable) from 0-2 (finishing its 2s of work), easily meeting its deadline of 4. T2 starts at t=2, but is **preempted at t=4** when T1's *second* period begins (T1 again has higher priority). T1 runs 4-6, occupying the processor for its second period's full 2s.
- **Result:** by t=6 (T2's deadline), **T2 has only run for 2 seconds total** (0 to 2), instead of its needed 3 — **T2 misses its deadline!**
- **Per Liu & Layland's theorem**, swapping priorities (T2 higher than T1) **won't fix this either** — confirmed by drawing the alternative Gantt chart (see Exercise 3.3 solution below).

### 4.12 Worked Example 2 — Fixed-Priority Scheduling SUCCEEDS

- Same setup, but **T2's worst-case execution time is now only 2 seconds** per its 6-second period (all else unchanged).

**Resulting Gantt chart (first 12 seconds):**
```
T1  T2  T1  T2  T1  idle
0   2   4   6   8   10  12
```

- **T1** executes 2 seconds in each of its 3 periods (0-4, 4-8, 8-12) → **no missed deadlines**.
- **T2** executes 2 seconds in each of its 2 periods (0-6, 6-12) → **no missed deadlines**.
- **This 12-second pattern repeats indefinitely** — no need to check further into the timeline.

---

## 5. Dynamic-Priority Scheduling — Earliest Deadline First (EDF)

### 5.1 Motivation
- Fixed-priority (even optimal rate-monotonic) scheduling **couldn't** handle the T1 (2s/4s) + T2 (3s/6s) example from §4.11.
- **Solution:** allow priorities to be **dynamically reassigned** — this is **Earliest Deadline First (EDF)**.

### 5.2 The EDF Rule
> Each time a thread becomes runnable, **re-assign priorities**: **the sooner a thread's next deadline, the higher its priority.**

- **EDF's optimality** is another of **Liu and Layland's theorems**: if ANY scheduling approach can meet all deadlines, EDF will too.

### 5.3 Worked Example — EDF Succeeds Where Rate-Monotonic Failed

Same T1 (2s/4s) and T2 (3s/6s) setup.

**Resulting Gantt chart (first 12 seconds, then repeats):**
```
T1  T2  T1  T2  T1
0   2   5   7   10  12
```

- **At t=0:** both runnable; T1's deadline (4) is sooner than T2's (6) → **T1 prioritized**, runs 0-2.
- **At t=2:** only T2 runnable → runs (2-5, its full 3s).
- **At t=4:** T1 becomes runnable again (2nd period) — but now T1's **new** deadline is 8, while T2's (still in-progress) deadline is 6 → **T2 has the earlier deadline now**, so T2 keeps running (this is the key difference from rate-monotonic: **priorities change dynamically based on current deadlines**, not fixed period length).
- T2 finishes its first period's 3s of work at t=5. T1 then runs its remaining 2s from 5-7.
- **At t=8:** T2 starts its second period; runs until t=10.
- **At t=10:** T1's third period begins; runs 10-12.
- **Neither thread ever misses a deadline**: T1 gets 2s in each of its periods (0-4, 4-8, 8-12); T2 gets 3s in each of its periods (0-6, 6-12).

### 5.4 Why EDF Succeeds Where Rate-Monotonic Fails
- The **key difference**: EDF re-prioritizes **dynamically** based on **current** deadlines, not a **fixed** period-based ranking.
    - At t=0: T1 prioritized (its deadline, 4, is sooner than T2's, 6).
    - At t=4 (T1's 2nd period start): T1's **new** deadline (8) is now **later** than T2's remaining deadline (6) → **T2 gets priority instead**.
- This flexibility lets the processor **finish T2's current period's work** before starting T1's next period — avoiding the pileup that doomed rate-monotonic scheduling.

### 5.5 Tie-Breaking in EDF
- **At t=8** in the example, T1 (becoming runnable for its 3rd period) has a deadline of 12 — **tied** with T2's deadline (also 12).
- **Convention:** break ties in favor of the **already-running thread** (here, T2) — this **minimizes context switches**.
- **Theoretically**, any tie-breaking rule works equally well for meeting deadlines; the "favor the incumbent" rule is simply more **efficient** in practice.

---

## 6. Dynamic-Priority Scheduling — Decay Usage Scheduling

### 6.1 Motivation — Beyond Hard Real-Time
- Most **everyday** computer usage isn't about hard deadlines — it's about **quick interactive response** and **efficient handling of long computations**.
- **Dynamic priority adjustment** can serve these goals too — as seen in **Mac OS X** and **Microsoft Windows**.

### 6.2 Base Priorities
- Users occasionally want to express opinions on thread priority (for urgency/importance/resource-allocation reasons) — e.g., a speculative SETI-style search program might reasonably get a **low priority** given its low chance of near-term payoff.
- These user-specified values become **base priorities** — a **starting point** for the OS's automatic adjustments.
- **Simplification used in this chapter:** assume all threads share the **same base priority** (since most users accept the default).

### 6.3 Round-Robin Among Tied Threads
- Threads tied for **top (adjusted) priority** are run **round-robin** — each gets a **time slice** (aka **quantum**), then the scheduler moves to the next thread.
- A thread need not use its **full** time slice — e.g., it might issue an I/O request and go to sleep early, at which point the scheduler **immediately** moves on to the next thread.

### 6.4 Why Adjust Priorities? Two Motivating Scenarios

#### Scenario A — Maximizing Throughput (CPU-bound + Disk-bound threads)
- Same graphics-rendering (CPU-bound) + virus-scanning (disk-bound) example from Chapter 2/§3.1.1.
- **Goal:** the moment the virus scanner's disk read completes, the scheduler should **immediately** switch to it, so it can process the data and issue its **next** disk request quickly — keeping the disk **continuously busy**. Meanwhile, the rendering thread gets time whenever the scanner goes back to waiting on the disk.
- **Requirement:** the disk-intensive thread needs a **higher** priority than the processor-intensive one.

#### Scenario B — Minimizing Response Time (Interactive vs. Long Computation)
- Example: computing digits of pi (long-running, CPU-bound) in one window, while typing a term paper in another.
- **During long pauses** (thinking about what to write), you don't mind the pi computation using the CPU.
- **The instant you start typing**, you want the word processor to respond **immediately** — requiring it to have **higher priority** at that moment.

### 6.5 The Common Pattern
> In both scenarios, a **computationally intensive** thread competes with a thread that's been **unable to use the processor for a while** (waiting on disk, or waiting on user keystrokes).

**Rule that emerges:** the OS should **increase** the priority of threads in the **waiting** state, and **decrease** the priority of threads in the **running** state.

- This is precisely what **decay usage schedulers** (e.g., Mac OS X's) do. (Microsoft Windows follows the same general pattern, though it isn't strictly a decay-usage scheduler.)

### 6.6 Decay Usage Scheduling Mechanics (Mac OS X Style)

- Each thread's **priority** = base priority **minus** an amount reflecting **recent processor usage** (with a floor — priority never drops below some minimum, no matter how much the thread has run).
- **Usage increases** while running (by adding elapsed run time); **decays** while waiting (multiplied by a constant periodically — Mac OS X uses **5/8, eight times per second**).

```
   Usage rises while running, decays while waiting (exponential decay)
   Priority falls while running, rises back toward base while waiting
```

- **Efficient implementation:** rather than continuously updating every thread's usage, the system recalculates usage **only at state-change points**:
    - **Running to (yields / time-slice ends / preempted):** add elapsed running time to usage (scaled by current **system load** in Mac OS X — heavier load leads to priority dropping **faster**, giving other threads a fairer chance).
    - **Waiting to done waiting:** usage is multiplied by **(5/8)^n**, where n = number of 1/8-second decay intervals elapsed. Because this is **exponential decay**, even a **fraction of a second** of waiting restores much of the base priority; Mac OS X approximates (5/8)^n as **0 for n >= 30** — so after **3.75 seconds** of waiting, a thread is **exactly** back at base priority, regardless of how much it ran before.

### 6.7 Microsoft Windows' Variation

- **Same net effect** (waiting threads get boosted priority relative to running threads), but **implemented differently**:
    - When a thread **wakes from waiting**, it's given an **elevated priority immediately**, which then **sinks back down (linearly)** toward the base as it runs (rather than starting at base and decaying downward while running, as in Mac OS X).
    - **No exponential decay** for the boost — instead, the **size of the boost depends on what the thread was waiting for**: a **small** boost after a disk wait, a **larger** boost after a keyboard-input wait, etc. Since longer-typical waits get larger boosts, the **net effect approximates** what exponential usage decay achieves.

### 6.8 Multilevel Feedback Queues
- Since the run queue can be an **array of thread lists per priority level** (§4.4), priority adjustments amount to **moving threads between array entries**.
- Both Mac OS X's and Windows' schedulers are therefore examples of the broader **multilevel feedback queue** scheduler class.
- **Historical note:** the **original** multilevel scheduler placed threads into levels mainly based on **memory usage**, with **longer time slices** for lower-priority levels. **Today**, the most important multilevel feedback queue schedulers **approximate decay-usage scheduling**.

### 6.9 Mac OS X vs. Windows — A Subtle Trade-off
- **Mac OS X:** running threads drop **below** base priority (not just down to it) — this normally **prevents permanent starvation** of any runnable thread, even against a long-running, higher-base-priority thread.
- **Windows:** running threads only sink **down to** the base (not below) — a Windows-partisan counterargument: *"if base priority reflects importance, maybe the less important thread SHOULD be starved."* **In practice, however, total starvation is bad** — partly due to **priority inversion** (explained in a later chapter) — so Windows includes an **escape hatch**: every few seconds, it **temporarily boosts** the priority of any thread otherwise unable to get dispatched.

### 6.10 "Magic Numbers" — Where Do They Come From?
- Examples of oddly-specific constants: decay factor 5/8 (not 1/2), decay frequency 8x/second (not 4x), time quantum 10ms vs. 30ms across systems, Windows' keyboard-wait boost of exactly 6 (not 5 or 7).
- **Answer:** these are **tuned by trial and error** — designers run experiments with **representative workloads**, vary parameters, and measure response time/throughput. **No single parameter set optimizes everything for everyone**, but careful, systematic experimentation finds values that keep **most users happy most of the time**. Some systems let **administrators** further tune these for their specific installation.

### 6.11 A Goal Decay Usage Scheduling Is NOT Good At: Precise Resource Allocation
- **Example:** wanting exactly **2/3 vs. 1/3** processor allocation between two CPU-bound threads.
- **In principle**, base priorities could be tuned to approximate this — but **in practice, it's very difficult** to find base priorities that reliably produce a **specific desired proportion**.
- **Conclusion:** if precise resource-allocation control matters, use a **different** scheduler family — **proportional-share scheduling** (§7).

---

## 7. Proportional-Share Scheduling

### 7.1 The Shift in Perspective
- Proportional-share scheduling takes a **longer-term view**: rather than asking "which thread is most important **right now**," it **paces** threads, doling out processor time at **controlled rates**.

### 7.2 Three Basic Mechanisms

| Mechanism | How It Works |
|---|---|
| **Weighted Round-Robin (WRR)** | Every thread gets the processor **equally often**, but threads with **larger allocations get longer time slices** each turn. |
| **Weighted Fair Queuing (WFQ)** / stride scheduling / virtual time round-robin (VTRR) | **Uniform** time slice for all, but threads with **larger allocations run more often** — smaller-allocation threads **"sit out"** some rotations. |
| **Lottery Scheduling** | **Uniform** time slice; threads chosen via a **weighted lottery** each round (not fixed rotation) — larger allocations mean higher odds, **on average**. |

### 7.3 Why Lottery Scheduling Is Impractical (Despite Research Interest)
- **Long-run** proportions are correct, but **short-run deviations can be severe**.
- **Example:** two threads, each should get 50% of processing; with a 1/20-second time slice, each *should* run 10x/second — but one could, by **bad luck**, get **completely shut out for a whole second** (analogous to flipping a coin 20x/second all day and occasionally getting 20 heads in a row somewhere in that huge number of flips).
- Despite this practical shortcoming, lottery scheduling has received **considerable research attention**.

### 7.4 WRR vs. WFQ — Worked Example (3:2:1 ratio, T1:T2:T3)

**WRR Gantt chart (times in ms):**
```
T1        T2      T3
0    15   25    30
```
*(T1 runs 0-15 [15ms], T2 runs 15-25 [10ms], T3 runs 25-30 [5ms] — proportional to 3:2:1, but in long, unequal-length chunks.)*

**WFQ Gantt chart (fixed 5ms slices; T2 sits out 1 round in 3, T3 sits out 2 rounds in 3):**
```
T1  T2  T3  T1  T2  T1
0   5   10  15  20  25  30
```

- **WRR advantage:** **fewer thread switches** (larger, less-frequent chunks).
- **WFQ advantage:** keeps threads' **accumulated runtimes more consistently close** to the target proportions at **all times** (not just eventually) — see Exercise 3.7's deeper exploration.

### 7.5 Linux's Completely Fair Scheduler (CFS)

- **Niceness** controls proportional share; core algorithm = **weighted round-robin**.
- *(A separate, fixed-priority mechanism handles real-time threads — CFS governs ordinary threads only.)*
- On multiprocessor systems: **CFS schedules each processor's threads**, while a **largely independent load-balancing mechanism** distributes threads across processors.

#### 7.5.1 Weights and Target Round-Robin Time
- Each **niceness level** maps to a **weight**; time slices = (thread's weight) / (total weight of all runnable threads) times **target round-robin time**.
- **Example:** target = 6ms. Two equal-niceness threads → each gets **3ms**, regardless of whether niceness is 0 or 19 (both equal, so still split evenly). Four equal-niceness threads → each gets **1.5ms**.
- **Key implication:** the **switching rate scales with system load** (unlike a fixed time slice) — as load increases, CFS **sacrifices some throughput** (more frequent switches) to **preserve responsiveness** (bounded wait between turns).
- **Configurable parameters:**
    - **Target round-robin time** (default **6ms** on uniprocessor systems) — controls responsiveness.
    - **Minimum time per thread** — once the number of runnable threads reaches a threshold (**default: 8**), per-thread time **stops shrinking further**; adding more threads instead **increases the total cycle time**, preserving throughput at the cost of some responsiveness.

#### 7.5.2 Worked Example — Niceness 0 vs. Niceness 5
- **Weights:** niceness 0 -> 1024; niceness 5 -> 335.
- **Proportions:** 1024/(1024+335) ~ **~4.5ms** out of each 6ms for niceness-0 thread; 335/(1024+335) ~ **~1.5ms** for niceness-5 thread (since 1024 is roughly 3x 335).
- **Geometric progression insight:** the SAME ~3:1 ratio would result from niceness **5 vs. 10** (weights 335 and 110, still ~3:1) — because **weights follow a geometric progression**, only the **relative difference** in niceness matters, not the absolute levels.
- **Musical analogy (from the text):** like a well-tempered scale, where a musical interval (e.g., a major fifth) sounds the same regardless of its absolute position, because it's the **frequency ratio** that matters.

#### 7.5.3 The Big Idea: Virtual Runtime
- CFS tracks each thread's **virtual runtime**: actual running time **scaled by weight**.
    - Niceness-0 thread: 1 real nanosecond of running = **1** virtual nanosecond credited.
    - Niceness-5 thread: 1 real nanosecond of running ~ **3** virtual nanoseconds credited (precisely: 1024/335 ns per real ns).
- **Scheduling rule:** always favor running whichever thread is **furthest behind** in virtual runtime — but **not** by switching constantly (inefficient); instead, **stick with the current thread** until its time slice expires or it's preempted by a waking thread, **then** pick the thread with the **minimum virtual runtime**.

#### 7.5.4 Worked Example — Equal Niceness (Figure 3.10-style)
- Two equal-niceness threads (A, B). Ideal: after 9ms elapsed, each should have run **4.5ms**.
- In practice (due to discrete time slices): maybe A has run **6ms**, B has run **3ms**.
- **Scheduler's move:** pick **B** next (it's "behind"), letting it catch up — the **gap** between the two threads' runtimes never grows too large (bounded roughly by the time-slice size).

#### 7.5.5 Worked Example — Different Niceness (Figure 3.11-style)
- Thread A (niceness 0), Thread B (niceness 5) — assume 1024/335 ~ **exactly 3** for simplicity (A should run 3x as much as B).
- **Ideal after 9ms:** A = 6.75ms real, B = 2.25ms real.
- **Actual (discrete slices):** A = 6ms, B = 3ms.
- **Naive read:** "B has run less (3 < 6), so run B next?" — **WRONG**, because B's **fair share** was only supposed to be 2.25ms, and it's already **exceeded** that (3 > 2.25)!
- **Correct approach — virtual runtime:** scale A's time by 1 (still 6 virtual ms), scale B's time by 3 (3 real ms x 3 = **9 virtual ms**).
- **Compare virtual runtimes:** A = 6 virtual ms (behind), B = 9 virtual ms (ahead) → **scheduler correctly picks A next**.
- **Sanity check:** if both threads had hit their **ideal** real times (A=6.75, B=2.25), their **virtual** runtimes would be **exactly tied** (6.75 and 2.25x3=6.75) — confirming the scaling correctly captures "fair progress."

#### 7.5.6 Handling New/Waking Threads
- **Problem:** if a newly created or recently-woken thread started accumulating virtual runtime from **zero** (or its actual elapsed wall-clock idle time), it could unfairly **monopolize** the CPU trying to "catch up" to threads that have been running/accumulating virtual runtime all along.
- **Solution:**
    - Threads only **briefly** out of the run queue: allowed to **catch up somewhat** (not from absolute zero).
    - Threads **non-runnable longer than a threshold**: upon waking, virtual runtime is **fast-forwarded** to just **slightly less than** the current minimum virtual runtime among runnable threads — ensuring the woken thread runs **soon**, but not for an excessively long "catch-up" stretch.
    - **Newly created threads:** given a virtual runtime **slightly greater than** the current minimum — as if they'd just run and were now waiting their turn.
    - **Conceptual parallel:** this achieves the **same responsiveness/throughput benefit** as decay-usage dynamic priority adjustments, without disrupting proportional-share correctness.

#### 7.5.7 Data Structure: The Red-Black Tree
- Run queue kept **sorted by virtual runtime**, implemented as a **red-black tree** (a balanced binary search tree where no leaf is more than **twice as deep** as any other).
- **Dispatch rule:** switch to the **leftmost** node (i.e., the thread with the **smallest/earliest** virtual runtime).
- **Switches happen when:** (a) the current time slice **expires**, or (b) a **new thread enters** the run queue (**unless** the currently running thread just recently started — there's a **configurable minimum runtime** before preemption is allowed).

#### 7.5.8 Why This Prevents Starvation
- Because runnable threads are positioned along a **timeline of virtual runtimes**:
    - **Waking threads** get inserted at **increasingly later** virtual runtimes as real time passes.
    - A **patiently-waiting runnable thread** keeps a **fixed** virtual runtime while it waits its turn.
    - **Eventually**, the patient thread will have the **lowest** virtual runtime in the tree and get chosen — **naturally preventing starvation**, a problem that **earlier Linux schedulers** could suffer from (waking threads repeatedly "jumping the line" ahead of patient ones).

---

## 8. Security and Scheduling

### 8.1 The Relevant Threat: Denial of Service (DoS)
- **Definition:** an attack aiming to **prevent legitimate users** from using a system.
- **Motivations range** from immature nuisance-making to sophisticated attacks (e.g., disrupting a **military coordination system**).

### 8.2 Attack Vector 1 — Abusing Administrative Control
- **Most direct attack:** if an attacker could **directly manipulate** scheduling parameters (deadline, priority, base priority, resource share) of a **victim's** thread, they could simply **assign it a terrible priority**.
- **Defense:** real OSes **guard thread-control interfaces** — only the thread's **owner** (whoever ran the program that created it) or an authorized **system administrator** can modify its scheduling parameters.
    - **This relies on** broader system security guarantees (tamper-resistance, user authentication, bug-free enforcement) — covered in later chapters.

### 8.3 Attack Vector 2 — Competing for Resources Instead
- Since direct manipulation is guarded, attackers instead **create many competing threads**, trying to **siphon off** enough of a scarce resource (processor time) that little remains for the target.

### 8.4 Defense Strategy 1 — Make Attacks "Cumbersome" and Detectable
- Recall: a **single high-fixed-priority thread** could **completely starve** all normal threads.
- **Defense:** **prohibit normal users** from creating such powerful threads — reserve **all fixed priorities and higher-than-normal priorities** (even decay-usage-adjustable ones) for **authorized administrators only**.
- **Consequence:** an attacker must now run **MANY concurrent (normal-priority) threads** to meaningfully drain processor time — since legitimate users have little reason to do this, such behavior becomes **distinguishable** from normal use.
- **Trade-off:** limiting the **number of threads per user** constrains DoS attacks, but also constrains **legitimate flexibility** — there's an inherent tension here.

### 8.5 Defense Strategy 2 — Use an Inherently DoS-Resistant Scheduling Policy
- **Proportional-share schedulers** are promising here.
- **Linux's version** can assign shares hierarchically to **users or larger groups**, subject to further subdivision.
    - Originally proposed by **Waldspurger** as part of **lottery scheduling** (though lottery scheduling itself is disfavored due to short-term unfairness, as discussed in §7.3).
    - Waldspurger later extended the **same hierarchical idea** to **stride scheduling** (a deterministic proportional-share scheduler), and it has since been applied to various other proportional-share schedulers.

### 8.6 The Complication of Long-Running Server Threads
- **Problem:** a server thread processes requests from **many different users** over its lifetime. **Which user's resource allocation should "pay" for the server's work?**
- **Naive fix:** create a **special dedicated user** for the server thread, with a resource allocation large enough for all the work it does on everyone's behalf.
    - **Flaw:** too **coarse-grained** — if one user submits many requests, they could **consume the server thread's entire allocation**, denying service to **other users'** requests to that **same** server thread (even though other, unrelated threads remain unaffected).

### 8.7 A More Refined Fix: Resource Containers
- **Proposal (recent research, per the chapter):** allocate resources not directly to **threads**, but to independent **resource containers**.
- At any moment, a thread draws from **one** resource container, but **can switch** which container it draws from **as it handles different requests**.
- **Benefits:**
    - **Fairly accounts** for server threads' usage **per requesting user**, even though it's the **same thread** doing the work.
    - Because **multiple threads** can draw from a **single** resource container, this same mechanism also **prevents users from gaining more processor time simply by spawning more threads** (since all those threads still draw from the same, limited container).

### 8.8 The Bigger Picture — No Silver Bullet
> **No single approach to processor scheduling, taken alone, prevents all denial-of-service attacks.**

- An attacker will simply **target a different scarce resource** if processor scheduling is well-defended.
- **Historical example:** in the 1990s, attackers frequently targeted a system's **limited capacity to establish new network connections** instead.
- **Conclusion:** comprehensive security requires addressing **processor scheduling, networking, AND other system components together** — not any single mechanism in isolation.

---

## 9. Glossary of Key Terms

| Term | Definition |
|---|---|
| **Scheduler** | The OS mechanism that decides which thread runs on a processor at each moment. |
| **Run queue** | Data structure holding threads that are runnable (ready to execute if dispatched). |
| **Wait queue** | Data structure (one per reason for waiting) holding threads blocked on some event. |
| **Busy waiting** | Repeatedly checking for an event in a loop, wasting processor time in a multi-threaded system. |
| **Dispatch** | The act of the scheduler assigning a runnable thread to actually run on a processor. |
| **Throughput** | The rate at which useful work is accomplished (e.g., transactions/second). |
| **Response time** | Elapsed time from a triggering event to the completed response. |
| **Processor affinity** | Scheduling a thread on the same processor it previously ran on, to benefit from warm caches. |
| **Urgency** | How soon a task needs to be done. |
| **Importance** | How much is at stake in completing a task in a timely fashion. |
| **Resource allocation** | The fraction of processing resources granted to a thread/user/group. |
| **Fair-share scheduling** | Balances cumulative processor usage over a LONG time scale (e.g., a week), largely obsolete today. |
| **Proportional-share scheduling** | Balances processor usage over a SHORT time scale (e.g., a second), based on specified shares; still widely used. |
| **Niceness** | UNIX-family parameter controlling a thread's scheduling preference (interpreted as importance OR resource proportion, depending on system). |
| **Fixed-priority scheduling** | Scheduling where the OS never automatically adjusts a thread's priority. |
| **Rate-Monotonic Scheduling (RMS)** | Optimal FIXED-priority assignment for periodic real-time tasks: shorter period equals higher priority. |
| **Earliest Deadline First (EDF)** | Optimal DYNAMIC-priority policy: the thread with the soonest deadline gets highest priority, re-evaluated whenever a thread becomes runnable. |
| **Gantt chart** | A bar-chart representation of which thread runs during which time interval; used to test schedule feasibility. |
| **Decay usage scheduling** | Dynamic-priority scheme where a thread's priority decreases while running (as usage accumulates) and increases (decays back toward base) while waiting. |
| **Base priority** | The user-specified starting priority before automatic adjustments are applied. |
| **Multilevel feedback queue** | A scheduler that stores threads in priority-level lists and moves them between levels as priorities are adjusted. |
| **Weighted Round-Robin (WRR)** | Proportional-share mechanism: uniform turn frequency, but larger shares get LONGER time slices. |
| **Weighted Fair Queuing (WFQ)** | Proportional-share mechanism: uniform time slice, but larger shares get to run MORE OFTEN (others "sit out" rounds). |
| **Lottery scheduling** | Proportional-share mechanism using weighted random selection each round; simple but suffers short-term unfairness. |
| **Completely Fair Scheduler (CFS)** | Linux's proportional-share scheduler for ordinary threads, based on weighted round-robin and virtual runtime tracking. |
| **Virtual runtime** | A thread's actual runtime scaled by its weight (inverse of niceness-derived weight), used by CFS to determine which thread is "behind." |
| **Red-black tree** | The balanced binary search tree structure CFS uses to keep the run queue sorted by virtual runtime. |
| **Denial of Service (DoS) attack** | An attack aiming to prevent legitimate users from using a system, e.g., by monopolizing scheduling resources. |
| **Resource container** | An abstraction allowing resource allocation to be tracked independently of which specific thread is currently doing the work, solving the multi-user server-thread accounting problem. |

---

## 10. Summary Tables

### 10.1 The Three Scheduler Families

| Family | Basis | Best Serves | Real Examples |
|---|---|---|---|
| **Fixed Priority** | Static, user/admin-assigned priority | Urgency, importance (in controlled, hard-real-time environments) | POSIX FIFO/RR real-time classes; hard-real-time systems (e.g., flight control) |
| **Dynamic Priority** | Priority auto-adjusted based on deadlines (EDF) or recent usage (decay usage) | EDF serves urgency (real-time); Decay usage serves importance, throughput, response time | EDF (real-time theory); Mac OS X, Microsoft Windows (decay-usage-style) |
| **Proportional Share** | Processor time allocated in proportion to specified shares | Resource allocation | Linux CFS (weighted round-robin + virtual runtime) |

### 10.2 Mechanism-to-Goal Mapping (per the chapter's own Figure 3.8)

| Mechanism | Goals Served |
|---|---|
| Fixed priority | Urgency, importance |
| Earliest Deadline First | Urgency |
| Decay usage | Importance, throughput, response time |
| Proportional share | Resource allocation |

### 10.3 Mac OS X vs. Microsoft Windows Decay-Usage-Style Scheduling

| Aspect | Mac OS X | Microsoft Windows |
|---|---|---|
| Direction of adjustment while running | Priority decays DOWN from base (can go below base) | Priority sinks DOWN toward base (linear) |
| Direction of adjustment while waiting | Priority restored back UP toward base (exponential decay of usage) | Priority BOOSTED above base immediately upon waking, then decays back |
| Boost/decay basis | Exponential decay of "usage," factor 5/8 every 1/8 second | Boost size depends on WHAT was waited for (larger boost for longer-typical waits, e.g. keyboard vs disk) |
| Starvation prevention | Priority can drop below base, so patient threads eventually surface naturally | Needs an explicit periodic "escape hatch" boost for starved threads |

### 10.4 Proportional-Share Mechanisms Compared

| Mechanism | Time Slice | Turn Frequency | Pros | Cons |
|---|---|---|---|---|
| WRR | Variable (proportional to share) | Uniform (every thread every round) | Fewer switches | Runtimes can deviate further from ideal proportion in the short term |
| WFQ / stride / VTRR | Uniform | Variable (larger shares run more often) | Runtimes track the ideal proportion more closely at all times | More frequent switches |
| Lottery | Uniform | Random, weighted | Simple concept | Poor short-term fairness guarantees (can go a long stretch unluckily) |

---

## 11. Worked Exercise Solutions

### Exercise 3.1 — Gantt Charts for Response Time

**Setup:** T1 triggered at t=0, needs 1.5s to respond. T2 triggered at t=0.3s, needs 0.2s to respond.

**(a) T1 runs to completion, then T2:**
```
T1              T2
0             1.5  1.7
```
- T1 response time = 1.5s (0 to 1.5).
- T2 response time = 1.7 - 0.3 = **1.4s** (T2 triggered at 0.3, finishes at 1.7).
- **Average = (1.5 + 1.4) / 2 = 1.45s.**

**(b) T1 preempted when T2 triggers; T2 runs to completion, then T1 resumes:**
```
T1        T2      T1
0    0.3  0.5    2.0
```
- T1 ran 0-0.3 (0.3s of its 1.5s done), then paused; T2 runs 0.3-0.5 (its full 0.2s); T1 resumes 0.5-2.0 (remaining 1.2s).
- T2 response time = 0.5 - 0.3 = **0.2s**.
- T1 response time = 2.0 - 0 = **2.0s**.
- **Average = (2.0 + 0.2) / 2 = 1.1s.**

**(c) Preempt at t=0.3, then round-robin (starting with T2), 0.05s quantum, until one completes:**
- T2 needs only 0.2s total — with round-robin quanta of 0.05s, alternating T2, T1, T2, T1, ...:
    - T2: 0.3-0.35 (0.05s done, 0.15s left)
    - T1: 0.35-0.40 (0.05s of its remaining 1.2s done)
    - T2: 0.40-0.45 (0.10s done, 0.10s left)
    - T1: 0.45-0.50
    - T2: 0.50-0.55 (0.15s done, 0.05s left)
    - T1: 0.55-0.60
    - T2: 0.60-0.65 (0.20s done — **T2 completes!**)
- **T2 response time** = 0.65 - 0.3 = **0.35s**.
- After T2 completes at t=0.65, T1 continues running **uninterrupted**. T1 had done: 0.3s (initial) + 4x0.05s (its RR turns) = 0.3 + 0.2 = 0.5s so far, out of 1.5s needed → **1.0s remaining**, run straight from 0.65 to 1.65.
- **T1 response time** = 1.65 - 0 = **1.65s**.
- **Average = (1.65 + 0.35) / 2 = 1.0s.**

**Comparison:** (a) avg 1.45s, (b) avg 1.1s, (c) avg 1.0s — **round-robin interleaving (c) achieves the best average response time** in this example, though (b)'s "run the more urgent one to completion" approach is close behind, and both beat naive FIFO (a).

### Exercise 3.2 — Linux Group Scheduling: Fair Per-User or Per-Thread?

- **Three threads total:** 1 by User 1, 2 by User 2 (both chewing CPU in infinite loops).
- **With default AUTOMATIC GROUPING (Linux >=2.6.38, grouped by session):** IF each user's threads run in **separate terminal sessions**, then grouping is typically **per session**, which — **if each user has their own session** — would give **each session (roughly each user) a fair 1/2 share**, meaning User 1's single thread gets **50%**, and User 2's two threads **split the other 50%** (25% each).
    - *(Caveat: this depends on the two threads from User 2 running in the SAME session/terminal — if they're in different sessions, each thread might get its own 1/3 share regardless of user.)*
- **With grouping DISABLED (noautogroup), or on versions <=2.6.37 (default = per-thread shares):** each of the **three threads** gets an equal, individual share — **1/3 each**, meaning User 2 (with 2 threads) gets **2/3 total**, while User 1 (with 1 thread) gets only **1/3 total**.
- **Which is preferable?** Arguably the **per-user fairness** (session-grouped) behavior is more desirable in a shared, multi-user system — it prevents a user from gaining an unfair advantage simply by **spawning more threads**, which otherwise creates a perverse incentive to run many threads purely to grab more CPU time (also relevant to the DoS discussion in §8.7's resource-container motivation).

### Exercise 3.3 — Alternative Fixed-Priority Assignment (T2 Higher Than T1) — Confirming It Also Fails

- T1: period/deadline 4s, 2s/period. T2: period/deadline 6s, 3s/period. **T2 now higher priority.**

```
T2      T1  T2 (misses deadline)
0       3   4    6
```
- **At t=0:** both runnable; T2 (higher priority) runs first: 0-3 (its full 3s).
- **At t=3:** T1 becomes available to run (it was runnable the whole time but lower priority); runs 3-4 — but T1 needs 2s and its deadline is at t=4! It only gets 1 second (3-4) before its deadline arrives.
- **T1 misses its deadline:** by t=4, T1 has only run for **1 second**, not its required 2.
- **Confirms Liu & Layland's theorem:** swapping which thread gets higher fixed priority does **not** fix the infeasibility — **some** deadline is missed either way, exactly as predicted.

### Exercise 3.4 — EDF Gantt Chart With Opposite Tie-Break (Preempt Incumbent)

- Same T1(2s/4s), T2(3s/6s) as §5.3, but now **at the t=8 tie** (both deadline 12), we preempt the **already-running T2 in favor of newly-runnable T1** (opposite of the book's convention).

- Up through t=8, the schedule is identical to §5.3's example (T1: 0-2, T2: 2-5, T1: 5-7, T2: 7-8...).
- **At t=8:** in the book's version, T2 (already running, having started at t=7) continues uninterrupted to t=10. In **this** alternative (preempt in favor of T1), at t=8 we'd instead **switch to T1** for its remaining 2s (T1's 3rd period runs 8-10 instead of T2 continuing).
- Then T2 (interrupted at t=8, having run only 1 of its 3 needed seconds from 7-8) resumes at t=10, needing its remaining 2s: runs 10-12.
- **Check deadlines:** T1's 3rd-period deadline is 12; it finished at 10 — **met**. T2's 2nd-period deadline is 12; it needed 3s total (1s done 7-8, then 2s done 10-12) — finishes exactly at **t=12**, its deadline — **still met, just barely**.
- **Conclusion:** as the chapter notes, **theoretically any tie-breaking rule works** for meeting deadlines — this alternative also succeeds, just with **one extra context switch** (less efficient, but not incorrect) compared to the book's "favor the incumbent" convention.

### Exercise 3.5 — SJF Minimizes Average Turnaround Time

**Setup:** T1 needs 1s, T2 needs 2s, T3 needs 3s; all ready at t=0; run to completion, one at a time.

**All 6 possible orderings, with turnaround times:**

| Order | T1 turnaround | T2 turnaround | T3 turnaround | Average |
|---|---|---|---|---|
| T1,T2,T3 | 1 | 3 | 6 | **(1+3+6)/3 = 3.33** |
| T1,T3,T2 | 1 | 6 | 4 | (1+6+4)/3 = 3.67 |
| T2,T1,T3 | 3 | 2 | 6 | (3+2+6)/3 = 3.67 |
| T2,T3,T1 | 6 | 2 | 5 | (6+2+5)/3 = 4.33 |
| T3,T1,T2 | 4 | 6 | 3 | (4+6+3)/3 = 4.33 |
| T3,T2,T1 | 6 | 5 | 3 | (6+5+3)/3 = 4.67 |

- **Shortest average turnaround time = 3.33**, achieved by the order **T1 (1s), T2 (2s), T3 (3s)** — i.e., **running the shortest job first each time**.
- **This ordering IS Shortest Job First (SJF).**

### Exercise 3.6 — Coin-Flip Recurrence for Runs of Consecutive Heads

**(a) Proving the recurrence** f(n,k,p):
- **Base case n < k:** impossible to have k consecutive heads in fewer than k flips → f = 0.
- **Base case n = k:** the ONLY way to get k consecutive heads in exactly k flips is if **all k flips are heads** → probability = p^k.
- **Recursive case n > k:** condition on whether a run of k heads occurs (a) **within the first n-1 flips** (probability f(n-1,k,p)), OR (b) **NOT within the first n-1 flips, but occurs ending exactly at flip n** — meaning flip (n-k) is tails (to end any prior potential run), followed by flips (n-k+1) through n all heads (probability of this specific tail-then-k-heads pattern = (1-p)*p^k), **AND** no run of k heads occurred in the first (n-k-1) flips before that tail (probability 1 - f(n-k-1,k,p), to avoid double-counting with case (a)). Summing the two mutually exclusive cases gives exactly the stated recurrence.

**(b) Runs of k heads OR k tails in n flips of a FAIR coin equals f(n-1, k-1, 1/2):**
- **Reasoning:** a run of k consecutive **identical** results (heads OR tails) in n flips corresponds to a run of (k-1) consecutive "**repeats**" in the derived (n-1)-length sequence of "did flip i+1 match flip i?" indicators, each of which behaves like an independent fair coin (match/no-match, each probability 1/2). A run of (k-1) consecutive "matches" in this derived (n-1)-flip sequence is exactly f(n-1, k-1, 1/2).

**(c) Why the "incorrect" formula (2f(n,k,1/2) - f(n,k,1/2)^2, or equivalently 1-(1-f(n,k,1/2))^2) is wrong:**
- **Flawed reasoning:** treating "a run of k heads occurs" and "a run of k tails occurs" as **independent** events and applying the standard union formula for independent events.
- **The fallacy:** both events are derived from the **same** sequence of coin flips, so they are **correlated**, not independent — making the naive union formula invalid.
- **Small-case check (n=k=1):** the correct answer (via part b, using f(0,0,1/2), a trivial always-true case) is probability **1** (in a single flip, there's trivially always "a run" of at least 1 identical result). The incorrect formula gives 2(0.5) - 0.25 = **0.75**, which does NOT match — confirming the incorrect formula understates the true probability by failing to account for the correlation between the two events.

### Exercise 3.7 — WRR vs. WFQ Virtual Runtime Graphs

- Using the 3:2:1 example (T1, T2, T3), with virtual-runtime accumulation rates of 2, 3, and 6 ms of "virtual" time per 1 ms actually run (inverse of their 3:2:1 shares), plotting **accumulated virtual runtime vs. real time** for each thread under both the WRR and WFQ schedules from §7.4:
- **For WRR** (large, infrequent chunks: T1 0-15, T2 15-25, T3 25-30): each thread's virtual-runtime line stays **flat** while it's NOT running (no progress), then rises **steeply** during its own long turn — producing a graph with **large deviations** from the ideal diagonal line during the long stretches when a thread isn't running, even though all three end up at the same point (30,30) by t=30.
- **For WFQ** (small, frequent 5ms slices: T1,T2,T3,T1,T2,T1): each thread's virtual-runtime line **rises in smaller, more frequent increments**, staying **much closer to the diagonal** throughout the entire 30ms window, since no thread goes for very long without getting at least a small turn.
- **Conclusion (matches §7.4's stated trade-off):** **WFQ keeps virtual runtime lines closer to the ideal diagonal at all intermediate times** — i.e., it more continuously/consistently meters out the correct proportion — while **WRR** only matches the target proportions at the **coarser checkpoints** (e.g., only exactly right at t=30), with **larger transient deviations** in between.

### Exercise 3.8 — CFS Choice After 4.5ms(A), 1.5ms(B), 3ms(A)

- **Setup:** Thread A (niceness 0, weight scaling x1) and Thread B (niceness 5, weight scaling x3, i.e., B's virtual runtime accrues 3x faster per real ms). Schedule: A runs 4.5ms, then B runs 1.5ms, then A runs 3ms — total 9ms elapsed.
- **Real runtimes:** A = 4.5 + 3 = **7.5ms**; B = **1.5ms**.
- **Virtual runtimes:** A (x1 scaling) = **7.5** virtual ms; B (x3 scaling) = 1.5 x 3 = **4.5** virtual ms.
- **Comparison:** B's virtual runtime (4.5) is LESS than A's (7.5) → **B is "behind"** in the fair proportional sense.
- **At the 9ms mark, the scheduler will choose Thread B to run next**, since it has the **lower (smaller) virtual runtime**, meaning it hasn't yet received its fair proportional share relative to A.

---

## 12. Annotated Notes / Historical References

| Reference | Relevance |
|---|---|
| **Codd et al., 1959 [34]** | Recognized early the need for a "supervisory program" (proto-OS) to take control when a program can't proceed until I/O completes — an early articulation of the thread-waiting/scheduling problem discussed in §2. |
| **Regehr [118]** | Quantified the (often underappreciated) cache-performance cost of thread/context switching, supporting the claim in §3.1.2 that the real cost of switching is lost cache performance, not the direct register save/restore. |
| **Liu and Layland, 1973 [103]** | Seminal article proving the two key theorems underlying rate-monotonic scheduling and EDF optimality (§4.9, §5.2), foundational to all hard-real-time scheduling theory discussed in this chapter. |
| **Sha, Rajkumar, and Sathaye [133]** | Survey of how rate-monotonic scheduling has been generalized to handle more realistic real-world constraints beyond the chapter's simplified assumptions. |
| **Russinovich and Solomon's book [126]** | Source for the detailed, publicly-documented description of Microsoft Windows' scheduler (since Windows' source code isn't public) — referenced for the decay-usage-style variant discussed in §6.7. |
| **Hellerstein [74]** | Mathematical modeling study showing that while decay-usage schedulers can, in principle, achieve specific proportional service-rate objectives by tuning base priorities, doing so is impractical in practice — directly supporting §6.11's claim. |
| **Waldspurger [152], [153]** | Original proposer of hierarchical resource allocation for lottery scheduling, later extended to stride scheduling (a deterministic proportional-share approach) — foundational to §8.5's DoS-resistance discussion. |
| **Chandra et al., "Surplus Fair Scheduling" [29]** | Addressed the conceptual question of how proportional-share weights should behave correctly in a MULTIPROCESSOR context (referenced in the chapter's end notes, beyond the single-processor focus of the main text). |
| **Li, Baumberger, and Hahn, Distributed Weighted Round Robin [100]** | A promising approach to efficiently implementing proportional-share scheduling across multiple processors without a synchronization bottleneck on a single shared run queue. |
| **Banga, Druschel, and Mogul [10]** | Introduced the **resource container** abstraction (§8.7), solving the multi-user server-thread fair-accounting problem. |

---

## 13. Big-Picture Takeaways

1. **Scheduling is fundamentally about deciding which thread runs next** — but "best" depends entirely on **which goal(s)** you're optimizing for, and different goals genuinely **conflict**.
2. **Thread states (Runnable, Running, Waiting) exist precisely to avoid wasting CPU time on busy-waiting** — the OS tracks run queues and wait queues so the scheduler only ever considers threads that can make real progress.
3. **The two core performance goals — throughput and response time — are often in tension.** Frequent switching helps response time but hurts throughput (via context-switch and cache overhead); batching work helps throughput but can hurt responsiveness.
4. **User control over scheduling breaks down into three genuinely distinct concepts — urgency, importance, and resource allocation — that a single "priority" number cannot simultaneously express**, as the ambiguity of UNIX "niceness" vividly illustrates.
5. **Fixed-priority scheduling is simple and provably optimal for periodic hard-real-time systems (via rate-monotonic scheduling)**, but is dangerous in open, general-purpose environments where it can lead to total starvation.
6. **Dynamic-priority scheduling generalizes this: EDF is optimal for real-time deadlines (more powerful than any fixed assignment), while decay-usage scheduling generalizes the same "reward waiting, penalize running" idea to everyday interactive/throughput goals** (used in Mac OS X and Windows).
7. **Proportional-share scheduling (culminating in Linux's CFS with its virtual-runtime/red-black-tree design) directly targets resource-allocation goals that decay-usage schedulers can't reliably achieve**, trading off simplicity for precise, tunable, starvation-resistant control over processor shares.
8. **Scheduling security boils down to preventing denial-of-service attacks** — achieved by (a) tightly guarding who can set scheduling parameters, (b) making abuse cumbersome/detectable via thread-count limits, and (c) using inherently fairer mechanisms like proportional-share and resource containers — though **no single defense is sufficient alone**, since attackers can always pivot to a different scarce resource.