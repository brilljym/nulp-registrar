# Real-time Pusher Integration for NU Regis v2

## Overview
This implementation provides real-time updates for the registrar workflow using Pusher. When registrars approve, release, or close requests, the system broadcasts events that automatically update the UI for both students and registrars.

## Features Implemented

### 1. Enhanced RealTimeEvent
- **Multi-channel support**: Events can be broadcast to multiple channels simultaneously
- **Dynamic channel targeting**: Supports both general notifications and request-specific updates

### 2. Enhanced RealTimeNotificationService
- **Request status updates**: New method `sendRequestStatusUpdate()` that broadcasts to both registrar and request-specific channels
- **Flexible channel configuration**: Methods now accept custom channel arrays

### 3. Controller Updates

#### RegistrarController
- All workflow methods now broadcast real-time events:
  - `acceptRequest()` - Broadcasts when request is accepted
  - `markAsReadyForRelease()` - Broadcasts when ready for release
  - `closeRequest()` - Broadcasts when request is completed
  - `approve()` - Broadcasts when request is approved

#### OnsiteRequestController
- All registrar workflow actions broadcast events:
  - `acceptRequest()` - Onsite request accepted
  - `markAsReadyForRelease()` - Onsite request ready for release
  - `closeRequest()` - Onsite request completed

### 4. Frontend Integration

#### Timeline Page (`resources/views/onsite/timeline.blade.php`)
- Listens to request-specific channel: `request-{request_id}`
- Shows real-time notifications when status changes
- Auto-refreshes page when status updates are received
- Displays toast notifications with update details

#### Registrar Dashboard (`resources/views/layouts/registrar.blade.php`)
- Listens to general registrar channel: `registrar-notifications`
- Shows enhanced notifications with request details
- Auto-refreshes relevant dashboard pages
- Plays notification sound for status updates
- Shows contextual information (student name, document type, etc.)

### 5. Channel Structure

#### Channels Used:
- `registrar-notifications` - General channel for all registrar notifications
- `request-{request_id}` - Specific channel for individual request updates

#### Event Structure:
```json
{
  "message": "Human-readable message",
  "type": "status-update|success|error|warning",
  "data": {
    "request_id": "Request ID or reference code",
    "status": "Current status",
    "status_update": true,
    "student_name": "Student full name",
    "document_type": "Document name",
    "registrar_name": "Registrar full name",
    "request_type": "onsite|online"
  },
  "timestamp": "ISO timestamp"
}
```

## Testing

### Demo Page
Visit `/pusher-demo` to test the real-time functionality:
- Shows connection status
- Displays subscribed channels
- Logs all received messages
- Allows sending test notifications

### Test Routes
- `/test-pusher` - Basic Pusher test
- `/test-request-update/{requestId?}` - Test request status updates

## Configuration Requirements

### Environment Variables
Make sure these are set in `.env`:
```
PUSHER_APP_ID=your_app_id
PUSHER_APP_KEY=your_key
PUSHER_APP_SECRET=your_secret
PUSHER_APP_CLUSTER=your_cluster
```

### Broadcasting Configuration
Ensure `config/broadcasting.php` has the correct Pusher settings and that the default broadcast driver is set to 'pusher' in `config/app.php`.

## Usage Flow

1. **Request Creation**: Student creates a request (online or onsite)
2. **Registrar Action**: Registrar performs an action (accept/release/close)
3. **Event Broadcasting**: System broadcasts to both channels:
   - General registrar dashboard receives notification
   - Specific request timeline receives update
4. **UI Updates**: 
   - Registrar sees notification and dashboard refreshes
   - Student timeline shows real-time status update and refreshes
5. **Automatic Refresh**: Both interfaces update without manual page refresh

## Benefits

- **Real-time visibility**: Both registrars and students see updates immediately
- **Reduced refresh fatigue**: No need to manually refresh pages
- **Enhanced UX**: Visual and audio notifications keep users informed
- **Scalable architecture**: Easy to extend for additional notification types
- **Maintainable code**: Centralized notification service for consistent behavior

## Future Enhancements

Potential additions could include:
- Push notifications for mobile apps
- Email notifications for major status changes
- SMS notifications for completed requests
- Real-time analytics dashboard
- Queue position updates for onsite requests