/**
 * Color Transformation Utilities
 * Provides functions to darken/brighten colors for automatic theme adaptation
 */

(function(window) {
  'use strict';

  const ColorTransform = {
    /**
     * Convert hex color to RGB object
     * @param {string} hex - Hex color (e.g., '#FF0000')
     * @returns {object} RGB object {r, g, b}
     */
    hexToRgb(hex) {
      const result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
      return result ? {
        r: parseInt(result[1], 16),
        g: parseInt(result[2], 16),
        b: parseInt(result[3], 16)
      } : null;
    },

    /**
     * Convert RGB to hex color
     * @param {number} r - Red (0-255)
     * @param {number} g - Green (0-255)
     * @param {number} b - Blue (0-255)
     * @returns {string} Hex color
     */
    rgbToHex(r, g, b) {
      const toHex = (c) => {
        const hex = Math.round(Math.max(0, Math.min(255, c))).toString(16);
        return hex.length === 1 ? '0' + hex : hex;
      };
      return '#' + toHex(r) + toHex(g) + toHex(b);
    },

    /**
     * Convert RGB to HSL
     * @param {number} r - Red (0-255)
     * @param {number} g - Green (0-255)
     * @param {number} b - Blue (0-255)
     * @returns {object} HSL object {h, s, l}
     */
    rgbToHsl(r, g, b) {
      r /= 255;
      g /= 255;
      b /= 255;

      const max = Math.max(r, g, b);
      const min = Math.min(r, g, b);
      let h, s, l = (max + min) / 2;

      if (max === min) {
        h = s = 0; // achromatic
      } else {
        const d = max - min;
        s = l > 0.5 ? d / (2 - max - min) : d / (max + min);
        
        switch (max) {
          case r: h = (g - b) / d + (g < b ? 6 : 0); break;
          case g: h = (b - r) / d + 2; break;
          case b: h = (r - g) / d + 4; break;
        }
        h /= 6;
      }

      return { h: h * 360, s: s * 100, l: l * 100 };
    },

    /**
     * Convert HSL to RGB
     * @param {number} h - Hue (0-360)
     * @param {number} s - Saturation (0-100)
     * @param {number} l - Lightness (0-100)
     * @returns {object} RGB object {r, g, b}
     */
    hslToRgb(h, s, l) {
      h /= 360;
      s /= 100;
      l /= 100;

      let r, g, b;

      if (s === 0) {
        r = g = b = l; // achromatic
      } else {
        const hue2rgb = (p, q, t) => {
          if (t < 0) t += 1;
          if (t > 1) t -= 1;
          if (t < 1/6) return p + (q - p) * 6 * t;
          if (t < 1/2) return q;
          if (t < 2/3) return p + (q - p) * (2/3 - t) * 6;
          return p;
        };

        const q = l < 0.5 ? l * (1 + s) : l + s - l * s;
        const p = 2 * l - q;

        r = hue2rgb(p, q, h + 1/3);
        g = hue2rgb(p, q, h);
        b = hue2rgb(p, q, h - 1/3);
      }

      return {
        r: r * 255,
        g: g * 255,
        b: b * 255
      };
    },

    /**
     * Darken a color by reducing its lightness
     * @param {string} hexColor - Hex color to darken
     * @param {number} amount - Amount to darken (0-100), default 30
     * @returns {string} Darkened hex color
     */
    darken(hexColor, amount = 30) {
      const rgb = this.hexToRgb(hexColor);
      if (!rgb) return hexColor;

      const hsl = this.rgbToHsl(rgb.r, rgb.g, rgb.b);
      
      // Reduce lightness
      hsl.l = Math.max(0, hsl.l - amount);
      
      // For very light colors, also reduce saturation slightly
      if (hsl.l > 70) {
        hsl.s = Math.max(0, hsl.s - 10);
      }

      const newRgb = this.hslToRgb(hsl.h, hsl.s, hsl.l);
      return this.rgbToHex(newRgb.r, newRgb.g, newRgb.b);
    },

    /**
     * Brighten a color by increasing its lightness
     * @param {string} hexColor - Hex color to brighten
     * @param {number} amount - Amount to brighten (0-100), default 30
     * @returns {string} Brightened hex color
     */
    brighten(hexColor, amount = 30) {
      const rgb = this.hexToRgb(hexColor);
      if (!rgb) return hexColor;

      const hsl = this.rgbToHsl(rgb.r, rgb.g, rgb.b);
      
      // Increase lightness
      hsl.l = Math.min(100, hsl.l + amount);
      
      // For very dark colors, also reduce saturation slightly for better readability
      if (hsl.l < 30) {
        hsl.s = Math.max(0, hsl.s - 10);
      }

      const newRgb = this.hslToRgb(hsl.h, hsl.s, hsl.l);
      return this.rgbToHex(newRgb.r, newRgb.g, newRgb.b);
    },

    /**
     * Calculate perceived brightness of a color (0-255)
     * Uses the formula from W3C: https://www.w3.org/TR/AERT/#color-contrast
     * @param {string} hexColor - Hex color
     * @returns {number} Brightness value (0-255)
     */
    getBrightness(hexColor) {
      const rgb = this.hexToRgb(hexColor);
      if (!rgb) return 128;
      
      return (rgb.r * 299 + rgb.g * 587 + rgb.b * 114) / 1000;
    },

    /**
     * Check if a color is considered "dark" (brightness < 128)
     * @param {string} hexColor - Hex color
     * @returns {boolean} True if color is dark
     */
    isDark(hexColor) {
      return this.getBrightness(hexColor) < 128;
    },

    /**
     * Check if a color is considered "light" (brightness >= 128)
     * @param {string} hexColor - Hex color
     * @returns {boolean} True if color is light
     */
    isLight(hexColor) {
      return this.getBrightness(hexColor) >= 128;
    },

    /**
     * Transform a theme's colors for dark mode
     * @param {object} colors - Theme colors object
     * @param {number} intensity - Transformation intensity (0-100), default 30
     * @returns {object} Transformed colors
     */
    transformForDarkMode(colors, intensity = 30) {
      const transformed = {};
      
      for (const [key, value] of Object.entries(colors)) {
        // Backgrounds should be darkened significantly
        if (key.includes('bg-')) {
          transformed[key] = this.darken(value, intensity + 10);
        }
        // Text colors should be brightened
        else if (key.includes('text-')) {
          transformed[key] = this.brighten(value, intensity);
        }
        // Border colors should be darkened slightly
        else if (key.includes('border')) {
          transformed[key] = this.darken(value, intensity - 10);
        }
        // Accent colors (primary, secondary, success, warning, error)
        // Should be adjusted based on their current brightness
        else {
          if (this.isLight(value)) {
            transformed[key] = this.darken(value, intensity - 10);
          } else {
            transformed[key] = this.brighten(value, 10);
          }
        }
      }
      
      return transformed;
    },

    /**
     * Transform a theme's colors for light mode
     * @param {object} colors - Theme colors object
     * @param {number} intensity - Transformation intensity (0-100), default 30
     * @returns {object} Transformed colors
     */
    transformForLightMode(colors, intensity = 30) {
      const transformed = {};
      
      for (const [key, value] of Object.entries(colors)) {
        // Backgrounds should be brightened significantly
        if (key.includes('bg-')) {
          transformed[key] = this.brighten(value, intensity + 10);
        }
        // Text colors should be darkened
        else if (key.includes('text-')) {
          transformed[key] = this.darken(value, intensity);
        }
        // Border colors should be brightened slightly
        else if (key.includes('border')) {
          transformed[key] = this.brighten(value, intensity - 10);
        }
        // Accent colors - adjust based on current brightness
        else {
          if (this.isDark(value)) {
            transformed[key] = this.brighten(value, intensity - 10);
          } else {
            transformed[key] = this.darken(value, 10);
          }
        }
      }
      
      return transformed;
    },

    /**
     * Automatically transform colors based on target mode and current color brightness
     * @param {object} colors - Theme colors
     * @param {string} targetMode - Target mode ('light' or 'dark')
     * @param {string} currentMode - Current mode of the theme colors ('light' or 'dark')
     * @param {number} intensity - Transformation intensity (0-100), default 30
     * @returns {object} Transformed colors
     */
    autoTransform(colors, targetMode, currentMode, intensity = 30) {
      // If modes match, no transformation needed
      if (targetMode === currentMode) {
        return { ...colors };
      }
      
      // Transform based on target mode
      if (targetMode === 'dark') {
        return this.transformForDarkMode(colors, intensity);
      } else {
        return this.transformForLightMode(colors, intensity);
      }
    }
  };

  // Export to window
  window.ColorTransform = ColorTransform;

})(window);
