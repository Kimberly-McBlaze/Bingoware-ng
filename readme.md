# Bingoware-ng v1.5.1
# Based on Bingoware v1.5 by Frederic Demers
# Updated and maintained by KimberlyMcBlaze with the help of Copilot.
## Original Bingoware URL: http://bingoware.sourceforge.net

* Author: Frederic Demers
* Software testing and new contributed graphics: Mike Suetkamp

* Last Version: 1.5 - 10 December 2003
* Modernized: January 1, 2026 - Updated for PHP 8.2+ compatibility

Bingoware is a simple open-source PHP script which facilitates the creation,
viewing and printing of randomly generated Bingo cards and provides a game 
play mode, which draws random bingo number and lists all the winning cards.

Bingoware is not intended for online gaming, but rather to ease the 
administration of a real-life Bingo. I once had to organize a Bingo and realized that
I could not photocopy Bingo cards (everybody would win at once!), so I created 
Bingoware. This software will help you create the cards, draw the numbers and 
verify the winning cards.  It allows the user to choose the winning pattern(s).

Using the Fooble Color Picker script (http://www.flooble.com/scripts/colorpicker.php)

Index:

1. Requirements
2. Installation Instructions
3. Key Definitions
4. Version History (Changelog)
5. ToDo


1. Requirements:
-----------------

- A modern web browser (Chrome, Firefox, Safari, Edge)
  - Note: Java applets are no longer required (replaced with CSS/HTML)

- **PHP 8.2 or higher** with default settings (no custom php.ini required)
  - Short open tags are NOT required
  
- A web server (Apache, Nginx, or PHP built-in server for development)
- Write permissions in the application directory for data storage

- approx 125 kB for the script files.  Creating sets of bingo cards
 will use up approx 1.5 kB per card (1000 cards ~= 1.5 MB)


2. Installation Instructions:
-------------------------------------------------------------------------

### Traditional Setup (Apache/Nginx)

1. Extract the files into the documents directory of your web server
2. Ensure the web server has write permissions for the application directory
3. Point your browser to http://localhost/bingoware/index.php
4. Change the configuration parameters from the 'configure' menu item as you wish

### Docker Setup (Recommended for Development)

1. Ensure Docker and Docker Compose are installed
2. Run `docker-compose up -d` from the project directory
3. Access the application at http://localhost:8080
4. To stop: `docker-compose down`

### PHP Built-in Server (Quick Testing)

```bash
cd /path/to/bingoware
php -S localhost:8000
```

Then open http://localhost:8000 in your browser.


3. Key Definitions:
---------------------------

**Free Squares Mode:**  Bingoware gives you some flexibility when generating your set of cards.
You can choose to have a free square in the center of every card (will not help for winning 
patterns such as the perimeter of the Bingo card), no Free squares at all (slightly longer games),
or a randomly placed free square on all cards.

**Set ID:** The set ID is a unique identifier given to your set of cards. The set ID will always
prefix the card numbers when displayed on screen or printed out. You can have several sets of 
cards saved on the computer, which will remain untouched, simply by changing the set ID to a 
different letter or word. For example, if you generate a set 'A' of 10 cards, the cards will 
be numbered A0001-A0010. Once set 'A' is generated, you can change the set ID to 'Freddy-' 
and generate a new set of 20 cards (numbered Freddy-0001 to Freddy-0020). The original set remains 
untouched. This feature is very useful if you want to personalize several sets of cards. It also 
allows you to reload a previously generated set of cards.

**Number of cards in play:**  In game mode, the software will open the current set of cards (as indicated
by the setid variable in the config mode).  If you do not distribute all the cards you generated,
because for instance you did not get the crowd you expected or are charging too much for you cards,
then you can tell Bingoware not to consider all the cards.  The trick is to issue out your cards in
sequential order, and enter the number of the last card given away in the "number of cards in play" box.  You can
always change the number throughout the game if you gain or lose some people.  Bingoware will only 
announce winning cards for cards that are in play.

**Manual vs Automatic Modes:**  Bingoware can generate Bingo draws for you when in Automatic Mode.
The Manual Mode is meant for people using a different physical random number generator (such
as a barrel, lottery balls, ...)  Bingoware then lets you enter the draw directly instead  of
generating a random number, and will still be able to announce the winners.


4. Version History (Changelog):
---------------------------------

**Version 1.5.1 (January 1, 2026)**
- Modernized for PHP 8.2+ compatibility
- Replaced short open tags `<?` with `<?php` throughout
- Replaced deprecated `ereg()` with `str_contains()`
- Replaced deprecated `ereg_replace()` with `preg_replace()`
- Replaced deprecated `each()` with `foreach`
- Removed `get_magic_quotes_gpc()` logic (magic quotes removed in PHP 5.4)
- Added safe unserialize with `['allowed_classes' => false]`
- Added null checks to prevent count() warnings on null values
- Added input sanitization using `filter_input()` for security
- Replaced Java applet menu with modern CSS/HTML hover menu
- Updated DOCTYPE to HTML5 with UTF-8 charset
- Fixed trimming before unserialize to avoid newline issues
- Improved browser compatibility (works in all modern browsers)

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


5. ToDo (most important first):
----------------------------------

- ~~Supports PHP 5.4 and HTML5 / CSS3~~ (✅ Done in v1.5.1 - PHP 8.2+ compatible)
- Refactoring all the code
- PHP GD library to create column headers as graphics
- A MySQL version which will be much faster than flat file (I hope)
- Look at sessions to be able to save data without using files to improve speed
- Ability to change the names of the winning patterns from config file
- Use of external ini file script instead of current settings.php file





