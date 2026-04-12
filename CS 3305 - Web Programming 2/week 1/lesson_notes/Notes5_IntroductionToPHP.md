# Introduction to PHP

---

## Table of Contents

1. [What is PHP?](#1-what-is-php)
2. [History and Version Timeline](#2-history-and-version-timeline)
3. [PHP Today — Scale and Adoption](#3-php-today--scale-and-adoption)
4. [Why is PHP Popular?](#4-why-is-php-popular)
5. [How PHP Works — Server-Side Execution](#5-how-php-works--server-side-execution)
6. [How PHP Fits Into the Web Server Architecture](#6-how-php-fits-into-the-web-server-architecture)
7. [Understanding "The Cloud"](#7-understanding-the-cloud)
8. [Setting Up a PHP Development Environment](#8-setting-up-a-php-development-environment)
9. [Summary — Key Concepts at a Glance](#9-summary--key-concepts-at-a-glance)

---

## 1. What is PHP?

**PHP** is a **widely-used, general-purpose scripting language** that is especially suited for **web development**. Its defining characteristic is that it can be **embedded directly into HTML** — meaning PHP instructions can be inserted inside an ordinary HTML document and executed by the server before the page is sent to the browser.

### The name — a recursive acronym

PHP's name has changed throughout its history:

- Originally stood for: **"Personal Home Page Tools"** (version 1, 1995)
- Changed in version 3 to: **"PHP: Hypertext Preprocessor"**
- This is a **recursive acronym** — the abbreviation PHP appears within its own full name

### Core purpose

PHP is designed to allow developers to create **dynamic, interactive websites** that respond to current conditions — for example:

- Displaying a **personalised greeting** using the user's name
- Showing content relevant to the **time of day**
- Loading the **latest blog posts** from a database
- Displaying a user's **shopping cart contents**
- Retrieving and displaying **user-specific data** from any database

Without PHP (or similar server-side languages), all of these would be impossible on a static website.

---

## 2. History and Version Timeline

PHP was created by **Rasmus Lerdorf**, a Danish-Canadian programmer. It began not as a full language but as a practical tool to solve a personal problem — maintaining his own website.

| Year | Version | Key development |
|---|---|---|
| **1995** | PHP 1.0 | Rasmus Lerdorf releases a set of scripts to maintain his personal website, published as **"Personal Home Page Tools (PHP Tools) version 1.0"** on **June 8, 1995** |
| **1997** | PHP 2 | The tools were extended and released as version 2; still called "Personal Home Page" |
| **1998** | PHP 3 | Name changed to the recursive acronym **"PHP: Hypertext Preprocessor"** — a major rewrite making PHP far more powerful and extensible |
| **2000** | PHP 4 | Significant improvements to **performance, reliability, and extensibility**; powered by the **Zend Engine** virtual machine — a major milestone in PHP's maturity |
| **2004** | PHP 5 | Powered by the new **Zend Engine II**; introduced improved object-oriented programming support; produced as **free software** by the PHP Group |
| *(Cancelled)* | PHP 6 | A planned experimental version that intended to introduce **native Unicode support** throughout PHP; ultimately **abandoned** due to technical difficulties |
| **2015** | PHP 7 | A landmark release delivering massive performance improvements; effectively replaced the abandoned PHP 6 |
| **2020** | PHP 8 | Introduced **Just In Time (JIT) compilation** for further improved performance; the current major version |

### Key milestone explained — Zend Engine (PHP 4)

The **Zend Engine** is a virtual machine at the core of PHP. Its introduction in PHP 4 was critical because it:
- Compiled PHP scripts into an intermediate form before execution, making them run much faster
- Improved reliability and memory management
- Made PHP viable for serious, large-scale commercial applications
- Transformed PHP from a niche scripting tool into a professional web development platform

### Just In Time (JIT) compilation — PHP 8

**JIT compilation** means PHP compiles code to machine code at runtime (just before it is needed), rather than interpreting it line by line every time. This results in:
- Significantly faster execution, especially for complex computations
- Better overall performance for demanding web applications

---

## 3. PHP Today — Scale and Adoption

Despite being over 30 years old, PHP remains one of the most widely deployed server-side languages on the web:

- Installed on over **20 million websites**
- Running on over **1 million web servers** worldwide
- Powers some of the world's most visited platforms, including WordPress (which powers over 40% of all websites), Facebook (in its early years), and Wikipedia

---

## 4. Why is PHP Popular?

PHP has maintained its widespread adoption for decades due to several key strengths:

### Accessibility for beginners
- PHP is **extremely simple for a newcomer** to learn — the syntax is forgiving and readable
- A working PHP web page can be created with very little code
- It is one of the most beginner-friendly server-side languages available

### Power for professionals
- Despite its simplicity, PHP offers **many advanced features** for experienced programmers
- Supports full **object-oriented programming** (OOP)
- Extensive standard library of built-in functions
- Powerful frameworks available (Laravel, Symfony, CodeIgniter) for building complex applications

### Embeddable in HTML
- PHP code is enclosed in **special start and end processing tags**: `<?php` and `?>`
- These tags allow a developer to **jump into and out of "PHP mode"** within an ordinary HTML document
- This means PHP instructions can be mixed directly into HTML without needing a completely separate file

**Example of PHP embedded in HTML:**
```html
<!DOCTYPE html>
<html>
  <body>
    <h1>Welcome, <?php echo "User"; ?>!</h1>
    <p>Today is <?php echo date("l, F j, Y"); ?>.</p>
  </body>
</html>
```

The PHP sections (`<?php ... ?>`) are executed on the server; the user's browser only receives the final HTML output.

### Free and open source
- PHP is produced as **free software** by the PHP Group
- No licensing costs — available to anyone to download, use, and modify
- Supported by a large, active global community of developers

### Cross-platform compatibility
- PHP runs on all major operating systems: **Windows, macOS, Linux**
- Compatible with almost all web servers: **Apache, NGINX, IIS**, and others
- Connects to virtually all major databases: MySQL, PostgreSQL, SQLite, Microsoft SQL Server, and more

### Server-side execution
- PHP runs **on the server**, not in the browser — clients never see the PHP source code
- This is a security advantage: business logic, database credentials, and sensitive operations are hidden from the end user

---

## 5. How PHP Works — Server-Side Execution

Understanding how PHP executes is fundamental to understanding web development with PHP.

### Server-side vs client-side — the key distinction

| | PHP (server-side) | JavaScript (client-side) |
|---|---|---|
| **Where code runs** | On the **web server** | In the user's **web browser** |
| **When it runs** | Before the page is sent to the browser | After the page has been received by the browser |
| **What the client receives** | Only the **final HTML output** — the PHP code itself is never sent | The **JavaScript source code** is sent to and visible in the browser |
| **Source code visibility** | Hidden from the user completely | Visible — users can inspect it in browser developer tools |
| **Access to server resources** | Full — can read files, query databases, send emails | None — sandboxed within the browser |

### The execution process (without PHP)

For a plain HTML page:
1. Browser sends an **HTTP request** to the web server
2. Web server retrieves the HTML file from its file system
3. Web server sends the HTML file back as an **HTTP response**
4. Browser renders and displays the page

### The execution process (with PHP)

When a web page contains PHP:
1. Browser sends an **HTTP request** to the web server
2. Web server detects that the requested file contains PHP script
3. Web server passes the PHP code to the **PHP Engine** for processing
4. The PHP Engine executes the PHP instructions — this may include querying a database, doing calculations, reading files, etc.
5. The PHP Engine returns the **generated HTML** back to the web server
6. The web server sends the final HTML as an **HTTP response** to the browser
7. The browser receives and renders the page — it sees **only HTML**, never the PHP source

> **Key point:** The client receives the results of running the PHP script without ever knowing what the underlying PHP code was. PHP is completely invisible to the end user.

---

## 6. How PHP Fits Into the Web Server Architecture

The diagram (image provided) illustrates the full flow of a PHP-powered web request:

```
┌─────────────────────────────────────────────┐
│               Web Browser                   │
│         [ User Interface ]                  │
└──────────────┬──────────────────────────────┘
               │                    ▲
   HTTP Request│                    │ HTTP Response
               ▼                    │
┌─────────────────────────────────────────────┐
│                 Web Server                  │
│                                             │
│   ┌──────────────────────────┐   [Database] │
│   │   Connection to Domain   │              │
│   └────────────┬─────────────┘    ▲         │
│                │ PHP Request      │HTML     │
│                ▼                  │Response │
│   ┌──────────────────────────┐    │         │
│   │       PHP Engine  ⚙⚙     │────┘         │
│   └──────────────────────────┘              │
└─────────────────────────────────────────────┘
```

### Step-by-step breakdown of the diagram

**Step 1 — HTTP Request (browser → web server)**
- The user requests a web page in their browser (e.g. by clicking a link or typing a URL)
- The browser sends an **HTTP Request** down to the web server

**Step 2 — Connection to Domain**
- The web server receives the request
- It identifies the domain and the resource being requested
- It determines that the requested file is a PHP file (e.g. `.php` extension)

**Step 3 — PHP Request (web server → PHP Engine)**
- Because the file contains PHP, the web server cannot serve it directly
- The web server forwards a **PHP Request** to the **PHP Engine**

**Step 4 — PHP Engine processes the code**
- The PHP Engine executes all the PHP instructions
- This may involve:
    - Querying a database (shown in the diagram as the server stack on the right)
    - Reading configuration files
    - Performing calculations
    - Building dynamic HTML content

**Step 5 — HTML Response (PHP Engine → web server)**
- The PHP Engine has finished processing
- It returns a pure **HTML Response** to the web server

**Step 6 — HTTP Response (web server → browser)**
- The web server takes the generated HTML
- It sends it back to the browser as a standard **HTTP Response**

**Step 7 — User Interface rendered**
- The browser receives the HTML
- It renders the **User Interface** — the complete, dynamic web page is displayed to the user
- The user sees only the final result — no PHP code whatsoever

### The role of the PHP Engine

The **PHP Engine** is the core component that:
- Interprets and executes PHP script files
- Has access to server resources (file system, databases, system tools)
- Converts PHP code into HTML output
- Is completely transparent to the end user

In PHP 4 and above, the PHP Engine is based on the **Zend Engine** virtual machine.

---

## 7. Understanding "The Cloud"

The term **"The Cloud"** has become widely used to describe server-side computing. Understanding what it actually refers to helps demystify a lot of modern web terminology.

### What "The Cloud" means in this context

- **The Cloud** = the web server (and all associated infrastructure) that exists somewhere on the internet, not on the user's local machine
- When PHP code runs "in the cloud", it means the code is executing on a **remote server** — not on the user's computer
- The user interacts with only the **output** (the web page displayed in the browser), while all the actual computing happens invisibly on the server

### The HTTP protocol — the bridge between browser and server

**HTTP (HyperText Transfer Protocol)** is the fundamental communication standard that makes web access possible:

- It is the **common protocol** that allows any computer connected to the internet to access files on any web server
- It defines the format and rules for **requests** (from browser to server) and **responses** (from server to browser)
- HTTP works regardless of the operating system, browser, or device — it is the universal language of the web
- When PHP is involved, HTTP carries the request to the server and carries the generated HTML response back — PHP itself never leaves the server

---

## 8. Setting Up a PHP Development Environment

To develop PHP websites locally on your own computer, you need to install the same server-side technologies that run on a real web server. This creates a **local development environment** — a miniature web server running on your personal machine.

### Required components

| Component | Purpose | Recommended software |
|---|---|---|
| **Web Server** | Accepts HTTP requests and serves files; passes PHP files to the PHP Engine | **Abyss Web Server X1** |
| **PHP Engine** | Processes and executes PHP script files; returns generated HTML to the web server | **PHP 8 (version 8.0.1)** |

### Why a local development environment is needed

- You need a web server running locally because PHP must be **processed by the server** before the browser can display it — simply opening a `.php` file in a browser will not work
- A local environment lets you develop and test your PHP code without needing to upload files to a live server for every change
- It mirrors the production environment so that code tested locally will behave the same way when deployed

### How the local environment works

When PHP is installed locally:
- Your own computer acts as both the **web server** and the **client**
- The browser sends HTTP requests to `localhost` (your own machine)
- The local web server processes the request, calls the PHP Engine, and returns the result
- The workflow is identical to a live server — just running entirely on your own computer

---

## 9. Summary — Key Concepts at a Glance

| Concept | Definition |
|---|---|
| **PHP** | A widely-used, general-purpose scripting language especially suited for web development; can be embedded into HTML |
| **Rasmus Lerdorf** | Creator of PHP; released the first version on June 8, 1995 as "Personal Home Page Tools" |
| **Recursive acronym** | PHP: Hypertext Preprocessor — the abbreviation "PHP" appears within its own full name |
| **Server-side execution** | PHP code runs on the web server, not in the browser; the client only ever receives the final HTML output |
| **Client-side execution** | Code (like JavaScript) that runs in the browser after the page has been received |
| **PHP Engine** | The core component that interprets and executes PHP code; based on the Zend Engine from PHP 4 onwards |
| **Zend Engine** | The virtual machine powering PHP from version 4; dramatically improved performance and reliability |
| **JIT compilation** | "Just In Time" — introduced in PHP 8; compiles code to machine code at runtime for better performance |
| **Embedded in HTML** | PHP code is inserted into HTML using `<?php` and `?>` tags; allows mixing PHP and HTML in one file |
| **HTTP** | HyperText Transfer Protocol — the universal communication standard between browsers and web servers |
| **The Cloud** | A common term for the remote web server and infrastructure where server-side code executes |
| **Dynamic website** | A site that generates content at request time based on current conditions (user data, database content, time, etc.) |
| **Static website** | A site that returns the same fixed content for every request — no server-side processing |
| **Local development environment** | A web server and PHP engine installed on your own computer for developing and testing PHP sites locally |
| **PHP Group** | The organisation responsible for producing and maintaining PHP as free, open-source software |

---

