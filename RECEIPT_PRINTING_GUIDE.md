# NU Regis Receipt Printing System

This document explains how to set up and use the thermal receipt printing functionality for the NU Regis queue system.

## Features

✅ **Queue Confirmation Receipts** - Print detailed receipts when customers confirm their queue placement  
✅ **Document Details** - Shows all requested documents with quantities and prices  
✅ **QR Code Integration** - Each receipt includes a QR code for verification  
✅ **Real-time Status** - Receipt shows current queue status and position  
✅ **Professional Layout** - Clean, professional thermal printer format  
✅ **Error Handling** - Comprehensive error handling with user feedback  

## Setup Instructions

### 1. Install Dependencies

The required package is already included:
```bash
composer require mike42/escpos-php
```

### 2. Configure Printer

1. **Connect your thermal printer** (POS-58, TM-T20, XP-58, etc.)
2. **Install printer drivers** and ensure it's recognized by Windows
3. **Note the exact printer name** from Windows printer settings
4. **Update your .env file**:
```env
POS_PRINTER_NAME="POS-58"
```

Replace `"POS-58"` with your actual printer name as it appears in Windows.

### 3. Test Printer Connection

Run the test script to verify everything is working:
```bash
php test_receipt_printer.php
```

Or test via the web interface:
- Go to any kiosk confirmation page
- Open browser console
- Run: `testPrinter()`

## Usage

### For Customers (Kiosk Interface)

1. **Confirm your queue placement** on the kiosk
2. **Click "Print Receipt"** button on the confirmation page
3. **Use Ctrl+P** as a keyboard shortcut alternative
4. **Collect your printed receipt** with queue details

### Receipt Contents

Each receipt includes:
- **Header**: NU Registrar Queue System branding
- **Customer Info**: Name, course, year level, reference code
- **Document List**: All requested documents with quantities and prices
- **Total Amount**: Complete cost breakdown
- **Queue Status**: Current status and position
- **QR Code**: For verification and tracking
- **Instructions**: Wait time and pickup instructions

## Supported Printers

### Tested Models
- **POS-58** (58mm thermal printer)
- **POS-80** (80mm thermal printer)
- **Epson TM-T20**
- **Epson TM-T88**
- **XP-58**

### Printer Requirements
- **Windows compatible** thermal printer
- **ESC/POS command support**
- **USB or Network connection**
- **Proper Windows drivers installed**

## Configuration Options

### Environment Variables

```env
# Default printer name
POS_PRINTER_NAME="POS-58"

# Alternative printer names for testing
# POS_PRINTER_NAME="Microsoft Print to PDF"
# POS_PRINTER_NAME="Your Custom Printer Name"
```

### Service Configuration

The `ReceiptPrintingService` class can be customized:

```php
$receiptService = new ReceiptPrintingService();

// Set custom printer name
$receiptService->setPrinterName('Custom-Printer-Name');

// Print receipt
$result = $receiptService->printQueueReceipt($request, 'student');
```

## API Endpoints

### Print Receipt
```
POST /kiosk/print-receipt/{type}/{id}
```
- `type`: 'student' or 'onsite'
- `id`: Request ID

### Test Printer
```
POST /kiosk/test-printer
```
Optional body: `{"printer_name": "Custom-Printer"}`

## Troubleshooting

### Common Issues

#### ❌ "Printer not found" Error
**Solution:**
1. Check printer name in Windows settings
2. Update `POS_PRINTER_NAME` in .env file
3. Restart the application

#### ❌ "Access denied" Error
**Solution:**
1. Run as administrator
2. Check printer permissions
3. Verify printer is not in use by another application

#### ❌ "Connection failed" Error
**Solution:**
1. Check USB/network connection
2. Verify printer is powered on
3. Reinstall printer drivers

#### ❌ "Garbled text" Error
**Solution:**
1. Check character encoding settings
2. Verify printer supports ESC/POS commands
3. Update printer firmware

### Testing Steps

1. **Test printer connection**:
   ```bash
   php test_receipt_printer.php
   ```

2. **Test from browser console**:
   ```javascript
   testPrinter()
   ```

3. **Test actual receipt printing**:
   - Go to kiosk confirmation page
   - Click "Print Receipt" button

### Log Files

Check Laravel logs for printing errors:
```bash
tail -f storage/logs/laravel.log
```

## Advanced Features

### Custom Receipt Layouts

Modify `App\Services\ReceiptPrintingService` to customize:
- Header/footer text
- Font sizes and styles
- QR code size and position
- Document layout

### Multiple Printer Support

```php
// Different printers for different document types
$transcriptPrinter = new ReceiptPrintingService();
$transcriptPrinter->setPrinterName('Printer-A');

$certificatePrinter = new ReceiptPrintingService();
$certificatePrinter->setPrinterName('Printer-B');
```

### Printer Status Monitoring

```php
$availablePrinters = ReceiptPrintingService::getAvailablePrinters();
foreach ($availablePrinters as $printer) {
    $service = new ReceiptPrintingService();
    $service->setPrinterName($printer);
    $status = $service->testPrinter();
    // Log or display status
}
```

## Security Notes

- Receipts contain sensitive information (names, reference codes)
- Ensure printers are in secure locations
- Consider printer access controls in production
- Monitor printing logs for audit trails

## Support

For issues or questions:
1. Check this documentation first
2. Review Laravel logs
3. Test with the provided test script
4. Contact system administrator

---

**Note**: This system requires Windows environment with proper thermal printer drivers. For Linux/Mac environments, additional configuration may be needed.