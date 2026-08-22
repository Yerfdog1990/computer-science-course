# Redundant Arrays of Inexpensive Disks (RAID)

> Based on OSTEP Chapter 38 — *Operating Systems: Three Easy Pieces* (Arpaci-Dusseau)

## Motivation

When using a disk we sometimes wish it were **faster** (I/O is a bottleneck), **larger** (data keeps growing), and **more reliable** (a failed, unbacked-up disk means data loss).

**CRUX:** How can we make a large, fast, and reliable storage system? What are the key techniques and trade-offs?

**RAID** (Redundant Array of Inexpensive Disks, Patterson/Gibson/Katz, UC Berkeley, late 1980s) uses multiple disks in concert to build a faster, bigger, more reliable disk system.

### Advantages over a single disk

1. **Performance** — multiple disks in parallel speed up I/O.
2. **Capacity** — large data sets need large disks.
3. **Reliability** — with redundancy, a RAID can tolerate the loss of a disk and keep operating.

### Transparency enables deployment (TIP)

A RAID **looks like a big disk** to the host — a linear array of blocks. Admins could swap a SCSI disk for a SCSI RAID array with **no changes** to OS or applications. Transparency solved the deployment problem and made RAID successful from day one.

---

## 38.1 Interface and RAID Internals

- To the file system, a RAID is a linear array of blocks that can be read or written.
- On a logical I/O, the RAID computes which disk(s) to access and issues one or more **physical I/Os** (e.g., a mirrored RAID does 2 physical writes per logical write).
- A **hardware RAID** is like a specialized computer: microcontroller + firmware, volatile DRAM to buffer blocks, sometimes **non-volatile memory** to buffer writes safely, and sometimes specialized parity logic. Connects via standard interface (SCSI, SATA).

## 38.2 Fault Model

- **Fail-stop model:** a disk is either **working** (all blocks readable/writable) or **failed** (permanently lost).
- Failure is assumed **easily detected** by the controller.
- Ignored for now: **silent failures** (disk corruption) and **latent sector errors** (single bad block on a working disk) — covered later in the data-integrity chapter.

## 38.3 How to Evaluate a RAID

Three axes, given `N` disks of `B` blocks each:

1. **Capacity** — useful capacity to clients (no redundancy: `N·B`; mirroring: `N·B/2`; parity: in between).
2. **Reliability** — how many disk failures tolerated.
3. **Performance** — depends heavily on workload.

### Performance metrics and workloads

- **Single-request latency** — reveals parallelism within one logical I/O.
- **Steady-state throughput** — total bandwidth of many concurrent requests (the main focus).
- **Sequential workload:** large contiguous requests → disk spends most time transferring → rate **S** MB/s.
- **Random workload:** small requests at random locations (e.g., DBMS transactions) → most time seeking/rotating → rate **R** MB/s. Generally **S ≫ R**.

**Example computing S and R** (seek 7 ms, rotation 3 ms, transfer 50 MB/s):

- Sequential 10 MB: 7 + 3 + 200 ms transfer = 210 ms → `S = 10 MB / 210 ms ≈ 47.62 MB/s` (near peak; positioning amortized)
- Random 10 KB: 7 + 3 + 0.195 ms = 10.195 ms → `R = 10 KB / 10.195 ms ≈ 0.981 MB/s`
- `S/R ≈ 50`

---

## 38.4 RAID Level 0: Striping

No redundancy — an **upper bound** on performance and capacity.

Blocks spread **round-robin** across disks; blocks in the same row form a **stripe**:

```
Disk0  Disk1  Disk2  Disk3
  0      1      2      3     ← stripe
  4      5      6      7
  8      9     10     11
 12     13     14     15
```

### Chunk size

- May place multiple blocks per disk before moving on (e.g., chunk = 2 blocks = 8KB → stripe = 32KB).
- **Small chunks:** files striped across many disks → more intra-file parallelism, but positioning time = **max** across all drives.
- **Large chunks:** less intra-file parallelism (relies on concurrent requests), but lower positioning time (a small file may sit on one disk).
- Best chunk size depends on workload; analysis here assumes chunk = 1 block (4KB). Most real arrays use larger (e.g., 64KB).

### The mapping problem (aside)

Given logical block `A` (chunk size = 1 block):

```
Disk   = A % number_of_disks
Offset = A / number_of_disks     (integer division)
```

E.g., block 14 with 4 disks: disk `14 % 4 = 2`, offset `14 / 4 = 3`.

### RAID-0 analysis

- **Capacity:** perfect — `N·B`.
- **Reliability:** perfect in the bad way — **any** disk failure loses data.
- **Performance:** excellent. Single-block latency ≈ single disk. Sequential throughput = `N·S`; random throughput = `N·R` — the upper bounds for all levels.

---

## 38.5 RAID Level 1: Mirroring

Keep **two copies** of each block, on separate disks:

```
Disk0  Disk1  Disk2  Disk3
  0      0      1      1
  2      2      3      3
  ...
```

- This layout (stripe of mirrored pairs) = **RAID-10** (1+0). Alternative: **RAID-01** (0+1), a mirror of two striped arrays.
- **Reads:** either copy may be read. **Writes:** both copies must be written (can proceed in parallel).

### The consistent-update problem (aside)

A write to multiple disks may be interrupted (power loss/crash) after updating one disk but not the other → copies **inconsistent**. Solution: a **write-ahead log** recording what the RAID is about to do, replayed on recovery. Since logging to disk per write is too costly, hardware RAIDs use a small **non-volatile (battery-backed) RAM** for this logging.

### RAID-1 analysis

- **Capacity:** expensive — `N·B/2` (mirroring level 2).
- **Reliability:** tolerates **1 failure for certain**; up to `N/2` if lucky (one failure per mirror pair). In practice, count on 1.
- **Performance:**
    - Read latency = single disk. Write latency ≈ single disk but slightly higher (must wait for **both** writes → worst-case seek/rotation of the two).
    - **Sequential write:** `(N/2)·S` — every logical write = 2 physical writes.
    - **Sequential read:** also `(N/2)·S` — each disk ends up receiving requests for every *other* block, rotating uselessly over skipped blocks, delivering only half its peak bandwidth.
    - **Random read (best case):** `N·R` — reads distribute across all disks.
    - **Random write:** `(N/2)·R` — two physical writes per logical write; still fairly good.

---

## 38.6 RAID Level 4: Saving Space With Parity

Uses **parity** to reduce the capacity penalty of mirroring — at a cost in performance. One **parity block per stripe**, on a dedicated parity disk:

```
Disk0  Disk1  Disk2  Disk3  Disk4
  0      1      2      3     P0
  4      5      6      7     P1
  8      9     10     11     P2
 12     13     14     15     P3
```

### Parity via XOR

- `XOR` of a set of bits = 0 if an even number of 1s, 1 if odd.
- **Invariant:** the number of 1s in any row (including parity) must be **even**.
- **Reconstruction:** if any one block in a stripe is lost, XOR the surviving data blocks with the parity block to recover it.
- For blocks (not bits): perform **bitwise XOR** across the data blocks; each result bit goes into the corresponding bit of the parity block.

### RAID-4 analysis

- **Capacity:** `(N−1)·B` (one disk devoted to parity).
- **Reliability:** tolerates exactly **1** disk failure; more is unrecoverable.
- **Sequential read:** `(N−1)·S` (all data disks).
- **Sequential write:** `(N−1)·S` via **full-stripe writes** — compute new parity by XORing the whole stripe, then write data + parity to all disks in parallel (the most efficient RAID-4 write).
- **Random read:** `(N−1)·R` (spread across data disks).

### Random writes: the small-write problem

Overwriting one block requires updating its parity too. Two methods:

1. **Additive parity:** read all *other* data blocks in the stripe, XOR with the new block → new parity. Scales badly with number of disks.
2. **Subtractive parity:** read old data and old parity, then:

   ```
   P_new = (C_old ⊕ C_new) ⊕ P_old
   ```

   Costs **4 physical I/Os** per logical write (read old data + old parity, write new data + new parity). (Cross-over point between methods depends on N — worth working out.)

- **The bottleneck:** every write must read+write the **parity disk**, so concurrent small writes to different data disks are **serialized** by the parity disk. This is the **small-write problem**.
- Parity disk does 2 I/Os per logical write → random-write throughput = **`R/2`** — terrible, and it does **not improve with more disks**.

**Latency:** read = single disk (T). Write = 2 reads + 2 writes (each pair parallel) ≈ **2T**.

---

## 38.7 RAID Level 5: Rotating Parity

Same as RAID-4 but **parity blocks rotate** across all disks, removing the parity-disk bottleneck:

```
Disk0  Disk1  Disk2  Disk3  Disk4
  0      1      2      3     P0
  5      6      7     P1      4
 10     11     P2      8      9
 15     P3     12     13     14
 P4     16     17     18     19
```

### RAID-5 analysis

- **Capacity, reliability, sequential read/write, latency:** identical to RAID-4.
- **Random read:** slightly better — `N·R` (all disks usable, including those holding parity for other stripes).
- **Random write:** much better — writes to different stripes hit different parity disks and proceed **in parallel**. With many random requests, all disks stay evenly busy → **`(N/4)·R`**. The factor 4 = the 4 I/Os each parity-based write still costs.
- RAID-5 has almost entirely **replaced RAID-4** in the market — except in systems doing only large (full-stripe) writes, where RAID-4's simplicity wins (e.g., NetApp WAFL [HLM94]).

---

## 38.8 RAID Comparison Summary

| | RAID-0 | RAID-1 | RAID-4 | RAID-5 |
|---|---|---|---|---|
| Capacity | N·B | N·B/2 | (N−1)·B | (N−1)·B |
| Reliability | 0 | 1 (sure), N/2 (lucky) | 1 | 1 |
| Sequential read | N·S | (N/2)·S | (N−1)·S | (N−1)·S |
| Sequential write | N·S | (N/2)·S | (N−1)·S | (N−1)·S |
| Random read | N·R | N·R | (N−1)·R | N·R |
| Random write | N·R | (N/2)·R | R/2 | (N/4)·R |
| Read latency | T | T | T | T |
| Write latency | T | T | 2T | 2T |

*(T = time of a single-disk request. Simplifications: mirrored writes actually take the max of two seeks; the mirrored sequential ½-penalty assumes a naive read pattern; RAID-4/5 parity updates mix a full seek+rotation read with a rotation-only write.)*

**Choosing a level:**

- Pure performance, no reliability → **RAID-0** (striping).
- Random I/O performance + reliability → **RAID-1** (mirroring); cost = capacity.
- Capacity + reliability → **RAID-5**; cost = small-write performance.
- Mostly sequential I/O + max capacity → **RAID-5** also best.

---

## 38.9 Other RAID Issues

- Other levels: **RAID-2, RAID-3** (original taxonomy), **RAID-6** (tolerates two disk failures, e.g., row-diagonal parity).
- **Hot spares** — a standby disk fills in for a failed one; performance during failure and **reconstruction** matters.
- More realistic fault models: **latent sector errors**, **block corruption** (see data-integrity chapter).
- **Software RAID** — cheaper (no special hardware) but faces the consistent-update problem without NVRAM.

## 38.10 Summary

RAID turns independent disks into a larger, faster, more reliable single entity — **transparently**, so hardware/software above needn't change. The right level depends on user priorities: mirroring is simple, reliable, and fast but capacity-costly; RAID-5 is capacity-efficient and reliable but poor at small writes. Picking a level and its parameters (chunk size, disk count) for a workload remains more art than science.

---

## Key Terms

**RAID · transparency · fail-stop fault model · latent sector error · capacity/reliability/performance axes · S and R (sequential vs random bandwidth) · striping (RAID-0) · stripe · chunk size · mapping problem · mirroring (RAID-1) · RAID-10 vs RAID-01 · consistent-update problem · write-ahead log / NVRAM · parity (RAID-4) · XOR invariant · full-stripe write · additive vs subtractive parity · small-write problem · rotating parity (RAID-5) · RAID-6 · hot spare · software RAID**

## Key References

- [P+88] Patterson, Gibson, Katz — *Redundant Arrays of Inexpensive Disks* (the RAID paper, SIGMOD 1988)
- [S84] Schneider — fail-stop processors
- [CL95] Chen & Lee — striping parameters in RAID-5
- [DAA05] Denehy et al. — consistent-update problem in software RAID
- [C+04] Corbett et al. — row-diagonal parity (double-failure correction)
- [HLM94] Hitz, Lau, Malcolm — WAFL / NetApp (RAID-4 with only large writes)
- [B+08] Bairavasundaram et al. — data corruption in the storage stack