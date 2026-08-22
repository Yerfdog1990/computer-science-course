# File Allocation Methods

## Overview

File allocation methods determine how the OS maps **logical files to physical disk blocks**. The goals: **efficient disk space utilization**, **fast read/write access** (sequential and random), and **minimal fragmentation**. The three primary techniques are **contiguous**, **linked**, and **indexed** allocation; modern file systems combine and extend them.

---

## 1. Contiguous Allocation

Each file occupies a **continuous sequence of blocks**. The directory entry stores only the **starting block address** and **length** (number of blocks).

- A file of `n` blocks starting at block `b` occupies `b, b+1, b+2, ..., b+n−1`.
- **Direct access is trivial:** the kth block of a file starting at `b` is at `b + k` (or `start + (k−1)` counting from 1). E.g., file A starts at block 9 → its 3rd block = 9 + 2 = **11**.

**Advantages**

- Simple to implement (just two numbers per file).
- Supports both **sequential and direct access**.
- Excellent performance — minimal seeks since blocks are adjacent; a whole file can be read in essentially one disk operation.

**Disadvantages**

- **External fragmentation** — free space becomes scattered between files, making it hard to place new large files (also some internal fragmentation in the last block).
- **File growth is difficult** — there may be no free contiguous space adjacent to the file when it needs to expand; may require finding a larger hole and copying.

---

## 2. Linked Allocation

Each file is a **linked list of disk blocks** scattered anywhere on disk. Each block holds data plus a **pointer to the next block**; the directory entry stores pointers to the **first (and last)** block. The last block holds a null pointer (e.g., −1).

**Advantages**

- **No external fragmentation** — any free block is usable.
- **Files grow dynamically** — just link on another block, anywhere.

**Disadvantages**

- **No efficient direct/random access** — reaching block k requires traversing k pointers sequentially from the start.
- Scattered blocks → **many seeks** → slow.
- **Pointer overhead** in every block reduces usable space.
- **Reliability** — one broken/corrupted pointer loses the rest of the file.

### File Allocation Table (FAT)

A popular implementation of linked allocation (MS-DOS, early Windows, FAT32):

- The next-pointers are moved out of the data blocks into a **centralized table** (the FAT), one entry per disk block, each holding the number of the next block in the file (end marked with a special value, e.g., −1).
- Example: File A starts at block 3 → FAT[3] = 4 → FAT[4] = 5 → FAT[5] = −1.

**Benefits over plain linked allocation:**

- The whole FAT can be **cached in memory** → random access becomes reasonable (walk the table in RAM, then do one disk access).
- **No pointer space wasted in data blocks.**

---

## 3. Indexed Allocation

All of a file's block pointers are collected into one dedicated **index block**; the directory entry points to the index block. The ith entry of the index block = disk address of the ith file block.

**Accessing block k:** read the index block → take its kth entry → access that data block.

**Advantages**

- Supports both **sequential and direct access** (fast random access).
- **No external fragmentation** — data blocks can live anywhere.
- Files can grow dynamically up to the index block's pointer capacity.

**Disadvantages**

- **Pointer/space overhead is high for small files** — a 2–3-block file still consumes a whole index block (linked allocation wastes only one pointer per block).
- Maximum file size limited by pointers per index block (without extensions below).
- Losing the index block corrupts the **entire file**.

### Indexing Schemes for Large Files

When one index block isn't enough:

1. **Linked scheme** — index blocks are chained: each holds pointers to data blocks plus a pointer to the next index block. Downside: finding a block may require traversing several index blocks.
2. **Multilevel index** — a hierarchy: a first-level index block points to second-level index blocks, which point to data blocks (extendable to more levels). Supports very large files; adds complexity.
3. **Combined scheme — the inode** (UNIX/Linux ext2/ext3/ext4):

   An **inode** stores file metadata (size, permissions, timestamps) plus a mix of pointers:

    - **Direct pointers** (e.g., first 10–12) — point straight at data blocks.
    - **Single indirect pointer** — points to a block of pointers.
    - **Double indirect pointer** — points to a block of pointers-to-pointer-blocks.
    - **Triple indirect pointer** — one more layer for extremely large files.

   **Why preferred:** small files (the common case) get fast direct access with no extra I/O, while the indirect layers scale efficiently to massive files.

---

## Advanced / Modern Approaches

### Extent-Based Allocation

Space is allocated in **extents** — contiguous chunks described by (start block, length) — instead of block-by-block. A file is a small list of extents.

- Blends the **sequential speed of contiguous** allocation with the **flexibility of indexing**; drastically reduces per-block pointer metadata and fragmentation.
- Used by **ext4, XFS, NTFS** for large files.

### Hybrid Allocation

Modern file systems pick a strategy by file size/access pattern: e.g., **direct blocks for small files**, **extents for large files**, **indirect/multilevel indexes for medium files**. This gives good performance across a wide range of workloads.

### Supporting Concerns

- **Dynamic block allocation:** blocks are allocated/freed on demand as files grow, shrink, or are deleted — requires careful free-space tracking (free lists/bitmaps); reusing recently freed blocks can improve locality.
- **Block size optimization:** larger blocks → less management overhead, better sequential throughput, but more **internal fragmentation** (rule of thumb: ~half a block wasted per file); smaller blocks → the reverse. Some systems support multiple sizes or **sub-block allocation** for small files.
- **Fragmentation management:** track free regions and metrics (largest free run, fragmentation ratio); prevent via smart placement, correct via **defragmentation** (merging adjacent free regions, relocating blocks).
- **Performance optimization:** caching, **prefetching** (predict and pre-read likely-next blocks), heat-map-style access tracking, intelligent block placement.
- **Recovery and consistency:** **journaling** (log old/new data before applying), checksums, and checkpointing keep the file system consistent across crashes.

---

## Comparison

| Feature | Contiguous | Linked | FAT | Indexed |
|---|---|---|---|---|
| Random access | Excellent | Poor | Good (table cached) | Excellent |
| Space overhead | None | Pointer per block | Table | Index block(s) |
| File growth | Difficult | Easy | Easy | Easy |
| External fragmentation | Yes | No | No | No |
| Implementation | Simple | Simple | Moderate | Complex |
| Reliability risk | — | Broken link loses rest of file | Table corruption | Lost index block loses file |

## Real-World File Systems

- **FAT32:** file allocation table (centralized linked allocation).
- **ext2/ext3/ext4:** inode combined scheme; ext4 adds **extents**.
- **NTFS:** Master File Table (MFT) — conceptually table-based like FAT but far more sophisticated; extent-style runs for file data.
- **XFS:** extent-based, B-tree indexed.

## Choosing a Method

Depends on: expected **file sizes and access patterns**, **performance requirements**, **storage device characteristics**, **reliability requirements**, and available system resources. Modern file systems almost always use **hybrid** approaches.

## Key Terms

**allocation method · contiguous allocation · starting block + length · external vs internal fragmentation · linked allocation · block pointer / null terminator · FAT (file allocation table) · indexed allocation · index block · linked / multilevel / combined indexing · inode · direct / single / double / triple indirect pointers · extent · hybrid allocation · sub-block allocation · defragmentation · prefetching · journaling**