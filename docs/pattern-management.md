# Managing Winning Patterns

Bingoware-ng now supports full CRUD (Create, Read, Update, Delete) operations for winning patterns. Admins can add custom patterns, edit existing ones, and enable/disable patterns for gameplay.

## Accessing Pattern Management

1. Navigate to the main menu
2. Click on **🎯 Winning Patterns**
3. You'll see a list of all available patterns

## Pattern List

The pattern management page displays:
- **Pattern Name**: The name of the pattern
- **Status Badges**:
  - `ENABLED`: Pattern is currently active in gameplay
  - `DEFAULT`: Pattern is built-in and cannot be deleted
- **Description**: Optional description of the pattern
- **Actions**:
  - **Enable checkbox**: Toggle pattern on/off for gameplay
  - **✏️ Edit button**: Modify the pattern (not available for "Normal" pattern)
  - **🗑️ Delete button**: Remove custom patterns (not available for default patterns)

## Adding a New Pattern

1. Click the **➕ Add New Pattern** button
2. Fill in the form:
   - **Pattern Name** (required): A unique name for your pattern
   - **Description** (optional): Brief description of the pattern
   - **Pattern Grid** (required): Click squares on the 5x5 grid to select which positions must be marked to win
   - **Enable this pattern**: Check to make it active immediately
3. Click **Save Pattern**

### Grid Selection Tips
- Click any square to toggle it on/off
- Selected squares will be highlighted in purple
- You must select at least one square
- The center square shows a star (★) to indicate the traditional "Free" position

## Editing a Pattern

1. Find the pattern in the list
2. Click the **✏️ Edit** button
3. Modify the name, description, or grid layout
4. Click **Save Pattern**

**Note**: The "Normal" pattern (any row, column, or diagonal) cannot be edited as it uses special logic.

## Deleting a Pattern

1. Find the custom pattern in the list
2. Click the **🗑️ Delete** button
3. Confirm the deletion

**Note**: Default patterns cannot be deleted, but they can be disabled.

## Enabling/Disabling Patterns

Toggle the **Enable** checkbox next to any pattern to activate or deactivate it for gameplay. Disabled patterns will not be checked during games.

## Default Patterns

Bingoware-ng comes with 11 built-in patterns:
1. **Normal**: Any row, column, or diagonal
2. **Four Corners**: All four corner squares
3. **Cross-Shaped**: Center column and center row
4. **T-Shaped**: Top row and center column
5. **X-Shaped**: Both diagonals
6. **+ Shaped**: Center row and center column forming a plus
7. **Z-Shaped**: Top row, diagonal, and bottom row forming a Z
8. **N-Shaped**: Left column, diagonal, and right column forming an N
9. **Box Shaped**: All squares except the perimeter
10. **Square Shaped**: Only the perimeter squares
11. **Blackout (Full Card)**: All 25 squares

## Validation Rules

When creating or editing patterns:
- **Pattern names must be unique** (case-insensitive)
- **At least one square must be selected**
- **Grid coordinates must be within the 5x5 grid** (0-4 for both row and column)
- **Pattern names are limited to 50 characters**
- **Descriptions are limited to 200 characters**

## How Patterns Work in Gameplay

During a bingo game:
1. Only **enabled** patterns are checked for winners
2. Each time a number is drawn, all cards are checked against all enabled patterns
3. If a card matches all required squares for a pattern, it's marked as a winner
4. Multiple patterns can trigger on the same card
5. Winners are displayed at the bottom of the Play page, grouped by pattern

## Migration from Old System

If you're upgrading from an older version of Bingoware-ng:
- Your existing pattern selections are automatically migrated to the new system
- Previously enabled patterns remain enabled
- All pattern grid layouts are preserved
- The migration happens automatically on first access

## Troubleshooting

### Pattern not appearing in game
- Verify the pattern is **enabled** (green ENABLED badge)
- Make sure at least one square is selected in the pattern grid
- Restart the game to apply pattern changes

### Cannot delete a pattern
- Default patterns cannot be deleted, only disabled
- Check if the pattern has the `DEFAULT` badge

### Duplicate name error
- Pattern names must be unique across all patterns
- Try using a different name or adding a suffix (e.g., "L-Shaped v2")

## Best Practices

1. **Test new patterns** before using them in live games
2. **Use descriptive names** that clearly indicate the pattern shape
3. **Keep enabled patterns reasonable** (3-5 active patterns work well)
4. **Document custom patterns** using the description field
5. **Back up your patterns** by copying the `data/patterns.json` file

## Technical Details

Patterns are stored in `data/patterns.json` in JSON format. Each pattern includes:
- Unique ID
- Name and description
- Grid layout (array of {col, row} coordinates)
- Enabled status
- Flags indicating if it's a default or special pattern

The system supports unlimited custom patterns, though practical considerations suggest keeping the total reasonable for usability.
