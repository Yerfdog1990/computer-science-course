# Disk Scheduling Algorithms

## Overview

Disk scheduling (also called **I/O scheduling**) determines the order in which pending I/O requests are serviced by the disk. By optimizing the path of the read/write head, these algorithms minimize seek time and improve throughput.

**Why it matters:**

- Multiple processes issue I/O requests, but the disk controller serves **one at a time** — others wait in a queue and must be scheduled.
- Requests far apart on disk cause **large disk-arm movement** if served naively.
- Hard drives are among the **slowest** components of a system, so they must be accessed efficiently.

## Key Terms

- **Seek time** — time to move the disk arm to the required track (the main quantity schedulers minimize).
- **Rotational latency** — time for the desired sector to rotate under the head.
- **Transfer time** — time to actually move the data (depends on rotation speed and bytes transferred).

```
Disk Access Time = Seek Time + Rotational Latency + Transfer Time
Total Seek Time  = Total Head Movement × Seek Time (per track)
```

- **Response time** — how long a request waits before service; consider **average** and **variance** (fairness).

**Goals:** minimize seek time · maximize throughput · minimize latency · ensure fairness (no starvation) · use resources efficiently.

---

## Worked Example Setup

Requests: **82, 170, 43, 140, 24, 16, 190** · Initial head: **50** · Disk tracks: **0–199** · Direction (where relevant): toward larger values.

---

## 1. FCFS (First-Come, First-Served)

Service requests **strictly in arrival order**.

- **Seek sequence:** 82, 170, 43, 140, 24, 16, 190
- **Total movement:** (82−50)+(170−82)+(170−43)+(140−43)+(140−24)+(24−16)+(190−16) = **642**

**Advantages:** every request gets a fair chance; no indefinite postponement (no starvation); trivially simple.

**Disadvantages:** makes no attempt to optimize seek time — generally the **worst** total movement; poor throughput.

## 2. SSTF (Shortest Seek Time First)

Always service the pending request **closest to the current head position**.

- **Seek sequence:** 43, 24, 16, 82, 140, 170, 190
- **Total movement:** 7+19+8+66+58+30+20 = **208**

**Advantages:** average response time decreases; throughput increases — a big improvement over FCFS.

**Disadvantages:** overhead of computing distances in advance; **starvation** possible for far-away requests if closer ones keep arriving; **high variance** of response time (favors some requests).

## 3. SCAN (Elevator Algorithm)

The arm moves in one direction to the **end of the disk**, servicing requests along the way, then reverses — like an elevator.

- **Seek sequence:** 82, 140, 170, 190, **199**, 43, 24, 16
- **Total movement:** (199−50) + (199−16) = 149 + 183 = **332**

**Advantages:** high throughput; low variance of response time; decent average response time; no starvation.

**Disadvantages:** the head may travel to the disk end even when no requests are there; **long wait for locations just visited** (a track just passed waits nearly a full sweep); mid-range tracks are favored over the edges.

## 4. C-SCAN (Circular SCAN)

Like SCAN, but services requests in **one direction only**; on reaching the end, the head **jumps back to the start** (servicing nothing on the return) and sweeps again. Treats the cylinders as a circular list.

- **Seek sequence:** 82, 140, 170, 190, **199**, (jump to **0**), 16, 24, 43
- **Total movement:** (199−50) + (199−0) + (43−0) = 149 + 199 + 43 = **391** (some texts exclude the return jump, giving 192)

**Advantages:** **more uniform wait times** than SCAN (edges no longer disadvantaged); eliminates starvation; good under heavy load.

**Disadvantages:** extra head movement for the return trip; higher total seek than SCAN under light load.

## 5. LOOK

SCAN, but the arm goes only as far as the **last pending request** in each direction (it "looks" ahead), then reverses — no wasted travel to the physical disk end.

- **Seek sequence:** 82, 140, 170, **190**, 43, 24, 16
- **Total movement:** (190−50) + (190−16) = 140 + 174 = **314**

**Advantages:** avoids unnecessary traversal to the disk end → less movement, faster response than SCAN.

**Disadvantages:** waiting time can still be high for some requests; performance depends on request distribution; not perfectly fair to new arrivals.

## 6. C-LOOK

C-SCAN's circular idea + LOOK's optimization: sweep only to the **last request** in the direction of travel, then jump to the **farthest request on the other side** (not the disk end) and continue.

- **Seek sequence:** 82, 140, 170, **190**, (jump to **16**), 24, 43
- **Total movement:** (190−50) + (190−16) + (43−16) = 140 + 174 + 27 = **341** (156 if the jump isn't counted)

**Advantages:** uniform, predictable wait times; less head movement than C-SCAN; avoids starvation.

**Disadvantages:** the circular jump still costs movement; under light load, plain LOOK does better; new requests may wait a full cycle.

---

## Other Scheduling Policies

- **RSS (Random Scheduling):** picks a pending request at random. Used mainly for **simulation/analysis benchmarks** and modeling stochastic systems — not efficient in practice.
- **LIFO (Last-In, First-Out):** newest request first. Can maximize locality for the most recent process, but is unfair and can **starve** older requests.
- **N-STEP SCAN:** requests are batched into groups of size **N**; each batch is serviced completely (with SCAN) before the next batch — new arrivals go into the next batch. **Completely eliminates starvation** and prevents arm stickiness.
- **F-SCAN:** two sub-queues. The current queue is serviced with SCAN while **all new arrivals go into the second queue**, which is serviced on the next sweep. Guarantees service to existing requests and prevents **arm stickiness** (head lingering in one hot area).

---

## Comparison

| Algorithm | Total movement (example) | Starvation? | Variance | Notes |
|---|---|---|---|---|
| FCFS | 642 | No | High | Fair but slow |
| SSTF | 208 | **Possible** | High | Greedy; best local choice |
| SCAN | 332 | No | Low | Elevator; goes to disk end |
| C-SCAN | 391 | No | Lowest | Uniform waits; one-direction service |
| LOOK | 314 | No | Low | SCAN without wasted travel |
| C-LOOK | 341 | No | Lowest | C-SCAN without wasted travel |

**Choosing:** SSTF gives the lowest raw seek totals but risks starvation and unfairness; SCAN/LOOK balance throughput and fairness; C-SCAN/C-LOOK give the most **uniform** waiting times and shine under heavy load. (Rough performance hierarchy: FCFS → SSTF → SCAN → C-SCAN → LOOK/C-LOOK.)

**Note (from OSTEP context):** these classic algorithms consider only **seek** distance. Modern drives also account for **rotation** (SPTF — shortest positioning time first), which is implemented inside the disk controller where exact head position is known.

## Key Terms

**disk/I-O scheduling · seek time · rotational latency · transfer time · disk access time · response time (average, variance) · FCFS · SSTF · SCAN (elevator) · C-SCAN (circular) · LOOK · C-LOOK · RSS · LIFO · N-STEP SCAN · F-SCAN · starvation · arm stickiness**