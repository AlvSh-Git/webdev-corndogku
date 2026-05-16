# CORNDOG-KU: Web Application Developer Guide
**CRITICAL INSTRUCTION FOR CLAUDE/AI AGENTS:** Read this document entirely before executing any commands, generating views, or writing logic. You MUST strictly adhere to the rules, schemas, and UI directives outlined below.

## 1. TECH STACK & ARCHITECTURE
- **Backend:** Laravel 11 (PHP 8.2+)
- **Frontend:** Tailwind CSS, jQuery (Loaded via CDN), Blade Templating. (Do NOT use Alpine.js or Vue/React to prevent Vite compilation issues).
- **Database:** MySQL
- **Strict Separation of Concerns:**
  - `layouts.app`: For authenticated Employee/Owner dashboards (Includes Top Navbar and Hidden Off-canvas Sidebar).
  - `layouts.guest`: For Authentication (Login/Register) and Public Customer pages (Home).

## 2. RESPONSIVE UI/UX & FIGMA DIRECTIVES
- **Figma for Design Intent, NOT Fixed Sizes:** Use the Figma MCP tool to read colors, typography, component styles (shadows, borders), and general layout structure. However, the final output **MUST BE FULLY RESPONSIVE**. 
- **Fluid Layouts:** Do NOT hardcode fixed pixel widths/heights that break on mobile screens. Translate the static Figma design into fluid, responsive layouts using Tailwind's grid, flexbox, and breakpoints (`sm:`, `md:`, `lg:`, `xl:`). Cards should stack on mobile and form grids on desktop.
- **NO Hallucinated Layouts:** Do not invent UI components. Mirror the visual aesthetics of the Figma frames, but adapt them intelligently for responsive web behavior.
- **NO "Label" Placeholders:** Never output raw "Label" text. Use realistic mock data or actual data.
- **Asset Management:** All product images, raw ingredients, and UI assets are stored in `public/assets/img/` and `public/assets/ui`

## 3. ROLE-BASED ACCESS & ROUTING
The system has 3 distinct roles:
1. **Owner:** Full access. (Dashboard, Product, Report, User Maintenance).
2. **Employee/Cashier:** Operational access. (Dashboard, Purchase/POS).
3. **Customer:** Public access. (Home, Menu, Custom Builder, User Profile, Chatbot).


## 4. DATABASE SCHEMA & MODELS (Strict Naming)
Adhere to this relationship structure (ERD):
- **Users:** `id`, `name`, `username`, `password`, `role` (enum: owner, employee, customer), `branch`, `status`.
- **Products:** `id`, `category_id`, `name`, `description`, `price`, `image`, `is_custom`.
- **Categories:** `id`, `name` (e.g., Original, Mozza, Custom).
- **Components (For Custom Corndog):** `id`, `type` (enum: filling, coating, topping), `name`, `price`, `image`.
- **Orders:** `id`, `user_id` (nullable for walk-in), `order_number`, `total_price`, `status`, `order_type` (online, dine-in, takeaway).
  - **Order Status Enum:** `Pending`, `Preparing`, `Ready`, `Completed`, `Cancelled`.
- **Order_Items:** `id`, `order_id`, `product_id`, `quantity`, `subtotal`, `custom_notes`.
- **Payments:** `id`, `order_id`, `payment_method`, `amount`, `status` (enum: Unpaid, Paid, Failed).
- **Chatbot_Logs:** `id`, `user_id`, `message`, `response`.

## 5. REQUIRED KEY FEATURES TO IMPLEMENT

### A. Feature: Custom Corndog Builder
- **Logic:** Customers can build a corndog by selecting 1 Filling, 1 Coating, and multiple Toppings.
- **UI:** Must fetch component images from `assets/img/` and use **jQuery** to toggle active states (classes) and dynamically calculate the total price in the DOM before adding to the cart. Make sure the component grid is responsive (e.g., 2 columns on mobile, 4 on desktop).

### B. Feature: Google SSO Login
- **Integration:** Use Laravel Socialite.
- **Flow:** Add a "Sign in with Google" button on the Figma-synced Login page. Authenticate and auto-register customers, assigning them the `customer` role.

### C. Feature: Customer Support Chatbot
- **UI:** A floating chat interface on the customer-facing pages.
- **Logic:** Implement a basic NLP or predefined response tree for FAQs (e.g., store hours, menu inquiries). Save interactions to the `Chatbot_Logs` table.

### D. Feature: WhatsApp Receipt via Evolution API
- **Trigger:** Upon a Payment status changing to `Paid`.
- **Integration:** Call the local/remote Evolution API instance to send a formatted WhatsApp message containing the `order_number`, items, total, and a thank-you note to the customer's registered phone number.

### E. Feature: Financial Report Export
- **Access:** Owner only.
- **Integration:** Use `barryvdh/laravel-dompdf` for PDF export and `maatwebsite/excel` for Excel export.
- **Data:** Export filtered revenue data, order counts, and itemized sales from the Reports view.

## 6. DEVELOPMENT WORKFLOW FOR AI
When asked to build a view or feature:
1. Scan this document for rules.
2. Check the database schema required.
3. Use Figma MCP to read the exact design aesthetics, but prioritize Tailwind responsive classes for the layout execution.
4. Implement using Tailwind + **jQuery**. Make sure to include the jQuery CDN `<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>` in the layout if not already present.
5. Ensure role middleware is applied correctly.