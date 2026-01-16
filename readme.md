# 🎱 Bingoware-ng

**Bingoware-ng** is a modern revival of the classic Bingoware PHP application, designed to help organize **real-life Bingo games**. It generates unique Bingo cards, manages number draws, and automatically detects winning cards—all without online gambling features.

This project updates the original codebase for **PHP 8.2+** while preserving its original functionality and spirit.

---

## ✨ Features

- 🎟️ Generate random Bingo card sets
- 🖨️ View and print Bingo cards
- 🔢 Automatic or manual number draws
- 🏆 Automatic detection of winning cards
- 🎯 **NEW:** Full CRUD for custom winning patterns (add, edit, delete)
- 🧩 Support for multiple winning patterns
- 🆔 Multiple independent card sets via Set IDs
- 🌐 **NEW:** Virtual Bingo Mode for remote play
- 🔗 Generate shareable card links for players
- 📱 Interactive cards with click-to-mark functionality
- 🌐 Works in all modern browsers

---

## 🚫 Non-Goals

- Online or real-money gambling
- Multiplayer or hosted Bingo services

Bingoware-ng is intended **only** to assist with the logistics of physical Bingo events.

---

## 📜 Project History

- **Original Author:** Frederic Demers  
- **Original Graphics & Testing (before v2.x):** Mike Suetkamp  
- **Revival & Maintenance:** KimberlyMcBlaze (with Copilot)

Original project: http://bingoware.sourceforge.net

---

## 📦 Requirements

### Software
- PHP **8.2 or higher**
- Apache, Nginx, or PHP built-in server
- Modern browser (Chrome, Firefox, Safari, Edge)

### Notes
- No custom `php.ini` required
- Short open tags (`<?`) are **not** used
- Application directory must be writable

### Disk Usage
- App size: ~125 KB
- Bingo cards: ~1.5 KB per card  
  *(1,000 cards ≈ 1.5 MB)*

---

## 🚀 Installation

### Option 1: Apache / Nginx

1. Extract files into your web server’s document root
2. Ensure write permissions are enabled
3. Open:  
   `http://localhost/bingoware/index.php`
4. Configure settings via the **Configure** menu

---

### Option 2: Docker (Recommended for Development)

```bash
docker-compose up -d
```

Access the app at:
```
http://localhost:8080
```

To stop:
```bash
docker-compose down
```

---

### Option 3: PHP Built-in Server (Quick Test)

```bash
cd /path/to/bingoware
php -S localhost:8000
```

Then open:
```
http://localhost:8000
```

---

## 🧠 Key Concepts

### Set ID
Each Bingo card set has a unique **Set ID**, which prefixes card numbers.

Example:
```
A0001 – A0010
Freddy-0001 – Freddy-0020
```

Multiple sets can coexist without overwriting each other.

---

### Free Squares Mode
Choose how “Free” squares behave:
- Center square (classic)
- No free squares
- Random free square placement

---

### Cards in Play
If fewer cards are distributed than generated, specify how many are **actively in play**.  
Only those cards will be checked for winners.

---

### Draw Modes
- **Automatic:** Random number generation
- **Manual:** Enter numbers manually (for physical draws)

Both modes fully support winner detection.

---

### Virtual Bingo Mode 🌐

**Virtual Bingo Mode** enables remote play by allowing administrators to generate and share URLs containing stacks of bingo cards that work on any device.

#### Enabling Virtual Bingo

1. Go to **Configure** menu
2. Enable the **Virtual Bingo Mode** checkbox
3. Adjust **Maximum Cards Per Request** (default: 12)
4. Save configuration

Once enabled, a **Virtual Bingo** menu item appears in the main menu.

#### How It Works

1. **Generate Card Stacks**: Administrator visits the Virtual Bingo page and generates a stack of cards (1-12 cards per stack)
2. **Get Shareable URL**: Each generation creates one URL containing all the cards in that stack
3. **Share URL**: Distribute the stack URL to players via email, chat, or any communication method
4. **Interactive Play**: Players open the URL to view all cards in the stack and can click/tap squares to mark them during play
5. **Persistent Marks**: Marks are saved in the browser and restored when reopening the stack
6. **Print Support**: Cards can be printed up to 4 cards per page for physical use

#### Key Features

- **Administrator Control**: Only administrators generate card stack URLs
- **Multiple Cards per URL**: Generate 1-12 cards in a single shareable URL
- **State Retention**: Previously generated stack URLs remain visible when returning to the Virtual Bingo page
- **Navigation**: Easy back-to-menu navigation from the Virtual Bingo page
- **Print Layout**: Optimized printing with up to 4 cards per printed page

#### Use Cases

- **Video conferencing**: Share stack URLs with remote participants during video calls
- **Radio bingo**: Players follow along remotely while listening to number calls
- **Hybrid events**: Mix in-person and remote players seamlessly
- **Email distribution**: Send stack URLs to participants ahead of time

#### Security Features

- Unique unguessable stack IDs (32-character hex) for each stack
- Configurable limits on cards per stack to prevent abuse
- Secure token-based access without exposing card numbers

---
## 🗂️ Known Issues
- None at this time!


## 🗂️ Changelog

- ### [2.6.4.3] - 2026-01-16
- **Bug Fixes:**
  - Fixed Quick Set Switch dropdown alignment on Play Bingo page
    - Changed layout from horizontal to vertical stacking
    - "Quick Set Switch:" label now appears above the dropdown
    - Dropdown select control in the middle with full width (max 250px)
    - "Current: A" value now appears below the dropdown
    - All elements are centered and properly aligned within the container
    - Improved visual hierarchy and better use of available space

- ### [2.6.4.2] - 2026-01-16
- **Bug Fixes:**
  - Fixed flashboard notification text color to be black instead of white for better readability
    - When `$maxNumber` is not 75, the flashboard shows a yellow background notification
    - Text color was white and barely readable; now changed to black for proper contrast
    - Change is scoped only to the notification element, other text remains unchanged
  - Changed default SET ID from `B` to `A` across the application
    - New installations and fresh configurations now use Set A by default
    - Ensures consistent default set ID throughout the application
- **UI Improvements:**
  - Moved quick set switch button below the winner indicator area
    - Quick set switch now appears after "No winners yet" / winner count display
    - Improved visual hierarchy and layout organization on Play Bingo page
    - Better positioning for landscape mode and mobile/responsive layouts

- ### [2.6.4.1] - 2026-01-15
- **Bug Fixes:**
  - Changed default bingo set from `C` to `A`
    - New installations and fresh configurations now use Set A by default
  - Fixed erroneous "Card Generation Failed" error message when switching to empty sets
    - Previously showed error message even when cards were successfully generated
    - Now correctly shows success message when generation succeeds
    - Error message only appears when generation actually fails
  - Fixed flashboard breaking when `$maxNumber` is not 75
    - When `$maxNumber` in `constants.php` is set to any value other than 75, the flashboard's 5×15 grid is now disabled
    - A notification banner explains the grid is disabled and how to restore it (set `$maxNumber = 75`)
    - The rest of the flashboard (Current Number display, Winning Pattern display) continues to work normally
    - Full flashboard functionality (including grid) automatically restored when `$maxNumber` is set back to 75

- ### [2.6.4] - 2026-01-15
- **New Features:**
  - Added update checker notification system
    - Automatically checks for new versions (once per 24 hours)
    - Non-intrusive notification with option to view release or dismiss
    - Uses localStorage to track dismissed updates
    - Handles network errors gracefully without bothering users
  - Auto-generate cards prompt when switching to empty set
    - Detects when switching from a set with cards to a set without cards
    - Prompts user to automatically generate same number of cards
    - Provides option to decline and continue without generating
- **Bug Fixes:**
  - Fixed winning patterns default state - only "Normal" pattern is now enabled by default
    - Previously both "Normal" and "Four Corners" were enabled, causing confusion
    - All other patterns now correctly default to disabled state

- ### [2.6.3.3] - 2026-01-15
- **Play Bingo UI & Flashboard Improvements:**
  - Improved quick switch visibility with theme-aware colors
    - Replaced hard-coded colors with CSS variables for proper light/dark mode support
    - Quick Set Switch control now uses `var(--bg-tertiary)`, `var(--border-color)`, and `var(--text-secondary)`
    - Control is now clearly visible and accessible in both light and dark themes
    - Enhanced contrast for better accessibility (WCAG AA compliant)
  - Fixed flashboard card set updating when set changes
    - Flashboard now dynamically updates the displayed card set ID when switching sets
    - Works correctly when set is changed via Settings (Configure page)
    - Works correctly when set is changed via Quick Set Switch control
    - Set ID is included in game state messages sent to flashboard
    - Flashboard updates both the header display and page title automatically
    - No manual reload required - updates happen in real-time

- ### [2.6.3.2] - 2026-01-15
- **View Cards Improvements:**
  - Added option to exclude virtual-player cards from "Show All Cards (for printing)" output
  - New checkbox in View Cards section: "Exclude virtual-player cards from print output"
  - When enabled, cards assigned to Virtual Bingo URLs are filtered out, preventing accidental printing for in-person players
  - Helps keep virtual-player cards separate from physical cards used in-person
  - Filter only appears when Virtual Bingo is enabled and card stacks have been generated
  - Backward compatible: default behavior shows all cards (same as before)

- ### [2.6.3.1] - 2026-01-15
- **Virtual Bingo Improvements:**
  - Added delete button for individual card stacks in Virtual Bingo administrator page
  - Each previously generated stack now has a "Delete" button with confirmation prompt
  - Deleted stacks are removed immediately from the UI with smooth animation
  - New API endpoint `/api/virtual_stacks.php` to handle stack deletion
  - Added `delete_virtual_stack()` function in `include/virtual_cards.php` for backend deletion
  - Includes UX safeguards: button disables during deletion, error handling for failures
  - Page auto-refreshes when last stack is deleted to update the UI

- ### [2.6.3] - 2026-01-15
- **Critical Bug Fixes:**
  - Fixed `preg_match()` warnings when saving configuration settings via the Configure page
    - Root cause: Using regex patterns with `preg_match()` for line matching was fragile and could cause "Unknown modifier" errors with certain settings values containing special regex characters
    - Solution: Replaced all `preg_match()` calls with safe `str_starts_with()` string prefix checks
    - This eliminates any possibility of regex delimiter or modifier errors when processing settings
    - Values are still properly escaped using `addslashes()` when written to config/settings.php
    - All settings (headers, footers, colors, virtual bingo, etc.) now save reliably without warnings
  - Settings file remains valid PHP and preserves all comments and unrelated lines

- ### [2.6.2] - 2026-01-15
- **Critical Bug Fixes:**
  - Fixed settings.php parse error when saving any configuration changes via the web UI
    - Root cause: Greedy regex patterns were matching across lines and corrupting variable assignments
    - Solution: Updated all regex patterns to use non-greedy matching (`.*?`) and anchor to line start (`^`)
    - Added proper escaping for special regex characters using `preg_quote()`
    - Removed problematic `trim()` call that was causing newline issues
    - All settings now save correctly without corrupting the PHP configuration file
  - Fixed Virtual Bingo disable workflow to prevent data loss
    - Added confirmation dialog when disabling Virtual Bingo if generated card stack URLs exist
    - Automatically deletes all virtual card stacks when user confirms disabling Virtual Bingo
    - No confirmation needed if no URLs have been generated
    - Added `delete_all_virtual_stacks()` and `has_virtual_stacks()` helper functions

- **Flashboard Improvements:**
  - Flashboard now displays the current card set ID in the header
  - Pattern description is now shown below pattern name when available
  - Enhanced pattern display to show both name and description for better clarity

- **Quality of Life Improvements:**
  - Added quick set switcher on Play Bingo page
    - Dropdown selector shows all available card sets with card counts
    - Seamlessly switch between different card sets during gameplay
    - Confirmation prompt prevents accidental set changes
  - Auto-generate prompt when switching to non-existent set
    - Detects when switching to a set that has no cards
    - Offers to auto-generate same number of cards as current set
    - One-click card generation for new sets
  - Added utility functions `get_available_sets()` and `get_set_card_count()` for set management

- ### [2.6.1] - 2026-01-15
- **Virtual Bingo Improvements:**
  - Added back-to-menu navigation button on Virtual Bingo page
  - Implemented state retention for generated card stacks - previously generated URLs now persist when navigating away and returning
  - Removed password protection entirely - Virtual Bingo is now open for administrators to generate card URLs
  - Redesigned card generation to create **stacked card URLs** - multiple cards (1-12) are now grouped into a single shareable URL
  - Changed to **administrator-generated URLs** - clarified that administrators generate and share URLs with players
  - Added print support with optimized layout for **up to 4 cards per printed page**
  - Made maximum cards per stack **configurable** with new default of **12 cards** (adjustable from 1-100)
  - All marks on cards in a stack are preserved in browser localStorage and restored on page reload

- ### [2.6] - 2026-01-15
- Added Virtual Bingo Support: enables remote play by allowing players to request and receive shareable links to individual bingo cards that work on any device.

- ### v2.5.3 - January 15, 2026
- **Bug Fixes:**
  - Fixed flashboard synchronization issues where the latest number and "Current Number" display would not reliably update
    - Root cause: Bridge script used brittle DOM scraping with fragile inline-style selectors (e.g., matching `grid-template-columns` and `linear-gradient` style strings) that could intermittently fail or return incomplete/stale draws
    - Solution: Replaced DOM scraping with stable, deterministic data source by exposing draws array in machine-readable JSON format via `data-draws` attribute on the play page
    - The `extractDrawsFromPage()` function now reads from the `data-draws` attribute instead of parsing DOM styles
    - This ensures flashboard receives consistent, accurate state updates after every draw
  - Flashboard now reliably updates with each number drawn
  - "Current Number" display consistently shows the latest drawn number
  - Exactly one cell (the latest) blinks/highlights in the flashboard grid at all times
  - No more desync issues between play page and flashboard display

### v2.5.2 - January 14, 2026
- **Bug Fixes:**
  - Fixed flashboard blinking state management where previous numbers would continue blinking after new numbers were drawn
    - Root cause: `latestNumber` state could become out of sync with the `draws` array during rapid updates or multiple postMessage events
    - Solution: Refactored flashboard state management to derive `latestNumber` from the `draws` array as single source of truth
    - The `getLatestNumber()` helper function now ensures consistent state derivation across all update paths
    - `updateBoard()` now applies the `latest` (blinking) class only after marking all called numbers, ensuring only one number blinks at a time
  - Fixed "Current Number" display reliability
    - Current Number display now uses the same derived `latestNumber` value from `draws` array
    - Eliminates race conditions that could cause the display to show stale values or stop updating
  - After each draw, exactly one number now blinks (the latest), and all previously drawn numbers remain lit without blinking
  - Current Number UI now continues to reflect the latest draw reliably during extended gameplay sessions

### v2.5.1 - January 14, 2026
- **Bug Fixes:**
  - Fixed flashboard not updating when numbers are drawn during gameplay
    - Root cause: Form submission caused page reload, breaking postMessage communication
    - Solution: Flashboard now periodically requests state updates from parent window, automatically re-establishing connection after page reloads
  - Fixed flashboard displaying "Check Winning Patterns page" instead of actual selected pattern
    - Root cause: Pattern information was not exposed to JavaScript
    - Solution: Added data attributes to play page containing enabled pattern names, JavaScript now reads and displays them correctly
  - Flashboard now correctly shows:
    - Current drawn number (e.g., N35, O62)
    - Selected winning pattern name when exactly one pattern is enabled
    - Pattern count (e.g., "2 patterns selected") when multiple patterns are enabled
    - "No pattern selected" message when no patterns are enabled
  - Real-time synchronization between play page and flashboard now works reliably across page reloads

### v2.5 - January 14, 2026
- **New Feature: Bingo Flashboard Display**
  - Added "Open Flashboard" button on Play Bingo page
  - New flashboard window displays full 5×15 bingo board with vertical BINGO labels
  - Number ranges: B (1-15), I (16-30), N (31-45), G (46-60), O (61-75)
  - All numbers always visible with darker/inactive default state
  - Called numbers illuminate with lighter/active color
  - Most recently called number blinks for emphasis
  - Displays current called number (e.g., N34)
  - Shows currently selected winning pattern
  - Real-time communication via postMessage for non-invasive updates
  - Designed for secondary monitor/display usage
  - No interference with existing gameplay controls or logic
  - Gracefully handles popup blockers and window closure

### v2.4.2 - January 14, 2026
- **Bug Fixes:**
  - Fixed JSON parsing errors on Winning Patterns page for all UI actions (Save Changes, Save Pattern, Edit, Reset to Default)
  - Root cause: Relative file paths in include files caused path resolution issues when API endpoints were called from `/api/` subdirectory
  - PHP warnings from failed file includes were corrupting JSON responses, causing "Unexpected token" errors in browser
  - Solution: Converted all relative paths to absolute paths using `__DIR__` in:
    - `include/functions.php` - Fixed paths for config/settings.php, patterns.php, winner_check.php, set files, and winningpatterns.dat; replaced echo with error_log
    - `config/settings.php` - Fixed path for include/constants.php
    - `include/patterns.php` - Fixed path for patterns.json storage and includes
  - All four actions now work correctly with valid JSON responses:
    - ✅ Save Changes (enable/disable patterns)
    - ✅ Save Pattern (create new pattern)
    - ✅ Edit (edit existing pattern)
    - ✅ Reset to Default (restore default patterns)

### v2.4.1 - January 14, 2026
- **Bug Fixes:**
  - Fixed "Add New Pattern" button not working - button now properly opens the pattern creation modal
  - Fixed "Edit" buttons not working - buttons now properly open the pattern editing modal
  - Fixed Save/Apply button not appearing - button now correctly shows when enable/disable changes are made
  - Root cause: JavaScript functions in patterns-ui.js were wrapped in an IIFE (closure) and not exposed to global scope, making them inaccessible from HTML onclick handlers
  - Solution: Exposed necessary functions to window object (openAddModal, openEditModal, markPatternChanged, savePatternChanges, etc.)

### v2.4 - January 14, 2026
- **Major Refactoring:**
  - **Separated Patterns API:** Created dedicated `api/patterns.php` endpoint for all pattern CRUD operations
  - **Extracted Inline Assets:** Moved patterns page CSS to `include/app.css` and JavaScript to `include/patterns-ui.js`
  - **Storage Utilities:** Created `include/storage.php` with centralized file operations (JSON read/write, atomic writes)
  - **Bootstrap System:** Created `include/bootstrap.php` for consistent initialization across all entry points
  - **JavaScript Consolidation:** Enhanced `include/modern-ui.js` with legacy function wrappers (`explain()`, `validate_number()`)
  - **Winner Checking Module:** Extracted winner checking logic into pure functions in `include/winner_check.php`
  - **Test Harness:** Added `tests/winner_check_smoke.php` for deterministic winner checking validation
- **Architecture Improvements:**
  - All entry points now use centralized bootstrap
  - Patterns library refactored to use storage utilities
  - Winner checking now uses pure, testable functions
  - Clear separation between API and UI layers
  - Reduced code duplication and improved maintainability

### v2.3.2 - January 14, 2026
- **Bug Fixes:**
  - Fixed delete pattern validation error: Deleting custom patterns no longer triggers "Pattern name is required" error
  - Reordered API endpoint checks in `patterns.php` to properly handle delete requests before create/update validation
- **Features:**
  - Added "Reset to Default" button in winning patterns section
  - Reset functionality removes all custom patterns and restores default patterns to original state
  - Added `reset_patterns_to_default()` function in `include/patterns.php`

### v2.3.1 - January 14, 2026
- **Documentation & Verification:**
  - Verified complete removal of all PHP error suppression operators (@) from filesystem operations
  - Confirmed all filesystem functions return consistent true/false values
  - Confirmed proper error logging is in place for all file operations
  - Updated version number to reflect completion of filesystem error handling cleanup

### v2.3 - January 14, 2026
- **Security & Code Quality Improvements:**
  - Removed all PHP error suppression operators (@) from filesystem operations
  - Added explicit error handling with proper logging for file operations
  - Created input validation/sanitization helper library (`include/input_helpers.php`)
  - Hardened request input handling in `menu.php` and `patterns.php`
  - Validated and sanitized `$_GET["numberinplay"]` parameter with safe URL encoding
  - Added comprehensive validation for patterns API endpoints (id, name, description, grid, enabled)
  - Improved error responses with JSON error messages for invalid inputs
  - All filesystem operations now return proper success/failure status

### v2.2 - January 2, 2026
- Added full CRUD (Create, Read, Update, Delete) support for winning patterns - [See documentation](docs/pattern-management.md)
- Moved card customization to its own menu.
- Added persistent winner indicator on Play Bingo page
- Winner indicator displays near number generation controls
- Shows real-time winner count without requiring scroll
- Visual feedback with trophy icon when winners are detected


### v2.1 - January 1, 2026
- Modernized the UI
- Added light/dark mode toggle
- Quality of life improvements

### v2.0.1 — January 1, 2026
- Fixed card generation issues introduced in v2.0

### v2.0 — January 1, 2026
- PHP 8.2+ compatibility
- Removed deprecated PHP functions
- Replaced Java applets with CSS/HTML
- Improved input sanitization and security
- HTML5 + UTF-8 compliance
- Modern browser support
- Project renamed to "Bingoware-ng"

## 🗂️ Previous Changelog from original author

**Version 1.5 (10 December 2003)**
- great new look -> graphics contributed by Mike Suetkamp --> Thank you!
- fixed header element of Rules.html file
- new "Free" square image
- "generate new set" no longer deletes previous set until the new set is generated
- no longer able to change page title on the fly from URL (security)
- ability to change the Bingo's maximum number, typically 75, to another number (multiple of 5)
- bux fix in manual draw mode, introduced in version 1.4

**Version 1.4 (28 October 2003)**
- new graphics contributed by Mike Suetkamp in the drawn numbers table --> Thank you!
- revamped entire code so that it is (only) compatible with the newer version of PHP
- ability to select multiple winning patterns simultaneously
- ability to change the font and background colours from the configuration file
- ability to use a text files of names which will be printed on the bottom left of each card
- ability to have a rules page that is printed on the back of each card (requires double-sided printer)
- fixed a minor bug in play.php which prevented the change of the BINGO letters
- fixed other minor bugs related to changes in the way browsers comply to Javascript standards
- created a much needed folder structure


**Version 1.3 (1 July 2002)**
- entire web-based configuration
- interactive user-defined winning patterns
- ability to change the number of cards in play (up to the total number in the set)
- ability to enter bingo draws if another random mechanism is in place (manual mode)
- new winners indicated separately in red from other winners
- set ID displayed on each page
- improved help file (and added context-sensitive help throughout the program)
- extracted Javascript into a separate file
- strict validation rules for data entry (set ID and Manual Draw Mode)
- removed webmaster's email address in footer
- other minor enhancements and bug fixes


**Version 1.2 (18 April 02)**
- ability to select 9 winning patterns (normal, full card, square, T, X, N, Z, + and Cross)
- ability to select 3 "Free Square" mode (no free square, center on all cards, random on
all cards)
- customized headers and footers for view and print pages
- created file constants.php to remove the constant informaton from the config file
- added a set_time_limit(0) instruction to avoid time-out problems
- modification of the card number display in print mode to each card instead of each set
- other minor enhancements


**Version 1.1 (14 April 02)**
- ability to choose a setid which enables the user to 
have multiple sets of Bingo cards that do not overwrite one another
- ability to change the page title on the fly from the URL
- minor bug fixes and other enhancement
		

**Version 1.0 (7 April 02)**
- initial release

---

## 🛠️ Roadmap

- ✅ **COMPLETED:** Full CRUD support for winning patterns - [See documentation](docs/pattern-management.md)
  
- ✅ **COMPLETED:** External Number Board & Caller Display: Add support for an external bingo number board with a dedicated display showing the currently called number, allowing easy mirroring to a second screen for player viewing—similar to a real-world bingo hall setup.
  
- ✅ **COMPLETED:** Virtual Bingo Support: Adapt the software for seamless use in virtual bingo sessions, enabling easy creation, saving, and sharing of individual bingo cards for remote play via video conferencing, radio, or other remote communication methods.
  
- More to be determined later.


---

## 🎨 Third-Party Credits

- Flooble Color Picker  
  http://www.flooble.com/scripts/colorpicker.php

---

## 📄 License

Open-source. See license file or original project for details.

















