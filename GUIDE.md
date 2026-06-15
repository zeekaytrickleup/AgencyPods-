# Trickleup — A Simple Guide

This is a plain-English guide to what the app does and how to use it.
No technical knowledge needed.

---

## 1. What is Trickleup?

Trickleup is an **internal dashboard for a digital agency**. It helps the agency keep
all its work organised in one place — who the clients are, what work is planned for
them, the files for that work, and the weekly to-do list.

Think of it as a tidy noticeboard for the whole agency that everyone logs into.

---

## 2. The big idea: "Pods"

The agency is split into small teams called **Pods** (named **Crimson, Ember, Cobalt,
and Dusk** — each has its own colour). A pod is basically a team that looks after a
group of clients.

The structure is like a tree:

```
Pod (a team, e.g. "Crimson")
 └── Client (a business the agency works for, e.g. "Bloom Bakery")
      └── Goal (a project for that client, e.g. "Website redesign")
           └── 4 sections of notes:  Goal · Stop · Start · Continue
                └── Files attached to each section (PDFs, images, etc.)
```

So: a **pod** has **clients**, each client has **goals (projects)**, and each goal is
described using four simple headings.

### What do the four headings mean?
For every project, you write short notes under:
- **Goal** – what we're trying to achieve.
- **Stop** – what we should *not* do (e.g. "don't change the logo").
- **Start** – what to begin doing now.
- **Continue** – what to keep doing regularly.

You can also **attach files** (briefs, designs, reports) under any of these headings.

---

## 3. Who uses it? (the three roles)

Different people see different things, depending on their role:

| Role | What they can do |
|---|---|
| **Super Admin** | Sees **everything** — all pods, all clients. Can add users, create pods, and view/download reports. The "owner" of the system. |
| **Pod Manager** | Sees only **their own pods**. Manages those clients and projects, and can **add their own team members**. |
| **Team Member** | Added by a manager. Can log in and **work on the clients & projects in their manager's pods**. |

This means people only ever see the work that's relevant to them.

---

## 4. The main screens

When you log in, there's a **sidebar on the left** to move around.

### 🏠 Dashboard (the main screen)
- Pick a **pod** from the sidebar.
- See its **clients** as cards. Click a client to see their **goals (projects)**.
- Click a goal to open its **Goal / Stop / Start / Continue** tabs, read the notes,
  and see/upload **files**.
- Buttons let you **add or edit** clients, goals, notes, and files.

### 📅 Weekly goals
- A simple **to-do list for the week**, one row per task (which client, what task,
  done or pending).
- Tick tasks off, add new ones, and **flip between weeks** with the arrows.
- Shows counts at the top: Total / Done / Pending.

### 📊 Reports *(Super Admin only)*
- A **summary across all pods**: how many pods, clients, goals, and how many tasks
  are done.
- Choose **Weekly** or **Monthly**, and page through time with the arrows.
- **Download a PDF** report (with the Trickleup letterhead and watermark) — either
  for everything, or for one pod.

### 👥 Users *(Super Admin only)*
- Add people, set their role, and decide which pods a manager looks after.
- Create new pods here too.

### 🙋 My team *(Pod Manager only)*
- Add or remove the people who help on your pods.

---

## 5. A typical day, by role

**Super Admin**
> Logs in → checks the **Reports** to see how the agency is doing this month →
> downloads a PDF for a meeting → adds a new manager in **Users** and assigns them a pod.

**Pod Manager**
> Logs in → opens their pod on the **Dashboard** → adds a new client and a project →
> writes the Goal/Stop/Start/Continue notes → uploads the client brief →
> adds this week's tasks under **Weekly goals** → invites a teammate under **My team**.

**Team Member**
> Logs in → opens the project they're working on → reads the notes →
> uploads their finished work → ticks off their weekly tasks.

---

## 6. How to log in

Open **http://127.0.0.1:8000** and use one of the demo accounts
(password is `password` for all of them):

| Role | Email |
|---|---|
| Super Admin | `admin@agencypods.test` |
| Pod Manager | `manager1@agencypods.test` |
| Pod Manager | `manager2@agencypods.test` |

*(On the login page you can just click a demo account to fill it in automatically.)*

---

## 7. In one sentence

> **Trickleup keeps an agency's teams, clients, projects, files, weekly tasks, and
> reports neatly organised in one place — and shows each person only what they need.**
