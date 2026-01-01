# 🎱 Bingoware-ng

**Bingoware-ng** is a modern revival of the classic Bingoware PHP application, designed to help organize **real-life Bingo games**. It generates unique Bingo cards, manages number draws, and automatically detects winning cards—all without online gambling features.

This project updates the original codebase for **PHP 8.2+** while preserving its original functionality and spirit.

---

## ✨ Features

- 🎟️ Generate random Bingo card sets
- 🖨️ View and print Bingo cards
- 🔢 Automatic or manual number draws
- 🏆 Automatic detection of winning cards
- 🧩 Support for multiple winning patterns
- 🆔 Multiple independent card sets via Set IDs
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

## 🗂️ Changelog

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

- To be determined

## 🛠️ Original author's roadmap

- Refactoring all the code
- PHP GD library to create column headers as graphics
- A MySQL version which will be much faster than flat file (I hope)
- Look at sessions to be able to save data without using files to improve speed
- Ability to change the names of the winning patterns from config file
- Use of external ini file script instead of current settings.php file


---

## 🎨 Third-Party Credits

- Flooble Color Picker  
  http://www.flooble.com/scripts/colorpicker.php

---

## 📄 License

Open-source. See license file or original project for details.



