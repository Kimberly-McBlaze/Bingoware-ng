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
- **Graphics & Testing:** Mike Suetkamp  
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

### v2.0.1 — January 1, 2026
- Fixed card generation issues introduced in v2.0

### v2.0 — January 1, 2026
- PHP 8.2+ compatibility
- Removed deprecated PHP functions
- Replaced Java applets with CSS/HTML
- Improved input sanitization and security
- HTML5 + UTF-8 compliance
- Modern browser support

### v1.5 — December 10, 2003
- Major visual redesign
- Security improvements
- Configurable Bingo number ranges

---

## 🛠️ Roadmap

- Full code refactor
- Optional MySQL backend
- PHP GD graphics for headers
- Session-based storage
- Configurable winning pattern names
- External `.ini` configuration support

---

## 🎨 Third-Party Credits

- Flooble Color Picker  
  http://www.flooble.com/scripts/colorpicker.php

---

## 📄 License

Open-source. See license file or original project for details.
