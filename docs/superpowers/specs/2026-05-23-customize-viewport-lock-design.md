# Design: Customize Corndog — Viewport-Locked Layout

**Date:** 2026-05-23
**Scope:** Transform `resources/views/customize.blade.php` into a non-scrollable, viewport-fitted app-like experience matching the Figma "customize users" frame, with a layered corndog image preview system.

---

## 1. Layout Architecture

The entire page fits within `100vh` — no global vertical scroll.

```
body  (h-screen overflow-hidden flex flex-col)
├── <header>  flex-none  ~64px  (sticky navbar, unchanged)
└── <section>  flex-1 overflow-hidden flex flex-col
    └── container  max-w-[1440px] flex-1 flex flex-col overflow-hidden  pt-4 pb-0
        ├── TOP ROW  flex-none          Title (left) + Stepper (right)
        ├── GRID     flex-1 overflow-hidden  grid-cols-1 lg:grid-cols-2
        │   ├── LEFT   Carousel: blob + layered corndog + arrows + pill + dots
        │   └── RIGHT  Step instruction card (or review panel on step 4)
        └── BOTTOM   flex-none          Next / Back buttons
<footer class="hidden">
```

### Sizing contract
- Header: fixed ~64 px, `flex-none`
- Top row: compact, `flex-none` — max ~90 px total vertical
- Grid: `flex-1 overflow-hidden` — takes all remaining height
- Bottom buttons: `flex-none` — ~72 px

---

## 2. Top Row — Title + Stepper

### Title block (left)
Scaled down from original `text-7xl` to fit viewport:

```
"CUSTOM"   — font-black text-4xl sm:text-5xl  color: #1a1a1a
"CORNDOG"  — font-black text-4xl sm:text-5xl  color: #A6171C
wavy SVG underline (same, scaled to width ~200px)
amber pill tagline — text-sm font-bold, py-2 px-4
```

### Stepper (right)
Circles `w-8 h-8`, `text-xs` labels, dashed connector lines. Active = red filled circle. Rendered by `renderStepper()` JS (unchanged logic, only CSS sizes adjusted via `#stepper` overrides in `<style>`).

---

## 3. Left Column — Layered Corndog Preview

### Container
```html
<div class="flex items-center gap-3 justify-center h-full overflow-hidden">
  <!-- Prev arrow -->
  <!-- Carousel center -->
  <!-- Next arrow -->
</div>
```

### Carousel center
```html
<div id="carousel-center" class="flex-1 flex flex-col items-center justify-center relative">

  <!-- Peach blob — NO overflow-hidden: corndog stick intentionally extends below -->
  <div class="corndog-blob w-[min(55vw,460px)] h-[min(55vw,460px)] relative">

    <!-- LAYER 0: base corndog (filling / variant) — centred with translate trick -->
    <img id="base-corndog"
         src="{{ asset('assets/img/custom_sosis_mozza.png') }}"
         alt="Corndog preview"
         class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2
                h-[42vh] w-auto object-contain z-0 select-none">

    <!-- LAYER 1: sauce overlay — identical centring, sits on top -->
    <img id="overlay-sauce"
         src=""
         alt=""
         class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2
                h-[42vh] w-auto object-contain z-10
                pointer-events-none select-none">

  </div>

  <!-- Selection pill -->
  <div class="selection-pill mt-3 px-6 py-2 rounded-full text-center min-w-[160px]" id="carousel-label">
    <span id="carousel-label-text" class="font-black text-base tracking-widest"
          style="color:var(--color-primary);">SOSIS &amp; MOZZA</span>
    <div id="carousel-label-price" class="text-xs font-semibold mt-0.5 hidden"
         style="color:var(--color-primary);"></div>
  </div>

  <!-- Dot indicators -->
  <div id="carousel-dots" class="flex items-center gap-2 mt-3"></div>

  <!-- Sauce chips (step 3) -->
  <div id="sauce-chips" class="hidden flex-wrap gap-2 justify-center mt-2 max-w-xs"></div>

</div>
```

### Arrows
`w-12 h-12 md:w-16 md:h-16` white rounded circles with primary-colored chevrons.

---

## 4. Right Column — Step Instruction Card

Positioned in the right grid cell, vertically centred. On step 4 this card hides and the review panel shows.

**Step card** (`id="step-card"`):
- Red filled circle `w-10 h-10` with step number
- Bold title `text-lg lg:text-xl`
- Gray description `text-sm`
- For step 3 only: "Add Sauce" button + "Max 2 sauce*" note appear inside card

**Review panel** (`id="review-panel"`):
- Hidden on steps 1–3, visible on step 4
- Grid of 3 review cards (Isian, Varian, Saos) + price breakdown

Both elements live in the right grid cell. No internal scroll is needed — the card content is short enough to fit.

---

## 5. Bottom Strip — Navigation Buttons

`flex-none` row below the grid, `py-3`:

```
[ ← Kembali ]   [ Next Pilih Varian ————————————————— ]
```

- "← Kembali": `hidden` on step 1, outlined border button, `px-8 py-4`
- "Next …": `flex-1`, red filled, `py-4`, `rounded-2xl`, full label per step
- On step 4: label becomes "Tambah ke Keranjang 🛒"

---

## 6. jQuery — Layer Update Logic

### `renderCarousel(animate)` changes
For **steps 1 and 2** (Isi and Varian), in addition to current dot/label/sauce-chip logic, update the base image:

```js
$('#base-corndog').attr('src', item.image);  // replaces carousel-img logic
```

For **step 3** (Saos / multiSelect), when a sauce is added:

```js
// Show most-recently-added sauce as overlay
var lastSauce = state.sauces.length
    ? STEPS[2].items[state.sauces[state.sauces.length - 1]].image
    : '';
$('#overlay-sauce').attr('src', lastSauce);
```

When all sauces are deselected, set `src=""` to hide the overlay.

### Image element swap
The single `<img id="carousel-img">` element is replaced by the two-layer system (`#base-corndog` + `#overlay-sauce`). The CSS transition `.fading` class is applied to `#base-corndog` only (the overlay is instant).

### `nextStep()` / `prevStep()`
Remove the `window.scrollTo` calls (already done). No other changes.

---

## 7. CSS Additions / Changes

```css
/* Stepper circles — compact */
#stepper > div > div:first-child {
    width: 2rem !important;
    height: 2rem !important;
    font-size: 0.875rem !important;
}
#stepper > div > span {
    font-size: 0.7rem !important;
    font-weight: 600 !important;
}

/* Corndog base transition */
#base-corndog { transition: opacity 0.2s ease, transform 0.2s ease; }
#base-corndog.fading { opacity: 0; transform: scale(0.95); }

/* Overlay: no transition (instant sauce swap) */
#overlay-sauce { transition: none; }
```

The existing `.corndog-blob`, `.selection-pill`, `.step-line`, `.hide-scrollbar`, `.sauce-chip` classes remain unchanged.

---

## 8. What Is NOT Changing

- All 4-step wizard state (`state.step`, `state.idx`, `state.sauces`)
- `renderStepper()`, `renderStepCard()`, `renderNextBtn()`, `renderReview()`, `renderAll()`
- Carousel prev/next item navigation (arrows + swipe)
- Sauce multi-select logic (max 2, toggleSauce)
- Cart add / "Tambah ke Keranjang" flow
- Navbar (unchanged)
- Footer (stays `hidden`)
- All Blade route helpers and `@vite` include

---

## 9. Files Changed

| File | Change |
|------|--------|
| `resources/views/customize.blade.php` | Full rewrite of HTML structure + scoped CSS; JS changes in `renderCarousel()` only |

No new files, no new routes, no controller changes.
