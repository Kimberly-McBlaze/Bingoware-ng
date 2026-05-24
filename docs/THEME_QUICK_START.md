# Theme System - Quick Start Guide

## 🎨 Creating Your First Custom Theme

### Step 1: Access Theme Manager
1. Navigate to the Theme Manager (🎨 icon in menu)
2. Click "➕ Create Custom Theme"

### Step 2: Configure Your Theme
1. **Theme Name**: Give it a descriptive name (e.g., "Ocean Breeze")
2. **Description**: Optional description
3. **Mode**: Select "Light Mode" or "Dark Mode" based on your design
4. **Auto-transform**: ✅ Keep checked for automatic color adaptation
5. **Transform Intensity**: Use slider to control transformation (30% recommended)

### Step 3: Choose Your Colors
Customize all colors in the palette:
- **Primary/Secondary**: Main accent colors
- **Success/Warning/Error**: Status colors
- **Backgrounds**: Primary, Secondary, Tertiary
- **Text**: Primary, Secondary, Muted
- **Border**: Border color

### Step 4: Save and Activate
1. Click "💾 Save Theme"
2. Click "✓ Activate" on your new theme
3. Toggle dark mode to see automatic transformation!

---

## 🌙 Understanding Auto-Transform

### With Auto-Transform Enabled (✅)
**You design for light mode:**
- White backgrounds (#FFFFFF)
- Dark text (#1F2937)
- Bright accents (#667eea)

**Dark mode automatically gives you:**
- Dark backgrounds (#1A202C)
- Light text (#F7FAFC)
- Adjusted accents

### With Auto-Transform Disabled (❌)
Colors stay exactly as you designed them, regardless of mode.
Use this for pre-designed dark themes.

---

## 📊 Transform Intensity Guide

| Intensity | Effect | Best For |
|-----------|--------|----------|
| 0-10% | Subtle changes | Themes already close to target mode |
| 20-30% | Balanced (default) | Most themes |
| 40-60% | Noticeable changes | High-contrast themes |
| 70-100% | Dramatic changes | Extreme transformations |

---

## 💡 Pro Tips

### Creating a Universal Theme
1. Design with light mode colors
2. Enable auto-transform
3. Set intensity to 30%
4. Test in both modes

### Creating a Dark-Only Theme
1. Design with dark colors
2. Set mode to "Dark Mode"
3. Disable auto-transform
4. Perfect for OLED screens!

### Creating a Light-Only Theme
1. Design with light colors
2. Set mode to "Light Mode"
3. Disable auto-transform
4. Perfect for printing!

---

## 🔍 Troubleshooting

**Problem**: Colors look weird in dark mode
- **Solution**: Check that mode is set correctly and auto-transform is enabled

**Problem**: Colors change too much
- **Solution**: Lower the transform intensity slider

**Problem**: Colors don't change enough
- **Solution**: Increase the transform intensity slider

**Problem**: I want exact control over dark mode colors
- **Solution**: Create two separate themes (one light, one dark) and disable auto-transform

---

## 🎯 Example Themes

### Ocean Theme (Light → Auto-Dark)
- Mode: Light
- Auto-transform: ✅ Yes
- Intensity: 30%
- Colors: Blues and teals
- Result: Professional, easy on eyes

### Midnight Theme (Pure Dark)
- Mode: Dark
- Auto-transform: ❌ No
- Colors: Deep blues and grays
- Result: Perfect for night gaming

### Pastel Dream (Light Only)
- Mode: Light
- Auto-transform: ❌ No
- Colors: Soft pastels
- Result: Great for daytime events

---

## 📚 More Information

See `THEME_MIGRATION_GUIDE.md` for technical details and API information.
