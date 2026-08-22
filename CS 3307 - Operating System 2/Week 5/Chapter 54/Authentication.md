# Authentication 

*(Based on Chapter 54: "Authentication" by Peter Reiher, UCLA)*

---

## Table of Contents

1. Introduction — Principals, Agents, Objects, and Credentials
2. The Crux — How to Securely Identify Processes
3. Attaching Identities to Processes
4. How to Authenticate Users? (The Three Classical Approaches)
5. Authentication by What You Know (Passwords)
6. Authentication by What You Have
7. Authentication by What You Are (Biometrics)
8. Authenticating Non-Humans
9. Other Authentication Possibilities (Location, Behavior)
10. Summary
11. Glossary of Key Terms
12. Summary Tables
13. Annotated Reference List
14. Big-Picture Takeaways

---

## 1. Introduction — Principals, Agents, Objects, and Credentials

### 1.1 The Core Problem
- The OS needs to enforce a wide range of security goals/policies — but this requires the OS to make **context-dependent** decisions about whether to perform a requested service.
- **The single most important element of that context: WHO is doing the asking.**

### 1.2 Real-World Analogy
- If your **significant other** asks you to pick up milk on the way home, you'll probably do it. If a **stranger** asks the same thing, you probably won't.
- **OS analogy:** if the **system administrator** asks the OS to install a new program, it probably should. If a script **downloaded from a random webpage** asks the same thing, the OS should be **much more careful**.

### 1.3 Key Terminology

| Term | Definition |
|---|---|
| **Principal** | A security-meaningful entity that can request access to resources — e.g., a human user, a group of users, or a complex software system. |
| **Agent** | The process (or other active computing entity) that performs a request **on behalf of** a principal. |
| **Object** | The particular resource being requested access to (a file, an IPC channel, etc. — *not* "object-oriented" in this context). |
| **Credential** | Data created/managed by the OS that records an access decision for future reference (e.g., a page table entry recording that a process may access certain pages). |

### 1.4 Working Backwards: How Does the OS Know Who's Asking?

- OS services are most commonly requested via **system calls**, which **trap** from user code into the OS.
- The OS gains control and can consult the **calling process's OS-controlled data structure** (e.g., its **Process Control Block**) to determine the process's **identity**.
- Based on that identity, the OS can make a **policy-based decision** on whether to perform the requested operation.

### 1.5 Credentials — Remembering Past Decisions
- **If the OS has ALREADY determined** that an agent process can access a given object, it can simply **remember** that decision — tracked in a per-process data structure (like the PCB).
- **Example already seen:** page tables are exactly this kind of credential — they record which pages/frames a process may access **at any given time**.
- **If the OS has NOT already produced such a credential**, it needs information about the **principal's identity** to decide whether to grant the request.

### 1.6 Types of Principal Identity
Different OSes use different notions of identity:
1. **User identity** — typically a human being. (All processes run by a given person often share this identity; the concept of "user" has expanded over time, as covered later.)
2. **Group identity** — a group of users. **Example:** salespeople might all get access to inventory data, while HR staff don't need it. *(This directly illustrates the principle of least privilege from the prior chapter — a rogue HR employee can't order the warehouse emptied if HR was never granted that right.)*
3. **Program identity** — the identity of the **program** a process is running (not the human running it). **Example:** Android grants certain privileges to **specific programs**; whenever they run, they can use these privileges — other programs cannot.

### 1.7 Why Getting This Right Is Crucial
- **The attachment of identity to process is itself a critical security issue.**
- **Consequences of getting it wrong:**
    - Misidentifying a **programmer's** process as an **accounting department** process → potential empty bank account (and the need to hire a new programmer!).
    - Failing to correctly identify the **company president** during an investor presentation → the system might block the president from data they legitimately need, with career-ending consequences for whoever is blamed.
- **The silver lining:** since **everything** except the OS's own activities happens via **some process**, if we get process identity right, we have the opportunity to **check policy on every important action**.

### 1.8 A Crucial Characteristic: "Sticky" Authentication
> **Once a principal has been authenticated, systems will almost ALWAYS rely on that authentication decision for at least the lifetime of the process.**

- **This puts a HIGH PREMIUM on getting authentication right the first time** — mistakes won't be readily corrected later, since the system trusts the initial decision for the process's entire life.

---

## 2. The Crux — How to Securely Identify Processes

> **CRUX:** For systems that support processes belonging to multiple principals, how can we be sure that each process has the correct identity attached? As new processes are created, how can we be sure the new process has the correct identity? How can we be sure that malicious entities cannot improperly change the identity of a process?

---

## 3. Attaching Identities to Processes

### 3.1 The Simple Case — Inheritance
- **Where do processes come from?** Usually, other processes create them.
- **Simplest identity-attachment method: the child inherits the parent's identity.**
- **Mechanically:** when the OS services a call from process A to create process B (e.g., `fork`), it:
    1. Consults A's PCB to determine A's identity.
    2. Creates a new PCB for B.
    3. **Copies in** A's identity.

### 3.2 The Limitation of Pure Inheritance
- If **all** processes always shared the **same single identity** (e.g., a special system identity assigned to a "primal" process created at boot, with all others as its descendants), we **couldn't** implement any policy that differentiates privileges between processes.
- **We must arrange for SOME processes to have DIFFERENT identities**, and use those differences to drive security policy.

### 3.3 Multi-User Systems — Identity by Human User
- On a multi-user system, we can assign process identities based on **which human user** they belong to.
- If security policies are primarily about "some people can do X, others can't," this gives us the mechanism to make those decisions.

### 3.4 Setting the User ID for New Processes

- **The user's typical "home base" process:** the **shell** (command-line systems) or the **window manager** (windowed systems) — both of which are, notably, **themselves processes**.
- When you type a command in a shell, or double-click an icon in a windowing system, you're asking the OS to **start a new process under your identity**.

### 3.5 The Recursive Question — How Did the Shell Get YOUR Identity?

- **Answer:** OS **privilege**. When a user first starts interacting with the system, the OS can **start a process for that user**, and — since the OS can freely manipulate its own data structures (like the PCB) — it can **set the new process's ownership** to the user who just logged in.

### 3.6 The Root Question — How Did the OS Determine the User's Identity?

- **Answer: the user LOGGED IN** — providing identity information to the OS to **prove** who they are.
- **New requirement identified:** the OS must be able to **query identity** from human users and **verify** they are who they claim — so we can attach **reliable** identities to processes, so we can use those identities to implement security policy.
- *(As the chapter notes: "One thing tends to lead to another in operating systems.")*

---

## 4. How to Authenticate Users? (The Three Classical Approaches)

### 4.1 The Two Sub-Problems
1. If the person is **not** an authorized user at all, **reject** the attempt entirely.
2. If they **are** authorized, determine **which** one.

### 4.2 The Three Classical Categories

> These are called "classical" in the **truly classical** sense — going back to the **ancient Greeks and Romans**.

1. **Authentication based on what you KNOW.**
2. **Authentication based on what you HAVE.**
3. **Authentication based on what you ARE.**

### 4.3 Ancient Historical Examples

| Era/Source | Example | Category |
|---|---|---|
| **Polybius, 2nd century BC [P-46]** | The Roman army used **"watchwords"** to distinguish friends from foes. | What you **know** |
| **Celer the Architect, ~100 AD [C100]** | A surviving letter of recommendation for a slave, presented to an imperial procurator. | What you **have** |
| **Book of Judges, ~5th century BC [JB-500]** | The Gileadites required refugees to say **"shibboleth"** — the Ephraimites (enemies) couldn't pronounce it correctly. | What you **are** (native dialect speaker) |

- *(A grim historical note: failing this ancient "biometric" test had severe consequences — the text notes some 42,000 Ephraimites were reportedly slain per the Book of Judges for mispronouncing the word.)*

---

## 5. Authentication by What You Know (Passwords)

### 5.1 Basic Concept
- **Most commonly implemented via passwords.**
- **Long (and largely inglorious) history** — dating back at least to **MIT's CTSS system** in the **early 1960s** [MT79].
- **Definition:** a password is a **secret known only to the party being authenticated**. By divulging it at login, the party proves their identity.

### 5.2 Effectiveness Depends On Several Assumptions
1. **Other people don't know the password** (if they do, the system is fooled).
2. **No one else can guess it.**
3. **The legitimate party knows (and remembers) it.**

### 5.3 Problem 1 — Other People Knowing the Password

- **Fewer parties who need to know it = fewer things to worry about.**
- The person being authenticated **must** know it (that's the whole point).
- We'd prefer **NO third parties** know it — including: don't write it on paper (anyone who steals the paper now has it).
- **BUT there's one more party who seems to need to know it: the SYSTEM ITSELF** — creating another vulnerability, since the **system's stored copy** might leak.

> **Historical note:** the first known incident of stored passwords leaking dates to **1962** [MT79]; such leaks continue "to this day with depressing regularity and much larger scope" (e.g., a 2016 leak of **over 100 million** plaintext-stored passwords [KA16]).

### 5.4 Tip: Avoid Storing Secrets
- Storing secrets (plaintext passwords, crypto keys) is hazardous — they **usually leak out eventually**.
- **Best practice:** don't store secrets if you don't need to. If you must, store them **hashed** (with a strong cryptographic hash), or failing that, **encrypted**.
- Store them in **as few places/copies** as possible — **remember temporary editor files, backups, and logs** too, since secrets can linger there.
- **Never** embed secrets in an executable given to others (it won't stay secret) — and be aware that even secrets kept only in a **running program's heap** have been divulged in some cases.

### 5.5 The Clever Insight — The System Doesn't Need to Know the Password!

> **Key realization:** the system is checking whether the user **knows** the password, not what the password actually **is**.

- **Solution:** store a **HASH** of the password, not the password itself.
- **At login:** hash the user's claimed password, and compare it to the **stored hash**. Match → believe they know it. No match → don't.
- **Benefit:** the system never needs to store the actual password — so if the stored (hashed) data leaks, the attacker **still doesn't have the actual passwords**.
- **Why this is safe:** hashing algorithms **can't be reversed** — an attacker with just the hash can't recover the password. And if the attacker tries to submit the **hash itself** as a login attempt, the system will **hash that hash**, which won't match the stored (single-hashed) value.

### 5.6 Cryptographic Hashes — Not Just Any Hash

- Storing a **different** value than the password isn't quite enough — we also need the stored value to give an attacker **NO help in guessing** the actual password.
- **Cryptographic hashes:** a special class of hash functions designed so it's **infeasible** to work backward from the hash to the input, other than by actually **guessing** the input and hashing it to check.
- **Cryptographic hashes are notoriously HARD TO DESIGN** — even smart people shouldn't try to invent their own. **Use ones designed by experts.**
- **Current standard (at time of writing):** **SHA-3** [B+09] is the US standard and a good choice.

### 5.7 Problem 2 — Guessing

#### The Length Factor
- **1-bit password:** attacker has a **50/50** chance on the first guess (and is guaranteed correct within 2 guesses).
- **8-bit password:** 256 possible values; attacker needs, on average, **128 guesses**.
- **General principle: the longer the password, the harder to guess.**

#### The Character-Set Factor
- Early passwords were often restricted to **letters of the alphabet only** — easier to type/remember, but this **drastically reduced** the number of possible bit patterns an attacker needed to try.
- **Over time**, systems expanded to allow **upper/lower case, numbers, and special characters** — **more possibilities = harder to guess.**

#### The Human Factor — Dictionary Attacks
- **Problem:** people don't choose truly random character strings — they pick **names or familiar words** (easy to remember).
- **Dictionary attack:** attackers try lists of names/words/common patterns (e.g., "123456") **before** trying random strings — ordered by **probability of being chosen** as a real password.
- **Effectiveness:** a good dictionary attack can crack **90% of passwords** for a typical site [G13].

#### Defense — Rate Limiting
- A well-designed system should **prevent remote dictionary attacks** by:
    - **Shutting off access** to an account after too many wrong guesses, OR (better):
    - **Drastically slowing down** password checking after a few wrong guesses — making a long dictionary attack take an **infeasible amount of time**.
- **There is no good reason** a system should allow, say, 15,000 unthrottled remote guesses at a single account's password.

### 5.8 Aside: Password Vaults

- A **password vault / key chain**: an **encrypted file** on your computer storing your passwords, itself protected by **one master password**.
- **Benefit:** reduces "remember a different password for every site" down to "remember one password" — and an attacker needs **both** the vault's master password **and** access to the vault file itself.
- **Limitation:** security is still bounded by the **strength of the passwords stored inside** — guessing/dictionary attacks against those still work if they're weak.
- Some vaults **generate strong (unmemorable) passwords for you** — fine, since **the vault**, not you, needs to remember them.
- **Cloud-based vaults:** if you give the cloud service **cleartext** passwords to store, you're sharing a secret with an entity that doesn't need it (unnecessary risk). If the cloud only ever sees **encrypted** passwords, the risk is much lower.

### 5.9 What If the Attacker Steals the Whole Password File?

- Assuming you've been paying attention: the file contains **hashes**, not passwords.
- **BUT:** if you used a **widely known** cryptographic hash algorithm (likely, since you should use a well-vetted one), the **attacker knows it too**.
- Given the hashed passwords + the hashing algorithm + a dictionary + compute power, the attacker can **grind away** at guessing offline, at their leisure.
- **Worse:** if **everyone** uses the same hash algorithm (likely in practice), the attacker can **pre-compute** the hash of every dictionary word **just once**, creating a **hashed dictionary** — then simply **string-compare** it against your stolen file — much faster than hashing fresh guesses for every single stolen password.

### 5.10 The Fix — Salting

- **Technique:** before hashing a new password, generate a **large random number** (e.g., 32 or 64 bits) — the **salt**. **Concatenate** it to the password, **then** hash the result and store that.
- You must **also store the salt itself** (typically right next to the hashed password in the password file), since verifying a login attempt requires **redoing** the same concatenation + hash.
- **Origin:** this concept was introduced in **Morris and Thompson's** early password security paper [MT79].

#### Why Salting Helps
- Without salting, the attacker needs only **ONE** translation table (dictionary word → hash) that works against **every** stolen password.
- **With salting**, the attacker needs a **SEPARATE** translation for **every possible salt value** — since stolen password files will typically have a **different salt per password**.
- **With a 32-bit salt:** that's **2³²** different translations needed **per dictionary word** — making **pre-computation** completely infeasible.
- Instead, for each stolen entry, the attacker must **freshly hash each guess** combined with **that specific entry's salt** — the attack remains **feasible** against badly-chosen passwords, but is **far more expensive**.

> **Bottom line:** any good password-based system that cares about security stores **cryptographically hashed AND salted** passwords. If yours doesn't, you're putting your users at risk.

### 5.11 Beyond Passwords — Multi-Factor Authentication

- There is a **widely held belief** in the security community that **passwords alone are a technology of the past**, no longer sufficiently secure on their own.
- **Best practice today:** use passwords as just **one of several** authentication mechanisms used **together** — **multi-factor authentication** (with **two-factor** being the most publicized version).
- **Familiar example:** an ATM requires your **PIN** (essentially a password) **plus** proof you physically **have** your ATM card.

---

## 6. Authentication by What You Have

### 6.1 Real-World Analogies
- ID cards you show to get in somewhere; **tickets** for event admission.

### 6.2 The Computer Analog

- **Special-purpose hardware (e.g., ATM card readers):** can **directly** verify possession of a specific physical item.
- **Most general-purpose devices** (desktops, laptops, tablets, phones) **lack** such special hardware.

### 6.3 Hardware Tokens With Direct Interfaces
- Devices that **plug into a port** (e.g., USB security tokens/"dongles") — with suitable software support, the OS can directly verify the device's presence.

### 6.4 Indirect Verification — Human-Relayed Codes
- Some **smart tokens** display a number/character string on a tiny built-in screen; the human **manually types** this into the computer.
- **The OS doesn't get DIRECT proof** of possession — but if **only** someone with the device could know the currently-displayed code, this is **nearly as good** as direct proof.
- **Why these codes must change frequently** (e.g., every few seconds, or every authentication attempt): if the code were **static**, anyone who **learned** it once wouldn't need the device anymore — the mechanism would effectively **degrade** from "what you have" into "what you know," with security now depending on how hard the (now-static) secret is to learn/guess.

### 6.5 Aside: Linux Login Procedure (Detailed Walkthrough)

A concrete example of how a real OS handles authentication end-to-end:

1. A **privileged login process** displays a prompt for a **username**; the user types it, and it is **echoed** to the terminal.
2. The login process prompts for the **password**; the user types it, and it is **NOT echoed**.
3. The login process **looks up** the username in the password file:
    - **Not found** → reject the login attempt.
    - **Found** → determine the internal **user ID**, **group ID**, the **initial shell**, and the **home directory** for this user; also retrieve the stored **salt** and **salted, hashed password**.
4. **Combine** the stored salt with the user-provided password, **hash** the result, and **compare** to the stored hash:
    - **No match** → reject the login.
5. **If they match:** **fork** a new process; set its **user and group** to the values determined in step 3 (the privileged login process is permitted to do this); **change directory** to the home directory; **exec** the appropriate shell.

```
[Username prompt] → user types username (ECHOED)
        │
        ▼
[Password prompt] → user types password (NOT echoed)
        │
        ▼
Look up username in password file
        │
   ┌────┴────┐
NOT FOUND   FOUND → retrieve UID, GID, shell, home dir, salt, stored hash
   │             │
   ▼             ▼
REJECT      Combine salt + provided password → hash → compare to stored hash
                  │
             ┌────┴────┐
          NO MATCH   MATCH
             │             │
             ▼             ▼
          REJECT      fork() new process, set UID/GID,
                       cd to home dir, exec shell
```

#### Why Failures Aren't Distinguished
- **Note:** login can fail either because the **username doesn't exist**, or because the **password doesn't match**. Linux (and most systems) **deliberately don't reveal which** condition failed.
- **Why:** this prevents attackers from **learning valid usernames** just by observing which guesses produce a "wrong password" vs. "no such user" response. **Not giving useful information to non-authenticated users** is a generally good security principle, applicable beyond just login.

#### A Thought Question Posed by the Chapter
> Why does Linux **echo** the typed username but **not** the password? Is there any security disadvantage to echoing the username? Is it *necessary* to echo it, or is it a trade-off of security for convenience? Why not echo the password?

- *(This is posed as a reflection question in the source material — worth considering: echoing the username helps the user **catch typos** conveniently, and usernames are typically **not secret** on their own (many systems' usernames are semi-public or guessable), so echoing them trades a small amount of information exposure for usability. The password, by contrast, IS the secret itself — echoing it would let anyone looking over the user's shoulder, or at the screen, read it directly.)*

### 6.6 The Core Weakness of "What You Have" — Losing It

- **What if you don't have it?** Left your phone at home, dongle fell out of your pocket, a pickpocket took your device.
- **Two-fold problem:**
    1. **You** can't authenticate — the computer doesn't care that you're pleading with it; it wants the "magic item."
    2. **Someone else** now HAS your item, and may be able to **impersonate you**.
- **Multi-factor authentication helps here too:** if a thief steals your token but **doesn't know your password**, they still can't fully impersonate you (barring writing your password on the stolen device itself — "it seemed like a good idea at the time...").

### 6.7 Academic Skepticism vs. Real-World Practicality — SMS-Based Authentication

- There's often a **gap** between what security academics warn against and what works acceptably in the real world — partly because the real world must balance security against **user convenience**, and partly because security researchers tend to flag **any** conceivable weakness, even impractical ones.
- **Example: SMS-based two-factor authentication** (a code texted to your phone).
    - **In theory**, this sounds weak: phones get lost, and researchers can imagine **exotic attacks** where the text message gets **redirected** to the attacker's phone.
    - **In practice:** people usually **keep their phones close** and notice quickly if lost, taking swift corrective action — so the **window of vulnerability** after a loss tends to be small. **Redirecting SMS messages**, while possible, is **far from trivial**, and usually **not worth the effort** for an attacker relative to the payoff.
    - **Lesson:** a mechanism that makes security purists recoil can still provide **quite reasonable security** in practice.
    - **However:** in **2016**, the **US NIST** issued draft guidance **deprecating** SMS-based two-factor authentication in some circumstances — illustrating that **"what works today might not work tomorrow."**

---

## 7. Authentication by What You Are (Biometrics)

### 7.1 The Basic Appeal
- Humans have **unique physical characteristics** (from DNA to facial appearance) and **unique behavioral characteristics** — if the OS can **accurately measure** these, it can distinguish individuals.
- **Attractive to many people — "most especially to those who have never tried to make it work."** It's workable, but **not perfect**, with its own set of challenges.

### 7.2 Example — Facial Recognition on a Phone

#### Challenges in Capturing the Data
- What if it's **not** the real owner holding the phone? What if they **resemble** the owner? Wear a **mask**? Hold up a **photo**?
- What about **dim lighting**, or the person **not facing the camera** directly?
- Even for the **legitimate** user: lighting, angle, or appearance changes (e.g., a new hairstyle) could throw off recognition.

#### How Computers Actually "See" a Face
- A computer program doesn't recognize faces like a person does — it converts the photo into **zeros and ones** (shadow/light data, color shades, contrasts, etc.) and processes it algorithmically.

### 7.3 Why You Can't Just Compare Bit-for-Bit (Like a Password Hash)

- **Passwords:** exact string match (or hash match) works, because the same password typed twice produces **identical** bits.
- **Biometrics:** two photos of the **same person**, taken **a second apart** under **identical** conditions, are **extremely unlikely** to produce the exact same bit pattern (lighting, angle, tiny movements all vary).
- **Conclusion: bit-for-bit comparison won't work for biometrics.**

### 7.4 The Real Approach — Feature Extraction and "Close Enough" Matching

- Instead, extract **higher-level features** from both the stored (reference) and just-captured biometric samples — e.g., nose length, eye color, mouth shape model — and **compare those feature sets**.
- **Even here**, an **exact** match is unlikely (e.g., lighting might slightly shift perceived eye color) — so the system must allow **some tolerance/"closeness"** in the comparison.

### 7.5 False Positives and False Negatives

| Term | Definition |
|---|---|
| **False Negative** | The system **incorrectly rejects** the legitimate user (too strict a match requirement). |
| **False Positive** | The system **incorrectly accepts** an impostor (too lenient a match requirement). |

- **Sensitivity** describes **how close** the match must be to count as a match.
- **The fundamental trade-off:** as the **false positive rate goes DOWN**, the **false negative rate tends to go UP**, and vice versa — you generally **can't minimize both simultaneously** for a given biometric implementation.

### 7.6 The Crossover Error Rate (CER)

```
Error Rate
    │╲                    ╱
    │ ╲                  ╱
    │  ╲   False        ╱
    │   ╲  Negative    ╱ False
    │    ╲  Rate      ╱  Positive
    │     ╲          ╱   Rate
    │      ●────────╱  ← Crossover point
    │     ╱ ╲
    │    ╱   ╲
    └──────────────────────────── Sensitivity
```

- The point where the **False Positive Rate** and **False Negative Rate** curves **cross** is called the **Crossover Error Rate (CER)** — a common metric summarizing a biometric system's **overall accuracy**, representing an **equal trade-off** between the two error types.
- **You don't always want to tune to the crossover point** — sometimes one error type matters far more than the other:
    - **Smart phone unlock:** frequent false negatives (locking out the legit owner over a bad fingerprint reading) are **very unpopular**, while a thief having a **similar** fingerprint is **rare** — so tolerating **more false positives** (fewer false negatives) may be preferred.
    - **Bank vault retinal scan:** occasionally requiring the manager to **re-scan** (false negative) is a minor inconvenience, but letting a robber in with a **fake eye** (false positive) is a **disaster** — so **minimizing false positives** matters more here, even at the cost of more false negatives.

### 7.7 Practical Limitations of Biometrics

#### Hardware Availability
- Many biometric techniques need **special hardware** not present on all machines.
- **Cameras:** common on phones/tablets/laptops, but **not** on embedded devices or servers.
- **Fingerprint readers:** relatively uncommon; even **rarer** hardware exists for more exotic biometrics.
- A **few** techniques (e.g., **typing pattern** analysis) need only commonly-available hardware (keyboards) — but there aren't many such techniques.
- Even when special hardware **is** available, **convenience** of using it can still be a limiting factor.

#### The Remote-Verification Danger
- **Key question:** is there a **physical gap** between where the biometric is **measured** and where it's **checked**?
- **Danger:** checking a biometric reading sent **across a network from an untrusted machine** is hazardous — anything arriving over the network is **just a pattern of bits**, and **anyone can construct any bit pattern they want**.
- **If a remote adversary knows** what bit pattern represents your fingerprint scan, they may **not need your actual finger or a scanner at all** — they can just **fabricate and send** that bit pattern directly.
- **When the scanning hardware is physically attached** to the verifying machine, there's **much less opportunity** to inject a spurious bit pattern that didn't actually come from the device. **The farther away and less controlled** the scanning hardware is, the **more opportunity** for this kind of spoofing.
- **Takeaway:** be **very careful** with biometric authentication data supplied to you **remotely**.

### 7.8 The Right Lesson to Take Away

> **The lesson is NOT that biometrics (or passwords, or tokens) are "pretty terrible."** No authentication method is perfect. Your job as a system designer is **not** to find a perfect mechanism, but to **choose mechanisms well-suited to your system and its environment** — and to **combine** mechanisms so that where one fails, another (which doesn't fail in the same circumstances) can compensate.

- **Examples of "good enough":** a solid built-in fingerprint reader; a long, unguessable password; a well-designed smart card that's nearly impossible to authenticate without holding.

---

## 8. Authenticating Non-Humans

### 8.1 The Problem
- Many running processes have **no associated human user** — e.g., a **web server** process, or code running an embedded **smart light bulb**.

### 8.2 The Mechanical Solution
- Simply create a **"user"** in the system for the non-human entity (e.g., `webserver`, `lightbulb`), and attach that identity to the relevant processes.
- **The real question:** how do we ensure **ONLY genuine** web-server-related processes get tagged with that identity — we don't want an arbitrary user on the machine spinning up processes that **falsely claim** to be the server.

### 8.3 Approaches to Assigning Non-Human Identities

1. **Password-protected non-human user:** assign a password to the `webserver` user; a privileged administrator logs in **as** that user (or otherwise authenticates) to spawn its processes, which then **inherit** that identity via the normal parent→child inheritance rule.
2. **Direct privileged creation (skip the login "go-between"):** provide a mechanism whereby a **privileged user** can directly create processes belonging to a **different** identity (e.g., `webserver`), without an intermediate login step.
3. **Ownership-change mechanism:** allow a process to **change its own ownership** — e.g., start under the administrator's identity, then **switch** to `webserver`.
4. **Temporary identity change** (remembering the original): allows a **temporary** switch, while still tracking the true underlying identity — *(explored further in a later chapter on access control).*

> **Important caveat:** any of these mechanisms **requires strong controls**, since they inherently allow one user to create processes belonging to **another** user — a powerful, and therefore dangerous, capability if misused.

### 8.4 The `sudo` Mechanism (Linux/Unix Example)

- **Most commonly, passwords are used** to authenticate the assignment of processes to non-human users — but **sometimes no separate authentication of the non-human identity is needed at all**.
- Instead, **trusted users** (e.g., system administrators) are simply **granted the right** to assign new identities to processes they create, using **only their own** authentication credentials — **no further proof** related to the target identity is required.
- **Example command:**
```
sudo -u webserver apache2
```
- This starts the `apache2` program under the **`webserver`** identity, rather than under the identity of whoever ran the `sudo` command.
- **Note:** `sudo` may still require the **invoking user** to authenticate (e.g., re-enter their own password) — for **extra assurance** that it's really the privileged user asking, and not, say, a random visitor who wandered up to the admin's unlocked, unattended computer during a coffee break.
- **Inheritance still applies:** any sub-processes spawned by `apache2` inherit the `webserver` identity from their parent, exactly as with human-owned processes.

---

## 9. Other Authentication Possibilities (Location, Behavior, Groups)

### 9.1 Aside: Other Authentication Approaches

#### Authentication by Location
- **DMV analogy:** you trust that the person behind the DMV counter is a legitimate employee **not** because you know them personally, showed ID, or heard a "secret DMV mantra" — but simply because they're **standing behind the counter that DMV employees stand behind**. This is **authentication based on location**.
- **Computer systems use case:** occasionally handy, most often in **mobile/pervasive computing**.
- **Caution:** if tempted to use this, think **very carefully** about how you actually obtain reliable evidence of the subject's true location — this is **"actually fairly tricky"** to get right, since location claims themselves can potentially be spoofed.

#### Authentication by What You Do (Behavior)
- Sometimes you can authenticate based on **characteristic behavior** — e.g., **typing patterns**, or delays between commands. *(This is technically a form of biometric.)* **Example:** Google introduced this kind of multi-factor authentication in Android phones.
- **A subtler variant:** sometimes you don't care **exactly who** someone is, but only whether they belong to the set of **"Well Behaved Users."** Many websites, for example, care less about visitor identity and more about whether visitors **use the site properly** — authenticating membership in a **behavioral class**, via ongoing interaction patterns, rather than pinning down a precise individual identity.

### 9.2 Group Identity — A Second Path to Access

- Sometimes we want to identify not individual users, but **GROUPS** of users sharing common (typically security-relevant) characteristics.
- **Example:** four or five system administrators, any of whom should be allowed to start the web server. Rather than granting the privilege to **each individually**, create a **group** with that privilege, and make the relevant admins **members**.
- **Groups are themselves a form of security-relevant principal** — decisions get made on the basis of **group membership**, rather than individual identity.
- **Two implementation approaches:**
    1. **Associate a group membership directly with each process.**
    2. **Use the process's individual identity as an index** into a **separate list** of the groups that person belongs to. — **More flexible**, since it lets each user belong to an **arbitrary number** of groups.
- **Widely supported:** most modern OSes (Linux, Windows) support groups, handling group membership/privileges largely **analogously** to individual identities — e.g., a **child process typically inherits its parent's group-related privileges** too, just like it inherits its user identity.
- **Important caveat:** group membership provides a **SECOND PATH** by which a user can gain access to a resource — this has **both benefits** (flexibility, ease of administration) **and dangers** (an extra avenue that must also be carefully controlled).

---

## 10. Summary

- To apply security policies to process actions, the OS must **know the identity** of the acting process, to make correct decisions.
- **The chain starts at boot:** a system user process is created whose purpose is to **authenticate users**. Users **log in**, providing authentication evidence; the system **verifies** it and assigns the verified identity to a **new process**, which the user then works through (typically spawning further processes).
- **Those further processes INHERIT the user's identity** from their parent, by default.
- **Special, tightly-controlled mechanisms** can allow a process's identity to be **changed**, or explicitly **set** to something other than its parent's identity (relevant for non-human users, `sudo`, etc.).
- **This lets the system be confident** that processes belong to the proper user/principal, and make security decisions **accordingly**.

### 10.1 The Three Classical Authentication Factors, Recapped

| Factor | Basis | Strengths | Weaknesses |
|---|---|---|---|
| **What you know** | Passwords, PINs | Cheap, familiar, no extra hardware | Guessable, phishable, can be forgotten, leak-prone if poorly stored |
| **What you have** | Tokens, smart cards, phones | Physical possession required | Can be lost/stolen; needs hardware or human-relay for verification |
| **What you are** | Biometrics (fingerprint, face, etc.) | Hard to "lose" or forget; unique per person | Imprecise matching (false pos/neg trade-off), needs special hardware, dangerous over untrusted networks |

### 10.2 The Case for Multi-Factor Authentication
- **A higher degree of security** comes from **combining** factors — e.g., a password **plus** a one-time code texted to your phone — since an attacker generally must defeat **more than one** independent mechanism simultaneously.

---

## 11. Glossary of Key Terms

| Term | Definition |
|---|---|
| **Principal** | A security-meaningful entity that can request resource access (human user, group, or software system). |
| **Agent** | The process/entity performing a request on behalf of a principal. |
| **Object** | The specific resource being requested (file, IPC channel, etc.). |
| **Credential** | OS-managed data recording a prior access decision, for future reference. |
| **Authentication** | The process of verifying that a claimed identity is genuine. |
| **Password** | A secret known only to the party being authenticated, used to prove identity. |
| **Cryptographic hash** | A hash function designed so that recovering the input from the output is computationally infeasible except by guess-and-check. |
| **Salt** | A random value concatenated to a password before hashing, to defeat precomputed dictionary attacks. |
| **Dictionary attack** | Guessing passwords using an ordered list of common words, names, and patterns, rather than random strings. |
| **Password vault / key chain** | An encrypted file storing multiple passwords, itself protected by one master password. |
| **Multi-factor authentication** | Requiring evidence from more than one authentication category (know/have/are) to verify identity. |
| **Security token / dongle** | A physical hardware device used for "what you have" authentication. |
| **Biometric** | A measurable physical or behavioral human characteristic used for authentication. |
| **False negative (biometrics)** | Incorrectly rejecting a legitimate user. |
| **False positive (biometrics)** | Incorrectly accepting an impostor. |
| **Sensitivity** | How close a biometric match must be to be accepted. |
| **Crossover Error Rate (CER)** | The point where false positive and false negative rates are equal; a standard biometric accuracy metric. |
| **sudo** | A Unix/Linux command allowing an authorized user to run a process under a different identity. |
| **Group (identity)** | A named set of users sharing security-relevant characteristics/privileges, itself a security principal. |
| **Inheritance (of identity)** | The default rule that a child process receives the same identity/privileges as its parent. |

---

## 12. Summary Tables

### 12.1 The Three Historical/Classical Authentication Categories

| Category | Ancient Example | Modern Example |
|---|---|---|
| What you know | Roman army watchwords [P-46] | Passwords, PINs |
| What you have | Slave's letter of recommendation [C100] | Smart cards, USB tokens, phones |
| What you are | The "shibboleth" pronunciation test [JB-500] | Fingerprints, facial recognition |

### 12.2 Password Security Layers

| Layer | Protects Against |
|---|---|
| Storing a hash (not the raw password) | Stolen password file directly revealing passwords |
| Using a strong cryptographic hash | Reversing the hash to derive the password |
| Adding a salt | Precomputed dictionary attacks across many stolen accounts at once |
| Rate limiting / lockout after failed attempts | Remote, unthrottled online guessing |
| Long passwords, varied character sets | Brute-force and simple dictionary guessing |

### 12.3 Biometric Trade-off Table

| Scenario | Which Error Matters More? | Why |
|---|---|---|
| Smartphone fingerprint unlock | Minimize false negatives | Owner inconvenience is common; imposter risk is low |
| Bank vault retinal scan | Minimize false positives | A false accept (robbery) is catastrophic; a false reject is just an inconvenience |

### 12.4 Ways to Assign Identity to Non-Human Processes

| Method | How It Works |
|---|---|
| Password-protected non-human user | Admin logs in as e.g. `webserver`, spawns processes that inherit that identity |
| Privileged direct creation | A privileged user directly creates processes under another identity |
| Ownership-change mechanism | A process starts under one identity, then switches to another |
| Temporary identity change | Process temporarily assumes another identity while its true identity is remembered |
| `sudo` | Trusted user authenticates as themselves, is granted the right to spawn processes as another user |

---

## 13. Annotated Reference List

| Citation | Work | Relevance |
|---|---|---|
| **[P-46]** | "The Histories" — Polybius (~146 BC) | Describes Roman army watchwords and their distribution — an ancient "what you know" authentication example, still illustrating the critical challenge of securely distributing shared secrets. |
| **[C100]** | Letter of recommendation for a slave — Celer the Architect (~100 AD) | A surviving ancient example of "what you have" authentication (a physical letter serving as proof of identity/status). |
| **[JB-500]** | Book of Judges 12:5-6, The Bible (~5th century BC) | The "shibboleth" story — an early, severe example of "what you are" (native-speaker) authentication. |
| **[MT79]** | "Password Security: A Case History" — Robert Morris & Ken Thompson (1979) | The seminal paper on early Unix password practices; introduced password **salting**, and documents early (1962-era) password leak incidents from CTSS. |
| **[B+09]** | "The road from Panama to Keccak via RadioGatun" — Bertoni, Daemen, Peeters, Van Assche | By the designers of **SHA-3**, the current (at time of writing) US standard cryptographic hash, recommended for password hashing. |
| **[G13]** | "Anatomy of a hack..." — Dan Goodin (2013) | Documents experts using dictionary attacks to crack ~90% of real passwords from a site — empirical evidence for dictionary attacks' effectiveness. |
| **[KA16]** | "VK.com Hacked! 100 Million Clear Text Passwords Leaked" — Swati Khandelwal (2016) | A concrete, large-scale example of the real-world danger of storing passwords in plaintext. |
| **[M+02]** | "Impact of Artificial 'Gummy' Fingers on Fingerprint Systems" — Matsumoto et al. (2002) | Demonstrated how easily commercial fingerprint scanners could be fooled with fake ("gummy") fingerprints — a classic illustration of biometric authentication's practical weaknesses. |
| **[TR78]** | "On the Extraordinary" — Marcello Truzzi (1978) | Referenced for its general epistemic stance (investigate claims rather than dismiss them outright) — relevant to the chapter's broader point about evaluating authentication mechanisms on their real-world merits, not just theoretical objections. |

---

## 14. Big-Picture Takeaways

1. **Authentication is fundamentally about attaching a reliable identity to a process** — since virtually all OS security policy enforcement ultimately reduces to "is this identified process allowed to do this?"
2. **Identity attachment starts with inheritance (child processes inherit their parent's identity), tracing back to a privileged process created at boot specifically to authenticate users** — meaning the very first authentication decision the system makes propagates its trust down an entire tree of descendant processes.
3. **Because authentication decisions are "sticky" for a process's entire lifetime, getting it right the first time carries an unusually high premium** — there's little opportunity to correct a mistaken identity assignment later.
4. **The three classical authentication factors — what you know, what you have, what you are — are genuinely ancient concepts**, not modern computing inventions, and each has enduring, characteristic strengths and weaknesses that no amount of new technology fully eliminates.
5. **Password security is a layered defense**: hashing (so the system needn't store the secret itself), cryptographic hashing specifically (so the hash can't be reverse-engineered), salting (so stolen files can't be cracked via precomputed dictionaries), and rate-limiting (so remote guessing is slow) — removing any one layer meaningfully weakens the whole scheme.
6. **Biometric authentication requires fundamentally different comparison logic than passwords** — because biometric readings are never bit-for-bit identical between samples, systems must tolerate "closeness," creating an unavoidable false-positive/false-negative trade-off that must be tuned to the specific application's risk profile.
7. **No single authentication mechanism is "the best" in the abstract — the right choice depends entirely on the system, its environment, and which failure mode (false accept vs. false reject, lost token vs. guessed password) is more costly in that specific context.** Multi-factor authentication is valuable precisely because it lets one mechanism's weaknesses be covered by another's strengths.
8. **Non-human processes (servers, embedded devices) still need identities for security policy to apply to them** — handled via dedicated system users, controlled identity-assignment mechanisms, and tools like `sudo` — with group identities providing a further, valuable but risk-bearing "second path" to access alongside individual identity.
9. **Practical security requires weighing real-world usability and attacker cost-benefit, not just theoretical worst-case vulnerabilities** — as illustrated by SMS-based two-factor authentication being simultaneously "theoretically weak" and "practically quite reasonable," at least until circumstances (and official guidance) change.