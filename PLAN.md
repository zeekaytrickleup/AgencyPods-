# Agency Pods — Implementation Plan

Turn the static prototype ([pod_dashboard_with_attachments.html](pod_dashboard_with_attachments.html))
into a real application.

**Stack:** Laravel (PHP) + MySQL + Blade/Tailwind front-end, running on XAMPP.
**Auth:** Login with two roles — `super_admin` and `pod_manager` — in v1.
**Files:** Real uploads stored on disk, with preview & download.

---

## 1. What the prototype already defines (our spec)

- **Pods** — Alpha/Beta/Gamma/Delta, each has a name + colour, and contains clients.
- **Clients** — belong to a pod; have a name + industry; contain goals.
- **Goals** — belong to a client; have a title + created date; each goal has **4 sections**:
  `Goal`, `Stop`, `Start`, `Continue`. Each section has **text + file attachments**.
- **Weekly goals** — a list of tasks (client, task, status done/pending) for a given week.
- **Reports** — cross-pod overview: totals (pods, clients, goals, weekly done) + per-pod
  breakdown + "Download PDF".
- **Roles** — `Pod Manager` (works inside a pod) and `Super Admin` (sees reports across all pods).

---

## 2. Data model (MySQL tables)

| Table | Key columns |
|---|---|
| `users` | id, name, email, password, **role** (`super_admin`\|`pod_manager`) |
| `pods` | id, name, color, **manager_id** (nullable → users.id) |
| `clients` | id, **pod_id**, name, industry |
| `goals` | id, **client_id**, title, created_at |
| `goal_sections` | id, **goal_id**, **type** (`goal`\|`stop`\|`start`\|`continue`), content (text) |
| `attachments` | id, **goal_section_id**, original_name, stored_path, size, mime, file_type (pdf/img/doc/xls), uploaded_by |
| `weekly_tasks` | id, **client_id**, task, status (`done`\|`pending`), week_start (date) |

**Relationships:** User (manager) →has many→ Pod →has many→ Client →has many→ Goal
→has many→ GoalSection →has many→ Attachment.
A **manager can own several pods** (`pods.manager_id`); each pod has one manager.
A Goal always has exactly 4 sections (auto-created when the goal is created).
**Weekly tasks** belong to a client and inherit their pod through that client.

---

## 3. Build phases

### Phase 0 — Project setup
- `composer create-project laravel/laravel agencypods` (or in place).
- Configure `.env` for the local XAMPP MySQL (`simply_motoring` server is already running).
- Create database `agency_pods`.
- Install Laravel Breeze (or Fortify) for auth scaffolding + Tailwind.

### Phase 1 — Database & models
- Write migrations for all 7 tables above (with foreign keys + cascade deletes).
- Create Eloquent models with relationships.
- Build a **seeder** that loads the exact prototype data (Pod Alpha/Beta/Gamma/Delta,
  Bloom Bakery, CoreFit Gym, LexLaw Solicitors, their goals, sections, and the weekly tasks)
  so the app boots looking identical to the mockup.

### Phase 2 — Auth & roles
- Login / logout (Breeze).
- `role` on users + `manager_id` on pods; middleware/policies:
  - **pod_manager** → scoped to **all pods where they are the manager** (and those pods'
    clients, goals, weekly tasks).
  - **super_admin** → full access + the Reports view.
- Seed one super admin + a couple of managers (one managing multiple pods) for testing.

### Phase 3 — Core dashboard (port the prototype)
- **Layout:** sidebar (pod list + Weekly/Reports nav) + main panel — reuse the existing CSS.
- **Clients view:** client grid + goal cards with the 4 collapsible Goal/Stop/Start/Continue tabs.
- CRUD: Add/edit/delete **clients**, **goals**, and edit each **section's text**.
- Wire the "Add client" / "Add goal" buttons (currently dead) to real forms.

### Phase 4 — File attachments (real uploads)
- Replace the mock upload zone with a real `<input type=file>` → Laravel `Storage`.
- Store under `storage/app/attachments/...`; save metadata to `attachments`.
- **Preview** (open in browser) + **Download** routes; **delete** attachment.
- Validate type/size; auto-detect `file_type` for the coloured icon.

### Phase 5 — Weekly goals
- Weekly tasks table per week; toggle done/pending (persist to DB).
- Add/edit/delete tasks; the Total/Done/Pending metric cards compute from DB.
- Scope to pod for pod managers.

### Phase 6 — Reports (Super Admin)
- Cross-pod overview metrics + per-pod breakdown (live counts from DB).
- **Download PDF** — generate with `barryvdh/laravel-dompdf` (per-pod and overall).

### Phase 7 — Polish & deploy
- Validation, flash messages, empty states (already designed in the mockup).
- Seed/demo data toggle; basic tests for policies and uploads.
- Run on XAMPP; document setup in README.

---

## 4. Decisions (confirmed) & remaining notes

- ✅ **A manager can own several pods** — modelled via `pods.manager_id`.
- ✅ **Weekly tasks are client-linked** and inherit their pod through the client.
- **PDF**: dompdf is the lightweight default; switch to a heavier renderer only if you need
  pixel-perfect charts.

---

## 5. Suggested order to start
Phase 0 → 1 → 2 give you a logged-in app with real data in the DB looking like the mockup.
That's the smallest end-to-end slice worth reviewing before building CRUD + uploads.
