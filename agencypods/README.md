# Trickleup

An internal dashboard for a digital agency to manage **pods** (teams), the **clients**
in each pod, and per-client **goals**. Each goal is broken into four sections —
**Goal / Stop / Start / Continue** — each with editable notes and file attachments.
Includes weekly task tracking and a Super-Admin reports view with PDF export.

Built with **Laravel 12 + MySQL (MariaDB) + Blade**, running on XAMPP.

---

## Features

- **Pods → Clients → Goals → Sections (Goal/Stop/Start/Continue) → Attachments**
- **Roles**
  - **Super Admin** — sees every pod, the Reports view + PDF export, and the **Users** admin
    (create users, assign roles, create pods, assign managers to pods).
  - **Pod Manager** — sees only the pods they manage (a manager can manage several pods), and
    manages their own **team** (add/remove team members).
  - **Team Member** — added by a manager; can sign in and work on the clients & goals in that
    manager's pods.
- **File attachments** — real uploads, stored privately and served through
  access-controlled routes (preview / download / delete).
- **Weekly goals** — per-week task list with done/pending toggle, scoped to your pods.
- **Reports** (Super Admin) — cross-pod metrics and **PDF export** (overall + per-pod).

---

## Requirements

- PHP 8.2+
- Composer
- MySQL / MariaDB (XAMPP)
- Node.js + npm (only needed to rebuild front-end assets)

---

## Setup

From `c:\xampp\htdocs\AgencyPods\agencypods`:

```bash
# 1. Install dependencies
composer install
npm install && npm run build

# 2. Environment
copy .env.example .env        # if .env is missing
php artisan key:generate      # if APP_KEY is empty

# 3. Database — already configured for XAMPP in .env:
#    DB_DATABASE=agency_pods  DB_USERNAME=root  DB_PASSWORD=(empty)
#    Create the database once:
#    "C:\xampp\mysql\bin\mysql.exe" -u root -e "CREATE DATABASE agency_pods"

# 4. Migrate + seed the demo data
php artisan migrate:fresh --seed

# 5. Run
php artisan serve
```

Then open **http://127.0.0.1:8000**.

### Seeded demo logins (password: `password`)

| Email | Role | Manages |
|---|---|---|
| `admin@agencypods.test` | Super Admin | everything |
| `manager1@agencypods.test` | Pod Manager | Pod Alpha + Pod Gamma |
| `manager2@agencypods.test` | Pod Manager | Pod Beta + Pod Delta |

---

## Provisioning users

**Public registration is intentionally disabled** — this is an internal tool. Users are
created in-app: a **Super Admin** adds users and pods from the **Users** screen, and each
**Pod Manager** adds their own people from the **My team** screen.

To create the very first super admin (or via script), use `php artisan tinker`:

```php
use App\Models\User;
use App\Models\Pod;
use Illuminate\Support\Facades\Hash;

// A pod manager
$u = User::create([
    'name' => 'Jane Doe',
    'email' => 'jane@agency.test',
    'password' => Hash::make('a-strong-password'),
    'role' => 'pod_manager',          // or 'super_admin'
]);

// Assign pods to that manager (a manager can own several)
Pod::whereIn('name', ['Pod Alpha', 'Pod Gamma'])->update(['manager_id' => $u->id]);
```

After that first super admin exists, everything else can be done through the UI.

---

## Data model

```
User (role: super_admin | pod_manager)
  └─ manages → Pod (manager_id, color)
                 └─ Client (industry)
                      ├─ Goal (title, created_at)
                      │    └─ GoalSection (type: goal|stop|start|continue, content)
                      │         └─ Attachment (original_name, stored_path, size, file_type)
                      └─ WeeklyTask (task, status, week_start)
```

Pod visibility is enforced everywhere through the `Pod::visibleTo($user)` scope and the
`InteractsWithPods` controller trait; the Reports routes are additionally gated by the
`role:super_admin` middleware.

---

## File storage

Uploads are stored on the **private** `local` disk under
`storage/app/private/attachments/` with hashed filenames, and streamed only through
authorized `attachments.preview` / `attachments.download` routes. No `storage:link`
is required.

Max upload size is validated at **20 MB**. If large uploads fail, raise
`upload_max_filesize` and `post_max_size` in your XAMPP `php.ini`.

---

## Tests

```bash
php artisan test
```

Tests run on in-memory SQLite (see `phpunit.xml`), so they never touch your dev database.
Coverage includes auth, role gating, pod scoping, client CRUD authorization,
file upload/download, and the weekly toggle.

---

## Deploying under Apache (XAMPP) instead of `php artisan serve`

Point a virtual host's `DocumentRoot` at the project's **`public/`** directory, e.g.:

```apache
<VirtualHost *:80>
    ServerName agencypods.local
    DocumentRoot "C:/xampp/htdocs/AgencyPods/agencypods/public"
    <Directory "C:/xampp/htdocs/AgencyPods/agencypods/public">
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

Add `127.0.0.1 agencypods.local` to your hosts file and restart Apache.
