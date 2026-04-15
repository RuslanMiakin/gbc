## AI Usage (RetailCRM → Supabase → Telegram → Dashboard)

This section summarizes how I used AI during development. I treated it as a **pair-programming + drafting tool**: I gave it clear constraints, iterated based on runtime feedback (logs/errors), and kept control over final decisions and correctness.

### Task: Seed RetailCRM with orders from a JSON dataset

**Prompt:**

Build a PHP script that reads orders from a local JSON dataset and creates them in RetailCRM using the Orders API. It must be cron-friendly and avoid creating duplicate orders. Also explain whether all JSON fields can be mapped directly into an order card, or whether the client must be created separately and linked.

Provide the solution using the RetailCRM API examples for `POST /api/v5/orders/create`.

**Why I asked this (my intent / reasoning):**

I needed a repeatable way to **bootstrap test data** into RetailCRM, while ensuring a scheduled job wouldn’t spam duplicates. I also wanted clarity on **data modeling** (order vs customer lifecycle).

**What AI produced:**

- A first-pass approach for a PHP importer driven by the RetailCRM Orders API.
- Guidance on mapping fields from JSON into the order payload.

**Issues / limitations:**

- Early drafts needed explicit emphasis on **idempotency / deduplication strategy** to be safe for cron usage.
- Some payload details required validation against the actual API behavior (not just the example snippet).

**My fix / improvement:**

- I validated the behavior against the API constraints and shaped the importer around **safe repeated execution** (no accidental duplicates).
- I adjusted field mapping expectations to match what can realistically be written into an order vs what should be handled separately.

---

### Task: Create a Supabase schema for RetailCRM orders

**Prompt:**

Extract the main fields from the sample orders JSON and generate SQL to create a Supabase table named `rcrm_orders`. Output only the table structure SQL.

**Why I asked this (my intent / reasoning):**

I wanted to stand up a database quickly so I could persist RetailCRM orders for analytics and downstream integrations.

**What AI produced:**

- SQL DDL for `rcrm_orders` based on the fields present in the sample JSON.

**Issues / limitations:**

- Field selection is only as good as the sample JSON; a schema based purely on one snapshot can miss edge cases.

**My fix / improvement:**

- I verified the schema against real order payloads and ensured the table aligns with what the import script actually sends.
- I added/confirmed project-specific fields (e.g., I created custom fields such as `utm_source` and an order type field on the CRM side, then ensured mapping was consistent end-to-end).

---

### Task: Import orders from RetailCRM into Supabase (with `--dry-run`)

**Prompt:**

Write a PHP script `import_retailcrm_to_supabase.php` that fetches orders from RetailCRM (`GET /api/v5/orders`), maps them to the `rcrm_orders` columns, and inserts them into Supabase using the Supabase REST API. Handle pagination correctly.

Add a `--dry-run` mode that prints which fields will be sent to the database (and an example row) without writing anything.

**Why I asked this (my intent / reasoning):**

I needed an automated pipeline (cron-ready) from RetailCRM into Supabase. The `--dry-run` mode was important for **debugging mappings safely** before writing data.

**What AI produced:**

- An initial version of the importer script with RetailCRM fetch + Supabase insert logic.
- A `--dry-run` mode proposal to preview outgoing data.

**Issues / limitations:**

- **RetailCRM pagination constraint:** I hit an error showing that `limit` only supports specific values (20 / 50 / 100). The initial approach needed correction.
- **Payload correctness:** `custom_fields` needed to be represented as an object (`{}`) in examples/requests to match expectations.

**My fix / improvement:**

- I used terminal output and logs to drive iteration: corrected pagination to respect allowed `limit` values and ensured stable pagination behavior.
- I enforced the correct shape for `custom_fields` and aligned the `--dry-run` output with the real payload structure.

---

### Task: Build and validate a Vercel dashboard (static UI + serverless API)

**Prompt:**

Create a lightweight dashboard inside `vercel_dashboard/` with a static frontend (e.g., `index.html` using Chart.js) and a serverless function (e.g., `api/stats.js`) that queries aggregated stats from Supabase via REST. Document required environment variables on Vercel (`SUPABASE_URL`, key).

After I move the dashboard into `vercel_dashboard/`, verify paths and overall integrity.

**Why I asked this (my intent / reasoning):**

I wanted a quick deployable analytics view without building a full app framework, and I needed AI to accelerate the initial structure and deployment checklist.

**What AI produced:**

- A proposed folder structure for a static dashboard + serverless API layer.
- A checklist for environment variables and deployment considerations.

**Issues / limitations:**ф

- After refactors/moving folders, path correctness must be validated in the real project layout; AI can’t “guess” the final filesystem state.

**My fix / improvement:**

- I checked the moved `vercel_dashboard/` layout, updated any broken references, and made sure the deployment expectations match the final structure.

---

### Task: RetailCRM → Telegram notification script (PHP 7.4, simple input)

**Prompt:**

Create a simple PHP 7.4 script for my own server that receives a webhook (or a simple GET request with fields like order cost) and sends a message to a Telegram chat.

**Why I asked this (my intent / reasoning):**

I needed a minimal integration point for operational notifications, optimized for reliability and ease of hosting (plain PHP).

**What AI produced:**

- A baseline approach for receiving input (webhook/GET parameters) and sending a Telegram message.

**Issues / limitations:**

- Requirements changed mid-stream (webhook body → GET parameters; script deleted → recreated), so the solution needed quick adaptation rather than a “perfect first version”.

**My fix / improvement:**

- I constrained the implementation to a simple, server-friendly PHP script and aligned the input format to the final decision (GET parameters).

---

### Takeaways

- **Controlled use of AI**: I used AI to accelerate scaffolding (scripts, SQL, deployment structure), but I kept correctness by validating against API behavior and runtime output.
- **Iterative debugging**: When real constraints surfaced (e.g., RetailCRM pagination `limit` values, payload shape for `custom_fields`), I fed that evidence back and corrected the implementation.
- **Engineering workflow**: I treat AI as a productive collaborator for drafting and reasoning, not as an authority—final behavior is validated by specs, logs, and the deployed environment.

