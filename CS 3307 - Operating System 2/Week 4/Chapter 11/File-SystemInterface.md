# File-System Interface

> Based on Silberschatz, Gagne & Galvin, *Operating System Concepts* (9th Edition), Chapter 11

## 11.1 File Concept

A file is the OS's logical unit of persistent storage — an abstract data type presented to users and programs.

### 11.1.1 File Attributes

Different OSes track different attributes:

| Attribute | Notes |
|---|---|
| **Name** | Some systems give special significance to names/extensions (`.exe`, `.txt`); some extensions matter to the OS (`.exe`), others only to applications (`.jpg`) |
| **Identifier** | Low-level name, e.g., inode number |
| **Type** | Text, executable, other binary, etc. |
| **Location** | Where on the drive |
| **Size** | Current size |
| **Protection** | Access rights |
| **Time & date** | Creation/modification/access |
| **User ID** | Owner |

### 11.1.2 File Operations

The file ADT supports: **create, write, read, reposition (seek), delete, truncate**.

- Most OSes require files to be **opened** before access and **closed** afterward (usually explicit; rarely automatic on first access).
- Info about open files lives in an **open file table**:
    - **File pointer** — current position for the next read/write.
    - **File-open count** — how many simultaneous opens (by different processes) are outstanding; when it reaches zero the entry can be removed.
    - **Disk location** of the file.
    - **Access rights.**

**File locking:**

- **Shared lock** — reading only (multiple readers).
- **Exclusive lock** — writing (and reading).
- **Advisory lock** — informational, not enforced (a "Keep Out" sign that may be ignored). Used by **UNIX**.
- **Mandatory lock** — enforced (a truly locked door). Used by **Windows**.

### 11.1.3 File Types

Three ways systems identify file type:

1. **Extensions** — Windows and others use special extensions (`.exe`, `.com`, `.bat`, `.doc`, ...).
2. **Creator attribute** — Macintosh stores which program created the file (via `create()`), so double-clicking opens the right app.
3. **Magic numbers** — UNIX stores identifying bytes at the start of certain files (try the `file` command on `/bin`, `/dev`).

### 11.1.4 File Structure

- Files may have internal structure the OS may or may not know about; OS support for particular formats increases OS size and complexity.
- **UNIX:** all files are just **sequences of bytes** — no internal structure imposed (exception: executable binaries, which the OS must know how to load).
- **Macintosh:** files have **two forks** — a **resource fork** (UI info: icons, button images) and a **data fork** (code/data), independently modifiable.

### 11.1.5 Internal File Structure

- Disk access happens in **physical blocks** (512 bytes or a power-of-two multiple; larger disks use larger blocks to keep block numbers within 32 bits).
- Files are logically organized in **logical units** (from 1 byte up to record-sized); how many fit per block = **packing**.
- **Internal fragmentation:** wasted space within allocated blocks. Rule of thumb — about **half a block wasted per file**; larger block sizes → more waste.

---

## 11.2 Access Methods

### 11.2.1 Sequential Access

Emulates magnetic tape:

- `read next` — read a record, advance.
- `write next` — write a record, advance.
- `rewind` — back to the beginning.
- `skip n records` — may or may not be supported; n may be restricted (e.g., ±1).

### 11.2.2 Direct Access

Jump to any record:

- `read n` / `write n` — access record number n (note: argument now required).
- Jump to record n (0 or end of file); query current record (to return later).

**Sequential access is easily emulated with direct access; the inverse is complicated and inefficient.**

### 11.2.3 Other Access Methods

- **Indexed access** builds on direct access: an index maps keys → record locations.
- Very large files may need **multi-tiered indexes** (indexes of indexes).

---

## 11.3 Directory Structure

### 11.3.1 Storage Structure

Options for organizing disks and file systems:

- One disk = one file system.
- One disk split into **partitions** (slices, mini-disks) — each a virtual disk with its own file system (or raw storage/swap).
- Multiple disks combined into one **volume** — a larger virtual disk with one file system spanning them.

### 11.3.2 Directory Operations

Search for a file · create a file · delete a file · list a directory (variously ordered) · rename a file (may change sort order) · traverse the file system.

### 11.3.3 Single-Level Directory

All files in one directory. Simple, but **every file needs a unique name** — unworkable with many files/users.

### 11.3.4 Two-Level Directory

- Each user gets their own directory; names need be unique only **within a user's directory**.
- A **master file directory** tracks each user's directory (maintained as users are added/removed).
- System (executable) files need a separate directory; if users can't access other directories, a **search path** (per-user list of directories to search for executables) is needed.

### 11.3.5 Tree-Structured Directories

The familiar general hierarchy:

- Each user/process has a **current directory**; access via **absolute pathnames** (from root) or **relative pathnames** (from current directory).
- Directories are stored like files, with a bit marking them as directories and OS-understood structure.
- Deleting non-empty directories: **Windows** requires emptying first; **UNIX** offers recursive subtree deletion (e.g., `rm -r`).

### 11.3.6 Acyclic-Graph Directories

Allows the **same file to appear in multiple places** (sharing between users/processes). UNIX implements this with two link types (`man ln`):

- **Hard link:** multiple directory entries referring to the same file. Valid only for **ordinary files in the same file system**. Requires a **reference (link) count**; when it reaches zero, disk space is reclaimed.
- **Symbolic link:** a special file containing the location of the linked file. Can link **directories** and files across **other file systems**. Windows's version: **shortcuts** (Windows supports only symbolic links).

Symlink issues when the original moves or is deleted: either find and fix all symlinks, or leave them **dangling** (discovered invalid on next use). Worse: what if the original is deleted and a *different* file with the same name appears before the link is used?

### 11.3.7 General Graph Directory

If **cycles** are allowed, problems arise:

1. **Infinite loops** in search — fix by not following links during searches (or only allowing symlinks to directories and skipping them).
2. **Disconnected subtrees** with nonzero reference counts — orphaned space that needs **garbage collection**. `chkdsk` (DOS) and `fsck` (UNIX) detect such problems; disconnected blocks not marked free get re-added with made-up names (usually safe to delete).

---

## 11.4 File-System Mounting

- Mounting combines multiple file systems into **one large tree**.
- `mount` takes a file system and a **mount point** (directory); afterward, references to that directory refer to the **root of the mounted file system**.
- Files previously stored in the mount-point directory are **hidden** while the mount is active — some systems therefore only allow mounting on empty directories.
- Mounting normally requires **root**, unless root pre-configures user-mountable file systems on set mount points. Anyone can run `mount` to list current mounts. Mounts may be **read-only** or otherwise restricted.
- **Windows (traditional):** extended two-tier structure — drive letters as the first tier, trees below. **Macintosh:** volumes auto-mount and appear on the desktop. Recent Windows also allows mounting at arbitrary directories, UNIX-style.

---

## 11.5 File Sharing

### 11.5.1 Multiple Users

Per-file info on multi-user systems: **owner** (controls access), **group** (users with special access), and rights for **owner / group / others (universe)**. Some systems have finer-grained controls naming specific users/groups.

### 11.5.2 Remote File Systems

Evolution of remote access: **ftp** (individual file transfer; account/password or anonymous) → **distributed file systems** (remote FS mounted into the local tree, accessed with normal file commands) → **WWW** (access without mounting, often via anonymous ftp-style transfer).

#### Client-Server Model

- The machine physically owning the files = **server**; the machine mounting them = **client**. A machine can be both (cross-linked file systems).
- User/group IDs must be **consistent across systems** — best within one organization.
- Security concerns: servers restrict mounts to trusted systems (**spoofing** risk), may restrict to read-only, and limit which file systems are exportable (usually public-ish, well-backed-up data). Classic example: **NFS**.

#### Distributed Information Systems

- **DNS** — unique naming across the Internet.
- **NIS** — maintained domain names but has security issues; NIS+ more secure but less adopted.
- **CIFS** (Microsoft) — network login with shared file access; older Windows used *domains*, newer (2000/XP) use *active directories*; usernames must match across the network.
- **LDAP** — secure **single sign-on** with authorization info in one central location; gaining popularity.

#### Failure Modes

- Local disk failure: known immediately, generally unrecoverable → fail the request.
- Remote failure: many possible causes (host? network?), recoverability unclear → most systems allow **blocking/delayed** responses hoping the remote side returns.

### 11.5.3 Consistency Semantics

When one user changes a shared file, when do others see it? Network delays rule out the atomic-operation solutions used for local synchronization.

- **UNIX semantics:** writes to an open file are **immediately visible** to all users with it open; may use a shared location pointer; file tied to one exclusive physical resource (can delay accesses).
- **Session semantics (AFS):** writes are **not** immediately visible; changes become visible only to users who **open the file after it is closed**. Multiple (possibly different) views can coexist; no one is delayed.
- **Immutable-shared-files semantics:** once declared shared, a file becomes **read-only** and its name can never be reused — sharing becomes trivially simple.

---

## 11.6 Protection

- **Reliability** (against accidental damage) → backups. **Protection** (against malicious access) → this section.
- Removing all access makes a file useless; we need **controlled access**.

### 11.6.1 Types of Access

Controlled low-level operations: **read, write, execute, append, delete, list**. Higher-level operations (e.g., copy) compose from these.

### 11.6.2 Access Control

- **ACLs:** exact allow/deny per user/group (AFS uses these for distributed access). Finely adjustable but complicated; AFS supports wildcards (trust all users on a host, or a username from anywhere).
- **UNIX: 9 permission bits** — R, W, X for each of **Owner, Group, Others** (`man chmod`):

| Bit | Files | Directories |
|---|---|---|
| **R** | View file contents | List directory contents |
| **W** | Change file contents | Create or delete files within |
| **X** | Execute as program | Access detailed info / enter; required to access any specific file. With X but not R, a user can access files **only if they already know the name** |

**Special bits:**

- **SUID / SGID** on executables: the runner temporarily assumes the identity of the file's owner/group — grants program-mediated access to otherwise inaccessible files. Usually root-restricted; a potential **security leak** if misused.
- **Sticky bit** on directories: users may delete **only their own files** (e.g., `/tmp` — anyone creates, only owners delete).
- Shown as `s`, `s`, `t` in the execute positions for user/group/others. Lowercase = execute also granted; uppercase (`S`, `S`, `T`) = execute NOT granted. Setting them requires the **numeric** form of `chmod`.

- **Windows:** access adjusted via a GUI ACL editor.

### 11.6.3 Other Protection Approaches and Issues

- **Passwords** on files/sub-directories/whole system — trade-off between number of passwords to remember and how much is exposed by one lost password.
- Systems originally lacking multi-user permissions (DOS, older Mac) must be retrofitted for network sharing.
- Access to a file requires access along its **entire path**; with cyclic structures, different paths to the same file can carry different access.
- Sometimes even **knowing a file exists** is a privacy concern — hence the R vs X distinction on UNIX directories.

---

## Key Terms

**file attributes · open file table · file pointer · file-open count · shared/exclusive lock · advisory vs mandatory locking · extensions / creator attribute / magic numbers · resource fork & data fork · physical block · internal fragmentation · sequential / direct / indexed access · partition · volume · single-level / two-level / tree directory · master file directory · search path · current directory · absolute vs relative pathname · acyclic-graph directory · hard link / symbolic link / shortcut · link count · dangling link · garbage collection (fsck, chkdsk) · mount point · client-server model · NFS · DNS / NIS / CIFS / LDAP · consistency semantics (UNIX / session / immutable-shared) · ACL · UNIX rwx bits · SUID / SGID / sticky bit**