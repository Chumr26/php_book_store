# Bootstrap 5.3 Big‑Bang Refactor Plan (Fixed Header)

## Goals

-   Migrate frontend to **Bootstrap 5.3** for a consistent design system.
-   Remove most custom/inline CSS and replace with Bootstrap components/utilities.
-   Keep **fixed header** (topbar + main header + nav) without overlap/gap issues.
-   Keep current PHP architecture (controllers + views) and routing (`index.php?page=...`) unless otherwise noted.

## Assumptions (confirm)

-   Bootstrap 5.3 via CDN.
-   Keep jQuery for existing scripts (cart/search) for now; migrate later if desired.
-   Keep current PHP routing and views; introduce `View/partials/` for reusable layout components.

## Non‑Goals (Phase 1)

-   Redesigning database schema or backend APIs.
-   Rewriting all JS to a SPA.
-   Pixel-perfect match to the current theme (we’ll get consistent Bootstrap styling first).

---

## High-Level Strategy (Big‑Bang)

1. **Introduce Bootstrap 5.3 + theme layer** (one stylesheet + minimal overrides).
2. **Replace header/nav with Bootstrap navbar** (fixed, responsive).
3. **Normalize page layout**: one content wrapper with a consistent top offset (no overlaps).
4. **Refactor all pages** to Bootstrap components:
    - grid, cards, forms, buttons, alerts, badges, pagination
5. **Remove inline styles and legacy CSS** progressively until only theme overrides remain.
6. **Regression pass**: responsive behavior, nav sticky behavior, search alignment, cart badge, modals.

---

## Step-by-Step Implementation

### Step 0 — Inventory & Freeze (1–2 hours)

-   Identify all view files and their inline CSS blocks.
-   Identify all JS dependencies (jQuery usage, bootstrap JS usage).
-   Snapshot current UI screenshots for:
    -   home, books list, book detail, cart, checkout/login/register, admin pages (if any)

**Acceptance check**: No code changes yet; we know the scope.

---

### Step 1 — Add Bootstrap 5.3 + Base Theme (0.5–1 day)

#### 1.1 Update CDN references

-   Replace Bootstrap 4.6 CSS/JS with Bootstrap 5.3 CSS + JS bundle.
-   Replace Popper/Bootstrap script tags accordingly.

#### 1.2 Create a theme CSS file

-   Create/fill: `Content/CSS/theme.css` (or reuse `Content/CSS/style.css` if preferred).
-   Add minimal tokens:
    -   header heights variables
    -   cover image sizing helper classes
    -   a few rules for the fixed header offset

#### 1.3 Remove Bootstrap 4 assumptions

-   Replace `.form-inline` usage (BS4) with BS5 utilities.
-   Replace `.input-group-append` usage (removed in BS5).

**Acceptance criteria**

-   Pages load without console errors.
-   No CSS 403/404 blocking layout (static assets might still be missing, but layout should render).

---

### Step 2 — Layout System (Partials) (0.5–1 day)

Create `View/partials/`:

-   `_head.php`: `<head>` common tags + CSS includes
-   `_header.php`: topbar + main header + nav (Bootstrap navbar)
-   `_footer.php`: footer
-   `_scripts.php`: JS includes at end of body
-   Optional: `_flash.php` for alerts

Update entry point(s) to render:

-   head -> header -> content -> footer -> scripts

**Key layout requirement**

-   Fixed header stack should not overlap content:
    -   Use `body { padding-top: var(--header-offset); }` fed by CSS vars
    -   Avoid extra `.main-content` margins

**Acceptance criteria**

-   No gap between nav and first content.
-   No overlap (content starts below nav).

---

### Step 3 — Header Refactor (Bootstrap Navbar) (1 day)

#### 3.1 Implement topbar (optional)

-   Use `.bg-light`, `.border-bottom`, `.small`, `.container`.

#### 3.2 Implement main navbar

-   Use `navbar navbar-expand-lg navbar-light bg-white shadow-sm fixed-top`.
-   Add brand (logo + text).
-   Add nav links: Trang chủ / Sách / Danh mục / ...
-   Add responsive toggler.

#### 3.3 Implement search as input-group (fix alignment)

-   Use:
    -   `.input-group`
    -   button inside `.input-group-text` or `.btn`
-   Ensure it aligns right and works on mobile.

#### 3.4 Auth links sizing

-   Use `.btn btn-outline-primary btn-sm` or `.nav-link small`.
-   Cart count as `.badge text-bg-danger` (BS5).

**Acceptance criteria**

-   Search button aligned correctly across breakpoints.
-   “Đăng nhập / Đăng ký” not oversized.
-   Nav collapses properly on small screens.

---

### Step 4 — Global Components (0.5–1 day)

Standardize repeated patterns:

-   Buttons: primary/outline/secondary, sizes
-   Alerts: `alert alert-*`
-   Forms: validation classes (optional)
-   Pagination: `pagination` component
-   Cards: `card` patterns for books/categories

Create view partials:

-   `View/partials/_book_card.php`
-   `View/partials/_category_card.php`
-   `View/partials/_pagination.php`

**Acceptance criteria**

-   Books and categories render with consistent card design.

---

### Step 5 — Page-by-page Refactor (Big-bang execution) (2–4 days)

Refactor each view to use Bootstrap structure + components, removing inline CSS.

#### 5.1 Home page

-   Hero/slider: wrap in `.container-fluid` or `.container` (choose consistency).
-   Categories section: `row row-cols-2 row-cols-md-4 g-3`, `card`.
-   Benefits blocks: use `card` or `feature` pattern.

#### 5.2 Books listing page

-   Filters/search: `offcanvas` (optional) or sidebar grid.
-   Book grid: `row row-cols-2 row-cols-md-4 g-3`.
-   Ensure cover area uses consistent sizing:
    -   `.book-cover` class with `object-fit: contain`
    -   `loading="lazy"` stays

#### 5.3 Book detail page

-   Two-column layout: cover + details (`col-lg-4` + `col-lg-8`).
-   Qty selector + add to cart as `btn btn-primary`.
-   Reviews in `list-group`.

#### 5.4 Cart + Checkout

-   Tables: `table` (responsive) or list cards on mobile.
-   Summary: `card` with totals.
-   Buttons: consistent CTA.

#### 5.5 Auth pages

-   Centered card forms: `.container` + `.row justify-content-center`.
-   Form feedback using BS5 validation classes.

**Acceptance criteria**

-   No inline `<style>` remaining in views (except a temporary transition if needed).
-   Visual consistency across pages.

---

### Step 6 — Remove Legacy CSS & Fix Asset Paths (0.5–1 day)

-   Delete/stop including old CSS files that conflict (Bootstrap 4 remnants).
-   Ensure only:
    -   Bootstrap 5.3
    -   `theme.css` (thin overrides)
-   Fix any 404 for CSS that is still referenced.

**Acceptance criteria**

-   No conflicting bootstrap styles.
-   Minimal custom CSS < ~200 lines (target).

---

### Step 7 — QA / Regression & Performance (1 day)

Checklist:

-   Header fixed doesn’t overlap, no gaps (all pages).
-   Mobile nav collapses and works.
-   Search works and aligned.
-   Book covers:
    -   Use proxy `/index.php?page=cover&isbn=...`
    -   `loading="lazy"` everywhere
-   Console: no JS errors.
-   Lighthouse basic pass: no major layout shift.

---

## Risks & Mitigations

-   **Bootstrap 4 → 5 class changes**: audit markup (`input-group-*`, `.ml-*`→`.ms-*`, `.mr-*`→`.me-*`, `.float-*` changes).
-   **JS dependencies**: Bootstrap 5 doesn’t need jQuery; keep jQuery only for your scripts.
-   **Fixed header**: must compute offset once; avoid padding+margin duplication.

---

## Deliverables

-   Updated includes in `View/header.php` / partialized header.
-   New `Content/CSS/theme.css` with minimal overrides.
-   Refactored views using Bootstrap grid/cards/forms.
-   Removed inline styles and deprecated BS4 markup patterns.

---

## Definition of Done

-   All user-visible pages render with Bootstrap 5.3 components.
-   No header overlap/gap issues.
-   Search button aligned; auth links sized appropriately.
-   Main content not overlapped by navigation.
-   Minimal custom CSS and consistent UI patterns.

---

## Next Action (after user confirmation)

-   Confirm: keep jQuery? keep current routing? (see assumptions)
-   Then implement Step 1 + Step 2 (Bootstrap 5.3 includes + partial layout) as the first PR chunk.
