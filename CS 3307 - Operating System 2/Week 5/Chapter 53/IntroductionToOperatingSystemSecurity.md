# Introduction to Operating System Security 
*(Based on Chapter 53: "Introduction to Operating System Security" by Peter Reiher, UCLA)*

---

## Table of Contents

1. Introduction — Why OS Security Matters So Much
2. What Are We Protecting? (The Scope of OS Power)
3. Security Goals and Policies (Confidentiality, Integrity, Availability, and More)
4. Designing Secure Systems — Saltzer & Schroeder's Principles
5. The Basics of OS Security (Mechanisms in Practice)
6. Summary
7. Glossary of Key Terms
8. Summary Tables
9. Annotated Reference List
10. Big-Picture Takeaways

---

## 1. Introduction — Why OS Security Matters So Much

### 1.1 The Stakes
- Security of computing systems is a **vital and ever-growing** topic — **much money has been lost** and **many people's lives harmed** when computer security has failed.
- Attacks on computer systems are so common as to be **essentially inevitable** in almost any computing scenario.
- **In principle**, any element of a computer system can be attacked, and flaws **anywhere** can give an attacker an opening. But **operating systems are especially critical** from a security standpoint.

### 1.2 Reason 1 — Everything Runs ON TOP of the OS

> **Rule:** if the software underneath you (OS, middleware, etc.) is insecure, everything built on top of it is *also* insecure.

- **Analogy:** building a house on sand — you may construct a solid house, but a flood can wash away the foundation, destroying the house **despite** the care taken in building it.
- **Application to OS security:** your application might have **zero security flaws of its own**, but if an attacker can misuse the **OS underneath** to steal your data, crash your program, or otherwise harm you, your own careful coding **doesn't matter**.
- **Why this hits OSes especially hard:** you might not care about the security of a specific web server or database you don't run — but **everyone** runs **some** operating system, and there are only a **handful** of choices. So a flaw in a **widely-used OS** has an **immense** blast radius, affecting huge numbers of users and pieces of software.

### 1.3 Reason 2 — The OS Has Ultimate Control of the Hardware

- **All software** ultimately depends on the underlying **hardware** (processor, memory, peripherals) behaving properly.
- **Question:** what has **ultimate control** of that hardware? **The operating system.**
- **Thought experiment:** think back to memory management, scheduling, file systems, synchronization — what happens to **each** of these if an adversary could force it to behave **arbitrarily badly**? The text is blunt: *if you understand what you've learned so far, you should find this prospect **deeply disturbing**.*
- **Bottom line:** our entire computing lives depend on the OS behaving **as defined** — and specifically, **not** behaving in ways that benefit **adversaries** instead of us.

### 1.4 Why Securing an OS Is Especially Hard

#### Challenge 1 — Size and Complexity
- **General software security principle:** more code + more complex algorithms = **more likely to contain flaws** (and security failures generally **stem from** such flaws).
- **Large, complex programs are harder to secure than small, simple ones.**
- **Not many programs are as large and complex as a modern operating system** — making OS security an especially tough problem.

#### Challenge 2 — Supporting Multiple, Mutually-Distrusting Processes
- OSes are meant to support **multiple processes simultaneously**, with mechanisms to **segregate** processes and protect shared hardware from cross-process interference.
- **If every process could be fully trusted**, security would be much easier.
- **BUT we don't trust everything equally.** Concrete example: when you run an **unfamiliar downloaded script**, do you want it to be able to **wipe your disk**, **kill your other processes**, and **use your network to send spam**? Probably not — but as the **owner** of the machine, **you** have the right to do all of those things yourself.
- **The catch:** unless the OS is careful, **any process it runs** — including that downloaded script — can potentially do **anything the owner can do**.

### 1.5 The Abstraction-Reliability Perspective

- One key OS role: provide **useful abstractions** for applications to build on (e.g., the **file system**, **virtual memory**, **scheduling**).
- **Applications rely on these abstractions behaving as defined** — including their **security behavior**. E.g., we expect the file system to **enforce** the access restrictions it claims to enforce; an application might rely on "this file is unwriteable" to guarantee a record isn't altered.
- **If the OS's implementation of these abstractions can't be trusted:**
    - Applications **cannot** build their own security guarantees on top of them.
    - At minimum, this means **much more work** for application developers, who must take **extra measures** themselves.
    - In the worst case, applications may be **simply unable** to achieve their security goals if the underlying abstractions (virtual memory, scheduling policy, etc.) **cannot be trusted**.

### 1.6 The Crux
> **How to Secure OS Resources:** In the face of multiple, possibly concurrent and interacting processes running on the same machine, how can we ensure that the resources each process is permitted to access are **exactly** those it should access, in **exactly** the ways we desire? What primitives are needed from the OS? What mechanisms should be provided by the hardware? How can we use them to solve the problems of security?

---

## 2. What Are We Protecting? (The Scope of OS Power)

### 2.1 The Short (Uncomfortable) Answer: EVERYTHING

- To achieve good protection, we need a comprehensive view of what we're protecting. **At the high level, the answer is: everything.**
- **A typical commodity OS has COMPLETE control of (almost) all hardware on the machine** — it can do **literally anything** the hardware permits: control the processor, read/write all registers, examine any main memory location, and perform any operation any peripheral supports.

### 2.2 The Disturbing List of What the OS CAN Do

Because of this total control, the OS is capable of:
- **Examining or altering** any process's memory.
- **Reading, writing, deleting, or corrupting** any file on any writable persistent storage (disks, flash drives).
- **Changing the scheduling** or **halting execution** of any process.
- **Sending any message anywhere** — including **altered versions** of messages a process wished to send.
- **Enabling or disabling** any peripheral device.
- **Giving any process access** to any other process's resources.
- **Arbitrarily taking away** any resource a process controls.
- **Responding to any system call with a maximally harmful lie.**

> **Conclusion:** processes are essentially **at the mercy of the operating system**. It is nearly impossible for a process to "protect" any part of itself from a **malicious** OS.

### 2.3 The Practical Implication

- We typically **assume** the OS is not actually malicious (footnote: *"If you suspect your operating system is malicious, it's time to get a new operating system."*).
- **BUT:** a flaw that lets a **malicious process** cause the OS to **misbehave** is **nearly as bad** — because it could grant that process the **full powers of the OS itself**.
- **This is why**: designing secure OSes, and **especially** applying security patches promptly, is **vitally important**. A security flaw in the OS can **completely compromise everything** on the machine.

### 2.4 Aside: Security Enclaves

- The OS controls "**almost all**" hardware — prompting the question: what **doesn't** it control?
- **Historically**, it really was **all** the hardware. Starting in the **1990s**, hardware developers began isolating **some** hardware, to a degree, from the OS.
- **First such hardware: TPM (Trusted Platform Module)** — provided assurance you were booting the **intended** version of the OS, protecting against attacks that try to boot **compromised** versions.
- **More recently:** general hardware elements try to control what can be done with **particularly sensitive data** (often **cryptography-related**) — these are called **security enclaves**, since they aim to allow only **safe use** of this data, **even by the OS itself** (the most powerful, trusted code in the system).
- **Common use case:** cloud computing environments, where **multiple OSes** run under virtual machines **sharing the same physical hardware**.
- **Reality check:** this is **harder than anyone expected** ("security tricks usually are"). Security enclaves **often don't provide quite as much isolation** as their designers hoped. But attacks on them tend to be **sophisticated and difficult**, typically requiring the ability to **already run privileged code** on the system — so even an imperfect enclave adds a **real, extra protective barrier**.

---

## 3. Security Goals and Policies

### 3.1 The Vague Starting Point
- "We want the OS to be secure" is a **vague** statement.
- **What we really mean:** there are things we want to happen, things we don't want to happen, and we want **high assurance** we get what we want.
- **Economic framing:** as in most of life, we usually pay for what we get — so it's worth identifying **exactly** which security properties we actually **need**, and paying only for **those** (not extras we don't need).

### 3.2 The Three Classic High-Level Security Goals

#### (a) Confidentiality
> If some information is supposed to be **hidden** from others, don't allow them to find it out.

- **Example:** you don't want someone learning your **credit card number** — it should stay **confidential**.

#### (b) Integrity
> If some information/component is supposed to be in a **particular state**, don't allow an adversary to **change** it.

- **Example:** you order **one pepperoni pizza** online — you don't want a malicious prankster changing it to **1000 anchovy pizzas**.
- **Important sub-aspect: Authenticity** — ensuring not just that information **hasn't changed**, but that it was **created by a specific party**, and not by an adversary impersonating them.

#### (c) Availability
> If information/a service is supposed to be **available** for use, ensure an attacker **cannot prevent** its use.

- **Example:** during your business's big sale, you don't want a competitor **blocking the streets** around your store, preventing customers from reaching you.

### 3.3 An Extra Dimension: Controlled Sharing

- All three goals above have an important extra dimension: we want **controlled sharing**.
    - We share secrets with **some** people, not others.
    - We let **some** people modify our databases, not just anyone.
    - Some systems should be available to a **specific set** of preferred users (e.g., paying customers), not everyone.
- **"Who's doing the asking" matters a lot** — in computers, as in everyday life.

### 3.4 Non-Repudiation

- Another important security aspect: ensuring that when someone tells us something, **they cannot later deny having done so** — this is **non-repudiation**.
- **Rationale:** the **harder and more expensive** it is to repudiate an action, the **easier** it is to hold someone **accountable**, and thus the **less likely** people are to act maliciously (since they might well get caught, and can't easily deny it).

### 3.5 From Big Goals to Concrete OS-Specific Goals

The three big goals need to be **drilled down** into specifics for a real system. Examples in a typical OS:

- **Confidentiality goal:** a process's memory space **cannot be arbitrarily read** by another process.
- **Integrity goal:** if a user writes a record to a file, another user who **shouldn't** be able to write it **can't change** the record.
- **Availability goal:** one process **cannot hog the CPU**, preventing other processes from getting their fair share.

### 3.6 From Concrete Goals to Fully Specific Policies

- Even goals at the level above **aren't specific enough** for a real system.
- **Example nuance:** maybe the integrity goal ("user's file shouldn't be overwritten by unauthorized users") should have an **exception** — e.g., two people **collaborating** on a report might both need write access — but an **unrelated third user** (not collaborating) should **still** be blocked.
- **We need this level of detail in our security goals.** OSes serve many different people with many different needs — OS security mechanisms must be **flexible** enough to describe such detailed goals.

### 3.7 Encoding Goals as Policies

- Ultimately, the OS software must **enforce** these flexible goals — meaning we need to encode them into forms **software can understand**: **security policies**.
- **Example policy:** *"Users A and B may write to file X, but no other user can write it."*
- With this level of specificity, backed by carefully designed/implemented **mechanisms**, we can hope to actually **achieve** our security goals.

### 3.8 A Key Implication: Mechanism vs. Policy

> In many cases, the OS **has** the mechanisms necessary to implement a desired policy with high assurance — **but only if someone tells the OS precisely what that policy is.**

- **With some important exceptions** (e.g., keeping a process's address space private **by default**, unless directed otherwise), the OS mostly supplies **general mechanisms** that can implement **many** different specific policies.
- **Without intelligent policy design and careful mechanism application**, what the OS **should or could** do may **not** be what it **actually will** do.

### 3.9 Aside: Security vs. Fault Tolerance

- Recall from the process abstraction discussion: **virtualization** protects a process from **accidental** interference by other processes (e.g., your memory shouldn't be accidentally overwritten by another process).
- **Question:** is this **actually different** from worrying about **malicious** behavior (the usual security context)? Have we already solved security via virtualization?
- **Answer: "Yes and no."**
    - **Yes**, if virtualization were **perfect** and allowed **zero** interaction between anything, we'd likely have solved most problems of **malice** too.
    - **BUT:** (1) most virtualization mechanisms **aren't totally bulletproof** — they work well against **accidents**, but not necessarily against **deliberate** attempts to subvert them; (2) we **don't actually want** total isolation — processes **share** OS resources by default (e.g., file systems) and can **optionally** share more. These **intentional relaxations** of virtualization aren't problematic when used properly, but they **do** open potential channels for **malicious** attacks; (3) the OS **doesn't always have complete control** of the hardware (see Security Enclaves, §2.4).

---

## 4. Designing Secure Systems — Saltzer & Schroeder's Principles

### 4.1 Origin
- Few readers will build their own OS, but many will build **large software systems**. Experience has distilled certain **design principles** that help build systems with security requirements.
- **Originally laid out by Jerome Saltzer and Michael Schroeder** in an influential 1975 paper [SS75] (some principles trace to even earlier observations by others).
- **Important caveat:** following these principles does **NOT guarantee** security — but **ignoring them is done at your own peril.**

### 4.2 The Eight Principles

#### 1. Economy of Mechanism
> Keep your system **as small and simple as possible.**

- Simple systems have **fewer bugs**, and their behavior is **easier to understand**. If you don't understand your system's behavior, you can't know if it achieves its security goals.

#### 2. Fail-Safe Defaults
> **Default to security, not insecurity.**

- If policies can be configured, the **default** setting should be the **more secure** option, not the less secure one.

#### 3. Complete Mediation
> Check whether an action meets security policy **EVERY SINGLE TIME** the action is taken.

- **Footnote caveat:** this principle is **often ignored** in practice, in favor of **lower overhead or usability** — a reminder that engineering design generally requires **balancing conflicting goals** (as seen earlier in scheduling trade-offs).

#### 4. Open Design
> Assume your adversary **knows every detail** of your design.

- If the system can **still** achieve its security goals even so, you're in good shape.
- **This does NOT necessarily mean** you must publicly disclose every detail — but your **security should not depend on secrecy of the design** (i.e., don't rely on "security through obscurity"). In practice, **attackers often DO learn everything anyway.**

#### 5. Separation of Privilege
> Require **separate parties or credentials** to perform critical actions.

- **Example:** two-factor authentication (password + possession of a hardware device) is **more secure** than either factor alone.

#### 6. Least Privilege
> Give a user or process the **MINIMUM** privileges required to perform the actions you wish to allow.

- **Rationale:** more privileges granted = **greater danger** of abuse. Even a **trusted, non-malicious** party might **make a mistake** — and an adversary can **leverage that mistake** to exploit the party's **superfluous** (unnecessary) privileges in harmful ways.

#### 7. Least Common Mechanism
> For different users/processes, use **SEPARATE** data structures or mechanisms to handle them.

- **Example:** each process gets its **own page table** in a virtual memory system — ensuring one process **cannot access** another's pages (a direct callback to the virtual memory chapter's isolation mechanisms).

#### 8. Acceptability
> A critical property, not always dear to programmers' hearts: **if your users won't use it, your system is worthless.**

- **Many promising secure systems have been abandoned** because they demanded **too much** of their users (excessive friction/inconvenience).

### 4.3 Beyond These Principles
- These principles aren't the **only** useful advice on secure design. There's also substantial material on:
    - **Converting a good design into secure code** (e.g., Seacord [SE13] for C).
    - **Evaluating whether a built system actually meets its security goals** (e.g., Dowd et al. [D+07]).
- These topics are **beyond the scope** of this introductory chapter, but are **extremely important** when it's time to actually build large, complex systems.

---

## 5. The Basics of OS Security (Mechanisms in Practice)

### 5.1 Built-in vs. User/Owner-Controlled Goals

- A typical OS has security goals spanning confidentiality, integrity, and availability.
- **Some goals are BUILT IN** to the OS model — because they're **extremely common**, or **necessary** to make more specific goals achievable at all.
    - Most built-in goals relate to **controlling process access to hardware** — since hardware is **shared** by all processes, and **uncontrolled sharing** would let one process interfere with another's security.
    - Other built-in goals relate to **OS-provided services** (file systems, memory management, IPC) — if these aren't carefully controlled, processes can **subvert** the system's security goals.
- **Other goals are controlled by owners/users** of the system (via configurable policies, as discussed in §3).

### 5.2 Process Handling as the Foundation of Security

> If the OS maintains a **clean separation of processes** that can **only** be broken with the OS's own help, then **neither** shared hardware **nor** OS services can be used to subvert security goals.

- This requires the OS to be **careful about allowing use of hardware and services**.

#### Example — Virtual Memory as a Protection Mechanism
- The OS controls **virtual memory**, which in turn **completely controls** which physical memory addresses each process can access.
- **Hardware support** prevents a process from even **naming** a physical memory address that isn't mapped into its own virtual memory space.
- *(The text notes, with some humor: "the software folks among us should remember to regularly thank the hardware folks for all the great stuff they've given us to work with.")*

### 5.3 System Calls as a Protection Checkpoint

- **System calls** offer the OS another key protection opportunity.
- In most OSes, processes access system services via an **explicit system call** — which **switches execution mode** from **user mode** to **supervisor (kernel) mode**, invoking the appropriate OS code.
- That invoked code can determine:
    1. **Which process** made the call.
    2. **What service** was requested.
- **Beyond just dispatching the right service code and tracking where to return control**, this same mechanism gives the OS the opportunity to **check whether the requested service should be allowed** under the system's security policy.
- **Device access, too:** since peripheral device access happens through **device drivers** (usually also accessed via system call), the **same mechanism** can enforce security policies for **hardware access**.

### 5.4 The Access Control Flow, Step by Step

```
Process performs a system call
              |
              v
Execution mode switches: USER MODE -> SUPERVISOR (KERNEL) MODE
              |
              v
OS uses the process identifier (from the Process Control Block
or similar structure) to determine the CALLING PROCESS's identity
              |
              v
OS applies ACCESS CONTROL mechanisms:
is this process AUTHORIZED to perform the requested action?
              |
       +------+------+
   AUTHORIZED      NOT AUTHORIZED
       |                  |
       v                  v
OS performs the      OS generates an ERROR CODE
action (or lets       for the system call, returns
the process do it     control to the process
without further
intervention)
```

### 5.5 Tip: Be Careful of the Weakest Link

- **Attackers share characteristics with you:** they're probably **smart**, and **"positively lazy"** — they won't do work they don't need to do.
- **Implication:** attackers go for the **easiest possible** way to break your system's security. They won't hunt for a zero-day buffer overflow if your password is literally **"password."**
- **Practical advice:** spend most of your security effort **identifying and strengthening your weakest link** — the least-protected, easiest-to-attack part of your system, the part you **can't** hide away or bolt extra protection onto.
- **Often, the weakest link is the HUMAN USERS**, not the software.
    - You can't easily "fix" human behavior, but you **can** design software **anticipating** that attackers will try to **fool legitimate users** into misusing it.
    - **Connects back to Least Privilege (§4.2, item 6):** if an attacker fools a user with **complete privileges** into misusing the system, the damage is **far worse** than fooling a user who can only damage **their own** assets.
- **General framing:** security thinking is **more adversarial** than most other system design considerations. *(Recommended further reading: Bruce Schneier's "Secrets and Lies" [SC00].)*

---

## 6. Summary

- **OS security is vital** for both the OS's own sake and its applications' sake — security failures allow **essentially limitless bad consequences**.
- Achieving system security is **challenging**, but **known design principles** (Saltzer & Schroeder's eight) help — and these principles are useful **beyond** OS design too, for **any** large software system.
- **Security goals** typically span **confidentiality, integrity, and availability** — but the **specific details** of these goals vary system to system, implying **different security policies** are needed for different systems' specific needs.
- **As with other areas of OS design**, we handle this variation by **separating specific policies** (which vary per-system) from **general mechanisms** (used across all systems to implement whichever policy is chosen).
- **Key mechanism preview:** **virtualization** of processes and memory is one crucial security mechanism, since it lets us **control process behavior** to a large extent — with **several other** important OS security mechanisms to be covered in later chapters.

---

## 7. Glossary of Key Terms

| Term | Definition |
|---|---|
| **Confidentiality** | Ensuring information meant to be hidden is not disclosed to unauthorized parties. |
| **Integrity** | Ensuring information/system state cannot be improperly altered by an adversary. |
| **Authenticity** | A sub-aspect of integrity: confirming information was created by the party it claims to be from. |
| **Availability** | Ensuring information/services remain usable, and an attacker cannot block legitimate access. |
| **Non-repudiation** | Ensuring someone cannot later deny having performed an action they took. |
| **Controlled sharing** | Selectively allowing some parties (not all) access to resources, information, or services. |
| **Security policy** | A precise, software-encodable statement of what specific access/behavior is and isn't allowed. |
| **Security mechanism** | The general-purpose tool/technique the OS provides to implement (potentially many different) policies. |
| **Security enclave** | Hardware that isolates particularly sensitive operations/data even from the OS itself. |
| **Trusted Platform Module (TPM)** | Early security-enclave-like hardware protecting the OS boot process from running compromised versions. |
| **Economy of mechanism** | Design principle: keep systems small and simple to minimize bugs and maximize understandability. |
| **Fail-safe defaults** | Design principle: default configuration should favor security over convenience/insecurity. |
| **Complete mediation** | Design principle: check policy compliance every single time a protected action is attempted. |
| **Open design** | Design principle: assume the adversary knows your full design; don't rely on secrecy for security. |
| **Separation of privilege** | Design principle: require multiple independent credentials/parties for critical actions. |
| **Least privilege** | Design principle: grant only the minimum privileges necessary for a task. |
| **Least common mechanism** | Design principle: use separate data structures/mechanisms per user/process to avoid cross-contamination. |
| **Acceptability** | Design principle: a security system users won't actually use provides no real protection. |
| **User mode / Supervisor (kernel) mode** | The two CPU execution modes; system calls switch from user mode to supervisor mode to perform protected operations. |
| **Access control** | The OS mechanism for deciding whether an identified, requesting process is authorized to perform a given action. |
| **Weakest link** | The least-protected, most easily attacked part of a system — often the human users rather than the software. |

---

## 8. Summary Tables

### 8.1 The Three Core Security Goals

| Goal | Definition | Example |
|---|---|---|
| **Confidentiality** | Hidden info stays hidden | Your credit card number isn't disclosed |
| **Integrity** | Protected state can't be altered by adversaries | Your pizza order isn't changed to 1000 anchovy pizzas |
| **Availability** | Legitimate access can't be blocked by attackers | Competitors can't block customers from reaching your sale |

### 8.2 The Eight Saltzer & Schroeder Design Principles

| # | Principle | One-Line Summary |
|---|---|---|
| 1 | Economy of mechanism | Keep it small and simple |
| 2 | Fail-safe defaults | Default to secure settings |
| 3 | Complete mediation | Check permissions every single time |
| 4 | Open design | Assume the adversary knows everything |
| 5 | Separation of privilege | Require multiple independent credentials |
| 6 | Least privilege | Grant only what's minimally necessary |
| 7 | Least common mechanism | Keep different users'/processes' mechanisms separate |
| 8 | Acceptability | Users must actually be willing to use it |

### 8.3 Goal → Policy → Mechanism Pipeline

| Level | Example | Who Defines It |
|---|---|---|
| **Big, general goal** | Confidentiality | Universal security theory |
| **OS-specific goal** | A process's memory can't be arbitrarily read by another process | OS designers |
| **Specific policy** | "Users A and B may write to file X, no one else can" | System owner/administrator |
| **Mechanism** | Access control checks at system-call time, virtual memory isolation | OS implementation |

### 8.4 What the OS CAN Do (Absent Restraint)

| Category | Examples |
|---|---|
| Memory | Examine or alter any process's memory |
| Storage | Read, write, delete, or corrupt any file |
| Scheduling | Change or halt any process's execution |
| Networking | Send any message anywhere, including altered ones |
| Devices | Enable or disable any peripheral |
| Inter-process | Give any process access to any other's resources; arbitrarily revoke resources |
| System calls | Respond with a "maximally harmful lie" |

---

## 9. Annotated Reference List

| Citation | Work | Relevance |
|---|---|---|
| **[SS75]** | "The Protection of Information in Computer Systems" — Jerome Saltzer & Michael Schroeder (1975) | The highly influential source of the eight secure-design principles covered in §4 — foundational to essentially all subsequent secure systems design thinking. |
| **[SE13]** | "Secure Coding in C and C++" — Robert Seacord (2013) | A well-regarded guide to avoiding major security mistakes specifically when coding in C — recommended for the "next step" beyond design, into secure implementation. |
| **[D+07]** | "The Art of Software Security Assessment" — Mark Dowd, John McDonald, Justin Schuh (2007) | A comprehensive treatment of how to evaluate whether a built software system actually meets its intended security goals; also covers avoiding security problems in coding. |
| **[SC00]** | "Secrets and Lies" — Bruce Schneier (2000) | A well-regarded, book-length, high-level perspective on the challenges of computer security, aimed at moderately technical readers — recommended for developing "adversarial" security thinking. |

---

## 10. Big-Picture Takeaways

1. **OS security matters disproportionately because everything else is built on top of the OS, and the OS has ultimate control of all hardware** — a flaw here doesn't just affect one application, it can compromise the entire machine and everything running on it.
2. **The OS is, definitionally, all-powerful over the machine** — meaning the entire security problem boils down to preventing that power from being **misused**, whether by a compromised OS or by a malicious process that manages to trick the OS into misbehaving on its behalf.
3. **Security goals decompose into confidentiality, integrity, and availability**, each further nuanced by controlled sharing and non-repudiation — but these big, abstract goals must be **drilled down** into system-specific, precisely-specified **policies** before software can actually enforce them.
4. **The mechanism/policy separation is central to OS security design**: the OS provides general, reusable **mechanisms** (like access control checks at system-call time, or virtual memory isolation), while **specific policies** (who can do what) are supplied by system owners/administrators — mirroring the same mechanism/policy split seen in scheduling.
5. **Virtualization (of processes and memory) solves most ACCIDENTAL interference problems, but is not automatically sufficient for security against DELIBERATE, malicious attacks** — because virtualization is rarely "bulletproof," and because we deliberately allow controlled sharing (file systems, IPC) that also opens potential attack channels.
6. **Saltzer and Schroeder's eight design principles remain foundational**, even decades later, and apply far beyond operating systems to any large software system with security requirements — from economy of mechanism and fail-safe defaults, through least privilege and complete mediation, to the often-overlooked but critical principle of acceptability.
7. **System calls are the OS's primary checkpoint for enforcing security policy** — the mode switch from user to supervisor mode gives the OS a natural, unavoidable opportunity to check whether a requesting process is authorized, before performing (or refusing) the requested action.
8. **The weakest link in a system is often its human users, not its code** — meaning good OS security design must anticipate that attackers will target legitimate users through social engineering, not just search for software vulnerabilities, and should minimize the damage any single fooled user can cause (via least privilege) rather than assuming users won't be fooled at all.