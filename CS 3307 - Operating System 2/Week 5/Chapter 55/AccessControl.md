# Access Control 

*(Based on Chapter 55: "Access Control" by Peter Reiher, UCLA)*

---

## Table of Contents

1. Introduction — From Policy to Actionable Decisions
2. The Crux and Key Terminology (Subjects, Objects, Access, Authorization)
3. When to Check — The Reference Monitor and Virtualization Shortcuts
4. The Two Fundamental Approaches — ACLs and Capabilities (The Nightclub Analogy)
5. Using ACLs for Access Control
6. Aside: Name Spaces in Distributed Systems
7. Using Capabilities for Access Control
8. ACLs vs. Capabilities — How Linux Actually Combines Both
9. Mandatory vs. Discretionary Access Control
10. Practicalities of Access Control Mechanisms
11. Role-Based Access Control (RBAC)
12. Privilege Escalation — setuid and sudo
13. Summary
14. Glossary of Key Terms
15. Summary Tables
16. Annotated Reference List
17. Big-Picture Takeaways

---

## 1. Introduction — From Policy to Actionable Decisions

### 1.1 Where We Are in the Pipeline
- We know our **security goals**, have a general sense of the **security policies** meant to enforce them, and (via authentication) have **evidence about who is requesting** services.
- **Now:** we need to turn this into something **actionable** — something **software can actually do**.

### 1.2 Two Key Steps
1. **Figure out** if the request fits within our security policy.
2. **If it does**, perform the operation; **if not**, make sure it isn't done.

> **Step 1 is called ACCESS CONTROL** — determining which resources/services can be accessed by which parties, in which ways, under which circumstances. At its core, it's a **binary decision**: yes or no.

### 1.3 A Concrete Motivating Example
- **User X** wishes to read and write file `/var/foo`.
- Under the covers, this likely means a process (running as User X) issues something like:
```
open("/var/foo", O_RDWR)
```
- *(Note: the chapter deliberately uses a generic notation, since this represents the **general concept** of a file-open system call, not the specific Linux implementation.)*
- **The question:** how should the system decide whether to **allow or reject** this? We know the system call **traps** to the OS, giving it the opportunity to decide — but **mechanically, what should that "something" actually be?**

### 1.4 The Crux
> **How can the operating system decide if a particular request made by a particular process belonging to a particular user at some given moment should or should not be granted? What information will be used to make this decision? How can we set this information to encode the security policies we want to enforce?**

---

## 2. The Crux and Key Terminology

### 2.1 The Core Vocabulary

| Term | Definition |
|---|---|
| **Subject** | The entity that wants to perform the access — e.g., a user or a process. |
| **Object** | The thing being accessed — e.g., a file or a device. |
| **Access** | The particular MODE of interaction with the object — e.g., reading or writing it. |
| **Authorization** | The process of determining whether a particular subject is allowed a particular mode of access to a particular object. |

- *(The chapter humorously footnotes its own repeated use of the word "particular" three times in one sentence — "a column of particulars," as it puts it.)*

---

## 3. When to Check — The Reference Monitor and Virtualization Shortcuts

### 3.1 The Reference Monitor
- The code implementing the access-control-decision algorithm is called the **reference monitor.**
- **Two critical properties needed:**
    1. **Correctness** — if wrong, you make **incorrect** access decisions (obviously bad).
    2. **Efficiency** — every check **injects overhead**, since it runs on **every relevant operation**.

### 3.2 The Tension With Complete Mediation
- Recall the **principle of complete mediation** (from the prior chapter on OS security): check security conditions **every single time**.
- **This creates tension:** we want to minimize overhead, but complete mediation says check **every time**. **We must balance cost against security benefit.**
- **BUT:** in some special cases, we can get **low cost without compromising security** — avoiding the trade-off entirely, at least in those cases.

### 3.3 The Virtualization Shortcut

> **Key idea:** if an object **inherently and unchangeably belongs** to a subject, the system can let that subject access it **freely**, with **no per-access check needed**.

- **Virtualization** lets us create exactly this kind of object.

#### Example 1 — Virtual Memory
- A process can access its **own virtual memory freely**, with **no OS-level access control check at the moment of use**.
- **Why this matters:** if we DID need to run the full access control algorithm on **every single memory reference**, the system would be **ridiculously slow**.
- *(Footnote caveat: page table bits determining read/write/execute permission still exist — but that check is done by **virtual memory hardware**, not a runtime OS access-control algorithm.)*

#### Example 2 — Virtualized Peripheral Devices
- If a process is given a **virtual device** (backed by a real physical device controlled by the OS), and **no other process** is allowed to use it, the OS need **not** check access every time the process uses it.
- **Example:** a process might be granted control of a **GPU** via one initial access-control decision, after which it can write to GPU memory / issue instructions **directly**, without further OS intervention.

### 3.4 The Catch — Virtualization Is Still an Illusion
- Virtualization is **mostly an OS-provided illusion** — processes actually **share** memory, devices, and other resources; the OS runs "behind the scenes" (sometimes with hardware help) to **maintain** that illusion.
- **This means:** the OS must **still** ensure only proper access happens — **without** the application's direct knowledge/participation.
- **Conclusion:** relying purely on virtualization just **pushes the problem down** to protecting the virtualization machinery **itself**. Eventually, we must confront the **general** problem: Subject X wants to read/write object `/tmp/foo` — maybe allowed, maybe not. **Now what?**

---

## 4. The Two Fundamental Approaches — ACLs and Capabilities (The Nightclub Analogy)

### 4.1 Not Actually New — Millennia-Old Concepts
- Computer scientists didn't **invent** these approaches — they've existed in **non-computer contexts for millennia**. Let's understand them generally first.

### 4.2 The Exclusive Nightclub Analogy — "Chez Andrea"

**Scenario:** an exclusive nightclub restricted to top OS researchers/developers, keeping out "database or programming language people."

#### Approach A — The Bouncer With a List
- Hire an **intimidating bouncer** holding a **list of approved members**.
- Would-be entrants **prove their identity**; the bouncer checks the **list**.
- Linus Torvalds or Barbara Liskov → **admitted**. Unaccomplished networking folks → **turned away**.

#### Approach B — Locks and Keys
- Put a **great lock** on the door, and hand out **keys** to approved OS buddies.
- Jerome Saltzer → pulls out his **key**, unlocks the door himself.
- Someone without a key (e.g., a computer architect with no OS credentials) → **stuck outside**.
- **Trade-off:** save on bouncer salary, but pay for locks/keys. Need **new keys** for new admittees; must handle **mistaken key issuance** and **lost keys** (requiring the lock to be changed so the lost key no longer works).

### 4.3 Mapping the Analogy to Computer Science

| Analogy | CS Term |
|---|---|
| Locks and keys | **Capability-based system** |
| Bouncer + list | **Access Control List (ACL) system** |

- **Capabilities** are like **keys**, **movie tickets**, or **subway tokens**.
- **Access control lists** are, well, like **lists**.

### 4.4 How Each Works in an OS

- **Capabilities:** when a process (belonging to user X) wants to read/write `/tmp/foo`, it **hands a capability specific to that file** to the system.
- **ACLs:** the system **looks up user X** on an ACL associated with `/tmp/foo`, only allowing access if X is **on the list**.
- **In both cases**, the check happens **at the moment of the request** (e.g., at `open()`), after trapping to the OS, but **before** the access is actually permitted — with an **early exit and error code** if the check fails.

### 4.5 An Important Shared Assumption
> Both mechanisms **assume the person/process trying to gain access has already been properly AUTHENTICATED**. If a fraud gets past the bouncer wearing a mask, or if we hand a key to the wrong random person without verifying identity, neither ACLs nor capabilities will keep out the riffraff. **This is exactly why authentication (previous chapter) is a prerequisite for effective access control.**

---

## 5. Using ACLs for Access Control

### 5.1 Per-Object Lists, Not One Giant List
- **Extended analogy:** if Chez Andrea gives each member a **private room** (plus shared spaces like the library, dining room, billiard parlor), we need fine-grained control — e.g., **Ken Thompson** (footnoted as "known to be a bit of a scamp" [T84]) shouldn't be able to sneak into **Whitfield Diffie's** room.
- **Better than ONE giant ACL for everything:** have **one ACL PER ROOM** (i.e., per object) — **simpler, shorter lists, faster checks.**
- **In OSes:** each **file** has its **own** ACL — the `open()` call examines the list for `/tmp/foo` specifically, not one mega-list encoding every file's permissions.

### 5.2 The ACL Check Process, Step by Step

1. `open()` traps to the OS.
2. OS consults the calling process's **PCB** to determine **who owns the process** (say, user X).
3. OS obtains the **ACL for `/tmp/foo`** (file metadata, likely stored with/near the rest of the file's metadata).
4. OS **looks up X** on that list.
    - **Not found** → **no access**.
    - **Found** → check whether X's ACL entry permits the **specific mode** of access requested (e.g., X might be allowed **read**, but not **write**).
5. If the requested mode isn't permitted, the OS **denies** access and returns an **error**.

### 5.3 Devilish Implementation Details

#### Detail 1 — Where Is the ACL Stored?
- ACLs must be **persistent** (since they encode our chosen security policy, which changes rarely) — so they live on **disk/flash**.
- **Unless cached**, we'd need a **separate device read** just to fetch the ACL every time a file is opened — on top of the reads already needed to access file data/metadata.
- **Best placement:** **close to, or embedded within**, something already being read — candidates: the file's **directory entry**, its **inode**, or the **first data block**. **Ideally embedded directly** (e.g., in the inode).

#### Detail 2 — How Big Is the List?
- **Naive approach:** a list of actual **user IDs + access modes**, potentially as large as the **total number of users known to the system** (could be **thousands**).
- **BUT:** most files belong to **one user**, shared with **at most a few friends** — so **most ACL entries would be wasted space** for most files.
- **Exception:** some files (e.g., common executables like `ls`, `mv`; shared font/config files) genuinely **need to be accessible to ALL users** — otherwise users can't do much of anything.
- **The resulting dilemma:** a naive fixed-size-list implementation would be **hugely wasteful** for the "everyone can access" files (reserving space for every possible user) and would also require **searching a long list** on every check — adding **undesirable overhead**.
- **Possible fix:** variable-sized ACLs, allocated exactly the space needed — but this raises its own file-system-design complications.

### 5.4 The Classic Unix Solution — 9-Bit ACLs

#### Historical Context
- Early **Bell Labs Unix**: persistent storage was **scarce and expensive**. Designers determined they could afford roughly **NINE BITS** per file's ACL.

#### The Clever Trick
- Rather than one bit per (access mode x user) combination (which nine bits couldn't remotely cover), the designers observed:
    1. Only **THREE modes** of access typically matter: **read, write, execute.**
    2. They partitioned ACL entries into **THREE GROUPS**, not by listing arbitrary users, but by **role relative to the file**:
        - **Owner** (identity already stored in the inode).
        - **Group** (a group ID, also stored in the inode).
        - **Everyone else** (no bits needed — it's simply the **complement** of owner + group).
    3. **3 groups x 3 modes = 9 bits** — exactly matching the available budget!

```
Unix 9-bit permission structure:
+-------------+-------------+-------------+
|    Owner    |    Group    |    Others   |
| rwx (3 bits)| rwx (3 bits)| rwx (3 bits)|
+-------------+-------------+-------------+
 (identity from inode) (GID from inode)  (implicit: not owner, not in group)
```

#### Why This Solution Is Brilliant — Two Problems Solved at Once
1. **Storage:** trivially small (9 bits), easily embedded **directly in the inode**.
2. **Access speed:** since you **already** need to access the inode to do almost anything with a file, embedding the ACL there means **NO extra seeks/reads**. And checking access is just **simple bit logic**, not searching an arbitrary-length list.
- **This exact logic still answers access control questions in most POSIX-compliant file systems today.**

#### Limitations
- **Cannot express complex sharing relationships** (e.g., "share with these specific 5 users, but not others").
- Some modern systems (e.g., **Windows**) allow **extended, more general ACLs** — but many systems still rely on the tried-and-true **9-bit Unix scheme**.
- *(Historical nuance: the CTSS system had an earlier, more limited condensed ACL scheme [C+63]; Multics introduced the general concept of **groups** in ACLs [S74] — Unix's approach is a **cross-breeding** of these earlier ideas.)*

### 5.5 ACLs — The Good and the Bad

#### Advantages
1. **Easy to determine who can access a resource** — just look at the object's ACL directly.
2. **Easy to CHANGE the set of allowed subjects** — just edit the ACL; nothing else grants access.
3. **ACL typically stored with/near the object** — if you can reach the file, you can reach its access control info too. **Especially valuable in distributed systems**, and generally good for performance (assuming good co-location design).

#### Disadvantages
1. **Storage/search cost problems** (as discussed above) — the practical solutions (like Unix's 9-bit scheme) **limit expressive power** to compensate.
2. **Hard to determine ALL resources a given principal can access** — you'd need to check **every single ACL in the system**, since the principal could appear on any of them.
3. **Distributed environments need a CONSISTENT identity namespace** — if a UCLA user wants to access a file on a Wisconsin machine, the Wisconsin machine checks UCLA's provided identity against **its own** ACL. Does "remzi at UCLA" mean the **same principal** as "remzi at Wisconsin"? If not, a remote user might get access they **shouldn't** — and maintaining a **consistent global namespace** across independent computing domains is **genuinely challenging**.

---

## 6. Aside: Name Spaces in Distributed Systems

### 6.1 The Problem
- **On a single machine:** the namespace problem is easy — if a name is already in use, don't allow reassigning it. `/etc/password` means the **same thing** to every user/process on that machine.
- **Across multiple machines:** ensuring **globally unique, consistently understood** names is much harder. How do you ensure a name created at **UCLA** doesn't collide with one already used at **Wisconsin**?

### 6.2 Three General Approaches (No Universally Right Answer)

1. **Don't bother — accept that namespaces differ per system.** *(Example: process IDs — a PID means something different on every machine.)*
2. **Require a central authority to approve name selection.** *(Example: roughly how AFS handles file name creation.)*
3. **Hand out PORTIONS of the namespace to each participant**, letting them assign names only within their own portion. *(Example: the World Wide Web / IPv4 address space.)*

> **None of these is universally "right" or "wrong."** Design your namespace approach for your **specific needs**, but understand the implications of your choice.

---

## 7. Using Capabilities for Access Control

### 7.1 Returning to the Nightclub — Keys for Different Rooms
- Chez Andrea could give members **keys** — different rooms have **different keys**, preventing mischief in others' rooms.
- **Long history in computing:** Dennis and van Horn [DV64] are perhaps the **earliest** example (itself inspired by the "program reference table" in the Burroughs B5000). Wulf et al. [W+74] describe the **Hydra** OS, which used capabilities as a **fundamental control mechanism**. Levy [L84] gives a **book-length** treatment of capabilities in early hardware/software.
- **In a PURE capability system:** there is **no ACL anywhere** — the process's set of capabilities is the **ENTIRE** encoding of its access permissions. *(Not how Linux/Windows work — but systems like Hydra explored this.)*

### 7.2 How `open()` Would Work in a Pure Capability System
- Either the **application provides a capability** as a parameter, or the OS **finds** the relevant capability.
- The OS checks: does this capability **permit** a read/write open on `/tmp/foo`?
    - **Yes** → OS opens the file.
    - **No** → error returned to the process.

### 7.3 What IS a Capability, Really?

> **Capabilities, like all computer information, are just BITS — data.**

- Since there are likely **many** resources to protect, and capabilities must be **resource-specific**, they're likely to be **fairly long and complex**.

### 7.4 The Core Security Problem With "Just Bits"

**Two troubling properties of anything made of bits:**
1. **Anyone can create ANY bit pattern they want** — there's no "reserved" or "proprietary" bit pattern a process is physically prevented from generating.
2. **Copying a bit pattern is trivial** — once a process has a working capability, it can make **as many copies as it wants**, storing them **anywhere**, including on a **different machine**.

**Implications:**
- **Property 1** implies: a process could potentially just **generate** the "right" bit pattern for a capability it shouldn't have, and grant itself access!
- **Property 2** implies: even if we solve forgery, a process could **stash copies** elsewhere — meaning we couldn't ever be sure we'd fully **revoked** access, since hidden copies might persist. It also implies a process could **pass a copy** to another process via IPC, effectively **granting** access to a third party.

### 7.5 The Real Solution — the OS Mediates Everything

> **We want capabilities to be UNFORGEABLE — solved by never letting a process directly touch a capability's raw bits.**

- The **OS itself controls and stores** capabilities in its own **protected memory**.
- Processes can **perform operations** on capabilities, but **only through OS mediation**.
- **Example:** if process A wants to give process B read/write access to `/tmp/foo`, A **cannot** simply send B the raw bit pattern. Instead, A makes a **system call** asking the OS to **grant** the capability to B — giving the OS a chance to **check its security policy** and **deny** the transfer if not permitted.

### 7.6 Implementation — a Per-Process Protected Capability List
- The OS maintains a **protected capability list per process** — simple enough, since a pointer to this list (stored in **kernel memory**) can just be added to the process's **existing PCB**.
- On an `open()` attempt, the call traps to the OS, which **consults the capability list** for a relevant entry, and proceeds accordingly.

### 7.7 The Overhead Problem — And Two Solutions

- **Naive concern:** keeping an **online** capability list of literally **everything** a principal might access could be **huge** (e.g., thousands of file-access capabilities per user) — high overhead.
- **Common approach:** the system **persistently stores** the full set of capabilities somewhere safe, and **imports** the relevant ones **as needed** — so a process's **active** capability list stays reasonably short (though there's still a question of **which** of a user's many capabilities to hand to each process they run).
- **Alternative — cryptographically protected capabilities:** capabilities need **not** be stored by the OS at all. If they're **sufficiently long** and created with **strong cryptography**, they **cannot be practically guessed**, and can be **left in the user's own hands** directly. *(Makes most sense in **distributed systems** — covered in a later chapter.)*

### 7.8 Capabilities — The Good and the Bad

#### Advantages
1. **Easy to determine what a given principal can access** — just look through **their** capability list.
2. **Revocation is easy IF the OS has exclusive control** of the capability (simply remove it from the list) — but **much harder** if capabilities have been copied/distributed outside OS control.
3. **Cheap to check IF readily available in memory** — especially since a capability can **itself contain a pointer** to the resource's data/software (arguably, this pointer **IS** the core implementation of many capability schemes).

#### Disadvantages
1. **Expensive to determine ALL principals who can access a given resource** — any principal might hold a relevant capability, so you'd need to check **everyone's** capability list.
2. **No well-developed equivalent of Unix's elegant 9-bit ACL trick** for keeping capability lists short/manageable.
3. **The system must create/store/retrieve capabilities while overcoming the forgery problem** — genuinely **challenging** to implement correctly.

### 7.9 A Key Advantage of Capabilities — Easy Privilege Restriction for Children

> **With ACLs:** a child process **inherits its parent's identity**, and thus **ALL** of that identity's privileges. Giving a child just a **SUBSET** of the parent's privileges is **hard** — requires creating a whole new principal, editing multiple ACLs, and reassigning identity, or some non-standard extension.

> **With capabilities:** trivially easy. If the parent has capabilities for X, Y, and Z, but only wants the child to have X and Y, it **simply transfers X and Y** at creation time, withholding Z.

---

## 8. ACLs vs. Capabilities — How Linux Actually Combines Both

### 8.1 User-Visible Mechanisms vs. Under-the-Covers Reality
- **In practice**, user-visible access control mechanisms tend to use **ACLs**, not capabilities.
- **BUT** operating systems make **extensive internal use of capabilities under the covers.**

### 8.2 A Concrete Linux Example — The Life of an Open File

1. When a process calls Linux's `open()`, the OS uses **ACLs** to decide whether to allow it.
2. **IF successful:** as long as the file stays open, **subsequent reads/writes do NOT re-check the ACL.**
3. Instead, Linux creates a **capability-like data structure** recording that this specific process has read/write privileges for **this specific, already-opened file** — attached to the process's **PCB**.
4. On each subsequent read/write, the OS just **consults this lightweight structure** — no need to re-locate and re-check the file's full ACL.
5. **On close():** this capability-like structure is **deleted** from the PCB. To access the file again, the process must **re-`open()` it**, which goes **back through the ACL check**.

```
open() call
    |
    v
CHECK ACL for the file  ----> Denied? -> error
    |
   Allowed
    |
    v
Create capability-like structure in the process's PCB
    |
    v
Subsequent read()/write() calls --> just check this lightweight
                                     PCB structure (no ACL re-check!)
    |
    v
close() --> structure deleted from PCB
    |
    v
Next access requires a fresh open() --> back to ACL check
```

- **Similar techniques apply to hardware devices and IPC channels**, especially since Unix-like systems often treat these as if they were files too.

### 8.3 Why This Hybrid Is Clever — Best of Both Worlds
- **ACL-checking cost is only paid ONCE per open()**, not on every read/write — avoiding the "check a list on every single operation" overhead.
- **Full capability-management overhead is avoided** because a capability-like structure is only created **AFTER a successful ACL check**, and only for files a process **actually** accesses.
- Since **any given process typically opens only a tiny fraction** of all the files it's technically permitted to open, the "capabilities for everything" scaling problem **never actually arises** in practice.

---

## 9. Mandatory vs. Discretionary Access Control

### 9.1 The Question: Who Decides Access Control Settings?

- **Common-sense answer:** whoever **owns** the resource (the user, for a personal file; the sysadmin/owner, for a system resource).
- **BUT:** for some systems and policies, this **isn't** the right answer.

### 9.2 The Military Example — Mandatory Access Control

- **Top Secret information:** even if you're **cleared** to see it, you're **not** allowed to freely share it with others — **even if** the information appears in a document **you personally created** (e.g., a report quoting/summarizing Top Secret material).
- **In such cases**, the simple "creator controls access" answer is **wrong**. Whoever is in **overall charge of information security** for the organization must make these decisions — and that authority can **OVERRIDE** the wishes of the individual who created/owns a given piece of information.

### 9.3 The Two Categories

| Type | Definition |
|---|---|
| **Discretionary Access Control (DAC)** | Whether almost anyone (or almost no one) gets access is **at the discretion of the owning user**. |
| **Mandatory Access Control (MAC)** | At least SOME access decisions are **mandated by a central authority**, which **can override** the owner's own wishes. |

- **Orthogonal to ACLs vs. capabilities:** the DAC/MAC choice is **independent** of whether you use ACLs or capabilities, and largely independent of implementation details (how access info is stored, etc.).
- **A mandatory system can ALSO include discretionary elements** — these can **further restrict** (but never **loosen**) the mandatory controls.

### 9.4 A Pointer for Further Study
- Most readers will **never** work with a MAC system directly, so the chapter doesn't go deep — but notes: the OS is **necessarily involved** in enforcing MAC. If you ever need this, **you'll hear about it** — and those who bother implementing MAC also tend to **rigorously enforce** compliance.
- **Reference for further reading:** Loscocco [L01] describes an NSA-built **special version of Linux** incorporating mandatory access control.

---

## 10. Practicalities of Access Control Mechanisms

### 10.1 The Default-Permission Problem
- Most systems use some ACL variant, mostly with **discretionary** control.
- **BUT:** modern computers can have **hundreds of thousands to millions of files** — manually setting permissions on each is **infeasible** for human users.
- **Solution:** each user establishes a **default permission** used for **every file they create**. Linux's `open()` call lets you specify **initial** permissions for a newly created file; the `umask()` call further controls default permissions for **all** files subsequently created by that process.

### 10.2 The Power (and Danger) of Good Defaults
- Owners **CAN** alter the default ACL afterward — but **experience shows they rarely do**.
- **Lesson:** properly chosen **defaults** matter enormously — a theoretically-tunable setting will, **in practice**, be left **unaltered** by almost everyone, almost always. This principle recurs throughout OS design.
- **Caution urged:** if you don't know what you're doing, **fiddling with access controls** could either **expose sensitive information** (too loose) or **break background daemon processes** (too tight).

### 10.3 Aside: The Android Access Control Model

- **Context:** Android devices run many small, independent, downloaded **apps**, each belonging to a **single device owner** — so there's no "multiple human users sharing one machine" problem in the classical sense.
- **The real challenge:** apps come from **many different developers** (some potentially **malicious**), and most apps have **no legitimate need** for most device resources. Granting too much access risks a malicious app reading **contacts**, making **calls**, or making **purchases**, among other harms.
- **The least privilege principle applies directly here:** don't give apps the owner's full privileges — but they need **some** privileges to be useful at all.

#### The Mechanism
- Android runs atop a version of **Linux**; part of the isolation comes from generating a **new Linux user ID for each installed app.**
- **Additional Android-specific layer — Permission Labels:**
    - Developers **declare** what accesses their app requires.
    - Users are **shown these permissions** when considering installation, and can **grant, decline (don't install), or (in some versions) limit** them (though limiting may reduce app functionality).
    - Developers also specify how **other apps** may communicate with theirs.
    - **Permission labels are set at app-design time**, and **encoded into the device at install time.**

#### Why Permission Labels Are Like Capabilities
- **Possession** of a label lets the app do something; **lack** of it prevents that action — directly analogous to capabilities.
- The set is fixed **statically at install** (user can subsequently adjust, at the cost of possible reduced functionality).
- **Permission labels are a form of mandatory access control** (the system, not the app, ultimately enforces the boundary).
- **Further reading:** Enck et al. [E+09] cover the Android security model in detail.

#### A Known Weakness
- **Users often don't fully grasp the implications** of granting a permission, and — faced with "grant it or don't use the app" — **tend to grant it anyway**. This can be **problematic** if the app turns out to be malicious.

### 10.4 Software Installers and Access Control
- Even though most users never touch access controls directly, **many software installers** take **real care** in setting appropriate permissions on the **executables and configuration files** they create.

### 10.5 The Organizational Roles Problem
- Large institutions found that **standard per-user access control** doesn't map well onto **organizational structure**: e.g., in a hospital, **doctors** need privileges **pharmacists** don't, and vice versa.
- **Better approach:** organize access control around **ROLES**, then assign users to the roles they're permitted to perform.
- **Especially valuable** when users **switch roles** over time — you don't need to fiddle with individual permissions on the fly; you just **switch their role**.
- Typically, a user holds a role's permissions **only while occupying that role**; exiting the role **loses** those privileges.

---

## 11. Role-Based Access Control (RBAC)

### 11.1 Origin
- Core ideas existed informally for a while; **formally laid out by Ferraiolo and Kuhn [FK92].** Now **widely used**, particularly in **large organizations**, where RBAC's group-operation efficiency significantly **eases management burden**.

### 11.2 Example — Programmers vs. Accountants
- **Grant a new library to all programmers, not accountants:** with RBAC, this is **ONE operation** — assign the privilege to the **Programmer role**.
- **If a programmer is promoted to management** (where library access is unneeded): simply **remove** the Programmer role from that person's available roles.

### 11.3 Why This Isn't (Just) About Distrust
> This restriction does **NOT** necessarily imply you suspect accountants of dishonesty. Recall the **principle of least privilege**: granting access relies not just on someone's **honesty**, but on their **caution**. If accountants **can't** access the library at all, **neither malice NOR carelessness** on their part can leak your library code. **Least privilege is a vital, practical part of building secure real-world systems — not just a theoretical nicety.**

### 11.4 RBAC vs. Simple ACL Groups — Key Differences

- **RBAC is more powerful than mere ACL groups.** Crucially, **RBAC allows a single user to hold MULTIPLE, DISJOINT roles**, switching between them as needed.

#### Worked Example — The Programmer-Turned-Manager
- Our promoted programmer, now a manager, **occasionally needs library access again** (e.g., to test a team member's code).
- **RBAC lets this person SWITCH** between the **Manager** role and the **Programmer** role.
- **While in Programmer mode** (testing code): has **library access**, but **NOT** access to **team member performance reviews**.
- **This prevents unintended leaks:** if the tested code were **maliciously crafted** (e.g., trying to sneak a read of performance review data or salaries), the manager, while acting in the **Programmer** role, **would not have permission** to access that review data — so the malicious code **couldn't succeed**, precisely because the **correct role was active at the correct time.**

### 11.5 Mechanics of Role Switching
- Systems often require a **new authentication step** to assume an RBAC role.
- Typically, taking on **Role A** requires **relinquishing** privileges tied to a **previous** Role B.
- **In the example:** switching to the code-testing (Programmer) role means **temporarily giving up** performance-review access; switching **back** to Manager restores review access but **removes** library access again.

### 11.6 Finer Granularity — Type Enforcement
- RBAC can offer granularity **finer** than simple read/write.
- **Example:** a **Salesperson** role might be permitted to **add a purchase record** for a product, but **NOT** a **re-stocking record** for that same product/file (since salespeople don't do restocking).
- This fine-grained approach is called **type enforcement**, associating detailed rules with objects via a **security context**. *(How exactly this is implemented has real implications for performance, storage, and authentication design.)*

---

## 12. Privilege Escalation — setuid and sudo

### 12.1 Building a Minimal RBAC With Existing Unix Tools
- You can build a **minimal RBAC-like system** under Linux using **ACLs and groups**, combined with a feature called **privilege escalation.**
- **Privilege escalation:** careful, controlled extension of privileges — typically letting a **program run with privileges beyond** those of the user who invoked it.

### 12.2 `setuid`
- **In Unix/Linux:** this mechanism is called **`setuid`** — it lets a program run with the privileges of a **different** user (usually one with **more** privilege than the invoking user).
- **Crucially:** these elevated privileges are **only granted during the program's run**, and are **lost when it exits.**
- **A carefully written `setuid` program** performs only a **LIMITED set of operations** using its elevated privileges, preventing abuse.
- **Building a simple RBAC this way:** define an **artificial user per role**, associate desired privileges with that user, and mark relevant programs as **`setuid`** to that role-user.

### 12.3 `sudo`
- The Linux `sudo` command (previously encountered in the authentication chapter) provides this kind of functionality directly:
```
sudo -u Programmer install_newprogram
```
- Runs the install command **as user `Programmer`**, rather than as the actual invoking user — **provided** the invoking user is on a **system-maintained list** of people allowed to assume the `Programmer` identity.
- **Secure use requires careful configuration** of which users may run which programs under which identities.
- **`sudo` typically requires a fresh authentication step**, just like other RBAC role-switches.

### 12.4 Tip: Privilege Escalation Considered Dangerous

> **The double-edged nature of privilege escalation.**

- **The benign use:** temporarily granting extra privilege for legitimate, controlled purposes (as above).
- **The dangerous flip side:** when an attacker **compromises** a program running under **very limited** privileges (e.g., one that just serves a few informational files, with **no write access**), you might think the damage is contained.
- **BUT:** attackers who gain **any** foothold will **immediately look for ways to escalate their privileges** — since even an "unprivileged" compromised application can do things an **outside** attacker cannot do directly, giving them a launchpad to search for **exploitable flaws** in code/configuration reachable from that foothold.
- **The ultimate prize: the `root`/superuser identity.** This special user has **far more privilege** than any other, existing specifically to allow **critical, far-reaching system administration**.
- **An attacker who achieves root has TOTAL control:** can read any file, alter any program, change any configuration, even **install a different OS**.
- **Lesson:** be **extremely careful** about allowing **any** path that could permit privilege escalation up to superuser/root.

### 12.5 More Advanced RBAC
- Beyond `setuid`/`sudo`, dedicated RBAC systems (as part of the OS, an add-on, or a programming environment) typically support **finer granularity** and **more careful role-assignment tracking.**
- **RBAC is often paired with MANDATORY access control:** otherwise, in the `sudo`/`Programmer` example, a user running **as** Programmer could potentially **change file permissions themselves** to make the install command available to non-programmers, undermining the whole point. **With MAC in place**, a user could **assume** the Programmer role for the installation, but **could NOT use that role** to grant salespeople/accountants the same ability.

---

## 13. Summary

- Implementing most security policies requires **controlling which users can access which resources in which ways** — this is **access control**, delivered by OS mechanisms.
- **A good mechanism provides (close to) complete mediation**, via a carefully designed and implemented **reference monitor**.
- **The two fundamental mechanisms: ACLs and capabilities.**
    - **ACLs:** specify precisely which subjects may access which objects, in which ways — presence/absence on the list determines the outcome.
    - **Capabilities:** work like keys — **possession** of the correct capability is **sufficient proof** access should be permitted.
- **User-visible mechanisms** more commonly use **ACLs**; capabilities are often built in **under the covers**, at a level below what users directly see.
- **Neither mechanism is inherently better** — each has properties well-suited to **some** situations, poorly suited to **others**. Understanding when to use which is the real skill.
- **Access control can be discretionary or mandatory** (some systems use **both**). **Enhancements** like **type enforcement** and **Role-Based Access Control** make it easier to implement complex, real-world security policies.
- **A crucial final caveat:** even a **perfectly correct and efficient** access control mechanism can only ever implement the **policies it's given.** *Security failures due to faulty access control MECHANISMS are rare. Security failures due to poorly designed POLICIES implemented by those mechanisms are NOT.*

---

## 14. Glossary of Key Terms

| Term | Definition |
|---|---|
| **Access control** | The process of determining whether a specific access request fits within security policy. |
| **Subject** | The entity (user or process) wanting to perform an access. |
| **Object** | The resource (file, device, etc.) being accessed. |
| **Access (mode)** | The particular kind of interaction requested (read, write, execute, etc.). |
| **Authorization** | Determining if a subject may perform a specific mode of access on a specific object. |
| **Reference monitor** | The code implementing the access-control-decision algorithm. |
| **Access Control List (ACL)** | A list, associated with an object, specifying which subjects may access it and how. |
| **Capability** | An unforgeable token (data) whose possession proves the holder may access a specific resource in a specific way. |
| **Revocation** | Taking away previously granted access — easy with ACLs, potentially hard with capabilities. |
| **Discretionary Access Control (DAC)** | Access decisions left to the discretion of the resource's owner. |
| **Mandatory Access Control (MAC)** | Access decisions (at least in part) dictated by a central authority, overriding the owner's wishes. |
| **Name space** | The set of names in use within a system, and the rules governing their assignment/uniqueness. |
| **Permission label (Android)** | A capability-like, install-time-fixed declaration of what an app can access and offer to other apps. |
| **Role-Based Access Control (RBAC)** | Assigning privileges to roles, then assigning users to roles, allowing group-level and multi-role privilege management. |
| **Type enforcement** | Fine-grained access rules tied to a specific object via a security context, beyond simple read/write. |
| **Security context** | The detailed access-rule metadata associated with an object under type enforcement. |
| **Privilege escalation** | A controlled mechanism allowing a program to temporarily run with more privilege than its invoker. |
| **setuid** | The Unix/Linux mechanism letting a program run under a different (often more privileged) user's identity, only for its execution. |
| **sudo** | A command allowing authorized users to run programs under a different identity, typically after re-authenticating. |
| **Superuser / root** | The maximally privileged user on a Unix/Linux system, the ultimate target of privilege-escalation attacks. |

---

## 15. Summary Tables

### 15.1 ACLs vs. Capabilities — Head-to-Head

| Question | ACLs | Capabilities |
|---|---|---|
| Who can access object X? | Easy — check X's ACL | Hard — must check every principal's list |
| What can principal P access? | Hard — must check every ACL in the system | Easy — check P's capability list |
| Revocation | Easy — edit the ACL | Easy if OS-controlled; hard if copies exist outside OS control |
| Cost of checking | Can require searching a list (mitigated by Unix's 9-bit trick) | Can be very cheap if held in memory (e.g., pointer presence check) |
| Restricting a child's privileges to a subset | Hard — usually requires new principal + ACL edits | Easy — just transfer the desired subset of capabilities |
| Typical real-world visibility | User-visible (e.g., Unix file permissions) | Usually under the covers (e.g., Linux's open-file structures) |
| Forgery risk | N/A (identity checked via authentication, not "guessing" a list entry) | A core challenge — capabilities are just bits and must be made unforgeable |

### 15.2 Unix 9-Bit Permission Scheme

| Group | Bits | Identity Source |
|---|---|---|
| Owner | rwx (3 bits) | Stored directly in the inode |
| Group | rwx (3 bits) | Group ID stored in the inode |
| Others | rwx (3 bits) | Implicit — the complement of owner and group |

### 15.3 Discretionary vs. Mandatory Access Control

| | Discretionary (DAC) | Mandatory (MAC) |
|---|---|---|
| Who decides? | The resource's owner | A central authority |
| Can the owner override the authority? | N/A (owner IS the authority) | No — authority can override the owner |
| Typical use case | Everyday personal files, most consumer systems | Military/classified information, high-security environments |
| Can they combine? | N/A | Yes — MAC systems can include DAC elements that further restrict (never loosen) |

### 15.4 RBAC vs. Simple ACL Groups

| | ACL Groups | RBAC |
|---|---|---|
| Assign privilege to many users at once | Yes | Yes |
| A user can hold multiple, disjoint roles, switching between them | No (typically) | Yes |
| Fine-grained, object-specific rules (type enforcement) | Not typically | Yes, in advanced RBAC systems |
| Requires re-authentication to switch context | No | Often yes |

---

## 16. Annotated Reference List

| Citation | Work | Relevance |
|---|---|---|
| **[C+63]** | "The Compatible Time Sharing System" — Corbato et al. (1963) | Documents CTSS's early, more limited condensed-ACL approach to disk data protection — a precursor to Unix's scheme. |
| **[DV64]** | "Programming Semantics for Multiprogrammed Computations" — Dennis & van Horn (1966) | The earliest discussion of using capabilities for computer access control, itself inspired by the Burroughs B5000's "program reference table." |
| **[E+09]** | "Understanding Android Security" — Enck, Ongtang, McDaniel | Detailed treatment of the Android security/permission-label model discussed in Section 10.3. |
| **[FK92]** | "Role-Based Access Controls" — Ferraiolo & Kuhn (1992) | The paper commonly credited as the first formal articulation of RBAC as a concept with defined properties. |
| **[L84]** | "Capability-Based Computer Systems" — Henry Levy (1984) | A full book-length treatment of capability use in early hardware and software (including Hydra). |
| **[L01]** | "Integrating Flexible Support for Security Policies Into the Linux Operating System" — Peter Loscocco (2001) | Describes the NSA's mandatory-access-control-enabled version of Linux — a recommended starting point for learning about MAC in practice. |
| **[S74]** | "Protection and Control of Information Sharing in Multics" — Jerome Saltzer (1974) | Introduced the general use of groups within access control lists, later adopted (in simplified form) by Unix. |
| **[T84]** | "Reflections on Trusting Trust" — Ken Thompson (1984) | Thompson's famous Turing Award lecture on hidden backdoors — referenced humorously regarding his own "scampish" reputation. |
| **[W+74]** | "Hydra: The Kernel of a Multiprocessor Operating System" — Wulf et al. (1974) | Describes Hydra, an OS making extensive, sophisticated use of capabilities for access control. |

---

## 17. Big-Picture Takeaways

1. **Access control is where abstract security policy meets concrete, actionable software decisions** — every request boils down to a binary yes/no, made by a reference monitor that must be both correct and efficient.
2. **Virtualization offers a valuable shortcut**, letting the OS skip per-access checks for resources that inherently and unchangeably belong to one subject (like virtual memory) — but this shortcut only pushes the real protection problem down into securing the virtualization machinery itself, not eliminating it.
3. **ACLs and capabilities are ancient, general concepts (the bouncer's list vs. the lock and key), not computer-science inventions** — and their complementary strengths and weaknesses (easy "who can access X" vs. easy "what can X access") mean most real systems, like Linux, end up using BOTH together rather than choosing just one.
4. **Unix's famous 9-bit permission scheme is a masterclass in constrained engineering** — turning a severe hardware limitation (9 bits) into an elegant, still-relevant-today solution by cleverly restructuring the problem (owner/group/other) rather than brute-forcing a per-user list.
5. **Capabilities' core security challenge is that they're "just bits"** — anyone can forge them and copy them freely, unless the OS mediates every capability operation, which is precisely how real systems make capabilities practically unforgeable and revocable.
6. **The discretionary/mandatory distinction is orthogonal to the ACL/capability distinction** — you can combine any access-control data structure with either a "the owner decides" or "a central authority overrides the owner" policy model, and high-security contexts (like military classification) genuinely need the latter.
7. **Role-Based Access Control solves a real organizational problem that plain ACL groups can't**: letting a single person hold multiple, mutually exclusive roles and switch between them, ensuring that privileges from one role never accidentally leak into actions taken under another.
8. **Privilege escalation is a double-edged sword** — the very mechanism (setuid/sudo) that enables clean, controlled RBAC-like systems is also attackers' primary target once they gain any foothold, since reaching root/superuser grants total, unrestricted control of the machine.
9. **No access control mechanism, however perfectly implemented, can compensate for a poorly designed security policy** — mechanism and policy remain separate concerns, and real-world security failures trace far more often to policy mistakes than to flaws in the underlying ACL or capability machinery itself.