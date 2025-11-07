# New Features - Lusis Route Management Platform

## 1. XML Import Functionality

### Overview
Import existing Tango routing configuration XML files into the database.

### How to Use
1. Navigate to **Import XML** from the sidebar
2. Select a target project
3. Enter a name for the route file
4. Upload your XML file (`routing_*.xml`)
5. Click **Import XML**

### Supported XML Structure
```xml
<routing>
  <deltas>...</deltas>
  <matches>...</matches>
  <rules>...</rules>
  <routes>...</routes>
</routing>
```

### Import Behavior
- **Reuses existing entities**: If a service, match, rule, or delta with the same name exists, it will be reused
- **Auto-creates services**: Services referenced in routes will be automatically created if they don't exist
- **Sequential priority**: Route priorities are assigned in the order they appear in the XML
- **Transactional**: All changes are wrapped in a database transaction - if import fails, nothing is saved

### Example
A sample XML file is provided at `storage/app/sample_routing.xml` for testing.

---

## 2. XML Export Functionality

### Overview
Export route files from the database back to XML format, compatible with Tango routing configuration.

### How to Use
1. Navigate to **Export XML** from the sidebar
2. Optional: Filter by project
3. Select a route file to export
4. Click **Export to XML**
5. The file will download automatically

### Export Contents
The exported XML includes:
- All routes in the selected route file
- Associated matches and their conditions
- Associated rules and their configurations
- Associated deltas and their transformations

### Export Format
- Well-formatted XML with proper indentation
- Compatible with Tango routing configuration format
- Filename: `routing_{name}_{timestamp}.xml`

### Quick Export
The export page includes a "Quick Export" section showing recent route files for one-click export.

---

## 3. Match Conditions Inline Editor

### Overview
Edit match conditions directly on the match details page without page navigation.

### Features

#### View Conditions
- See all conditions in a table format
- Field, Operator, and Value columns clearly displayed
- Real-time statistics showing condition count

#### Add Conditions
1. Click **Add Condition** button
2. Enter field name (e.g., `tg:MTI`)
3. Select operator (EQUAL, SUP, INF, ELT, IN)
4. Enter value (optional)
5. Click ✓ to save or ✗ to cancel

#### Edit Conditions
1. Click directly in any field/operator/value cell
2. Make your changes
3. Save and Cancel buttons appear automatically
4. Click ✓ to save or ✗ to cancel

#### Delete Conditions
1. Click the trash icon next to any condition
2. Confirm deletion
3. Condition is removed with fade animation

### Technical Details
- **AJAX-powered**: Changes are saved without page reload
- **Real-time feedback**: Success/error alerts display immediately
- **Change detection**: Save/Cancel buttons only appear when changes are made
- **Auto-validation**: Required fields are validated before saving

### Operators Explained
| Operator | Description | Example |
|----------|-------------|---------|
| EQUAL | Exact match | `tg:MTI EQUAL 0100` |
| SUP | Greater than (>) | `tg:AMOUNT SUP 1000` |
| INF | Less than (<) | `tg:MTI INF 7000` |
| ELT | In list | `tg:TYPE ELT [A,B,C]` |
| IN | Contains | `tg:DATA IN "test"` |

---

## API Endpoints

### Match Conditions API
- `POST /match-conditions` - Create a new condition
- `PUT /match-conditions/{id}` - Update an existing condition
- `DELETE /match-conditions/{id}` - Delete a condition

### XML Import/Export
- `GET /xml/import` - Show import form
- `POST /xml/import` - Process XML import
- `GET /xml/export` - Show export form
- `POST /xml/export` - Generate and download XML

---

## Testing

### Test XML Import
1. Create a new project
2. Go to Import XML
3. Use the sample file: `storage/app/sample_routing.xml`
4. Import and verify all entities are created

### Test XML Export
1. Create or select a route file with routes
2. Go to Export XML
3. Select the file and export
4. Open the downloaded XML to verify structure

### Test Inline Conditions
1. Go to any match details page
2. Add a new condition
3. Edit an existing condition
4. Delete a condition
5. Verify statistics update in real-time

---

## Notes

- All features use Bootstrap 5 styling
- AJAX requests require CSRF token (automatically handled)
- Import validates XML structure before processing
- Export groups routes by service for better organization
- Inline editor uses jQuery for DOM manipulation

---

## Future Enhancements

- [ ] Batch import multiple XML files
- [ ] XML validation preview before import
- [ ] Export with custom filters
- [ ] Import/export history tracking
- [ ] Inline editor for other relationships
- [ ] Drag-and-drop XML upload
