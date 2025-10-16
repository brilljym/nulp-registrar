# NU Regis Hybrid Printing Setup Guide

## Overview
The hybrid printing system allows your NU Regis application to work both locally (with thermal printing) and in production on Hostinger (with print job queuing).

## Architecture

```
┌─────────────────┐    ┌──────────────────┐    ┌─────────────────┐
│   Web Browser   │    │  Laravel App     │    │ Local Print     │
│    (Kiosk)      │────│  (Hostinger)     │────│   Service       │
└─────────────────┘    └──────────────────┘    └─────────────────┘
                              │                          │
                              ▼                          ▼
                       ┌──────────────┐         ┌─────────────────┐
                       │   Database   │         │ Thermal Printer │
                       │ (Print Jobs) │         │    (Local)      │
                       └──────────────┘         └─────────────────┘
```

## Setup Instructions

### 1. Local Development
For testing with local thermal printer:

```bash
# Copy local environment
cp .env.local .env

# Add your printer name
POS_PRINTER_NAME=POS-58

# Your app will print directly to thermal printer
php artisan serve
```

### 2. Production Deployment (Hostinger)

#### Step 1: Deploy Main App
```bash
# Copy production environment
cp .env.production .env

# Update database credentials
PRINT_REMOTE_DB_HOST=your-hostinger-db-host.com
PRINT_REMOTE_DB_NAME=your_database_name
PRINT_REMOTE_DB_USER=your_database_user
PRINT_REMOTE_DB_PASS=your_database_password

# Deploy to Hostinger (however you deploy)
```

#### Step 2: Setup Local Print Service (Kiosk Machine)
```bash
# On your local kiosk machine, configure the remote API
# Edit local_print_service.php line 21:
'remote_api_url' => 'https://your-nu-regis-site.com/api',

# Install required packages
composer require chillerlan/php-qrcode

# Start the print service
start_print_service.bat
```

## How It Works

### Production Flow:
1. User confirms queue placement on kiosk
2. Web app (on Hostinger) creates print job in database
3. Local print service polls Hostinger API every 5 seconds
4. Local service downloads print job and prints to thermal printer
5. Local service marks job as completed in remote database

### Local Development Flow:
1. User confirms queue placement
2. App detects local environment
3. Prints directly to thermal printer
4. Also logs to print jobs table for tracking

## API Endpoints

### For Local Print Service:
- `GET /api/print-jobs/pending` - Get pending print jobs
- `PUT /api/print-jobs/{id}/completed` - Mark job as printed
- `PUT /api/print-jobs/{id}/failed` - Mark job as failed
- `GET /api/print-jobs/status` - Get printing statistics

## Monitoring

### Check Print Job Status:
```bash
# View pending jobs
curl https://your-site.com/api/print-jobs/status

# Response:
{
  "success": true,
  "statistics": {
    "pending": 2,
    "completed_today": 15,
    "failed_today": 0
  }
}
```

### Local Service Logs:
```
[2025-10-11 09:30:15] 🚀 Local Print Service started
[2025-10-11 09:30:15] 📡 Polling: https://nu-regis.com/api
[2025-10-11 09:30:15] 🖨️ Printer: POS-58
[2025-10-11 09:30:20] 📄 Found 1 pending print job(s)
[2025-10-11 09:30:21] 🖨️ Processing job #123: TEST001
[2025-10-11 09:30:23] ✅ Job #123 printed successfully
```

## Troubleshooting

### Print Service Won't Start:
1. Check if `mike42/escpos-php` is installed
2. Verify printer name in configuration
3. Ensure printer is connected and ready

### Jobs Not Printing:
1. Check internet connection on kiosk machine
2. Verify API URL is accessible
3. Check database permissions for API endpoints

### Production Setup Issues:
1. Ensure API routes are accessible (not behind auth)
2. Check database connection from local service
3. Verify CORS settings if needed

## Security Notes

- Print job API endpoints should be rate-limited
- Consider IP whitelisting for print service endpoints
- Print jobs contain customer data - ensure HTTPS
- Regularly clean up old print job records

## Files Overview

- `app/Models/PrintJob.php` - Print job database model
- `app/Services/HybridPrintingService.php` - Main hybrid printing logic
- `app/Http/Controllers/Api/PrintJobController.php` - API for print jobs
- `local_print_service.php` - Local service that polls and prints
- `start_print_service.bat` - Windows batch file to start service
- `config/printing.php` - Printing configuration

## Benefits

✅ **Best of both worlds**: Cloud hosting + local thermal printing  
✅ **Reliable**: If local printing fails, jobs are queued for retry  
✅ **Scalable**: Can add multiple kiosk machines  
✅ **Maintainable**: Clear separation between web app and printing  
✅ **Cost-effective**: Use shared hosting while keeping hardware local