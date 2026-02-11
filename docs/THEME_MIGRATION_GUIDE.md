# Theme System Redesign - Migration Guide

## What Changed

The theme system has been redesigned to support automatic color transformation when switching between light and dark modes.

### New Features

1. **Automatic Color Transformation**
   - When you toggle dark mode, your theme colors automatically darken
   - When you toggle light mode, colors automatically brighten
   - Backgrounds, text, borders, and accents all transform intelligently

2. **New Theme Fields**
   - **Mode** (`light` or `dark`): Indicates whether the theme is designed for light or dark mode
   - **Auto-transform** (boolean): Enable/disable automatic color transformation
   - **Transform Intensity** (0-100): Control how much colors change (default: 30)

### For Existing Users

**Your existing themes will work seamlessly:**
- They will default to `auto_transform=true` and `transform_intensity=30`
- This means colors will automatically transform when switching modes
- You can disable auto-transform or adjust intensity in the Theme Manager

### Creating Themes

**For a light theme that transforms to dark:**
1. Design your theme with light colors (white backgrounds, dark text)
2. Set Mode to "Light"
3. Enable "Auto-transform colors when switching modes"
4. Adjust "Color Transformation Intensity" to taste (30% is a good start)
5. Save the theme

**For a pre-designed dark theme:**
1. Design your theme with dark colors
2. Set Mode to "Dark"  
3. If you want colors to stay exactly as-is, disable "Auto-transform"
4. Save the theme

### How It Works

When you switch between light and dark modes:
- **Dark mode**: Backgrounds darken, text brightens, borders darken
- **Light mode**: Backgrounds brighten, text darkens, borders brighten
- **Accent colors** (primary, secondary, etc.) adjust based on their brightness

The transformation is automatic and reversible!

### API Changes

For developers integrating with the Theme API:

**New fields in theme objects:**
```json
{
  "id": "theme_xxx",
  "name": "My Theme",
  "mode": "light",
  "auto_transform": true,
  "transform_intensity": 30,
  "colors": { ... }
}
```

**Creating/updating themes:**
```javascript
// POST api/themes.php
{
  "name": "My Theme",
  "mode": "light",
  "auto_transform": "1",
  "transform_intensity": "30",
  "colors": "{...}"
}
```

### Troubleshooting

**Q: My theme looks wrong in dark mode**
- Check that "Auto-transform" is enabled
- Try adjusting the "Transform Intensity" slider
- Verify your theme's Mode setting matches your design

**Q: I want my dark theme to stay exactly as-is**
- Edit your theme
- Uncheck "Auto-transform colors when switching modes"
- Save

**Q: Colors are changing too much/not enough**
- Adjust the "Color Transformation Intensity" slider
- 0% = no change, 100% = maximum change
- 30% is recommended for most themes
