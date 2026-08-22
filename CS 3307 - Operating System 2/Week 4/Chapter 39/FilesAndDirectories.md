# Interlude: Files and Directories

> Based on OSTEP Chapter 39 — *Operating Systems: Three Easy Pieces* (Arpaci-Dusseau)

## Motivation

The OS already virtualizes the **CPU** (process) and **memory** (address space). The third piece of the virtualization puzzle is **persistent storage** — devices (HDDs, SSDs) that keep data intact across power loss. The OS must take extra care: this is where users keep data they really care about.

**CRUX:** How should the OS manage a persistent device? What are the APIs? What are the important aspects of the implementation?

---

## 39.1 Files and Directories

Two key abstractions:

### File

- A **linear array of bytes**, each readable/writable.
- Each file has a **low-level name**, usually a number — the **inode number (i-number)**; users typically don't see it.
- The OS is unaware of file structure (picture, text, code); the file system's job is simply to store the data persistently and return exactly what was written.

### Directory

- Also has an inode number, but its contents are specific: a list of **(user-readable name, low-level name)** pairs — e.g., ("foo", "10").
- Entries refer to files or other directories → nesting builds an arbitrary **directory tree/hierarchy**.
- The tree starts at the **root directory** (`/` in UNIX); separators name sub-directories down to the target, e.g., the **absolute pathname** `/foo/bar.txt`.
- Same names may exist in different locations (e.g., `/foo/bar.txt` and `/bar/foo/bar.txt`).
- File name extensions (`.c`, `.jpg`, `.mp3`) are **conventions only** — usually not enforced.

**TIP — Naming matters:** In UNIX virtually everything is named through the file system — files, devices, pipes, even processes. Uniform naming simplifies the conceptual model. The file system unifies access to disks, USB, CD-ROM, etc., under a single tree.

---

## 39.3 Creating Files: `open()`

```c
int fd = open("foo", O_CREAT|O_WRONLY|O_TRUNC, S_IRUSR|S_IWUSR);
```

- `O_CREAT`: create if it doesn't exist; `O_WRONLY`: write-only; `O_TRUNC`: truncate existing content to zero bytes. Third argument sets permissions (owner read+write here).
- Older `creat()` ≈ `open()` with `O_CREAT|O_WRONLY|O_TRUNC`. (Ken Thompson, asked what he'd redo in UNIX: "I'd spell creat with an e.")

### File descriptors

- `open()` returns a **file descriptor**: an integer, **private per process**, used for all subsequent access.
- A fd is a **capability** — an opaque handle granting the power to perform operations; think of it as a pointer to a file object with "methods" like `read()`/`write()`.
- Managed per-process: xv6 keeps `struct file *ofile[NOFILE]` in the proc structure, indexed by fd.

**TIP — Use `strace`** (Linux; `dtruss` on Mac, `truss` on older UNIX) to trace a program's system calls, arguments, and return values. Useful flags: `-f` (follow forks), `-t` (timestamps), `-e trace=open,close,...`.

---

## 39.4 Reading and Writing Files

Tracing `cat foo`:

```
open("foo", O_RDONLY|O_LARGEFILE) = 3
read(3, "hello\n", 4096)          = 6
write(1, "hello\n", 6)            = 6
read(3, "", 4096)                 = 0
close(3)                          = 0
```

- fd is **3** because every process starts with three open files: **0 = standard input, 1 = standard output, 2 = standard error**.
- `read(fd, buffer, size)` returns bytes read; **0 means end of file**.
- The write to fd 1 prints to the screen (possibly via `printf()` internally).
- Writing follows the same pattern: open for writing → `write()` (repeatedly) → `close()`.

---

## 39.5 Non-Sequential Access: `lseek()`

```c
off_t lseek(int fd, off_t offset, int whence);
```

- `SEEK_SET`: offset = `offset`; `SEEK_CUR`: current + `offset`; `SEEK_END`: file size + `offset`.
- For each open file the OS tracks a **current offset** — where the next read/write begins. Updated two ways:
    1. **Implicitly** — each read/write of N bytes adds N.
    2. **Explicitly** — via `lseek()`.

**ASIDE — `lseek()` does NOT perform a disk seek.** It only changes a variable in OS memory. (Random access patterns *following* lseek may cause disk seeks, but the call itself does no I/O.)

### The open file table

Each fd refers to an entry in the system-wide **open file table**; the entry tracks the underlying file, current offset, and readability/writability. xv6:

```c
struct file {
    int ref;             // reference count
    char readable, writable;
    struct inode *ip;    // underlying file
    uint off;            // current offset
};
```

**Example traces:**

- Open 300-byte file, `read()` 100 bytes ×3: offset goes 0 → 100 → 200 → 300; 4th read returns 0 (EOF).
- Opening the **same file twice** allocates two fds and two **independent** open-file-table entries with independent offsets.
- `open` → `lseek(fd, 200, SEEK_SET)` → `read(fd, buf, 50)` → offset 250.

---

## 39.6 Shared File Table Entries: `fork()` and `dup()`

Usually fd ↔ open-file-table entry is one-to-one (even two processes reading the same file get separate entries). Sharing occurs:

- **`fork()`:** parent and child **share** the entry — a child's `lseek()` changes the offset the parent sees. The entry's **reference count** is incremented; it's removed only when all sharers close/exit. Useful for cooperating processes writing to one output file with no extra coordination.
- **`dup()`** (and `dup2()`, `dup3()`): creates a new fd referring to the **same** open file entry. Key for shell **output redirection**.

---

## 39.7 Writing Immediately: `fsync()`

- `write()` normally just tells the file system to write **eventually** — writes are buffered in memory (e.g., 5–30 s) for performance; a crash in between loses data.
- Apps needing stronger guarantees (e.g., DBMS recovery protocols) call **`fsync(fd)`**: forces all dirty data for that file to disk; returns when writes complete.
- **Gotcha:** for a newly created file, you may also need to `fsync()` the **containing directory** so the file is durably part of it. Often overlooked → application bugs [P+13, P+14].

---

## 39.8 Renaming Files: `rename()`

- `mv` uses `rename(old, new)`.
- **Atomic with respect to crashes**: after a crash the file has either the old name or the new name — no in-between state.
- Enables **atomic file update** (the editor pattern):

```c
int fd = open("foo.txt.tmp", O_WRONLY|O_CREAT|O_TRUNC, S_IRUSR|S_IWUSR);
write(fd, buffer, size);   // new version of the file
fsync(fd);
close(fd);
rename("foo.txt.tmp", "foo.txt");   // atomically swap into place
```

**ASIDE — `mmap()` and persistent memory:** `mmap()` maps byte offsets of a **backing file** to virtual addresses; the process then accesses persistent data with plain loads/stores (**persistent memory** style), eliminating format translation between memory and storage. Crashes mid-update can leave inconsistent state, so applications need failure-atomic update mechanisms.

---

## 39.9 Getting Information About Files: `stat()`

- **Metadata** = data about files. Retrieved via `stat()` (pathname) or `fstat()` (fd), filling a `struct stat`:
    - device ID, **inode number**, mode/protection, **link count**, owner uid/gid, **size**, block size, blocks allocated, **access/modification/status-change times**.
- Kept per file in a structure called the **inode** — a persistent data structure on disk (active ones cached in memory).

---

## 39.10–39.13 Removing Files and Directory Operations

- `rm` uses the mysteriously named **`unlink()`** — explained under hard links below.
- **Directories cannot be written directly** — their format is file-system metadata; you update them indirectly (by creating files/dirs within). This lets the FS guarantee directory integrity.
- **`mkdir()`** creates a directory. An "empty" directory has two entries: **`.`** (itself) and **`..`** (parent). See them with `ls -a`.
- **Reading directories:** `opendir()`, `readdir()`, `closedir()` — loop over `struct dirent` entries (name, inode number, type, ...). Directories hold little info, so `ls -l` additionally calls `stat()` on each file.
- **`rmdir()`** deletes a directory, but only if it's **empty** (just `.` and `..`) — a safety requirement.

**TIP — Beware powerful commands:** `rm -rf *` from the wrong directory (e.g., root) destroys everything. Power = double-edged sword.

---

## 39.14 Hard Links

- **`link(old, new)`** (command: `ln`) creates another name in a directory that refers to the **same inode number**. No data is copied — two human-readable names for one file. Verify with `ls -i` (same i-number).
- Creating a file really does two things: (1) create the **inode** tracking all file info; (2) **link** a human-readable name to it in a directory.
- After linking, the original name and the new name are indistinguishable — both are just links to the same inode.

### Why "unlink"?

- The inode keeps a **reference count (link count)** — how many names link to it.
- `unlink()` removes the name→inode link and decrements the count; **only when it reaches zero** does the FS free the inode and data blocks (truly deleting the file).
- Example: create file (Links: 1) → `ln` twice (Links: 3) → each `rm` decrements until 0.

## 39.15 Symbolic (Soft) Links

Hard-link limits: can't link to **directories** (cycle risk) or **across partitions/file systems** (inode numbers are unique only within one FS). Hence **symbolic links** (`ln -s`):

- A symlink is a **file itself** — a **third file type** (besides regular files and directories); `ls -l` shows `l` (vs `-` regular, `d` directory).
- Its **data is the pathname** of the target — so its size equals the pathname length (e.g., 4 bytes for "file", 15 for "alongerfilename").
- **Dangling reference:** removing the target leaves the symlink pointing at a nonexistent pathname (`cat` fails) — unlike hard links.

---

## 39.16 Permission Bits and Access Control Lists

Files are commonly **shared**, unlike private CPU/memory abstractions, so file systems need sharing controls.

### UNIX permission bits

`-rw-r--r--`: first char = type (`-` file, `d` dir, `l` symlink); then **nine permission bits** in three groups — **owner / group / other**, each with **read (r), write (w), execute (x)**.

- Change with `chmod`, e.g., `chmod 600 foo.txt` → `rw-------` (read=4, write=2, OR'd for owner; 0 for group/other).
- **Execute bit:** for regular files — can it be run (scripts/programs). For **directories** — allows `cd` into it, and (with write) creating files within.
- **Superuser (root)** can access all files regardless of permissions — an inherent security risk if impersonated.

### Access control lists (ACLs)

E.g., AFS keeps an **ACL per directory** — a precise list of exactly who can read/write/insert/delete/administer (`fs listacl`, `fs setacl`). More general and powerful than owner/group/other.

**TIP — TOCTTOU (Time Of Check To Time Of Use):** a gap between a validity check (e.g., `lstat()` on a mail inbox) and the use (writing to it) lets an attacker swap the file (e.g., `rename()` inbox → `/etc/passwd`) in between, tricking a privileged service into writing a sensitive file → privilege escalation. No great general fix; mitigations: fewer root services, `O_NOFOLLOW` (open fails on symlinks), transactional file systems. Be careful in privileged code.

---

## 39.17 Making and Mounting a File System

- **`mkfs`:** given a device (e.g., `/dev/sda1`) and FS type (e.g., ext3), writes an **empty file system** (starting with a root directory) onto the partition.
- **`mount`** (syscall `mount()`): takes an existing directory as a **mount point** and pastes the new file system onto the tree at that point. E.g., after `mount -t ext3 /dev/sda1 /home/users`, the new FS's root is accessed as `/home/users/`.
- Beauty of mount: **unifies all file systems into one tree** — ext3, proc (process info), tmpfs (temp files), AFS (distributed), etc., all glued into one naming hierarchy. Run `mount` to see what's mounted where.

---

## 39.18 Summary / Key Terms

- **File:** array of bytes with a low-level name (**i-number**).
- **Directory:** collection of (human-readable name → i-number) tuples; has its own i-number; always contains `.` and `..`.
- **Directory tree** rooted at `/` organizes everything.
- Access requires `open()` → **file descriptor** (private, per-process) → entry in the **open file table** (tracks file, **current offset**, etc.).
- `read()`/`write()` implicitly update the offset; `lseek()` changes it explicitly (random access).
- **`fsync()`** forces data to persistent media (hard to use correctly + fast).
- **Hard links** and **symbolic links** give multiple names to one file; deleting a file is just the last **`unlink()`**.
- Sharing controls: **permission bits** (basic) and **ACLs** (precise).

## Key System Calls / Tools

| Operation | API |
|---|---|
| Create/open file | `open()` (with `O_CREAT`), `creat()` |
| Read / write | `read()`, `write()` |
| Random access | `lseek()` |
| Duplicate fd | `dup()`, `dup2()`, `dup3()` |
| Force to disk | `fsync()` |
| Rename (atomic) | `rename()` |
| Metadata | `stat()`, `fstat()` |
| Remove file | `unlink()` |
| Directories | `mkdir()`, `opendir()`/`readdir()`/`closedir()`, `rmdir()` |
| Links | `link()`, `symlink()` (`ln`, `ln -s`) |
| Permissions | `chmod()` |
| FS assembly | `mkfs`, `mount()` |
| Tracing | `strace` / `dtruss` / `truss` |

## Key References

- [SR05] Stevens & Rago — *Advanced Programming in the UNIX Environment* (the place to begin)
- [MJLF84] McKusick et al. — Fast File System (introduced symbolic links, long names)
- [K84] Killian — *Processes as Files* (/proc)
- [L84] Levy — capability-based systems
- [BD96] Bishop & Dilger — TOCTTOU races in file access
- [P+13, P+14] Pillai et al. — crash-consistency bugs in applications