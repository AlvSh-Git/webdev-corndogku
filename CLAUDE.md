# CORNDOG-KU: Web Application Developer Guide
**CRITICAL INSTRUCTION FOR CLAUDE/AI AGENTS:** Read this document entirely before executing any commands, generating views, or writing logic. You MUST strictly adhere to the rules, schemas, and UI directives outlined below.

## 0. COMMANDS
```bash
composer setup          # First-time: install deps, key:generate, migrate, build
composer dev            # Dev server + queue + Vite (concurrently)
composer test           # Run Pest test suite
php artisan migrate     # Run new migrations
php artisan tinker      # REPL
npm run build           # Build frontend assets (Vite + Tailwind)
```

## 1. TECH STACK & ARCHITECTURE
- **Backend:** Laravel 13 (PHP 8.3+)
- **Frontend:** Tailwind CSS, Blade Templating, native HTML/CSS for simple interactivity (jQuery loaded via CDN only if strictly necessary). Do NOT use Alpine.js or Vue/React to prevent Vite compilation issues.
- **Tailwind v4 (CSS-first):** Configured via `@tailwindcss/vite` + `@import "tailwindcss"` in `resources/css/app.css` — there is **no `tailwind.config.js`**. Brand design tokens are CSS variables in `app.css` (`--color-primary`, `--color-accent`, `--color-light`, `--color-border`, …); views apply them inline, e.g. `style="color:var(--color-primary)"`. Reuse these tokens, don't hardcode hex.
- **⚠️ BUILD GOTCHA — the live server has no Node.** The compiled Vite output in `public/build/assets/` is **committed to git** and served as-is; Tailwind is NOT compiled on the fly in prod. Any *new* or *arbitrary-value* utility class (`bottom-8`, `rounded-[2rem]`, `sm:bottom-10`, `lg:order-*`) that isn't already in the built CSS will **silently do nothing** (element looks broken/"gone"). For layout-critical styling either (a) reuse a class already present in `public/build/assets/app-*.css`, or (b) use an **inline `style="…"`** (supports `clamp()`, media-independent). If you must add new classes, run `npm run build` AND commit the regenerated `public/build/` assets.
- **Database:** MySQL (`DB_CONNECTION=mysql` — override the sqlite default in `.env.example`)
- **Test Runner:** Pest (`composer test`)
- **Strict Separation of Concerns:**
  - `layouts.app`: For authenticated Employee/Owner dashboards (Includes Top Navbar and Hidden Off-canvas Sidebar).
  - `layouts.guest`: For Authentication (Login/Register) and Public Customer pages (Home).

## 2. RESPONSIVE UI/UX & FIGMA DIRECTIVES
- **Figma for Design Intent, NOT Fixed Sizes:** Use the Figma MCP tool to read colors, typography, component styles (shadows, borders), and general layout structure. However, the final output **MUST BE FULLY RESPONSIVE**. 
- **Fluid Layouts:** Do NOT hardcode fixed pixel widths/heights that break on mobile screens. Translate the static Figma design into fluid, responsive layouts using Tailwind's grid, flexbox, and breakpoints (`sm:`, `md:`, `lg:`, `xl:`). 
- **Image Handling:** Use `aspect-ratio`, `object-cover`, or natural dimensions (`h-auto`) to handle images cleanly without distortion or unwanted white borders.
- **NO "Label" Placeholders:** Never output raw "Label" text. Use realistic mock data or actual data.

## 3. ROLE-BASED ACCESS & ROUTING
The system has 3 distinct roles:
1. **Owner:** Full access. (Dashboard, Product, Financial Report, User Maintenance).
2. **Employee/Cashier:** Operational access. (Dashboard, Order Processing/POS).
3. **Customer:** Public access. (Home, Menu, Custom Builder, Cart, Order History, Checkout).

## 4. DATABASE SCHEMA & MODELS (Strict Naming)
Adhere to this relationship structure (ERD):
- **Users:** `id`, `name`, `username` (unique, auto-generated from email local-part), `email`, `phone` (nullable), `password` (nullable), `google_id` (nullable), `avatar` (nullable), `role` (enum: owner, employee, customer), `status`.
- **Products:** `id`, `category_id`, `name`, `description`, `price`, `image`, `is_custom`.
- **Categories:** `id`, `name` (e.g., Original, Mozza, Custom).
- **Components (For Custom Corndog):** `id`, `type` (enum: filling, coating, topping), `name`, `price`, `image`.
- **Orders:** `id`, `user_id`, `order_number`, `total_price`, `status`, `payment_method`.
  - **Order Status Enum:** `Pending`, `Preparing`, `Ready`, `Completed`, `Cancelled`.
- **Order_Items:** `id`, `order_id`, `product_id`, `quantity`, `subtotal`, `custom_notes` (JSON column to store specific custom corndog components: base, filling, sauce).
- **Payments (Midtrans):** `id`, `order_id`, `transaction_id`, `amount`, `status` (enum: pending, success, settlement, failed).
- **Chatbot_Logs:** `id`, `user_id` (nullable FK), `message`, `response` (nullable).

## 5. IMPLEMENTED & REQUIRED KEY FEATURES

### A. Feature: Custom Corndog Builder & Layered Cart
- **Logic:** Customers can build a corndog by selecting components. The cart/checkout must store this as JSON in `custom_notes`.
- **UI:** In the cart and order history, custom corndogs MUST be displayed using a stacked absolute-positioning technique (Layer 1: Base image, Layer 2: Transparent Sauce image overlapping it) to show the exact user creation.

### B. Feature: Midtrans Payment Integration
- **Integration:** Handled via Midtrans Snap API.
- **Flow:** When payment is marked 'success' or 'settlement', the system must save the order, map cart items to `order_items`, and CLEAR the user's cart session.

### C. Feature: Google SSO Login
- **Integration:** Laravel Socialite.
- **Flow:** Authenticate users via `/auth/google/callback`. Update or Create the user using `email` or `google_id`. Ensure `password` is nullable.

### D. Feature: Fonnte WhatsApp API (Receipts & Notifications)
- **Integration:** Call the Fonnte API endpoint (`https://api.fonnte.com/send`) using standard Laravel `Http::post`.
- **Trigger:** Send a formatted WhatsApp message (Order ID, items, total price, status) when an order is paid or marked ready. Must use the Fonnte API Token stored in the `.env` file.

### E. Feature: Financial Report & Native CSV Export
- **Access:** Owner only (`/owner/reports`).
- **Integration:** Do NOT use third-party packages like `maatwebsite/excel`. 
- **Flow:** Generate a native CSV export using PHP's `php://output` stream. Calculate Revenue, Total Orders, and Average Value ONLY from successful/completed orders. Ensure date filters use Carbon to format `d/m/Y` inputs into valid database timestamps.

### F. Feature: Customer Service Chatbot
- **Integration:** Groq API (OpenAI-compatible endpoint `https://api.groq.com/openai/v1/chat/completions`, model `llama-3.1-8b-instant`) via `Http::post`, in `Customer/ChatbotController`. Auth uses `env('GROQ_API_KEY')`. Uses lightweight RAG: live menu + store status injected into the system prompt.
- **UI/Flow:** A floating chat widget positioned at the bottom-right of Customer pages (rendered via `layouts.guest`). Must use native JavaScript (Fetch API) or simple jQuery for real-time asynchronous messaging without reloading the page. Do NOT use Vue/React.
- **Database Logging:** All chat interactions (the customer's message and the bot's response) MUST be saved into the `chatbot_logs` table for history tracking and context retention.

### G. Store Status & Schedule (cache-driven)
- Open/closed state is computed in the base `Controller` (`calcStoreStatus()`), driven by two cache keys: `jadwal_operasional` (weekly schedule) and `manual_override` (owner toggle). There is no DB table for this.
- When `jadwal_operasional` is empty, it falls back to `defaultSchedule()` (Mon–Sat, Sun closed) — this **gates checkout/cart closed** on a cache-cold deploy until the owner saves a schedule via the Jadwal page.
- Shared base-`Controller` helpers (use these, don't re-derive): `storeAddress()`, `defaultSchedule()`, `operationalSchedule()`, `scheduleHours()`, `calcStoreStatus()`, `scheduleKeyFor()`.
- Store **address** single source of truth: `config/store.php` (`STORE_ADDRESS` env). Blade reads `config('store.address')`; controllers use `$this->storeAddress()`. Never hardcode the address.

## 6. ENVIRONMENT VARIABLES (Required)
Copy `.env.example` → `.env` and fill in these keys:

| Key | Purpose |
|-----|---------|
| `GOOGLE_CLIENT_ID` | Google SSO (Socialite) |
| `GOOGLE_CLIENT_SECRET` | Google SSO (Socialite) |
| `GOOGLE_REDIRECT_URI` | Google SSO callback URL |
| `MIDTRANS_SERVER_KEY` | Midtrans payment (server-side) |
| `MIDTRANS_CLIENT_KEY` | Midtrans payment (client-side Snap) |
| `MIDTRANS_IS_PRODUCTION` | `false` for sandbox, `true` for live |
| `FONNTE_TOKEN` | WhatsApp notification API |
| `GROQ_API_KEY` | Chatbot LLM (Groq) — also add to `.env.example`, currently missing |
| `STORE_ADDRESS` | Store address — single source of truth (see `config/store.php`) |
| `GOOGLE_PLACES_API_KEY` | Google reviews on the home page |
| `GOOGLE_PLACE_ID` | Google Places lookup for reviews |
| `DB_CONNECTION` | Set to `mysql` (default in example is sqlite) |

## 7. CONTROLLER DIRECTORY STRUCTURE
```
app/Http/Controllers/
  Auth/          → SocialiteController (Google SSO), WhatsAppResetController (WA password reset)
  Owner/         → DashboardController, ProductController, ReportController, UserMaintenanceController, CategoryController, JadwalController
  Customer/      → MenuController, CartController, CheckoutController, HistoryController, ProfileController, WelcomeController
                   ChatbotController (Groq), WishlistController
  Cashier/       → DashboardController, PurchaseController
  Concerns/      → Shared controller traits: ManagesOrderStatus, NormalizesPhone, RestoresOrderStock
  Controller.php → Base controller — store-status/schedule helpers (see §5G)
  AuthController.php   → Login / Register / Logout
```
