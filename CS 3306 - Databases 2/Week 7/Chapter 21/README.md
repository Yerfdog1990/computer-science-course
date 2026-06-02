# Chapter 21 — Application Development and Administration
## Study Guide with Detailed Examples

This bundle contains seven detailed Markdown files covering the full content of Chapter 21. Each file has been expanded with **concrete examples, code snippets, diagrams, and comparison tables** to supplement the original lecture material.

---

## File Index

| File | Section | Topics Covered |
|---|---|---|
| `21.1_Motivation_and_Overview.md` | 21.1 | RAD tools, web paradigm, 6 core architectural challenges |
| `21.2_Web_Interfaces_and_Architectures.md` | 21.2 | 2-tier vs 3-tier, HTML forms, HTTP, CGI, Servlets, JSP, cookies, session management |
| `21.3_Performance_Tuning.md` | 21.3 | Bottleneck identification, buffer pools, RAID, denormalization, partitioning, indexes, query optimization |
| `21.4_Performance_Benchmarks.md` | 21.4 | TPC consortium, TPC-A/B/C/D/H/R, tpmC metric, price-performance ratio, Full Disclosure Reports |
| `21.5_Standardization_Efforts.md` | 21.5 | ODBC, JDBC driver types, PreparedStatement, ResultSet cursors, ORM/JPA/Hibernate, N+1 problem |
| `21.6_Electronic_Commerce.md` | 21.6 | Catalog personalization, EAV vs JSONB, auction concurrency (OCC), payment ACID + 2PC, tokenization, PCI DSS |
| `21.7_Legacy_Systems_and_Migration.md` | 21.7 | Hierarchical/network DBs, API wrapper strategy, CDC replication, ETL pipeline, phased vs big bang cutover |

---

## Key Themes Across the Chapter

### Abstraction and Decoupling
Every major topic in this chapter is fundamentally about **separating concerns**:
- Three-tier architecture separates presentation, business logic, and data storage
- ODBC/JDBC separate application code from database wire protocols
- ORM separates object-oriented application models from relational schemas
- API wrappers separate modern applications from legacy system internals

### The Performance-Correctness Trade-Off
Many design decisions in this chapter involve trading one property for another:
- **Normalization** (correct, slow reads) vs **Denormalization** (fast reads, harder to keep correct)
- **Pessimistic locking** (guaranteed correctness, poor concurrency) vs **Optimistic concurrency control** (high concurrency, retry complexity)
- **ORM** (fast development, less SQL control) vs **Raw JDBC** (full control, verbose code)

### Security at Every Layer
- **HTTP layer**: TLS encryption, HTTPS enforcement
- **Application layer**: Session management (HttpOnly cookies), input validation
- **Database layer**: PreparedStatements prevent SQL injection
- **Data layer**: AES-256 encryption at rest, tokenization for PCI scope reduction

---

## Quick Reference: Important Metrics and Rules

| Rule / Metric | Value | Applies To |
|---|---|---|
| Five-Minute Rule | Cache if accessed ≥ once per 5 min | Standard data blocks in buffer pool |
| One-Minute Rule | Cache if accessed ≥ once per 1 min | Large/multimedia data blocks |
| tpmC | New-Order transactions per minute | TPC-C OLTP benchmark metric |
| Price/tpmC | Total 3-year cost ÷ tpmC | TPC price-performance comparison |
| TPC FDR | Full Disclosure Report required | All official TPC published results |
| 2PC | Two-Phase Commit protocol | Distributed financial transactions |
| ACID | Atomicity, Consistency, Isolation, Durability | All financial payment operations |
| PCI DSS | Payment Card Industry Data Security Standard | Any system storing card data |

---

## Recommended Study Order

For exam preparation, read sections in this order:

1. **21.1** — Big picture overview (read first, short)
2. **21.2** — Web architecture fundamentals (foundational for everything else)
3. **21.5** — JDBC/ODBC/ORM (most technically detailed; allow extra time)
4. **21.3** — Performance tuning (practical, heavy on examples)
5. **21.4** — Benchmarks (conceptual; understand tpmC and FDR)
6. **21.6** — E-commerce (applied integration of prior sections)
7. **21.7** — Legacy migration (read last; builds on all prior concepts)
