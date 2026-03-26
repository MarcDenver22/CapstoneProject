# DTR (Daily Time Record) Export System - Implementation Summary

## Overview
A complete DTR export system has been implemented with support for **Excel** and **PDF** formats following the **Civil Service Form No. 48** standard.

## What Was Added

### 1. **Packages Installed**
- `maatwebsite/excel` - For Excel export functionality
- `barryvdh/laravel-dompdf` - For PDF generation

### 2. **Backend Components**

#### Export Classes
- **`App\Exports\DtrExport`** - Handles Excel export with proper formatting
- **`App\Exports\DtrExport`** - Handles PDF data preparation

#### Controller Methods (ReportController)
- **`dtrExportPage()`** - Shows DTR export interface
- **`exportDtrExcel()`** - Exports DTR as Excel file
- **`exportDtrPdf()`** - Exports DTR as PDF file

### 3. **Frontend Components**

#### View
- **`resources/views/hr/dtr-export.blade.php`** - DTR export interface with:
  - Employee selection dropdown
  - Month/Year selectors
  - Excel/PDF export buttons
  - Format preview table
  - Usage instructions

#### PDF Template
- **`resources/views/exports/dtr-export.blade.php`** - Professional DTR PDF layout with:
  - Header (Civil Service Form No. 48)
  - Employee information section
  - Detailed DTR table with borders
  - Total hours calculation
  - Signature lines

### 4. **Routes**
```
GET /hr/dtr                        -> dtrExportPage (Show export interface)
GET /hr/dtr/export-excel          -> exportDtrExcel (Download Excel file)
GET /hr/dtr/export-pdf            -> exportDtrPdf (Download PDF file)
```

## Features

### Excel Export Features
- ✅ Standard Civil Service format
- ✅ Proper headers and styling
- ✅ Borders and cell formatting
- ✅ Column width optimization
- ✅ Font configuration (Calibri, 10px)
- ✅ Centered alignment
- ✅ Automatic filename with employee name, month, year

### PDF Export Features
- ✅ Professional layout matching Civil Service standards
- ✅ Employee information section
- ✅ Daily attendance table with:
  - Day number
  - Date (Mon, Tue, etc.)
  - Time In
  - Time Out
  - Hours Worked (calculated)
  - Attendance Status
- ✅ Total hours summary
- ✅ Signature lines for:
  - Employee
  - Supervisor
  - Department Head
- ✅ Generation timestamp
- ✅ A4 paper size with proper margins
- ✅ Professional monospace font (Courier New)

### Data Processing
- Groups attendance records by day
- Calculates hours worked automatically
- Handles missing records gracefully
- Validates date ranges
- Logs export actions in audit trail

## How to Use

### For HR Personnel

1. **Access DTR Export Page**
   - Navigate to: HR Dashboard → Reports → DTR Export
   - Or directly access: `/hr/dtr`

2. **Select Parameters**
   - Choose Employee from dropdown
   - Select Month (January - December)
   - Select Year (2020 - current year)

3. **Export**
   - Click "Export as Excel" for `.xlsx` file
   - Click "Export as PDF" for `.pdf` file

4. **File Details**
   - Files are named: `DTR_[Employee Name]_[Month]_[Year].[ext]`
   - All exports are logged in audit trail
   - Can be printed or distributed to employees

## Database/Model Integration

**Uses existing models:**
- `App\Models\Attendance` - Retrieves attendance records
  - `user_id` - Employee ID
  - `attendance_date` - Date of attendance
  - `time_in` - Time In (timestamp)
  - `time_out` - Time Out (timestamp)
  - `status` - Attendance status (present, late, absent, etc.)
  - `liveness_verified` - Face recognition verification

- `App\Models\User` - Employee information
  - `name` - Full name
  - `email` - Email address
  - `position` - Job position
  - `department` - Department relation

## Export Format Details

### Excel Format
| Column | Width | Content |
|--------|-------|---------|
| Day | 8% | 1-31 |
| Date | 12% | Mon, Tue, etc. |
| Time In | 12% | HH:MM format |
| Time Out | 12% | HH:MM format |
| Hours Worked | 15% | Decimal hours |
| Status | 15% | PRESENT, LATE, ABSENT, etc. |

### PDF Format
- **Header**: "DAILY TIME RECORD - Civil Service Form No. 48"
- **Info Section**: Employee name, position, department, month/year, office hours
- **Table**: Same as Excel with professional styling
- **Footer**: Generated timestamp and signature lines

## Audit Logging

All DTR exports are logged with:
- Export type (excel_dtr or pdf_dtr)
- User ID (who exported)
- Employee ID (whose DTR)
- Month and Year
- Filename
- Timestamp

Access in: **Admin** → **Audit Logs** → Filter by "export"

## Customization Options

### To change office hours:
Edit: `resources/views/exports/dtr-export.blade.php`
```blade
<div class="info-value">8:00 AM - 5:00 PM</div>
```

### To add company logo or seal:
Edit PDF template to include image before the header

### To change date range display:
Modify the year selection in `dtr-export.blade.php`

### To auto-select current month/year:
Already implemented! Form defaults to current month/year

## Notes

1. **File naming**: Files include employee name for easy organization
2. **Timezone**: Uses server timezone for time calculations
3. **Hours calculation**: Automatically computed from time_in and time_out difference in minutes, converted to hours
4. **Missing records**: Shows "-" for days without attendance records
5. **Future dates**: Automatically skips dates beyond current date
6. **Signature lines**: Left blank for manual signing after printing

## Testing Checklist

- [ ] Access /hr/dtr page
- [ ] Select employee, month, year
- [ ] Export to Excel
- [ ] Export to PDF
- [ ] Verify file formats
- [ ] Check audit logs for export entries
- [ ] Print PDF and verify layout
- [ ] Open Excel and verify formatting
- [ ] Test with different employees
- [ ] Test with different month/year combinations

## Support

For issues:
1. Check browser console for JavaScript errors
2. Review Laravel logs in `storage/logs/laravel.log`
3. Verify all models and relationships are correctly defined
4. Ensure attendance records exist for selected period
