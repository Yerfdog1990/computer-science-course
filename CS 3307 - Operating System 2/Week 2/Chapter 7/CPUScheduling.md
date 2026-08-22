# CPU Scheduling 

*(Based on OSTEP Chapter 7: "Scheduling: Introduction")*

---

## Table of Contents

1. Background & Motivation
2. Workload Assumptions
3. Scheduling Metrics
4. First In, First Out (FIFO / FCFS)
5. Shortest Job First (SJF)
6. Shortest Time-to-Completion First (STCF / PSJF)
7. Response Time — A New Metric
8. Round Robin (RR)
9. The Fundamental Trade-off: Turnaround vs. Response Time
10. Incorporating I/O
11. The Problem of the Unknown Future
12. Glossary of Key Terms
13. Summary Comparison Table
14. Key Formulas Recap
15. Worked Homework Questions & Solutions
16. Annotated Reference List

---

## 1. Background & Motivation

- **Scheduling** is the high-level policy layer of the OS that decides **which process/job runs next** on the CPU. It sits on top of the low-level **mechanisms** (like context switching, saving/restoring registers, timer interrupts) that make multitasking physically possible.
    - *Mechanism* = **how** something is done (e.g., how a context switch swaps register state).
    - *Policy* = **what** decision is made (e.g., which of 5 ready processes should get the CPU next).
- Scheduling is **not unique to computing**. Its roots trace back to **operations management / operations research**, where the same problem shows up in very different contexts:
    - Assembly lines deciding which product to build next.
    - Factories scheduling machine repair jobs.
    - Airports scheduling runway usage.
    - Hospitals scheduling patients in an ER (triage is essentially a scheduling policy).
- In all of these domains, a recurring desire drives design: **efficiency** — get the most useful work done with the resources available, while (usually) also being fair to those waiting.
- **The Crux (guiding question of the chapter):** How do we build a general framework for reasoning about scheduling policies? What assumptions should we make about the "jobs" being scheduled? What yardsticks (metrics) do we use to judge whether a policy is "good"? And what were the earliest, simplest approaches used historically?

### Why This Matters
Every general-purpose OS — Linux, Windows, macOS, mobile OSes — has a scheduler at its core. Bad scheduling decisions manifest as:
- Sluggish, laggy interactive systems (poor **response time**).
- Long-running batch jobs that never seem to finish (poor **turnaround time**).
- Some processes getting almost no CPU time at all (**starvation**, a fairness problem).

Understanding scheduling policy trade-offs is foundational to understanding *why* real schedulers (like Linux's **CFS**, or the upcoming **Multi-Level Feedback Queue**) are built the way they are.

---

## 2. Workload Assumptions

Before designing any policy, we need a simplified model of what we're actually scheduling — collectively called the **workload**. The chapter deliberately starts with **unrealistic** assumptions, and *systematically relaxes them one at a time* — this is a classic teaching/engineering technique: **start simple, add realism incrementally, and see what breaks.**

The initial (unrealistic) workload assumptions about jobs/processes:

| # | Assumption | Realistic? |
|---|---|---|
| 1 | Each job runs for the **same amount of time**. | No |
| 2 | **All jobs arrive at the same time.** | No |
| 3 | Once started, each job **runs to completion** (no interruption/preemption). | No |
| 4 | All jobs **only use the CPU** — they perform **no I/O**. | No |
| 5 | The **run-time of each job is known in advance** by the scheduler. | No — least realistic of all |

### Why Start With Unrealistic Assumptions?
- It lets us reason about *simple* policies first (like FIFO) in a *tractable* setting.
- Each subsequent section of the chapter **relaxes exactly one assumption**, showing:
    - What goes wrong when that assumption is removed.
    - What new policy or mechanism is needed to compensate.
- By the end, all five assumptions are relaxed (or partially relaxed), leading toward realistic schedulers such as the **Multi-Level Feedback Queue (MLFQ)** — the subject of the *next* chapter.

### The Especially Problematic Assumption
Assumption 5 (the OS knows the run-time of each job) is singled out as particularly troubling: it implies an **omniscient scheduler** — one that can see into the future. Real schedulers cannot know in advance how long a process will run (a process could loop forever, wait on user input, etc.). This assumption is the last one addressed, and its removal motivates the **MLFQ** approach in the following chapter, which uses **past behavior as a predictor of future behavior**.

---

## 3. Scheduling Metrics

A **metric** is simply a quantity used to measure and compare the performance of different policies. Without an agreed-upon metric, we cannot say one scheduler is "better" than another — comparisons become subjective.

### 3.1 Turnaround Time (primary metric of this chapter)

$$
T_{turnaround} = T_{completion} - T_{arrival}
$$

- Measures the *total* time a job spends in the system, from arrival until it fully finishes.
- Under Assumption 2 (all jobs arrive at t=0), `T_arrival = 0`, so temporarily `T_turnaround = T_completion`. This simplification is later removed once we relax Assumption 2.
- Turnaround time is a **performance metric** — it cares only about throughput/efficiency of completing jobs, not about how "fair" the experience feels to any individual job while it's running.

### 3.2 Fairness

- Measured, for example, by **Jain's Fairness Index** [J91].
- Fairness asks: *is the CPU being shared reasonably evenly among competing jobs?*
- **Performance and fairness are frequently in tension.** A scheduler that aggressively optimizes turnaround time might starve some jobs of CPU time for long stretches, which is efficient in aggregate but unfair to those jobs.
- This tension is a recurring theme: you'll see it concretely later when comparing **STCF** (great turnaround, poor fairness/response) against **Round Robin** (great fairness/response, poor turnaround).

### 3.3 (Preview) Response Time
A third metric — **response time** — is introduced later in the chapter (Section 7 below) once interactive, time-shared systems enter the picture. It's called out separately here because historically it didn't exist as a *concern* until interactive computing existed; early batch systems only cared about turnaround time.

> **Key takeaway:** Before judging *any* scheduler as "good" or "bad," always ask: **according to which metric?** A scheduler can be excellent for one metric and terrible for another simultaneously (this is exactly what happens with STCF vs. RR).

---

## 4. First In, First Out (FIFO / FCFS)

**First In, First Out**, also called **First Come, First Served (FCFS)**, is the simplest possible scheduling discipline.

### 4.1 Policy
Jobs are run strictly in the order they arrive in the system. No reordering, no consideration of job length.

### 4.2 Advantages
- **Extremely simple** to implement (just a FIFO queue).
- Under the basic assumptions (equal-length jobs, all arriving together), it performs **reasonably well** — see Example 1.

### 4.3 Example 1 — Equal-Length Jobs (all assumptions hold)

Three jobs A, B, C arrive at essentially the same time (`T_arrival = 0` for all), but FIFO must impose *some* order, so assume A arrives an instant before B, which arrives an instant before C. Each job runs for **10 seconds**.

```
Time:   0    10   20   30
        |----A----|
                  |----B----|
                            |----C----|
```

- A completes at t = 10
- B completes at t = 20
- C completes at t = 30

**Average turnaround time:**
$$
\frac{10 + 20 + 30}{3} = \frac{60}{3} = 20 \text{ seconds}
$$

This is optimal under these idealized conditions — nothing better is possible when all jobs are equal length and arrive together, since *someone* has to go first, second, third.

### 4.4 Example 2 — Relaxing Assumption 1 (unequal job lengths)

Now suppose A runs for **100 seconds**, while B and C each run for only **10 seconds**. FIFO still runs them in arrival order: A, then B, then C.

```
Time:   0                100  110  120
        |--------A(100)--------|
                                |-B(10)-|
                                        |-C(10)-|
```

- A completes at t = 100
- B completes at t = 110
- C completes at t = 120

**Average turnaround time:**
$$
\frac{100 + 110 + 120}{3} = \frac{330}{3} = 110 \text{ seconds}
$$

Compare this to Example 1's 20 seconds — a **dramatic** degradation, even though the *total* amount of work (120 seconds) is only slightly more than before (30 seconds vs. 120 — actually much more work here, but the point is the *relative* penalty to B and C is severe: two 10-second jobs each effectively "cost" over 100 seconds of wait).

### 4.5 The Convoy Effect

- This phenomenon — where a big, slow job blocks smaller/faster jobs behind it — is called the **convoy effect** [B+79], a term first used to describe a similar issue in **database systems**.
- **Real-world analogy:** Think of a grocery store checkout line. If a "ten-items-or-less" express line didn't exist, someone buying 3 full carts of groceries could hold up dozens of people who only wanted to buy a candy bar.
- **Takeaway:** FIFO's fatal flaw is that it is **completely blind to job length**. It treats a 1-second job exactly the same as a 1000-second job when deciding order — purely based on arrival time.

### 4.6 When Does FIFO Actually Perform Fine?
FIFO performs identically to the "best possible" schedule specifically when:
- All jobs have roughly equal length, **or**
- Jobs happen to arrive already sorted from shortest to longest (a coincidence, not something FIFO can guarantee).

This observation foreshadows Homework Question 4 in Section 15.

---

## 5. Shortest Job First (SJF)

### 5.1 Motivation
Given the convoy effect problem in FIFO, the natural question is: *what if we simply prioritize shorter jobs?* This idea wasn't invented for computers — it was **borrowed directly from operations research** [C54, PV56], where it was originally used to schedule the repair of broken machines: fix quick jobs first so more machines are back online sooner, rather than tying up the repair queue with one big overhaul.

### 5.2 Policy
Run the **shortest job first**, then the next-shortest, and so on — a purely length-based greedy ordering. It is **non-preemptive**: once a job is chosen and starts running, it runs to completion without interruption (Assumption 3 still holds here).

> **Tip (from the book):** "Shortest Job First" is a *general principle*, applicable anywhere queueing happens and perceived wait matters. Grocery stores' "10 items or less" express lanes are a real-world implementation of the SJF principle.

### 5.3 Example — SJF on the Same Workload as Example 2

Same jobs: A = 100s, B = 10s, C = 10s. But now scheduled shortest-first: B, then C, then A.

```
Time:   0   10   20                 120
        |-B-|
            |-C-|
                |---------A(100)---------|
```

- B completes at t = 10
- C completes at t = 20
- A completes at t = 120

**Average turnaround time:**
$$
\frac{10 + 20 + 120}{3} = \frac{150}{3} = 50 \text{ seconds}
$$

Compared to FIFO's 110 seconds on the identical workload, SJF achieves **more than a 2× improvement** (110 → 50) simply by reordering execution — no extra hardware or resources needed, just a smarter policy.

### 5.4 Optimality (Under Current Assumptions)
Given that **all jobs arrive at the same time** (Assumption 2 still holds) and jobs run to completion once started (Assumption 3 still holds), **SJF is provably optimal** for minimizing average turnaround time. (The book jokingly notes: "you are in a systems class, not theory... no proofs are allowed" — but the intuitive argument is: front-loading short jobs minimizes the number of jobs that have to "wait behind" any given job.)

### 5.5 The Cracks Start to Show — Relaxing Assumption 2

What happens once jobs are allowed to **arrive at different times** rather than all at once?

**Example:** Job A arrives at t = 0 and needs **100 seconds**. Jobs B and C both arrive at **t = 10** and each need only **10 seconds**.

Because SJF (as defined so far) is **non-preemptive**, once A starts running at t=0, it **cannot be interrupted** — even though B and C (much shorter jobs) show up 10 seconds later and would clearly be better to run first.

```
Time:   0        10                100  110  120
        |------------A(100)----------------|
                 ^                  |-B(10)-|
            [B, C arrive here]              |-C(10)-|
```

- A completes at t = 100
- B completes at t = 110 (started only after A finished)
- C completes at t = 120

**Average turnaround time:**
$$
\frac{100 + (110 - 10) + (120 - 10)}{3} = \frac{100 + 100 + 110}{3} = \frac{310}{3} \approx 103.33 \text{ seconds}
$$

- Even though B and C are short jobs, they suffer the **exact same convoy effect** problem as under FIFO, simply because SJF cannot preempt a currently running job. Being "shortest job first" only helps *at the moment of choosing what to run* — it does nothing once a bad choice is already locked in and running.

**Key insight:** The problem isn't the *ordering rule* itself — it's the **non-preemptive** nature of the scheduler. This directly motivates the next scheduler: STCF.

---

## 6. Shortest Time-to-Completion First (STCF) / Preemptive Shortest Job First (PSJF)

### 6.1 Motivation
To fix the late-arrival convoy problem above, we relax **Assumption 3** (jobs must run to completion once started) and add **preemption** capability — using the same context-switching machinery described in earlier chapters.

### 6.2 Policy
Any time a **new job enters the system**, the scheduler compares the **remaining time** of *every* job currently in the system (including the newly arrived one) and immediately switches to run whichever job has the **least time left**. This is why it's also called **Preemptive Shortest Job First (PSJF)** [CK68].

### 6.3 Example — STCF on the Same Late-Arrival Workload

A arrives at t=0 needing 100s. B and C arrive at t=10, each needing 10s.

- At t=10, when B and C arrive, the scheduler compares:
    - A's remaining time: 90 seconds (100 - 10 already run)
    - B's remaining time: 10 seconds
    - C's remaining time: 10 seconds
- B and C have less remaining time, so A is **preempted**.

```
Time:   0    10   20   30                        120
        |-A(10)-|
                |-B(10)-|
                        |-C(10)-|
                                |------A resumes (90s)------|
```

- B completes at t = 20
- C completes at t = 30
- A resumes and finally completes at t = 120 (10s run + 90s remaining = 100s total CPU time, finishing at t=120)

**Average turnaround time:**
$$
\frac{(120 - 0) + (20 - 10) + (30 - 10)}{3} = \frac{120 + 10 + 20}{3} = \frac{150}{3} = 50 \text{ seconds}
$$

This is a **massive improvement** over plain SJF's 103.33 seconds on the identical workload — simply by allowing preemption when shorter jobs arrive.

### 6.4 Optimality
Given the relaxed assumptions so far (jobs can arrive at any time, and preemption is allowed), **STCF is provably optimal** for average turnaround time. This follows fairly intuitively from SJF's optimality in the "all arrive together" case — STCF just re-applies that logic continuously as new jobs arrive.

### 6.5 Aside: Preemptive vs. Non-Preemptive Schedulers
- **Non-preemptive schedulers** (common in old batch-processing systems): each job runs to completion (or until it voluntarily yields, e.g., for I/O) before the OS considers switching to a different job.
- **Preemptive schedulers**: the OS can forcibly stop a running process at (almost) any time to run something else. Virtually **all modern general-purpose OS schedulers are preemptive**.
- This capability directly relies on the **context-switching mechanism**: saving the state of the currently running process and restoring the state of another.

### 6.6 STCF's Remaining Weakness
STCF (and its relatives) optimize turnaround time beautifully, but — as the next section shows — they can be **terrible for response time**, especially in interactive settings.

---

## 7. Response Time — A New Metric

### 7.1 Why a New Metric Was Needed
Early computing was dominated by **batch processing**: jobs were submitted, ran without user interaction, and results were collected later. Turnaround time was the *only* metric that mattered.

But once **time-shared, interactive systems** emerged — where a human sits at a terminal typing commands and expects to see feedback *quickly* — a new concern arose: how long does a user have to **wait before seeing any response at all**, regardless of how long the whole job takes to finish?

### 7.2 Definition

$$
T_{response} = T_{firstrun} - T_{arrival}
$$

- This measures time from arrival **until the job is first given the CPU** — *not* until it finishes.
- Footnote from the book: some definitions include the time until the job actually *produces* a visible response, but here we use the "best case" version — assuming the response appears instantaneously once the job starts running.

### 7.3 Example — Response Time Under STCF

Recall the STCF schedule from Section 6.3: A arrives at t=0, B and C arrive at t=10; final schedule was A(0-10), B(10-20), C(20-30), A resumes(30-120).

- A's response time: `0 - 0 = 0` (it started running immediately at arrival)
- B's response time: `10 - 10 = 0` (it started running the instant it arrived, at t=10)
- C's response time: `20 - 10 = 10` (C arrived at t=10 but had to wait for B to finish before starting at t=20)

**Average response time:**
$$
\frac{0 + 0 + 10}{3} = 3.33 \text{ seconds}
$$

### 7.4 Where STCF-Family Schedulers Fall Apart

Consider a *simpler, starker* case: three jobs, **A, B, C**, **all arrive at the same time** and all need **5 seconds** each (equal length, so STCF/SJF behaves just like FIFO here — arbitrary tie-break order, say A, B, C).

```
Time:   0    5    10   15
        |-A-|
            |-B-|
                |-C-|
```

- A's response time: 0
- B's response time: 5 (waited for A to fully finish)
- C's response time: 10 (waited for both A and B to fully finish)

**Average response time (SJF-style scheduling):**
$$
\frac{0 + 5 + 10}{3} = 5 \text{ seconds}
$$

- **Imagine sitting at an interactive terminal**, having typed a command, and being told: "sorry, you need to wait 10 seconds before the system even *looks* at your request" — simply because some other job happened to be scheduled ahead of yours. This is a genuinely bad interactive experience, even though from a pure turnaround-time perspective, nothing is "wrong."
- This motivates the need for a scheduler explicitly designed to **minimize response time**, even if it costs some turnaround-time efficiency.

---

## 8. Round Robin (RR)

### 8.1 Policy

Round Robin [K64] takes a radically different approach: instead of running any job to completion (or even to its next preemption point based on length), RR runs **every job for a fixed, short burst called a time slice** (a.k.a. **scheduling quantum**), then moves on to the next job in the queue — cycling through repeatedly until all jobs finish.

- Because of this behavior, RR is also called **time-slicing**.
- **Constraint:** the time slice length must be a **multiple of the timer-interrupt period** (the hardware clock interrupt that lets the OS regain control periodically). E.g., if the timer fires every 10ms, valid time slices are 10ms, 20ms, 30ms, etc.

### 8.2 Example — RR vs. SJF for Response Time

Three jobs A, B, C arrive together, each needing **5 seconds**. Compare SJF (runs to completion, one at a time) vs. RR with a **1-second time slice**.

**SJF/FIFO-style (Figure 7.6 in book):**
```
Time:   0    5    10   15
        |-A-|
            |-B-|
                |-C-|
```
Average response time = (0 + 5 + 10) / 3 = **5 seconds**

**Round Robin, 1-second slices (Figure 7.7 in book):**
```
Time:   0  1  2  3  4  5  6  7  8  9  10 11 12 13 14 15
        A  B  C  A  B  C  A  B  C  A  B  C  A  B  C
```
- A first runs at t=0 → response time 0
- B first runs at t=1 → response time 1
- C first runs at t=2 → response time 2

**Average response time (RR):**
$$
\frac{0 + 1 + 2}{3} = 1 \text{ second}
$$

RR improves average response time **5× over SJF** on this workload (5s → 1s) — a dramatic gain for interactivity.

### 8.3 The Time-Slice Trade-off (Amortization)

- **Shorter time slice** → jobs get touched more frequently → **better response time**.
- **But too short a time slice** → the fixed overhead cost of context switching (saving/restoring state) begins to **dominate** total execution time, wasting CPU cycles on bookkeeping instead of useful work.

> **Tip — Amortization:** When an operation has a fixed cost, you can reduce its *relative* impact by performing it less often. Example: if a context switch costs **1 ms**, and the time slice is **10 ms**, then **10%** of all CPU time is wasted on switching. If the time slice is increased to **100 ms**, only **<1%** of time is spent switching — the fixed cost has been "amortized" over a longer useful work period.

- **Beyond registers:** context-switch costs aren't just about saving/restoring a handful of CPU registers. Switching processes also **flushes and reloads**:
    - CPU **caches** (L1/L2/L3)
    - **TLBs** (Translation Lookaside Buffers, used for virtual memory address translation)
    - **Branch predictors**
    - Other on-chip hardware state that has "warmed up" for the currently running program.
    - Reloading all of this for a new process is a real, measurable performance cost [MB91], even though modern CPUs are extremely fast.

- **Designer's Dilemma:** Pick a time slice **long enough to amortize context-switch overhead**, but **short enough to remain responsive**. This is a genuine engineering trade-off with no single correct answer — it depends on workload characteristics.

### 8.4 RR's Weakness — Turnaround Time

Using the same 3-job, 5-second-each example, but now measuring **turnaround time** instead of response time, with RR (1-second slices):

- A finishes at **t = 13**
- B finishes at **t = 14**
- C finishes at **t = 15**

**Average turnaround time (RR):**
$$
\frac{13 + 14 + 15}{3} = \frac{42}{3} = 14 \text{ seconds}
$$

Compare to FIFO/SJF's result on the same workload (5, 10, 15 → average 10 seconds — note: even simple FIFO here beats RR badly, since all jobs are equal length). **RR is nearly the worst possible policy for turnaround time** — sometimes *worse* than plain FIFO — because it deliberately **stretches out every job's finish time** by only giving each one a small sliver of CPU time before moving on, over and over.

### 8.5 Why RR Is Bad for Turnaround Time (Intuition)
- Turnaround time only cares about **when a job finishes** — it doesn't care how "smoothly" or "fairly" the job's execution was spread out over time.
- RR by design maximizes the *spread* of every job's execution across the full timeline (interleaving them), which necessarily **delays every job's completion** compared to running fewer jobs to completion at a time.

---

## 9. The Fundamental Trade-off: Turnaround vs. Response Time

This is one of the most important conceptual takeaways of the chapter:

> **Any policy that is "fair"** — i.e., evenly divides CPU time among active processes on a small time scale (like RR) — **will perform poorly on turnaround-time-based metrics.**

This is not a flaw specific to RR — it's a **fundamental, inherent trade-off**:

| If you value... | You should... | But you sacrifice... |
|---|---|---|
| **Turnaround time** | Run jobs to completion / favor short jobs (SJF, STCF) | Response time / fairness |
| **Response time / Fairness** | Interleave jobs frequently (RR) | Turnaround time |

- **Two families of schedulers**, as categorized by the chapter:
    1. **SJF / STCF family** — optimize **turnaround time**, but are **bad for response time**.
    2. **Round Robin family** — optimizes **response time**, but is **bad for turnaround time**.
- Colloquially: **"You can't have your cake and eat it too."** (The book includes a humorous footnote about this idiom's confusing phrasing and its equivalents in other languages — e.g., Italian: *"Avere la botte piena e la moglie ubriaca"* — "having a full wine barrel and a drunk wife.")
- **Overlap as a partial mitigation:** In many systems contexts (not just CPU scheduling), starting one operation and then switching to other useful work while waiting (rather than idling) improves overall utilization — this principle reappears in the I/O discussion below.

---

## 10. Incorporating I/O

### 10.1 Motivating Relaxation of Assumption 4

Real programs are not pure CPU crunchers — **all realistic programs perform I/O** (reading input, writing output, network access, disk access, etc.). The book makes a philosophical point: a program with no input would always produce identical output (useless); a program with no output is like the proverbial tree falling with no one to hear it — its execution wouldn't matter to anyone.

### 10.2 The Scheduling Decision at I/O Time

Whenever a running job issues an I/O request:
- The CPU sits idle **from that job's perspective** — the job is now **blocked**, waiting for the I/O device (e.g., disk) to respond, which could take milliseconds or longer depending on system load.
- **The scheduler should almost certainly run a different, ready job on the CPU during this wait**, rather than leaving the CPU idle.

Whenever an I/O operation **completes**:
- A hardware **interrupt** is raised.
- The OS moves the waiting process from **blocked** back to **ready**.
- The scheduler must then decide: should this newly-ready job run immediately, or wait its turn?

### 10.3 Example — Mixing CPU-Bound and I/O-Bound Jobs

Two jobs:
- **Job A**: needs 50ms of total CPU time, but issues an I/O request every 10ms of CPU use (assume each I/O operation takes 10ms). So A's pattern is: 10ms CPU → 10ms I/O → 10ms CPU → 10ms I/O → ... (5 such CPU sub-bursts total).
- **Job B**: needs a single, uninterrupted 50ms CPU burst, with **no I/O** at all.

**Naive scheduling (run A fully, then B) — Figure 7.8:**
```
Time:   0    10   20   30   40   50   60   70   80   90  100 ... 150
CPU:    A         A         A         A         A         B(50ms)
Disk:        A         A         A         A
```
- During each of A's I/O waits, the **CPU sits completely idle** — wasted capacity, since B could easily be running there instead.
- Total time to finish both jobs stretches out unnecessarily because of this poor overlap.

**Improved scheduling — treat each CPU burst as a separate "job" — Figure 7.9:**
```
Time:   0   10   20   30   40   50   60   70   80   90  100
CPU:    A    B    A    B    A    B    A    B    A    B
Disk:        A         A         A         A
```
- **Key idea:** treat each **10ms sub-burst of A** as if it were its own independent "job" for scheduling purposes.
- At t=0, the scheduler must choose between: a 10ms sub-job of A, or a 50ms job B. Under an **STCF-like policy**, the shorter one (A's sub-burst) is chosen.
- When A's first sub-burst finishes and A goes off to do I/O, **only B is ready**, so B runs.
- When A's I/O completes and its next 10ms sub-burst becomes ready, it **preempts B** (since 10ms < B's remaining time) and runs again.
- This produces much better **overlap**: the CPU is kept busy running B *while A is off doing I/O elsewhere* — nothing sits idle unnecessarily.

### 10.4 Why This Matters
- By treating each CPU burst (rather than the whole job) as the unit of scheduling, **interactive/I/O-heavy processes get scheduled frequently and briefly** — exactly matching their natural behavior pattern (short compute bursts interspersed with waiting).
- Meanwhile, **CPU-intensive jobs** (like B) still make steady progress **filling in the gaps** left by I/O-bound jobs — maximizing overall CPU utilization.
- This is the conceptual seed of how real schedulers **balance interactive and batch/CPU-bound workloads simultaneously**.

---

## 11. The Problem of the Unknown Future

### 11.1 Relaxing the Final Assumption (Assumption 5)

We've now relaxed:
- ✅ Assumption 1 (equal job length) — via SJF/STCF.
- ✅ Assumption 2 (simultaneous arrival) — via STCF.
- ✅ Assumption 3 (run-to-completion) — via preemption (STCF, RR).
- ✅ Assumption 4 (no I/O) — via per-burst scheduling.
- ❌ Assumption 5 (job length is known in advance) — **still unaddressed**, and arguably the **least realistic** assumption of all.

### 11.2 Why This Is Hard

In a real, general-purpose OS, the scheduler typically has **little to no advance knowledge** of how long any given process will run. A process might:
- Run briefly and exit.
- Loop indefinitely (a web server, a daemon).
- Alternate unpredictably between CPU bursts and I/O waits based on user input or network conditions.

Yet SJF/STCF *fundamentally depend* on knowing (or estimating) job length to make their scheduling decisions. Without that knowledge, how can we build a scheduler that:
- Behaves *like* SJF/STCF (good turnaround time) when possible, **and**
- Behaves *like* RR (good response time) for interactive jobs,
- **...without an oracle that knows the future?**

### 11.3 The Answer (Preview of Next Chapter)

The chapter closes by foreshadowing the solution: a scheduler called the **Multi-Level Feedback Queue (MLFQ)**, which cleverly **uses a process's recent past behavior as a predictor of its future behavior** — e.g., a process that has used only short CPU bursts recently is *likely* interactive and should be prioritized for responsiveness; a process that has run for a long continuous stretch is *likely* CPU-bound and can be deprioritized without much harm to interactivity. This is the topic of the **next chapter** and is not detailed further here.

---

## 12. Glossary of Key Terms

| Term | Definition |
|---|---|
| **Job / Process** | A unit of work being scheduled by the OS. |
| **Workload** | The collective set of assumptions/characteristics about the jobs being scheduled. |
| **Turnaround Time** | Time from a job's arrival to its completion. |
| **Response Time** | Time from a job's arrival to when it is *first* scheduled to run. |
| **Fairness** | How evenly CPU time is distributed among competing jobs (e.g., via Jain's Fairness Index). |
| **Preemption** | Forcibly stopping a running process to run a different one. |
| **Context Switch** | The mechanism of saving one process's state and restoring another's, enabling preemption. |
| **Convoy Effect** | Short jobs getting stuck waiting behind a long job in line. |
| **Time Slice / Quantum** | The fixed duration a process runs before RR switches to the next job. |
| **Amortization** | Spreading a fixed cost over more useful work to reduce its relative overhead. |
| **CPU Burst** | A continuous stretch of CPU usage by a process before it blocks (e.g., for I/O). |
| **Oracle Scheduler** | A hypothetical scheduler with perfect foreknowledge of job lengths (not realistic). |
| **Non-preemptive Scheduler** | Once a job starts, it runs until completion or voluntary yield. |
| **Starvation** | A job receiving little or no CPU time over an extended period, due to scheduler bias. |

---

## 13. Summary Comparison Table

| Scheduler | Preemptive? | Needs Job Length Known? | Optimizes For | Main Weakness | Real-World Analogy |
|---|---|---|---|---|---|
| **FIFO / FCFS** | No | No | Simplicity | Convoy effect with unequal job lengths | Standard single checkout line |
| **SJF** | No | Yes | Turnaround time (if all arrive together) | Convoy effect if long job already running when short jobs arrive | Express repair of quick fixes first |
| **STCF / PSJF** | Yes | Yes | Turnaround time (general case) | Poor response time; can starve long jobs | Emergency room triage, constantly re-prioritized |
| **Round Robin** | Yes | No | Response time / fairness | Poor turnaround time; overhead if slice too short | Everyone gets a short turn, repeated |

---

## 14. Key Formulas Recap

| Metric | Formula | Meaning |
|---|---|---|
| **Turnaround Time** | `T_turnaround = T_completion − T_arrival` | Total time in system |
| **Response Time** | `T_response = T_firstrun − T_arrival` | Time until first scheduled |

**Averages** are simply computed by summing the metric across all jobs and dividing by the number of jobs:
$$
\text{Average} = \frac{\sum_{i=1}^{N} T_i}{N}
$$

---

## 15. Worked Homework Questions & Solutions

> These correspond to the review questions at the end of the chapter. Worked reasoning (not just numeric answers) is provided below to build intuition; exact simulator-verified numbers may vary slightly depending on tie-breaking rules used by `scheduler.py`.

### Q1. Three jobs of length 200 each, using SJF and FIFO.
- Since all jobs are **equal length**, SJF and FIFO behave **identically** (no reordering benefit possible — there's no "shorter" job to prioritize).
- Assuming arrival order A, B, C, each length 200:
    - Completion times: 200, 400, 600 → **Average turnaround = 400**
    - Response times: 0, 200, 400 → **Average response = 200**

### Q2. Same, but jobs of length 100, 200, 300.
- **FIFO** (in arrival order, say 100 → 200 → 300):
    - Completion: 100, 300, 600 → **Average turnaround = (100+300+600)/3 = 333.33**
    - Response: 0, 100, 300 → **Average response = 133.33**
- **SJF** (reorders to shortest-first: 100 → 200 → 300, which happens to match arrival order here):
    - Same result as FIFO in this specific case: **Average turnaround = 333.33**, **Average response = 133.33**
    - *(Note: SJF only differs from FIFO when arrival order does NOT already match length order.)*

### Q3. Same, but also with RR, time-slice = 1.
- With RR and a tiny 1-unit time slice, jobs of length 100, 200, 300 get interleaved almost perfectly evenly.
- **Response time** improves dramatically — each job gets its first turn almost immediately (within the first few time units), so average response time approaches **~1–2** (near-zero wait for first scheduling).
- **Turnaround time** gets *much worse* than FIFO/SJF, because every job's completion is stretched out by constant interleaving — expect average turnaround to approach the **sum of all job lengths** territory (since the last job to finish, of length 300, effectively can't complete until nearly the full combined runtime, and even the length-100 job is dragged out because it only gets 1 unit of work at a time before yielding to the others).

### Q4. For what workloads does SJF deliver the same turnaround time as FIFO?
- **When jobs are already equal length**, or
- **When jobs happen to arrive in the exact order of shortest-to-longest** (so FIFO's arrival order coincidentally matches SJF's length-based order).
- In both cases, SJF has **no reordering to do**, so it produces identical results to FIFO.

### Q5. For what workloads and quantum lengths does SJF deliver the same response time as RR?
- If there is only **a single job**, response time is trivially 0 for both, regardless of scheduler or quantum.
- More generally, if the **RR quantum is set very large** — larger than or equal to the length of the longest job — RR effectively degenerates into running jobs to completion one at a time, in arrival order, which is exactly SJF/FIFO's behavior (assuming SJF has already reordered by length and RR happens to process them in that same order). In that special case, both would show the same response times.
- In the general, more realistic case, **RR usually achieves better (lower) response time** than SJF, because RR gives every job an early first turn, while SJF/STCF can force some jobs to wait through several others' full run first.

### Q6. What happens to response time with SJF as job lengths increase?
- As job lengths increase, **response time increases roughly proportionally for the later-scheduled jobs**, since each job in the SJF ordering must wait for **all shorter jobs ahead of it to run to completion** before it gets a first turn.
- Concretely: the **last job in the SJF order (typically the longest)** experiences the **worst response time**, roughly equal to the sum of all jobs scheduled ahead of it.
- Using the OSTEP simulator (`scheduler.py`), one could vary the length of a specific job and observe this near-linear growth in its response time as its position in the queue and/or its own length grows.

### Q7. What happens to response time with RR as quantum length increases? Worst-case formula?
- As the **RR quantum length increases**, response time for later jobs in the queue also **increases**, because each job ahead in the rotation "holds" the CPU longer before yielding — delaying when a later job gets its very first turn.
- **Worst-case response time formula:** For **N** jobs in the RR queue, each getting a quantum of length **Q**, the **last job (Nth in rotation)** must wait for **all N−1 other jobs** to take their first turn before it gets scheduled:

$$
T_{response}^{worst} = (N - 1) \times Q
$$

- Example check: with N=3 jobs and Q=1 (from Section 8.2's example), worst-case response time = (3−1) × 1 = 2 — matching job C's response time of 2 in that example. ✅

---

## 16. Annotated Reference List

| Citation | Work | Relevance |
|---|---|---|
| **[B+79]** | "The Convoy Phenomenon" — Blasgen, Gray, Mitoma, Price (1979) | Earliest known reference to the convoy effect, originally observed in database systems, later applied to OS scheduling. |
| **[C54]** | "Priority Assignment in Waiting Line Problems" — A. Cobham (1954) | Pioneering operations-research paper applying an SJF-style approach to scheduling machine repairs. |
| **[K64]** | "Analysis of a Time-Shared Processor" — Leonard Kleinrock (1964) | Possibly the first formal reference to and analysis of Round-Robin scheduling for time-shared systems. |
| **[CK68]** | "Computer Scheduling Methods and their Countermeasures" — Coffman & Kleinrock (1968) | Foundational early survey/analysis of multiple basic scheduling disciplines. |
| **[J91]** | "The Art of Computer Systems Performance Analysis" — R. Jain (1991) | The standard reference text for performance measurement techniques, including Jain's Fairness Index. |
| **[O45]** | "Animal Farm" — George Orwell (1945) | Referenced humorously ("some assumptions are more unrealistic than others," echoing "some animals are more equal than others"); a political allegory, not a technical work. |
| **[PV56]** | "Machine Repair as a Priority Waiting-Line Problem" — Phipps & Van Voorhis (1956) | Follow-on operations-research work generalizing SJF to STCF-style approaches in machine repair contexts. |
| **[MB91]** | "The effect of context switches on cache performance" — Mogul & Borg (1991) | Empirical study of how context switching disrupts CPU cache state, informing the discussion on context-switch overhead. |
| **[W15]** | "You can't have your cake and eat it" — Wikipedia (2015) | Cited humorously regarding the idiom used to describe the turnaround/response trade-off; notes equivalent idioms across languages (e.g., Tamil: "can't have both the moustache and drink the soup"). |

---

## 17. Big-Picture Takeaways

1. **No single scheduler is universally "best"** — every policy makes trade-offs depending on which metric you prioritize.
2. **Turnaround time favors running short/known-length jobs to completion** (SJF/STCF); **response time favors frequent, fair interleaving** (RR).
3. **Preemption** (the ability to interrupt a running job) is essential for handling realistic workloads where jobs arrive at unpredictable times.
4. **I/O changes everything** — treating CPU bursts (not entire jobs) as the scheduling unit allows CPU-bound and I/O-bound jobs to coexist efficiently, improving overall utilization via overlap.
5. **The biggest open problem** after this chapter is that real schedulers **don't know job lengths in advance** — solved (partially) by the upcoming **Multi-Level Feedback Queue**, which learns from a process's recent behavior instead of requiring foreknowledge.