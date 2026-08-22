# Hard Disk Drives (HDD)

> Based on OSTEP Chapter 37 — *Operating Systems: Three Easy Pieces* (Arpaci-Dusseau)

Hard disk drives have been the main form of **persistent data storage** for decades, and much of file system design is built around their behavior. Understanding disk operation is essential before building file system software.

**CRUX:** How do drives store data? What is the interface? How is data laid out and accessed? How does disk scheduling improve performance?

---

## 1. The Interface

- A drive consists of a large number of **sectors** (512-byte blocks), each of which can be read or written.
- Sectors are numbered `0` to `n − 1` → the disk is viewed as an **array of sectors**; `0` to `n − 1` is the drive's **address space**.
- **Multi-sector operations** are possible (file systems often read/write 4KB or more at a time).
- **Atomicity guarantee:** manufacturers only guarantee that a single 512-byte write is atomic. If power is lost mid-write, only part of a larger write may complete — a **torn write**.

### The "Unwritten Contract" (Schlosser & Ganger)

Assumptions clients make that aren't in the interface spec:

1. Accessing two blocks **near each other** in the address space is faster than blocks far apart.
2. **Sequential access** (contiguous chunks) is the fastest access mode — much faster than random access.

*Note: "block" and "sector" are often used interchangeably.*

---

## 2. Basic Geometry

| Component | Description |
|---|---|
| **Platter** | Circular hard surface (e.g., aluminum) coated with a thin magnetic layer; stores data persistently via magnetic changes, even when powered off |
| **Surface** | Each platter has 2 sides; each side is a surface |
| **Spindle** | Binds platters together; connected to a motor spinning them at a constant rate |
| **RPM** | Rotation rate: typically **7,200–15,000 RPM**. At 10,000 RPM, one rotation ≈ 6 ms |
| **Track** | A concentric circle of sectors on a surface; thousands per surface (hundreds fit in the width of a human hair) |
| **Disk head** | Reads (senses) or writes (induces changes in) magnetic patterns; **one head per surface** |
| **Disk arm** | Moves across the surface to position the head over the desired track |

---

## 3. A Simple Disk Drive

Built up one track at a time (example: single track, 12 sectors of 512 bytes, addressed 0–11, rotating counter-clockwise).

### 3.1 Rotational Delay (single track)

- To read a sector, the disk **waits for it to rotate under the head**.
- This wait is the **rotational delay** (rotation delay).
- If full rotation time is `R`: reading sector 0 when starting at sector 6 costs about `R/2`; the worst case (e.g., sector 5) costs nearly a **full rotation**.

### 3.2 Seek Time (multiple tracks)

- Real disks have millions of tracks. Accessing a sector on a different track requires moving the arm — a **seek**.
- Seeks and rotations are among the **most costly** disk operations.
- **Phases of a seek:**
    1. **Acceleration** — arm starts moving
    2. **Coasting** — arm moves at full speed
    3. **Deceleration** — arm slows down
    4. **Settling** — head is carefully positioned over the correct track (often significant: **0.5–2 ms**)
- During a seek, the platter keeps rotating, so after arrival only a short rotational delay may remain.

### 3.3 Transfer

The final phase of I/O: data is actually read from or written to the surface.

**Complete I/O picture: seek → rotational delay → transfer.**

### 3.4 Other Details

- **Track skew:** sectors on adjacent tracks are offset (e.g., by 2 blocks) so sequential reads crossing track boundaries don't miss the next block while the head repositions. Without skew, the drive would wait nearly a full rotation after each track switch.
- **Multi-zoned disks:** outer tracks have more sectors than inner tracks (more physical room). The disk is organized into **zones** — consecutive sets of tracks with the same sectors-per-track; outer zones hold more sectors.
- **Cache (track buffer):** small memory (~8–16 MB) holding data read from / written to disk. E.g., on a read, the drive may cache the whole track to serve later requests quickly.
    - **Write-back caching** (immediate reporting): acknowledge the write once data is in cache memory. Appears faster, but **dangerous** — can break ordering requirements needed for correctness (see file-system journaling).
    - **Write-through:** acknowledge only after data is actually on disk.

---

## 4. Dimensional Analysis (Aside)

Setting up units so they cancel — useful for I/O math.

**Rotation time from RPM (10K RPM disk):**

```
Time (ms)/rotation = (1 min / 10,000 rot) × (60 s / 1 min) × (1000 ms / 1 s)
                   = 60,000 ms / 10,000 rot = 6 ms/rotation
```

**Transfer time (512 KB at 100 MB/s):**

```
(512 KB / 1 req) × (1 MB / 1024 KB) × (1 s / 100 MB) × (1000 ms / 1 s) = 5 ms
```

---

## 5. I/O Time: Doing the Math

**I/O time:**

```
T(I/O) = T(seek) + T(rotation) + T(transfer)
```

**I/O rate** (useful for comparing drives):

```
R(I/O) = Size(transfer) / T(I/O)
```

### Two workloads

- **Random workload:** small reads (e.g., 4KB) to random locations (common in databases).
- **Sequential workload:** many consecutive sectors, no jumping around.

### Example drives (Seagate)

| Spec | Cheetah 15K.5 (performance, SCSI) | Barracuda (capacity, SATA) |
|---|---|---|
| Capacity | 300 GB | 1 TB |
| RPM | 15,000 | 7,200 |
| Average seek | 4 ms | 9 ms |
| Max transfer | 125 MB/s | 105 MB/s |
| Platters | 4 | 4 |
| Cache | 16 MB | 16/32 MB |

### Random workload (4KB read), Cheetah:

- `T(seek)` = 4 ms (manufacturer average; a full end-to-end seek is 2–3× longer)
- `T(rotation)` = 2 ms (15,000 RPM = 250 rot/s → 4 ms/rotation → average half rotation = 2 ms)
- `T(transfer)` = 30 μs (4KB ÷ 125 MB/s — vanishingly small)
- `T(I/O)` ≈ 6 ms → **R(I/O) ≈ 0.66 MB/s**
- Barracuda: `T(I/O)` ≈ 13.2 ms → **R(I/O) ≈ 0.31 MB/s**

### Sequential workload (100 MB transfer):

- One seek + one rotation, then a long transfer.
- Cheetah: ≈ 800 ms → **≈ 125 MB/s**; Barracuda: ≈ 950 ms → **≈ 105 MB/s** (near peak rates)

### Results summary

| | Cheetah | Barracuda |
|---|---|---|
| R(I/O) Random | 0.66 MB/s | 0.31 MB/s |
| R(I/O) Sequential | 125 MB/s | 105 MB/s |

**Key takeaways:**

1. **Huge gap between random and sequential performance** — ~200× (Cheetah) to 300×+ (Barracuda).
2. Big difference between high-end "performance" drives and low-end "capacity" drives (hence different pricing).

> **TIP — Use disks sequentially.** Transfer data sequentially whenever possible; if not, use large chunks (the bigger the better). Small random I/Os make performance suffer dramatically.

### Aside: Why "average seek ≈ ⅓ of full seek"

- Based on average seek **distance**, not time. For tracks 0..N, average over all pairs of `|x − y|`:
- Sum via integral: ∫∫ |x−y| dy dx = N³/3; divide by N² possible seeks → average distance = **N/3**, i.e., one-third the full distance.

---

## 6. Disk Scheduling

Because I/O is so expensive, the **disk scheduler** decides which pending request to service next. Unlike job scheduling, the length of a disk request can be estimated well (seek + rotation), so the scheduler can approximate **SJF (shortest job first)** by greedily picking the fastest request.

### 6.1 SSTF: Shortest Seek Time First

- Orders the queue by track; picks requests on the **nearest track** first.
- Example: head on inner track, requests at sector 21 (middle) and 2 (outer) → service 21 first, then 2.
- **Problems:**
    1. **Drive geometry unavailable to the OS** (it sees an array of blocks) → fix: **NBF (nearest-block-first)**, scheduling by nearest block address.
    2. **Starvation** (fundamental): a steady stream of requests to the current track starves requests to other tracks.

**CRUX:** How to implement SSTF-like scheduling but avoid starvation?

### 6.2 Elevator (SCAN / C-SCAN)

- **SCAN:** moves back and forth across the disk, servicing requests in order across tracks. One pass (outer→inner or inner→outer) is a **sweep**. Requests for already-passed tracks wait for the next sweep.
- **F-SCAN:** freezes the queue during a sweep; requests arriving mid-sweep are queued for later → avoids starving far-away requests.
- **C-SCAN (Circular SCAN):** sweeps only outer→inner, then resets to the outer track. Fairer than pure back-and-forth SCAN, which favors middle tracks (they're passed twice per round trip).
- Called the **elevator algorithm** — like an elevator committed to a direction, not jumping to the "closest" floor. In disks, it prevents starvation.
- **Limitation:** SCAN (and SSTF) **ignore rotation**, so they don't fully adhere to SJF.

**CRUX:** How to account for rotation costs too?

### 6.3 SPTF: Shortest Positioning Time First

(a.k.a. SATF — shortest access time first)

- Considers **both seek and rotation**.
- Example: head over sector 30 (inner track); choose sector 16 (middle track, short seek but nearly full rotation) vs. sector 8 (outer track, longer seek but less rotation)?
    - **"It depends"** on the relative cost of seek vs. rotation:
        - Seek ≫ rotation → SSTF-style choices are fine.
        - Seek fast relative to rotation → better to seek further (sector 8) than wait for the long rotation (sector 16).
- On modern drives, seek and rotation costs are **roughly equivalent**, so SPTF helps.
- Hard for the OS to implement (doesn't know track boundaries or head position) → **SPTF is usually done inside the drive**.

### 6.4 Other Scheduling Issues

- **Where is scheduling done?** Older systems: entirely in the OS. Modern systems: disks accept **multiple outstanding requests** and have sophisticated internal schedulers (accurate SPTF, since the controller knows exact head position and track layout). The OS picks its best few requests (e.g., 16) and issues them; the disk services them in the best order.
- **I/O merging:** e.g., requests for blocks 33, 8, 34 → merge 33+34 into one two-block request, then reorder. Especially important at the OS level — fewer requests sent to disk means lower overhead.
- **Work-conserving vs. non-work-conserving:**
    - *Work-conserving:* issue any pending request immediately; disk never idle if work exists.
    - *Non-work-conserving (anticipatory scheduling):* sometimes **waiting briefly** lets a "better" (nearer) request arrive, improving overall efficiency. Deciding when/how long to wait is tricky (see Iyer & Druschel [ID01]; implemented in Linux).

---

## 7. Summary

The chapter presents a detailed **functional model** of disk operation: sectors and address spaces, platters/tracks/heads, rotational delay, seek, and transfer; the massive random-vs-sequential performance gap; and scheduling (SSTF → SCAN/elevator → SPTF, plus merging and anticipatory scheduling). This model underpins file system design on top of disks.

---

## Key Terms

**sector · torn write · unwritten contract · platter · surface · spindle · RPM · track · disk head · disk arm · rotational delay · seek (accelerate/coast/decelerate/settle) · transfer · track skew · multi-zoned disk · track buffer/cache · write-back vs write-through · T(I/O) = seek + rotation + transfer · SSTF · NBF · starvation · SCAN / F-SCAN / C-SCAN (elevator) · SPTF/SATF · I/O merging · work-conserving vs anticipatory scheduling**

## Key References

- [RW92] Ruemmler & Wilkes — *An Introduction to Disk Drive Modeling*
- [ADR03] Anderson, Dykes, Riedel — *More Than an Interface: SCSI vs. ATA*
- [SG04] Schlosser & Ganger — the "unwritten contract" of disk drives
- [CKR72] Coffman, Klimko, Ryan — early disk scheduling (SCAN, F-SCAN)
- [ID01] Iyer & Druschel — anticipatory (non-work-conserving) scheduling
- [SCO90] Seltzer, Chen, Ousterhout — rotation-aware scheduling