# Customize Viewport-Lock Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Transform `resources/views/customize.blade.php` into a non-scrollable, viewport-fitted wizard matching the approved design spec.

**Architecture:** All changes are confined to a single Blade file. HTML structure is tightened for viewport-fit, the single-image carousel is replaced with a two-layer system (`#base-corndog` + `#overlay-sauce`), and `renderCarousel()` JS is updated to drive both layers.

**Tech Stack:** Laravel 11 Blade, Tailwind CSS (utility classes), jQuery 3.7.1

**Spec reference:** `docs/superpowers/specs/2026-05-23-customize-viewport-lock-design.md`

---

## File Modified

| File | Sections changed |
|------|-----------------|
| `resources/views/customize.blade.php` | `<style>` block, title HTML, blob+image HTML, right-column HTML, `renderCarousel()` JS, touch handler JS |

---

### Task 1: Update CSS Block

Update the scoped `<style>` block (lines 9–72).

**Changes:**
- Rename `#carousel-img` → `#base-corndog` (transition + fading rules)
- Add `#overlay-sauce { transition: none; }` rule
- Shrink stepper circles from `3rem` to `2rem`, font from `1.25rem` to `0.875rem`
- Add `#base-corndog.fading` rule

- [ ] **Step 1: Replace the carousel-img transition rules**

  In `customize.blade.php`, find:
  ```css
  /* Carousel transition */
  #carousel-img {
      transition: opacity 0.2s ease, transform 0.2s ease;
  }
  #carousel-img.fading {
      opacity: 0;
      transform: scale(0.95);
  }
  ```
  Replace with:
  ```css
  /* Corndog base transition */
  #base-corndog { transition: opacity 0.2s ease, transform 0.2s ease; }
  #base-corndog.fading { opacity: 0; transform: scale(0.95); }

  /* Overlay: instant sauce swap */
  #overlay-sauce { transition: none; }
  ```

- [ ] **Step 2: Shrink stepper circle CSS override**

  Find:
  ```css
  /* Stepper override — circles rendered by JS use these sizes */
  #stepper > div > div:first-child {
      width: 3rem !important;
      height: 3rem !important;
      font-size: 1.25rem !important;
  }
  #stepper > div > span {
      font-size: 0.8rem !important;
      font-weight: 600 !important;
  }
  ```
  Replace with:
  ```css
  /* Stepper override — compact for viewport fit */
  #stepper > div > div:first-child {
      width: 2rem !important;
      height: 2rem !important;
      font-size: 0.875rem !important;
  }
  #stepper > div > span {
      font-size: 0.7rem !important;
      font-weight: 600 !important;
  }
  ```

- [ ] **Step 3: Commit CSS changes**

  ```bash
  git add resources/views/customize.blade.php
  git commit -m "style(customize): compact stepper + rename carousel-img → base-corndog CSS"
  ```

---

### Task 2: Scale Down Title + Compact Subtitle

Scale the oversized title and subtitle pill so the top row consumes minimal vertical space.

- [ ] **Step 1: Shrink CUSTOM/CORNDOG text and wavy underline**

  Find:
  ```html
  <div class="text-6xl sm:text-7xl lg:text-[90px] xl:text-[108px] font-black leading-none tracking-tight">
      <span style="color: #1a1a1a;">CUSTOM</span>
      {{-- Spark accent --}}
      <span class="inline-block ml-1 text-3xl lg:text-4xl" style="color: #FFBE54;">✦</span>
  </div>
  <div class="text-6xl sm:text-7xl lg:text-[90px] xl:text-[108px] font-black leading-none tracking-tight"
       style="color: var(--color-primary);">CORNDOG</div>
  {{-- Wavy underline --}}
  <svg class="mt-1" width="340" height="14" viewBox="0 0 340 14" fill="none" xmlns="http://www.w3.org/2000/svg">
      <path d="M2 7 Q28 2 54 7 Q80 12 106 7 Q132 2 158 7 Q184 12 210 7 Q236 2 262 7 Q288 12 314 7 Q327 4 338 7"
            stroke="#A6171C" stroke-width="3.5" stroke-linecap="round" fill="none"/>
  </svg>
  ```
  Replace with:
  ```html
  <div class="text-4xl sm:text-5xl font-black leading-none tracking-tight">
      <span style="color: #1a1a1a;">CUSTOM</span>
      <span class="inline-block ml-1 text-xl sm:text-2xl" style="color: #FFBE54;">✦</span>
  </div>
  <div class="text-4xl sm:text-5xl font-black leading-none tracking-tight"
       style="color: var(--color-primary);">CORNDOG</div>
  <svg class="mt-1" width="200" height="10" viewBox="0 0 200 10" fill="none" xmlns="http://www.w3.org/2000/svg">
      <path d="M2 5 Q18 1 34 5 Q50 9 66 5 Q82 1 98 5 Q114 9 130 5 Q146 1 162 5 Q178 9 194 5"
            stroke="#A6171C" stroke-width="2.5" stroke-linecap="round" fill="none"/>
  </svg>
  ```

- [ ] **Step 2: Compact the subtitle pill**

  Find:
  ```html
  <div class="mt-5 inline-flex items-center gap-2 px-6 py-3.5 rounded-full font-bold text-base lg:text-lg"
       style="background-color: #FFBE54; color: #1a1a1a;">
      Buat Corndog Favoritemu Sesuai Seleramu
      <svg class="w-5 h-5 flex-none" viewBox="0 0 24 24" fill="#A6171C">
  ```
  Replace the opening div only:
  ```html
  <div class="mt-3 inline-flex items-center gap-2 px-4 py-2 rounded-full font-bold text-sm"
       style="background-color: #FFBE54; color: #1a1a1a;">
      Buat Corndog Favoritemu Sesuai Seleramu
      <svg class="w-4 h-4 flex-none" viewBox="0 0 24 24" fill="#A6171C">
  ```

- [ ] **Step 3: Commit**

  ```bash
  git add resources/views/customize.blade.php
  git commit -m "style(customize): scale down title + compact subtitle pill for viewport fit"
  ```

---

### Task 3: Replace Blob + Single Image with Two-Layer System

This is the core structural change. The single `#carousel-img` is replaced by `#base-corndog` (filling/variant layer) and `#overlay-sauce` (sauce drizzle layer, transparent PNG).

- [ ] **Step 1: Replace the blob div and image**

  Find:
  ```html
  {{-- Peach blob --}}
  <div class="corndog-blob w-80 h-80 sm:w-96 sm:h-96 lg:w-[500px] lg:h-[500px]
              flex items-center justify-center relative">
      <img id="carousel-img"
           src="{{ asset('assets/img/custom_sosis_mozza.png') }}"
           alt="Corndog preview"
           class="h-[280px] sm:h-[380px] lg:h-[560px] w-auto object-contain drop-shadow-xl">
      {{-- Spark accent near image --}}
      <div class="absolute top-3 right-3 text-3xl pointer-events-none"
           style="color: #A6171C;">✦</div>
  </div>
  ```
  Replace with:
  ```html
  {{-- Peach blob — NO overflow-hidden: corndog stick extends below --}}
  <div class="corndog-blob w-[min(55vw,460px)] h-[min(55vw,460px)] relative">

      {{-- LAYER 0: base corndog (filling / variant) --}}
      <img id="base-corndog"
           src="{{ asset('assets/img/custom_sosis_mozza.png') }}"
           alt="Corndog preview"
           class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2
                  h-[42vh] w-auto object-contain z-0 select-none">

      {{-- LAYER 1: sauce overlay — sits on top, instant swap --}}
      <img id="overlay-sauce"
           src=""
           alt=""
           class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2
                  h-[42vh] w-auto object-contain z-10
                  pointer-events-none select-none">

      {{-- Spark accent --}}
      <div class="absolute top-3 right-3 text-2xl pointer-events-none z-20"
           style="color: #A6171C;">✦</div>
  </div>
  ```

- [ ] **Step 2: Compact the selection pill and dots below blob**

  Find:
  ```html
  <div class="selection-pill mt-5 px-8 py-3 rounded-full text-center min-w-[220px]"
  ```
  Replace with:
  ```html
  <div class="selection-pill mt-3 px-6 py-2 rounded-full text-center min-w-[160px]"
  ```

  Find:
  ```html
  <span class="font-black text-xl tracking-widest"
  ```
  Replace with:
  ```html
  <span class="font-black text-base tracking-widest"
  ```

  Find:
  ```html
  <div id="carousel-label-price" class="text-sm font-semibold mt-1 hidden"
  ```
  Replace with:
  ```html
  <div id="carousel-label-price" class="text-xs font-semibold mt-0.5 hidden"
  ```

  Find:
  ```html
  <div id="carousel-dots" class="flex items-center gap-3 mt-5"></div>

  {{-- Step 3 sauce chips (hidden except step 3) --}}
  <div id="sauce-chips" class="hidden flex-wrap gap-2 justify-center mt-4 max-w-sm"></div>
  ```
  Replace with:
  ```html
  <div id="carousel-dots" class="flex items-center gap-2 mt-3"></div>

  {{-- Step 3 sauce chips (hidden except step 3) --}}
  <div id="sauce-chips" class="hidden flex-wrap gap-2 justify-center mt-2 max-w-xs"></div>
  ```

- [ ] **Step 3: Compact the carousel arrows**

  Find both arrow buttons (btn-prev and btn-next):
  ```html
  class="flex-none w-16 h-16 md:w-20 md:h-20 rounded-full bg-white shadow-md flex items-center justify-center
         font-bold text-2xl md:text-4xl hover:shadow-lg transition-shadow active:scale-95"
  ```
  Replace both with:
  ```html
  class="flex-none w-12 h-12 md:w-16 md:h-16 rounded-full bg-white shadow-md flex items-center justify-center
         font-bold text-xl md:text-3xl hover:shadow-lg transition-shadow active:scale-95"
  ```

- [ ] **Step 4: Commit**

  ```bash
  git add resources/views/customize.blade.php
  git commit -m "feat(customize): replace carousel-img with two-layer base-corndog + overlay-sauce system"
  ```

---

### Task 4: Fix Right Column — Remove Scroll, Compact Cards

The right column must not scroll; all content (step card + review + buttons) fits in the viewport.

- [ ] **Step 1: Remove overflow-y-auto from right column**

  Find:
  ```html
  <div class="h-full overflow-y-auto hide-scrollbar flex flex-col gap-4 py-2 pb-24">
  ```
  Replace with:
  ```html
  <div class="h-full flex flex-col justify-center gap-3 py-2">
  ```

- [ ] **Step 2: Compact the step instruction card**

  Find:
  ```html
  <div id="step-card"
       class="bg-white rounded-2xl p-8 md:p-10 shadow-lg">
      <div class="flex items-start gap-4">
          <div id="step-card-num"
               class="w-12 h-12 rounded-full flex items-center justify-center
                      text-white font-bold text-xl flex-none"
  ```
  Replace with:
  ```html
  <div id="step-card"
       class="bg-white rounded-2xl p-6 shadow-lg">
      <div class="flex items-start gap-4">
          <div id="step-card-num"
               class="w-10 h-10 rounded-full flex items-center justify-center
                      text-white font-bold text-base flex-none"
  ```

  Find:
  ```html
  <p id="step-card-title" class="font-bold text-xl lg:text-2xl" style="color: var(--color-black);">
  ```
  Replace with:
  ```html
  <p id="step-card-title" class="font-bold text-lg lg:text-xl" style="color: var(--color-black);">
  ```

  Find:
  ```html
  <p id="step-card-desc" class="text-base text-gray-500 mt-2 leading-relaxed">
  ```
  Replace with:
  ```html
  <p id="step-card-desc" class="text-sm text-gray-500 mt-1 leading-relaxed">
  ```

- [ ] **Step 3: Compact the navigation buttons**

  Find:
  ```html
  <button id="btn-back"
          type="button"
          class="hidden sm:inline-flex items-center justify-center px-10 py-5 rounded-2xl
                 font-bold text-base border-2 transition-opacity hover:opacity-70"
  ```
  Replace with:
  ```html
  <button id="btn-back"
          type="button"
          class="hidden sm:inline-flex items-center justify-center px-8 py-4 rounded-2xl
                 font-bold text-base border-2 transition-opacity hover:opacity-70"
  ```

  Find:
  ```html
  <button id="btn-next-step"
          type="button"
          class="flex-1 max-w-4xl mx-auto w-full py-5 rounded-2xl font-bold
                 text-xl md:text-2xl tracking-wide
                 transition-opacity hover:opacity-85 active:scale-[0.99]"
  ```
  Replace with:
  ```html
  <button id="btn-next-step"
          type="button"
          class="flex-1 max-w-4xl mx-auto w-full py-4 rounded-2xl font-bold
                 text-lg tracking-wide
                 transition-opacity hover:opacity-85 active:scale-[0.99]"
  ```

- [ ] **Step 4: Commit**

  ```bash
  git add resources/views/customize.blade.php
  git commit -m "style(customize): remove right-column scroll, compact cards and buttons"
  ```

---

### Task 5: Update `renderCarousel()` + Touch Handler JS

Replace `carousel-img` references in JavaScript with the two-layer system.

- [ ] **Step 1: Replace the image-update block in renderCarousel()**

  Find:
  ```js
  // Image with fade transition
  var imgEl = document.getElementById('carousel-img');
  if (animate) {
      imgEl.classList.add('fading');
      setTimeout(function () {
          imgEl.src = item.image;
          imgEl.classList.remove('fading');
      }, 180);
  } else {
      imgEl.src = item.image;
  }
  ```
  Replace with:
  ```js
  // Update base image (steps 1 & 2: filling/variant)
  if (animate) {
      $('#base-corndog').addClass('fading');
      setTimeout(function () {
          $('#base-corndog').attr('src', item.image).removeClass('fading');
      }, 180);
  } else {
      $('#base-corndog').attr('src', item.image);
  }

  // Update sauce overlay (step 3 only — show last-added sauce)
  if (step.multiSelect) {
      var lastSauce = state.sauces.length
          ? STEPS[2].items[state.sauces[state.sauces.length - 1]].image
          : '';
      $('#overlay-sauce').attr('src', lastSauce);
  } else {
      $('#overlay-sauce').attr('src', '');
  }
  ```

- [ ] **Step 2: Update the touch handler to target #base-corndog**

  Find:
  ```js
  $('#carousel-img').on('touchstart', function (e) {
      touchStartX = e.originalEvent.touches[0].clientX;
  }).on('touchend', function (e) {
      var dx = e.originalEvent.changedTouches[0].clientX - touchStartX;
      if (Math.abs(dx) > 40) {
          dx < 0 ? nextItem() : prevItem();
      }
  });
  ```
  Replace with:
  ```js
  $('#carousel-center').on('touchstart', function (e) {
      touchStartX = e.originalEvent.touches[0].clientX;
  }).on('touchend', function (e) {
      var dx = e.originalEvent.changedTouches[0].clientX - touchStartX;
      if (Math.abs(dx) > 40) {
          dx < 0 ? nextItem() : prevItem();
      }
  });
  ```

- [ ] **Step 3: Commit**

  ```bash
  git add resources/views/customize.blade.php
  git commit -m "feat(customize): update renderCarousel JS for two-layer image system"
  ```

---

### Task 6: Verify Viewport Fit + Final Commit

Manual verification checklist in browser at `http://corndogku.test/customize`:

- [ ] **Step 1: Open the page and verify no vertical scrollbar appears**
  - Body should be `h-screen overflow-hidden` — no page scroll at any step

- [ ] **Step 2: Verify all 4 wizard steps work**
  - Step 1 (Isi): corndog image changes with arrows; `#base-corndog` updates; `#overlay-sauce` src is `""`
  - Step 2 (Varian): corndog image changes; `#overlay-sauce` src is `""`
  - Step 3 (Saos): adding a sauce sets `#overlay-sauce` src to that sauce's image; removing all clears it to `""`
  - Step 4 (Review): carousel hidden; review panel visible; no overflow

- [ ] **Step 3: Verify stepper circles are compact (~32px)**

- [ ] **Step 4: Verify title is `text-4xl sm:text-5xl` (not huge)**

- [ ] **Step 5: Final commit if any tweaks were made**

  ```bash
  git add resources/views/customize.blade.php
  git commit -m "fix(customize): final viewport-fit tweaks after visual verification"
  ```

---

## Self-Review Against Spec

**Spec coverage check:**
- ✅ Section 1 (Layout Architecture): `h-screen overflow-hidden flex flex-col` on body already done; grid `flex-1 overflow-hidden` already done; Tasks 1–4 handle remaining layout
- ✅ Section 2 (Top Row): Task 2 scales title to `text-4xl sm:text-5xl`; stepper CSS done in Task 1
- ✅ Section 3 (Left Column): Task 3 rebuilds blob to `w-[min(55vw,460px)]`, adds `#base-corndog` + `#overlay-sauce`
- ✅ Section 4 (Right Column): Task 4 removes scroll, compacts step card
- ✅ Section 5 (Bottom Strip): Task 4 compacts buttons (`py-4`, `px-8`)
- ✅ Section 6 (jQuery Layer Update): Task 5 updates `renderCarousel()` with two-layer logic
- ✅ Section 7 (CSS Additions): Task 1 covers all CSS changes
- ✅ Section 8 (What is NOT changing): Wizard state, stepper/stepcard/review render functions, cart flow, navbar — none touched

**No placeholders present in this plan.**

**Type consistency:** `#base-corndog` and `#overlay-sauce` IDs used consistently across HTML (Task 3) and JS (Task 5). `renderCarousel` function name unchanged.
