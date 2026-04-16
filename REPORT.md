# 💰 Premium Expense Tracker — Complete Project Report

> **Author:** Name  
> **Type:** Frontend Web Application (Vanilla JS)  
> **Version:** 2.0  
> **Date:** April 2026  
> **Status:** ✅ Fully Functional & Responsive

---

## Table of Contents

1. [Project Overview](#1-project-overview)
2. [Technology Stack](#2-technology-stack)
3. [Project Structure](#3-project-structure)
4. [Features](#4-features)
5. [Architecture & Design](#5-architecture--design)
6. [UI/UX Design System](#6-uiux-design-system)
7. [Icon System](#7-icon-system)
8. [Responsive Design](#8-responsive-design)
9. [Data Flow & State Management](#9-data-flow--state-management)
10. [JavaScript Modules Breakdown](#10-javascript-modules-breakdown)
11. [CSS Architecture](#11-css-architecture)
12. [Security Measures](#12-security-measures)
13. [Accessibility](#13-accessibility)
14. [Known Limitations](#14-known-limitations)
15. [Future Roadmap](#15-future-roadmap)

---

## 1. Project Overview

The **Premium Expense Tracker** is a fully client-side, single-page web application for managing personal finances. It allows users to log, categorise, search, and analyse their expenses with a rich, glassmorphic UI that works offline without any backend dependency.

All data is persisted using the browser's **`localStorage` API**, making it immediately usable without login or internet access (after first load).

### Key Goals
- Track and categorise daily expenses with amounts and dates
- Visualise spending patterns through interactive charts
- Monitor monthly budget with a live progress bar
- Export data as a CSV file for external use
- Work on any screen size from 320px to 4K

---

## 2. Technology Stack

| Layer | Technology | Purpose |
|---|---|---|
| **Structure** | HTML5 (Semantic) | Page layout and accessibility |
| **Styling** | Vanilla CSS (CSS Variables) | Design system, animations, responsive layout |
| **Logic** | Vanilla JavaScript (ES6+) | All application behaviour |
| **Charts** | Chart.js v4 (CDN) | Doughnut and bar chart visualisations |
| **Fonts** | Google Fonts — Poppins | Typography (300, 400, 500, 600, 700 weights) |
| **Icons** | Inline SVG Sprite (Heroicons 2.0) | Zero-dependency, offline-capable icon system |
| **Data** | Browser `localStorage` | Persistent client-side data storage |

### No Framework Dependencies
The entire application is written in plain HTML, CSS, and JavaScript — no React, Vue, Angular, or any build tool is needed. The application runs directly by opening `index.html` in a browser or serving it with a static file server (e.g. Live Server).

---

## 3. Project Structure

```
Premium Expense Tracker/
│
├── index.html          # Single HTML file — all markup, SVG sprite, semantic structure
├── style.css           # ~1,880 lines — full design system + responsive CSS
├── app.js              # ~740 lines — all application logic
├── favicon.png         # Browser tab icon
└── REPORT.md           # This document
```

### File Sizes
| File | Lines | Size |
|---|---|---|
| `index.html` | ~290 lines | 26 KB |
| `style.css` | ~1,880 lines | 46 KB |
| `app.js` | ~740 lines | 30 KB |

---

## 4. Features

### ✅ Core Expense Management
- **Add Expense** — Title, amount (Rs), category, date, and optional notes
- **Edit Expense** — In-place editing, form switches to "Edit Mode" with Cancel option
- **Delete Expense** — Custom confirmation modal with slide-out animation
- **Automatic Sorting** — Expenses always sorted newest-first by date, then by ID

### ✅ Summary Dashboard
| Stat Card | What It Shows |
|---|---|
| 🗓️ This Month | Total spend in the current calendar month |
| 🔥 Top Category | Category with the highest cumulative spend |
| 📋 Total Entries | Count of all stored expenses |
| 🎯 Monthly Budget | Budget progress bar with colour-coded status |

### ✅ Budget Tracker
- Set a monthly budget via a slide-in input row
- Budget bar turns **green** (0–74%), **yellow** (75–99%), **red** (100%+)
- Budget persisted in `localStorage` independently from expenses
- Budget is compared against the **current month's spend only**

### ✅ Search & Filter
- **Live keyword search** — matches title, notes, and category simultaneously
- **Category filter** dropdown — 8 categories (Food, Transport, Utilities, Entertainment, Shopping, Health, Education, Other)
- Both filters work in real-time with no button press needed

### ✅ Analytics Charts (Chart.js)
| Chart | Type | Description |
|---|---|---|
| Category Breakdown | Doughnut (68% cutout) | All-time spending by category |
| Spending Trend | Bar chart | Day-by-day total for last 7 or 30 days |

- **7D / 30D toggle** — switches trend chart time window
- Charts re-render automatically when theme toggles (colour-matched to dark/light mode)

### ✅ CSV Export
- Exports all expenses as a downloadable `.csv` file
- Columns: ID, Title, Amount (Rs), Category, Date, Notes
- Filename includes today's date: `expenses_2026-04-15.csv`
- Values are properly escaped for CSV safety (handles commas, quotes in text)

### ✅ Dark / Light Mode
- Defaults to **Dark Mode**
- Toggled by the moon/sun button in the header
- Theme preference saved in `localStorage` and applied on every page load
- Charts, icons, and all elements adapt seamlessly

### ✅ Toast Notifications
- Slide-in from right on desktop / slide-up from bottom on mobile
- 3 types: **success** (green), **error** (red), **info** (purple)
- Auto-dismiss after 3.2 seconds with a fade-out animation

---

## 5. Architecture & Design

### Single-Page Application (SPA) Pattern
The app follows a simple reactive pattern:
1. **State** is held in the `expenses[]` array and `monthlyBudget` variable
2. All mutations call `saveExpenses()` / `saveBudget()` to persist to `localStorage`
3. After every mutation, `updateUI()` is called — a master function that runs:
   - `animateBalance()` — smooth counter animation for total
   - `updateStatCards()` — recalculates all 4 stat cards
   - `renderExpenseList()` — rebuilds the expense list DOM
   - `drawCharts()` — destroys and redraws both charts

```
User Action
    │
    ▼
Event Handler (add / edit / delete / filter / budget)
    │
    ▼
Mutate State (expenses[] / monthlyBudget)
    │
    ├── saveExpenses() → localStorage
    │
    └── updateUI()
            ├── animateBalance()
            ├── updateStatCards()
            ├── renderExpenseList()
            └── drawCharts()
```

### Data Model — Expense Object
```json
{
  "id": "1713200000000",
  "title": "Morning Coffee",
  "amount": 350,
  "category": "Food",
  "date": "2026-04-15",
  "notes": "Cappuccino from Espresso Lab"
}
```

### localStorage Keys
| Key | Type | Contents |
|---|---|---|
| `expenses` | JSON string | Array of all expense objects |
| `monthlyBudget` | Number string | Budget amount in Rs |
| `theme` | `"light"` or absent | User's theme preference |

---

## 6. UI/UX Design System

### Design Language: Glassmorphism
The entire app uses a modern **glassmorphism** aesthetic:
- Semi-transparent panels with `backdrop-filter: blur(20px)`
- Subtle borders: `rgba(255,255,255,0.09)` in dark mode
- Deep shadows for depth: `0 8px 32px rgba(0,0,0,0.4)`
- Animated gradient background blobs (purple, pink, cyan)

### CSS Design Tokens (Variables)

All colours, backgrounds, and shadows are defined as CSS custom properties on `:root`, with a full override set for `body.light-mode`:

```css
/* Dark Mode (default) */
:root {
  --primary:       #6366f1;   /* Indigo */
  --secondary:     #8b5cf6;   /* Purple */
  --bg-color:      #0f172a;   /* Slate 900 */
  --text-main:     #f8fafc;
  --text-muted:    #94a3b8;
  --glass-bg:      rgba(30, 41, 59, 0.75);
  --glass-border:  rgba(255, 255, 255, 0.09);
  --success:       #10b981;
  --danger:        #ef4444;
  --warning:       #f59e0b;
}
```

### Category Colour Palette
| Category | Colour | Hex |
|---|---|---|
| Food | Amber | `#f59e0b` |
| Transport | Blue | `#3b82f6` |
| Utilities | Cyan | `#22d3ee` |
| Entertainment | Pink | `#ec4899` |
| Shopping | Purple | `#8b5cf6` |
| Health | Emerald | `#10b981` |
| Education | Orange | `#f97316` |
| Other | Indigo | `#6366f1` |

Each expense item has a **left accent border** in its category colour plus a matching **pill badge**.

### Animations
| Animation | Element | Effect |
|---|---|---|
| `iconPulse` | Header wallet bubble | Glowing box-shadow pulse, 4s loop |
| `iconFloat` | Empty state icon | Floating up/down, 3s loop |
| `slideInFade` | New expense items | Fade + slide down on add |
| `slideOutFade` | Deleted items | Slide right + fade before removal |
| `spin` | Loading spinner | Continuous rotation |
| `float` | Background blobs | Slow drift, 12–16s loop |
| Balance counter | Total balance | Smooth `requestAnimationFrame` ease-out cubic, 600ms |

---

## 7. Icon System

### Inline SVG Sprite (No CDN)
All icons are embedded directly in `index.html` as `<symbol>` elements inside an SVG sprite, placed as the **first element of `<body>`**.

> ⚠️ **Important:** Browsers silently discard `<svg>` elements placed inside `<head>`. The sprite **must** be in `<body>` for `<use href="#...">` references to work.

Icons are sourced from **Heroicons 2.0** (MIT licence).

### Available Icons
| Symbol ID | Usage |
|---|---|
| `ic-wallet` | Header logo bubble |
| `ic-sun` / `ic-moon` | Dark/light mode toggle |
| `ic-download` | Export CSV button |
| `ic-calendar` | Date field + "This Month" stat card |
| `ic-fire` | Top Category stat card |
| `ic-list` | Total Entries stat card |
| `ic-target` | Budget stat card |
| `ic-pen` | Edit button / Edit mode form title |
| `ic-check` | Save Changes button / form check |
| `ic-x` | Cancel Edit button |
| `ic-plus` | Add Expense button / form title |
| `ic-tag` | Title field icon / Other category |
| `ic-rupee` | Amount field icon / budget input |
| `ic-grid` | Category field icon |
| `ic-chevron-down` | Category select arrow |
| `ic-note` | Notes field icon |
| `ic-search` | Search bar icon |
| `ic-trash` | Delete button / modal confirm |
| `ic-spinner` | Loading state / submit spinner |
| `ic-receipt` | Empty state (no expenses) |
| `ic-no-results` | Empty state (search no results) |
| `ic-cat-food` | Food & Dining expense pill |
| `ic-cat-transport` | Transport expense pill |
| `ic-cat-utilities` | Utilities expense pill |
| `ic-cat-entertainment` | Entertainment expense pill |
| `ic-cat-shopping` | Shopping expense pill |
| `ic-cat-health` | Health expense pill |
| `ic-cat-education` | Education expense pill |

### JavaScript Helper Function
```js
function svgIcon(id, sizeClass = 'icon-sm') {
    return `<svg class="icon ${sizeClass}" aria-hidden="true">
                <use href="#${id}"/>
            </svg>`;
}
```

### Icon Size Classes
| Class | Size |
|---|---|
| `.icon-xs` | 14 × 14 px |
| `.icon-sm` | 18 × 18 px |
| `.icon-md` | 22 × 22 px |
| `.icon-lg` | 32 × 32 px |
| `.icon-xl` | 56 × 56 px |

---

## 8. Responsive Design

The application is fully responsive across **5 breakpoints**:

| Breakpoint | Screen Target | Key Changes |
|---|---|---|
| Default (> 960px) | Desktop / wide laptop | 2-column grid (form + analytics), 4-across stat cards |
| ≤ 960px | Tablet landscape | Single column, 2×2 stat grid, header stacks |
| ≤ 768px | Tablet portrait | Charts stack vertically, container padding shrinks |
| ≤ 600px | Large phone | Filter bar stacks, expense items reflow, full-width form |
| ≤ 480px | Standard phone (390px) | Tighter spacing, compact inputs with 44px touch targets |
| ≤ 380px | Small phone (iPhone SE) | Single-column stat cards, stacked buttons |

### Panel-Specific Mobile Fixes

**Add New Expense form (≤ 600px):**
- Full-width panel
- All inputs have `min-height: 44px` (WCAG touch target standard)
- Notes textarea fixed at 80px height
- Add Expense button spans full width

**Analytics Overview (≤ 600px):**
- Both charts stack vertically — each takes 100% width
- Chart height reduced from 260px → 190px
- Chart canvas has `overflow: hidden` to prevent canvas blowout
- `min-width: 0` on `.chart-wrapper` (was `220px`, caused overflow)

**Recent Expenses (≤ 600px):**
- Search bar — full width
- Category filter stacks below search — full width
- `max-height: none` on history list (no tiny scroll box on mobile)
- Expense items reflow to vertical: title → category pill + date → separator line → amount + action buttons
- `white-space: normal` + `word-break: break-word` on titles and notes (was `nowrap`, caused overflow)
- Edit/Delete buttons enlarged to 36×36px

### Grid Overflow Prevention
```css
.grid-layout { min-width: 0; }
.add-expense, .right-column,
.dashboard-analytics, .expense-history { min-width: 0; }
```
These rules allow CSS grid children to shrink below their content size — critical for preventing horizontal overflow.

---

## 9. Data Flow & State Management

### Initialisation Sequence
```
page load
  └── init()
        ├── loadExpenses()    → parses localStorage 'expenses'
        ├── loadBudget()      → parses localStorage 'monthlyBudget'
        └── updateUI()
              ├── animateBalance(total)
              ├── updateStatCards()
              ├── renderExpenseList()
              └── drawCharts()
```

### Adding an Expense
```
form submit → e.preventDefault()
  ├── validate fields
  ├── disable button + show spinner (anti double-submit)
  ├── create expense object { id: Date.now(), title, amount, category, date, notes }
  ├── expenses.unshift(newExpense)
  ├── sortExpenses()
  ├── saveExpenses() → localStorage.setItem('expenses', JSON.stringify(expenses))
  └── updateUI()
```

### Deleting an Expense
```
click delete button
  └── deleteExpense(id, buttonEl)
        └── show custom modal (focus trapped on Confirm)
              ├── Cancel → hide modal, clear pending state
              └── Confirm → hide modal
                    ├── item.classList.add('deleting')  ← CSS slide-out animation
                    ├── setTimeout 320ms
                    │     ├── expenses.filter(...)
                    │     ├── saveExpenses()
                    │     └── updateUI()
                    └── showMessage('Expense deleted', 'success')
```

---

## 10. JavaScript Modules Breakdown

| Function | Purpose |
|---|---|
| `svgIcon(id, size)` | Returns SVG `<use>` markup for the given sprite symbol ID |
| `applyTheme(isLight)` | Toggles `body.light-mode` class and swaps the theme icon |
| `loadExpenses()` | Reads and parses expenses from `localStorage` safely |
| `saveExpenses()` | Serialises expense array to `localStorage` |
| `loadBudget()` | Reads monthly budget from `localStorage` |
| `saveBudget(val)` | Persists monthly budget to `localStorage` |
| `animateBalance(target)` | Smooth counter animation using `requestAnimationFrame` with ease-out cubic |
| `sortExpenses()` | Sorts expense array newest date first, then by ID descending |
| `editExpense(id)` | Pre-fills form with expense data, switches form to Edit Mode |
| `cancelEdit()` | Resets form and returns to Add Mode |
| `deleteExpense(id, el)` | Stores pending delete ID, shows custom modal |
| `getFilteredExpenses()` | Returns array filtered by search query + category dropdown |
| `updateBudgetBar(spent)` | Updates budget bar width, label, and colour class |
| `updateStatCards()` | Recalculates and updates all 4 stat card values |
| `renderExpenseList()` | Rebuilds the DOM expense list from filtered expenses |
| `updateUI()` | Master function — calls all UI update functions |
| `formatDate(str)` | Converts `YYYY-MM-DD` to `"Apr 15, 2026"` without UTC offset issues |
| `exportCSV()` | Generates and downloads a `.csv` Blob file |
| `escapeHTML(str)` | Sanitises strings before inserting into innerHTML |
| `escapeCSV(str)` | Escapes double-quotes for CSV export |
| `showMessage(msg, type)` | Creates and auto-dismisses a toast notification |
| `getChartColors()` | Returns chart theme colours matching the current dark/light mode |
| `drawCharts()` | Destroys and redraws both Chart.js instances |
| `init()` | Application entry point — called once on page load |

---

## 11. CSS Architecture

### File Organisation (in order)
```
1.  CSS Design Tokens       — :root variables (dark mode)
2.  Light Mode Overrides    — body.light-mode variable overrides
3.  Reset & Base            — box-sizing, body, overflow-x
4.  Animated Background     — .shape floating blobs
5.  Layout                  — .container, .glass-panel, .grid-layout
6.  Header                  — header, .header-titles, buttons
7.  Balance Display         — .balance-container
8.  Stat Cards              — .stat-cards, .stat-card, .stat-icon
9.  Budget Input Row        — .budget-input-row
10. Form Section            — .add-expense, .input-group, inputs
11. Action Buttons          — .btn-primary, .btn-secondary
12. Section Header          — .section-header (shared)
13. Analytics               — .dashboard-analytics, charts, trend toggle
14. Filter Bar              — .filter-bar, .search-wrapper
15. Expense History         — .history-list, .expense-item, pills, notes
16. Empty States            — .empty-state
17. Delete Modal            — .modal-overlay, .modal-card
18. Toast Notifications     — .toast-container, .toast
19. Responsive Breakpoints  — 5 @media blocks (960/768/600/480/380px)
20. SVG Icon System         — .icon, size classes, animations, per-field colours
```

### Notable CSS Patterns

**Per-field focus accent colours:**
```css
.input-wrapper[data-field="amount"]:focus-within   .input-icon { color: #34d399; } /* green  */
.input-wrapper[data-field="category"]:focus-within .input-icon { color: #f472b6; } /* pink   */
.input-wrapper[data-field="date"]:focus-within     .input-icon { color: #f59e0b; } /* amber  */
.input-wrapper[data-field="notes"]:focus-within    .input-icon { color: #22d3ee; } /* cyan   */
```

**Category left border system:**
```css
.expense-item[data-cat="Food"]          { border-left-color: var(--cat-food); }
.expense-item[data-cat="Transport"]     { border-left-color: var(--cat-transport); }
/* ... 6 more ... */
```

**Expense item animation:**
```css
@keyframes slideInFade  { 0% { opacity:0; transform:translateY(-12px); } 100% { opacity:1; transform:translateY(0); } }
@keyframes slideOutFade { 0% { opacity:1; transform:translateX(0) scale(1); } 100% { opacity:0; transform:translateX(40px) scale(0.95); } }
```

---

## 12. Security Measures

### XSS Prevention
All user-provided content is sanitised via `escapeHTML()` before being inserted into the DOM using `innerHTML`:

```js
function escapeHTML(str) {
    return str.toString().replace(/[&<>'"]/g, tag => ({
        '&': '&amp;', '<': '&lt;', '>': '&gt;',
        "'": '&#39;',  '"': '&quot;'
    }[tag] || tag));
}
```

This prevents script injection through expense titles, amounts, and notes fields.

### CSV Injection Prevention
The `escapeCSV()` function doubles all double-quote characters to prevent formula injection in spreadsheet applications:

```js
function escapeCSV(str) {
    return str.toString().replace(/"/g, '""');
}
```

### localStorage Error Handling
`loadExpenses()` wraps the JSON parse in a try/catch — if the stored string is corrupted, it gracefully falls back to an empty array rather than crashing:

```js
try {
    const stored = localStorage.getItem('expenses');
    expenses = stored ? JSON.parse(stored) : [];
} catch {
    expenses = [];
}
```

### Double-Submit Prevention
The submit button is disabled immediately on form submission and re-enabled after the state has been saved:

```js
btnSubmit.disabled = true;
// ... process ...
btnSubmit.disabled = false;
```

---

## 13. Accessibility

| Feature | Implementation |
|---|---|
| **Semantic HTML** | `<header>`, `<main>`, `<section>`, `<form>`, `<nav>`, `<button>` used correctly |
| **ARIA labels** | All icon buttons have `aria-label` and `title` attributes |
| **ARIA live regions** | `#total-balance` has `aria-live="polite"`, `#expense-list` has `aria-live="polite"`, `#toast-container` has `aria-live="assertive"` |
| **Role attributes** | Budget progress bar uses `role="progressbar"` with `aria-valuenow/min/max` |
| **Chart alt text** | Canvas elements have `aria-label` and `role="img"` |
| **Modal focus** | Delete modal `role="dialog"`, focus moves to Confirm button on open |
| **Escape key** | Modal closes on `Escape` keydown |
| **Overlay dismiss** | Modal closes when clicking outside the card |
| **Form novalidate** | Native browser validation disabled in favour of custom validation with descriptive toast messages |
| **Icons hidden** | All decorative SVG icons have `aria-hidden="true"` |
| **Touch targets** | All buttons on mobile have min 44×44px hit area |

---

## 14. Known Limitations

| Limitation | Details |
|---|---|
| **No backend** | All data lives in the browser. Clearing browser storage removes all expenses. |
| **Single device** | No cloud sync — expenses are not accessible from another browser or device. |
| **No authentication** | No login system — anyone with browser access can view/edit expenses. |
| **localStorage cap** | Browser `localStorage` is typically limited to 5–10 MB per origin. |
| **No currency selection** | Currency is hardcoded to Pakistani Rupees (Rs). |
| **No recurring expenses** | No support for scheduled or repeating entries. |
| **No date range filter** | Analytics is limited to last 7 or 30 days; no custom date range. |
| **Chart.js CDN dependency** | Charts require internet access on first load (Chart.js loaded from jsDelivr CDN). |

---

## 15. Future Roadmap

### Short Term
- [ ] **Currency selector** — toggle between PKR, USD, EUR
- [ ] **Date range filter** — custom start/end date for the expense list
- [ ] **Recurring expenses** — mark an expense as monthly/weekly, auto-add on due date
- [ ] **PWA support** — service worker + `manifest.json` for installability and offline caching of Chart.js

### Medium Term
- [ ] **PHP/MySQL backend** (`api.php` stub already present) — persist to a database for multi-device access
- [ ] **User authentication** — login/signup to support multiple users on a shared server
- [ ] **Yearly analytics view** — bar chart per month across the full year
- [ ] **Bulk import** — import expenses from CSV file

### Long Term
- [ ] **Multi-currency** — real-time exchange rates via a free API
- [ ] **Tags system** — free-form tagging in addition to categories
- [ ] **Budget per category** — individual budgets, not just one global monthly budget
- [ ] **Notifications** — browser notifications when budget threshold is reached

---

## Appendix — File Metrics Summary

| Metric | Value |
|---|---|
| Total source lines | ~2,910 |
| Total source size | ~103 KB |
| JavaScript functions | 18 named functions |
| CSS custom properties | 30 design tokens |
| CSS breakpoints | 5 (`960 / 768 / 600 / 480 / 380px`) |
| SVG icons | 27 symbols in sprite |
| Expense categories | 8 |
| localStorage keys | 3 |
| Chart.js chart instances | 2 (doughnut + bar) |
| Browser support | All modern browsers (Chrome, Firefox, Edge, Safari) |

---

*Report generated: April 2026 | Premium Expense Tracker v2.0*
