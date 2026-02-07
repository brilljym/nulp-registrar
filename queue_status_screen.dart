import 'package:flutter/material.dart';
import 'dart:async';
import 'dart:convert';
import 'package:http/http.dart' as http;
import '../theme/nu_theme.dart';
import '../services/api_service.dart';
import 'package:flutter_tts/flutter_tts.dart';
import 'package:vibration/vibration.dart';
import 'package:onesignal_flutter/onesignal_flutter.dart';

class QueueStatusScreen extends StatefulWidget {
  final String referenceId;
  final String? referenceType;
  final Map<String, dynamic>? referenceData;

  const QueueStatusScreen({
    Key? key,
    required this.referenceId,
    this.referenceType,
    this.referenceData,
  }) : super(key: key);

  @override
  State<QueueStatusScreen> createState() => _QueueStatusScreenState();
}

class _QueueStatusScreenState extends State<QueueStatusScreen> {
  Timer? _timer;
  Timer? _queueTimer;
  DateTime _currentTime = DateTime.now();
  String _currentlyProcessing = "Transcript of Records - Juan Dela Cruz";
  int _yourQueueNumber = 7;
  String _status = "Processing";
  int _currentNumber = 5;
  String _studentName = "";
  List<Map<String, dynamic>> _documents = []; // Support multiple documents
  String _documentName = ""; // Keep for backward compatibility
  DateTime? _expectedReleaseTime;
  String? _expectedReleaseDate; // From API/database
  String _referenceNumber = ""; // Reference number for display
  int _position = 0; // Queue position
  String _queueNumber = ""; // Queue number from database
  bool _isDarkMode = false; // Dark mode preference
  Map<String, dynamic>? _debugInfo; // Debug information from API

  // Text-to-speech and vibration
  late FlutterTts _flutterTts;
  String _previousStatus = ""; // Track previous status to detect changes

  // Debug variables
  String? _onesignalPlayerId;
  DateTime? _lastUpdated;
  bool _isConnected = false;

  @override
  void initState() {
    super.initState();
    print('QueueStatusScreen initState called for referenceId: ${widget.referenceId}');
    _loadDarkModePreference();
    print('Dark mode preference loaded: $_isDarkMode');

    // Initialize OneSignal and get player ID
    _initializeOneSignal();

    // Initialize TTS
    _flutterTts = FlutterTts();
    _initializeTts();

    _initializeData();
    _startTimer();
    _startQueueRefresh();
    // Initial refresh from API
    _refreshDataFromAPI();

    print('DEBUG: Initial status after init: $_status');
    print('DEBUG: TTS initialized: $_flutterTts');
  }

  void _initializeData() {
    print('_initializeData called with referenceId: ${widget.referenceId}, referenceType: ${widget.referenceType}');
    if (widget.referenceData != null) {
      final data = widget.referenceData!;

      if (widget.referenceType == 'transaction') {
        _studentName = data['student_name'] ?? 'Unknown Student';
        _status = data['status'] ?? 'Processing';
        _expectedReleaseDate = data['expected_release_date']; // Get from API
        _queueNumber = data['queue_number'] ?? ''; // Get queue number from data
        _referenceNumber = widget.referenceId; // Set reference number

        // Handle multiple documents
        if (data['documents'] != null && data['documents'] is List) {
          _documents = List<Map<String, dynamic>>.from(data['documents']);
          // Add queue_number to each document if not present
          for (var i = 0; i < _documents.length; i++) {
            _documents[i]['queue_number'] = _documents[i]['queue_number'] ?? _queueNumber;
          }
        } else if (data['document_name'] != null) {
          // Single document fallback
          _documents = [{'name': data['document_name'], 'status': _status, 'quantity': data['quantity'] ?? 1, 'queue_number': _queueNumber.isNotEmpty ? _queueNumber : 'A${_yourQueueNumber.toString().padLeft(3, '0')}'}];
          _documentName = data['document_name'];
        } else {
          _documents = [{'name': 'Unknown Document', 'status': _status, 'quantity': 1, 'queue_number': _queueNumber.isNotEmpty ? _queueNumber : 'A${_yourQueueNumber.toString().padLeft(3, '0')}'}];
          _documentName = 'Unknown Document';
        }

        // Generate queue number based on reference ID (simple hash)
        if (widget.referenceId == 'NU822694') {
          // Special case for demo
          _yourQueueNumber = 2;
          _currentNumber = 1;
          _status = 'In Queue';
          _position = _yourQueueNumber - _currentNumber;
        } else {
          _yourQueueNumber = widget.referenceId.hashCode.abs() % 20 + 1;
          _currentNumber = (_yourQueueNumber > 5) ? _yourQueueNumber - 2 : 1;
          _position = _yourQueueNumber - _currentNumber;
        }

      } else if (widget.referenceType == 'onsite_request') {
        _studentName = data['full_name'] ?? 'Unknown Student';
        _status = data['status'] ?? 'Processing';
        _expectedReleaseDate = data['expected_release_date']; // Get from API
        _queueNumber = data['queue_number'] ?? ''; // Get queue number from data

        // Handle multiple documents
        if (data['documents'] != null && data['documents'] is List) {
          _documents = List<Map<String, dynamic>>.from(data['documents']);
          // Add queue_number to each document if not present
          for (var i = 0; i < _documents.length; i++) {
            _documents[i]['queue_number'] = _documents[i]['queue_number'] ?? _queueNumber;
          }
        } else if (data['document_name'] != null) {
          // Single document fallback
          _documents = [{'name': data['document_name'], 'status': _status, 'quantity': data['quantity'] ?? 1, 'queue_number': _queueNumber.isNotEmpty ? _queueNumber : 'A${_yourQueueNumber.toString().padLeft(3, '0')}'}];
          _documentName = data['document_name'];
        } else {
          _documents = [{'name': 'Form 137', 'status': _status, 'quantity': 1, 'queue_number': _queueNumber.isNotEmpty ? _queueNumber : 'A${_yourQueueNumber.toString().padLeft(3, '0')}'}];
          _documentName = 'Form 137';
        }

        // Generate queue number based on reference ID (simple hash)
        if (widget.referenceId == 'NU822694') {
          // Special case for demo
          _yourQueueNumber = 2;
          _currentNumber = 1;
          _status = 'In Queue';
          _position = _yourQueueNumber - _currentNumber;
        } else {
          _yourQueueNumber = widget.referenceId.hashCode.abs() % 20 + 1;
          _currentNumber = (_yourQueueNumber > 5) ? _yourQueueNumber - 2 : 1;
          _position = _yourQueueNumber - _currentNumber;
        }
      }

      print('Initial data loaded - Expected release date: $_expectedReleaseDate'); // Debug
      print('Documents loaded: $_documents'); // Debug

      // Set current processing display
      if (_documents.isNotEmpty) {
        if (_documents.length == 1) {
          final quantity = (_documents[0]['quantity'] ?? 1) as int;
          if (quantity > 1) {
            _currentlyProcessing = "${_documents[0]['name']} (${quantity}x) - $_studentName";
          } else {
            _currentlyProcessing = "${_documents[0]['name']} - $_studentName";
          }
          _documentName = _documents[0]['name']; // For backward compatibility
        } else {
          final totalQuantity = _documents.fold<int>(0, (sum, doc) => sum + ((doc['quantity'] ?? 1) as int));
          _currentlyProcessing = "${_documents.length} Documents ($totalQuantity total) - $_studentName";
          _documentName = "${_documents.length} Documents";
        }
      }

      _setExpectedReleaseTime();
    } else {
      // If no reference data, use fallback calculation
      _documents = [{'name': 'Unknown Document', 'status': 'Processing', 'quantity': 1, 'queue_number': 'A${_yourQueueNumber.toString().padLeft(3, '0')}'}];
      _documentName = 'Unknown Document';
      _calculateExpectedReleaseTime();
    }

    // Trigger TTS/vibration for initial status if it matches criteria
    _handleStatusChangeNotification(_status);

    print('_initializeData completed. Status: $_status, Documents: ${_documents.length}, Expected time: $_expectedReleaseTime');
  }

  void _setExpectedReleaseTime() {
    print('_setExpectedReleaseTime called. _expectedReleaseDate: $_expectedReleaseDate');
    // Use the expected_release_date from API/database if available
    print('Setting expected release time. _expectedReleaseDate: $_expectedReleaseDate'); // Debug
    if (_expectedReleaseDate != null && _expectedReleaseDate!.isNotEmpty) {
      try {
        _expectedReleaseTime = DateTime.parse(_expectedReleaseDate!);
        print('Successfully parsed expected release time: $_expectedReleaseTime'); // Debug
        return;
      } catch (e) {
        print('Error parsing expected release date: $_expectedReleaseDate, Error: $e');
      }
    }

    // Fallback to calculation if no date provided or parsing failed
    print('Falling back to calculated release time'); // Debug
    _calculateExpectedReleaseTime();
    print('_setExpectedReleaseTime completed. Final expected time: $_expectedReleaseTime');
  }

  void _calculateExpectedReleaseTime() {
    print('_calculateExpectedReleaseTime called for status: $_status, document: $_documentName');
    final now = DateTime.now();

    // Check if it's outside business hours and adjust accordingly
    DateTime businessTime = _getNextBusinessTime(now);

    switch (_status.toLowerCase()) {
      case 'ready for release':
      case 'ready_for_pickup':
      case 'completed':
      case 'released':
      // Document is ready for pickup
        _expectedReleaseTime = now;
        break;

      case 'processing':
      case 'in progress':
      case 'your turn!':
      // Currently being processed - estimate based on document type
        final processingMinutes = _getDocumentProcessingTime(_documentName);
        _expectedReleaseTime = businessTime.add(Duration(minutes: processingMinutes));
        break;

      case 'accepted':
      case 'pending':
      case 'in queue':
      case 'in_queue':
      case 'waiting':
      // In queue - calculate based on position and processing times
        final queuePosition = _yourQueueNumber - _currentNumber;
        final averageProcessingTime = _getDocumentProcessingTime(_documentName);
        final estimatedMinutes = (queuePosition * averageProcessingTime) + averageProcessingTime;
        _expectedReleaseTime = businessTime.add(Duration(minutes: estimatedMinutes));
        break;

      case 'submitted':
      case 'under review':
      // Initial review process
        final reviewMinutes = 30 + (_yourQueueNumber * 5); // Base review + queue factor
        _expectedReleaseTime = businessTime.add(Duration(minutes: reviewMinutes));
        break;

      default:
      // Unknown status - conservative estimate
        _expectedReleaseTime = businessTime.add(const Duration(minutes: 45));
        break;
    }

    // Ensure release time is within business hours
    _expectedReleaseTime = _adjustToBusinessHours(_expectedReleaseTime!);
    print('_calculateExpectedReleaseTime completed. Calculated time: $_expectedReleaseTime');
  }

  int _getDocumentProcessingTime(String documentName) {
    // More realistic processing times based on document complexity
    final docLower = documentName.toLowerCase();

    if (docLower.contains('transcript')) {
      return 20; // Transcript processing
    } else if (docLower.contains('diploma')) {
      return 25; // Diploma verification and processing
    } else if (docLower.contains('certificate')) {
      return 15; // Certificate generation
    } else if (docLower.contains('form')) {
      return 10; // Form processing
    } else if (docLower.contains('clearance')) {
      return 12; // Clearance verification
    } else if (docLower.contains('enrollment')) {
      return 8; // Enrollment verification
    } else {
      return 15; // Default processing time
    }
  }

  DateTime _getNextBusinessTime(DateTime time) {
    // Business hours: 8:00 AM to 5:00 PM, Monday to Friday
    final hour = time.hour;
    final weekday = time.weekday;

    // If it's weekend, move to Monday 8 AM
    if (weekday == DateTime.saturday || weekday == DateTime.sunday) {
      final daysToAdd = weekday == DateTime.saturday ? 2 : 1;
      return DateTime(time.year, time.month, time.day + daysToAdd, 8, 0);
    }

    // If before business hours, start at 8 AM today
    if (hour < 8) {
      return DateTime(time.year, time.month, time.day, 8, 0);
    }

    // If after business hours, start at 8 AM next business day
    if (hour >= 17) {
      final nextDay = weekday == DateTime.friday ? time.add(const Duration(days: 3)) : time.add(const Duration(days: 1));
      return DateTime(nextDay.year, nextDay.month, nextDay.day, 8, 0);
    }

    // During business hours, return current time
    return time;
  }

  DateTime _adjustToBusinessHours(DateTime time) {
    final hour = time.hour;
    final weekday = time.weekday;

    // If it falls on weekend, move to Monday
    if (weekday == DateTime.saturday || weekday == DateTime.sunday) {
      final daysToAdd = weekday == DateTime.saturday ? 2 : 1;
      return DateTime(time.year, time.month, time.day + daysToAdd, 9, 0);
    }

    // If after business hours, move to next business day
    if (hour >= 17) {
      final nextDay = weekday == DateTime.friday ? time.add(const Duration(days: 3)) : time.add(const Duration(days: 1));
      return DateTime(nextDay.year, nextDay.month, nextDay.day, 9, 0);
    }

    // If before business hours, move to 8 AM same day
    if (hour < 8) {
      return DateTime(time.year, time.month, time.day, 8, 0);
    }

    return time;
  }

  void _startTimer() {
    _timer = Timer.periodic(const Duration(seconds: 1), (timer) {
      print('Timer tick - updating current time');
      setState(() {
        _currentTime = DateTime.now();
        // This will trigger a rebuild and update the expected time display
      });
    });
  }

  void _startQueueRefresh() {
    _queueTimer = Timer.periodic(const Duration(seconds: 10), (timer) {
      print('Queue refresh timer tick - calling API');
      // Refresh data from API
      _refreshDataFromAPI();
    });
  }

  Future<void> _refreshDataFromAPI() async {
    print('_refreshDataFromAPI called for referenceId: ${widget.referenceId}');
    try {
      final result = await ApiService.validateReference(widget.referenceId);
      if (result != null && mounted) {
        setState(() {
          final data = result['data'];
          if (result['type'] == 'transaction') {
            _studentName = data['student_name'] ?? _studentName;
            final oldStatus = _status;
            // Use displayStatus if available, otherwise fall back to status
            final newStatus = data['displayStatus'] ?? data['status'] ?? _status;
            _status = newStatus;
            _debugInfo = data['debug_info']; // Store debug info
            _handleStatusChangeNotification(_status); // Add TTS and vibration notification
            _expectedReleaseDate = data['expected_release_date']; // Get from API
            _queueNumber = data['queue_number'] ?? ''; // Get queue number from API

            // Handle multiple documents from API
            if (data['documents'] != null && data['documents'] is List) {
              _documents = List<Map<String, dynamic>>.from(data['documents']);
              // Add queue_number to each document if not present
              for (var i = 0; i < _documents.length; i++) {
                _documents[i]['queue_number'] = _documents[i]['queue_number'] ?? _queueNumber;
              }
            } else if (data['document_name'] != null) {
              _documents = [{'name': data['document_name'], 'status': _status, 'quantity': data['quantity'] ?? 1, 'queue_number': _queueNumber.isNotEmpty ? _queueNumber : 'A${_yourQueueNumber.toString().padLeft(3, '0')}'}];
              _documentName = data['document_name'];
            }

            print('Expected release date from API (transaction): $_expectedReleaseDate'); // Debug
          } else if (result['type'] == 'onsite_request') {
            _studentName = data['full_name'] ?? _studentName;
            final oldStatus = _status;
            // Use displayStatus if available, otherwise fall back to status
            final newStatus = data['displayStatus'] ?? data['status'] ?? _status;
            _status = newStatus;
            _debugInfo = data['debug_info']; // Store debug info
            _handleStatusChangeNotification(_status); // Add TTS and vibration notification
            _expectedReleaseDate = data['expected_release_date']; // Get from API
            _queueNumber = data['queue_number'] ?? ''; // Get queue number from API

            // Handle multiple documents from API
            if (data['documents'] != null && data['documents'] is List) {
              _documents = List<Map<String, dynamic>>.from(data['documents']);
              // Add queue_number to each document if not present
              for (var i = 0; i < _documents.length; i++) {
                _documents[i]['queue_number'] = _documents[i]['queue_number'] ?? _queueNumber;
              }
            } else if (data['document_name'] != null) {
              _documents = [{'name': data['document_name'], 'status': _status, 'quantity': data['quantity'] ?? 1, 'queue_number': _queueNumber.isNotEmpty ? _queueNumber : 'A${_yourQueueNumber.toString().padLeft(3, '0')}'}];
              _documentName = data['document_name'] ?? 'Form 137';
            }

            print('Expected release date from API (onsite): $_expectedReleaseDate'); // Debug
          }

          // Update current processing display
          if (_documents.isNotEmpty) {
            if (_documents.length == 1) {
              final quantity = (_documents[0]['quantity'] ?? 1) as int;
              if (quantity > 1) {
                _currentlyProcessing = "${_documents[0]['name']} (${quantity}x) - $_studentName";
              } else {
                _currentlyProcessing = "${_documents[0]['name']} - $_studentName";
              }
              _documentName = _documents[0]['name'];
            } else {
              final totalQuantity = _documents.fold<int>(0, (sum, doc) => sum + ((doc['quantity'] ?? 1) as int));
              _currentlyProcessing = "${_documents.length} Documents ($totalQuantity total) - $_studentName";
              _documentName = "${_documents.length} Documents";
            }
          }

          // Update queue number based on status
          _updateQueueBasedOnStatus();

          // Set expected release time from API data or recalculate
          _setExpectedReleaseTime();
        });
      } else {
        print('No result from API for reference: ${widget.referenceId}'); // Debug
      }
    } catch (e) {
      print('Failed to refresh data from API: $e');
      print('DEBUG: Current status remains: $_status');
    }
  }

  void _updateQueueBasedOnStatus() {
    print('_updateQueueBasedOnStatus called with status: $_status');
    // Map database status to queue behavior
    switch (_status.toLowerCase()) {
      case 'submitted':
      case 'under review':
      // Just submitted, haven't reached payment yet
        break;
      case 'payment pending':
      case 'pending payment':
      // Waiting for payment, maintain current queue number
        break;
      case 'accepted':
      case 'pending':
      case 'in queue':
      case 'in_queue':
      case 'waiting':
      // Still in queue, maintain current queue number
        break;
      case 'processing':
        _currentNumber = _yourQueueNumber - 1; // Almost your turn
        break;
      case 'your turn!':
      case 'in progress':
        _currentNumber = _yourQueueNumber; // Your turn
        break;
      case 'ready for release':
      case 'ready_for_pickup':
      case 'completed':
      case 'released':
        _currentNumber = _yourQueueNumber + 1; // Past your turn, completed
        break;
      default:
      // Keep existing queue number
        break;
    }

    // Use API data for expected release time or recalculate
    _setExpectedReleaseTime();
    print('_updateQueueBasedOnStatus completed. Current number: $_currentNumber, Your number: $_yourQueueNumber');
  }

  // Initialize Text-to-Speech
  void _initializeTts() async {
    await _flutterTts.setLanguage("en-US");
    await _flutterTts.setSpeechRate(0.5);
    await _flutterTts.setVolume(1.0);
    await _flutterTts.setPitch(1.0);
  }

  // Initialize OneSignal and get player ID
  void _initializeOneSignal() async {
    try {
      // Wait a bit for OneSignal to initialize
      await Future.delayed(const Duration(seconds: 2));

      // Get player ID
      final playerId = OneSignal.User.pushSubscription.id;
      final isConnected = playerId != null && playerId.isNotEmpty;

      setState(() {
        _onesignalPlayerId = playerId;
        _isConnected = isConnected;
      });

      print('OneSignal initialized - Player ID: $playerId, Connected: $isConnected');
    } catch (e) {
      print('Failed to initialize OneSignal: $e');
      setState(() {
        _isConnected = false;
      });
    }
  }

  // Handle status change notifications with TTS and vibration
  void _handleStatusChangeNotification(String newStatus) {
    print('Status change detected: $_previousStatus -> $newStatus');
    bool statusChanged = _previousStatus != newStatus;

    // Check if status is in_queue or ready_for_pickup (handle both underscore and space formats)
    String normalizedStatus = newStatus.toLowerCase().replaceAll('_', ' ');
    print('Normalized status: $normalizedStatus');

    if (normalizedStatus == 'waiting' || normalizedStatus == 'in queue' || normalizedStatus == 'ready for pickup') {
      // Always trigger TTS and vibration when status matches criteria (not just on changes)
      print('Triggering TTS and vibration for status: $normalizedStatus');
      _speakStatusUpdate(newStatus);
      _vibrateDevice();
    }

    _previousStatus = newStatus;
  }

  // Speak status update
  void _speakStatusUpdate(String status) async {
    String message = "";
    String normalizedStatus = status.toLowerCase().replaceAll('_', ' ');

    switch (normalizedStatus) {
      case 'waiting':
        message = "You are waiting in position $_position. Please wait for your turn.";
        break;
      case 'in queue':
        message = "Your request is now in queue. Please wait for your turn.";
        break;
      case 'ready for pickup':
        message = "Your request is ready for pickup. Please proceed to the counter.";
        break;
      default:
        print('TTS: No message for status: $normalizedStatus');
        return; // Don't speak for other statuses
    }

    print('TTS: Speaking message: $message');
    await _flutterTts.speak(message);
  }

  // Vibrate device
  void _vibrateDevice() async {
    print('Vibration: Checking if device has vibrator...');
    bool? hasVibrator = await Vibration.hasVibrator();
    print('Vibration: hasVibrator = $hasVibrator');
    if (hasVibrator == true) {
      print('Vibration: Triggering vibration pattern');
      Vibration.vibrate(pattern: [0, 500, 200, 500]);
    } else {
      print('Vibration: Device does not support vibration');
    }
  }

  @override
  void dispose() {
    _timer?.cancel();
    _queueTimer?.cancel();
    _flutterTts.stop();
    super.dispose();
  }

  String _formatTime(DateTime time) {
    return "${time.hour.toString().padLeft(2, '0')}:${time.minute.toString().padLeft(2, '0')}:${time.second.toString().padLeft(2, '0')}";
  }

  String _formatTime12Hour(DateTime time) {
    int hour = time.hour;
    String period = hour >= 12 ? 'PM' : 'AM';
    if (hour == 0) hour = 12;
    if (hour > 12) hour -= 12;

    return "${hour.toString()}:${time.minute.toString().padLeft(2, '0')} $period";
  }

  String _formatDate(DateTime date) {
    const months = [
      'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
      'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'
    ];
    return "${months[date.month - 1]} ${date.day}, ${date.year}";
  }

  String _formatDateWithDay(DateTime date) {
    const months = [
      'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
      'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'
    ];
    const days = [
      'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'
    ];
    return "${months[date.month - 1]} ${date.day}, ${date.year}\n${days[date.weekday - 1]}";
  }

  String _formatDateWithDayInline(DateTime date) {
    const months = [
      'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
      'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'
    ];
    const days = [
      'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'
    ];
    return "${months[date.month - 1]} ${date.day}, ${date.year} - ${days[date.weekday - 1]}";
  }

  String _formatDateTime(DateTime dateTime) {
    const months = [
      'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
      'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'
    ];
    return "${months[dateTime.month - 1]} ${dateTime.day}, ${dateTime.year}";
  }

  String _getStatusText() {
    final status = _status.toLowerCase();

    String statusText;
    switch (status) {
      case 'processing':
      case 'ready_for_release':
      case 'ready for release':
        statusText = 'Being Processed';
        break;
      case 'pending':
      case 'registrar_approved':
        statusText = 'Pending Review';
        break;
      case 'ready_for_pickup':
      case 'ready for pickup':
        statusText = 'Ready for Pickup';
        break;
      case 'in_queue':
      case 'in queue':
        statusText = 'In Queue';
        break;
      case 'waiting':
        statusText = 'Waiting in Queue';
        break;
      case 'completed':
        statusText = 'Completed';
        break;
      default:
        statusText = _status;
        break;
    }

    return statusText;
  }

  Color _getStatusColor() {
    switch (_status.toLowerCase()) {
      case "your turn!":
      case "in progress":
        return const Color(0xFF4CAF50); // Green
      case "ready for release":
      case "ready_for_release":
      case "ready_for_pickup":
      case "ready for pickup":
      case "completed":
      case "released":
        return const Color(0xFF2E7D32); // Dark Green
      case "processing":
        return const Color(0xFFFF9800); // Orange
      case "accepted":
      case "pending":
      case "registrar_approved":
      case "in queue":
      case "in_queue":
      case "waiting":
        return const Color(0xFF2196F3); // Blue
      case "payment pending":
      case "pending payment":
        return const Color(0xFFE91E63); // Pink/Red for payment
      case "submitted":
      case "under review":
        return const Color(0xFF9C27B0); // Purple
      default:
        return nuGray;
    }
  }

  IconData _getStatusIcon() {
    switch (_status.toLowerCase()) {
      case "your turn!":
      case "in progress":
        return Icons.play_circle;
      case "ready for release":
      case "ready_for_release":
      case "ready_for_pickup":
      case "ready for pickup":
      case "completed":
      case "released":
        return Icons.check_circle;
      case "processing":
        return Icons.refresh;
      case "accepted":
      case "pending":
      case "registrar_approved":
      case "in queue":
      case "in_queue":
      case "waiting":
        return Icons.hourglass_empty;
      case "payment pending":
      case "pending payment":
        return Icons.payment;
      case "submitted":
      case "under review":
        return Icons.visibility;
      default:
        return Icons.info;
    }
  }

  String _getFormattedStatus() {
    switch (_status.toLowerCase()) {
      case "your turn!":
        return "YOUR TURN!";
      case "in progress":
        return "IN PROGRESS";
      case "ready for release":
      case "ready_for_release":
        return "BEING PROCESSED";
      case "ready_for_pickup":
      case "ready for pickup":
        return "READY FOR PICKUP";
      case "completed":
        return "COMPLETED";
      case "processing":
        return "PROCESSING";
      case "accepted":
        return "ACCEPTED";
      case "pending":
        return "PENDING";
      case "registrar_approved":
        return "APPROVED";
      case "in queue":
      case "in_queue":
        return "IN QUEUE";
      case "waiting":
        return "WAITING";
      case "released":
        return "RELEASED";
      case "payment pending":
      case "pending payment":
        return "PAYMENT PENDING";
      case "submitted":
        return "SUBMITTED";
      case "under review":
        return "UNDER REVIEW";
      default:
        return _status.toUpperCase();
    }
  }

  String _getStatusDescription() {
    switch (_status.toLowerCase()) {
      case "your turn!":
      case "in progress":
        return "Your document is currently being processed";
      case "ready for release":
      case "ready_for_release":
        return "Your document is being processed by the registrar";
      case "ready_for_pickup":
      case "ready for pickup":
        return "Your document is ready for pickup at the registrar's office";
      case "completed":
        return "Your document is ready for pickup at the registrar's office";
      case "released":
        return "Your document has been released and collected";
      case "processing":
        return "Your request is being processed by our team";
      case "accepted":
      case "pending":
      case "registrar_approved":
      case "in queue":
      case "in_queue":
        return "Your request is in the queue and will be processed soon";
      case "waiting":
        return "Your request is waiting in the queue";
      case "payment pending":
      case "pending payment":
        return "Payment is required to proceed with your request";
      case "submitted":
      case "under review":
        return "Your request has been submitted and is under review";
      default:
        return "Please wait for further updates on your request";
    }
  }

  bool _isInQueueOrBeyond() {
    final status = _status.toLowerCase();
    return ['accepted', 'pending', 'registrar_approved', 'in queue', 'in_queue', 'waiting', 'processing', 'in progress', 'your turn!', 'ready for release', 'ready_for_release', 'ready_for_pickup', 'completed', 'released'].contains(status);
  }

  bool _isInQueueCompleted() {
    final status = _status.toLowerCase();
    return ['processing', 'in progress', 'your turn!', 'ready for release', 'ready_for_release', 'ready_for_pickup', 'completed', 'released'].contains(status);
  }

  bool _isProcessingOrBeyond() {
    final status = _status.toLowerCase();
    return ['processing', 'in progress', 'your turn!', 'ready for release', 'ready_for_release', 'ready_for_pickup', 'completed', 'released'].contains(status);
  }

  bool _isProcessingCompleted() {
    final status = _status.toLowerCase();
    return ['ready for release', 'ready_for_release', 'ready_for_pickup', 'completed', 'released'].contains(status);
  }

  bool _isCompleted() {
    final status = _status.toLowerCase();
    return ['ready for release', 'ready_for_release', 'ready_for_pickup', 'completed', 'released'].contains(status);
  }

  bool _isAllStepsCompleted() {
    final status = _status.toLowerCase();
    return ['completed'].contains(status);
  }

  String _getCurrentProcessingLabel() {
    final status = _status.toLowerCase();

    String label;
    switch (status) {
      case 'ready for release':
      case 'ready_for_release':
        label = 'Being Processed';
        break;
      case 'ready_for_pickup':
      case 'completed':
      case 'released':
        label = 'Ready for Pickup';
        break;
      case 'processing':
      case 'in progress':
      case 'your turn!':
        label = 'Now Processing';
        break;
      case 'in queue':
      case 'in_queue':
        label = 'In Queue';
        break;
      case 'waiting':
        label = 'Waiting in Queue';
        break;
      case 'accepted':
      case 'pending':
      case 'registrar_approved':
        label = 'Pending Review';
        break;
      default:
        label = 'Status';
        break;
    }

    return label;
  }

  Widget _buildDetailRow(String label, String value) {
    return Row(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        SizedBox(
          width: 100,
          child: Text(
            '$label:',
            style: const TextStyle(
              color: nuGray,
              fontSize: 14,
              fontWeight: FontWeight.w500,
            ),
          ),
        ),
        Expanded(
          child: Text(
            value,
            style: const TextStyle(
              color: Colors.black87,
              fontSize: 14,
              fontWeight: FontWeight.w600,
            ),
          ),
        ),
      ],
    );
  }

  Widget _buildDocumentCard(Map<String, dynamic> document, int index) {
    final docName = document['name'] ?? 'Unknown Document';
    final docStatus = document['status'] ?? _status;
    final quantity = (document['quantity'] ?? 1) as int; // Ensure int type
    final queueNumber = document['queue_number'] ?? (_queueNumber.isNotEmpty ? _queueNumber : 'A${(index + 1).toString().padLeft(3, '0')}');

    return Container(
      margin: const EdgeInsets.only(bottom: 8),
      padding: const EdgeInsets.all(12),
      decoration: BoxDecoration(
        color: _getDocumentStatusColor(docStatus).withOpacity(0.05),
        borderRadius: BorderRadius.circular(12),
        border: Border.all(
          color: _getDocumentStatusColor(docStatus).withOpacity(0.2),
          width: 1,
        ),
      ),
      child: Row(
        children: [
          Container(
            padding: const EdgeInsets.all(6),
            decoration: BoxDecoration(
              color: _getDocumentStatusColor(docStatus).withOpacity(0.1),
              borderRadius: BorderRadius.circular(6),
            ),
            child: Icon(
              _getDocumentIcon(docName),
              color: _getDocumentStatusColor(docStatus),
              size: 16,
            ),
          ),
          const SizedBox(width: 12),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Row(
                  children: [
                    Expanded(
                      child: Text(
                        docName,
                        style: const TextStyle(
                          color: Colors.black87,
                          fontSize: 14,
                          fontWeight: FontWeight.w600,
                        ),
                      ),
                    ),
                    if (quantity > 1) ...[
                      const SizedBox(width: 8),
                      Container(
                        padding: const EdgeInsets.symmetric(horizontal: 6, vertical: 2),
                        decoration: BoxDecoration(
                          color: nuBlue.withOpacity(0.1),
                          borderRadius: BorderRadius.circular(8),
                          border: Border.all(
                            color: nuBlue.withOpacity(0.3),
                            width: 1,
                          ),
                        ),
                        child: Text(
                          'Qty: $quantity',
                          style: TextStyle(
                            color: nuBlue,
                            fontSize: 10,
                            fontWeight: FontWeight.w600,
                          ),
                        ),
                      ),
                    ],
                  ],
                ),
                const SizedBox(height: 2),
                Text(
                  _getFormattedDocumentStatus(docStatus),
                  style: TextStyle(
                    color: _getDocumentStatusColor(docStatus),
                    fontSize: 12,
                    fontWeight: FontWeight.w500,
                  ),
                ),
              ],
            ),
          ),
          Container(
            padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
            decoration: BoxDecoration(
              color: _getDocumentStatusColor(docStatus),
              borderRadius: BorderRadius.circular(12),
            ),
            child: Text(
              '#$queueNumber',
              style: const TextStyle(
                color: Colors.white,
                fontSize: 10,
                fontWeight: FontWeight.w600,
              ),
            ),
          ),
        ],
      ),
    );
  }

  Color _getDocumentStatusColor(String status) {
    switch (status.toLowerCase()) {
      case "your turn!":
      case "in progress":
        return const Color(0xFF4CAF50); // Green
      case "ready for release":
      case "ready_for_release":
        return const Color(0xFFFF9800); // Orange - being processed
      case "ready_for_pickup":
      case "ready for pickup":
      case "completed":
      case "released":
        return const Color(0xFF2E7D32); // Dark Green
      case "processing":
        return const Color(0xFFFF9800); // Orange
      case "accepted":
      case "pending":
      case "registrar_approved":
      case "in queue":
      case "in_queue":
      case "waiting":
        return const Color(0xFF2196F3); // Blue
      case "payment pending":
      case "pending payment":
        return const Color(0xFFE91E63); // Pink/Red for payment
      case "submitted":
      case "under review":
        return const Color(0xFF9C27B0); // Purple
      default:
        return nuGray;
    }
  }

  IconData _getDocumentIcon(String documentName) {
    final docLower = documentName.toLowerCase();

    if (docLower.contains('transcript')) {
      return Icons.school;
    } else if (docLower.contains('diploma')) {
      return Icons.workspace_premium;
    } else if (docLower.contains('certificate')) {
      return Icons.verified;
    } else if (docLower.contains('form')) {
      return Icons.description;
    } else if (docLower.contains('clearance')) {
      return Icons.check_circle_outline;
    } else if (docLower.contains('enrollment')) {
      return Icons.how_to_reg;
    } else {
      return Icons.insert_drive_file;
    }
  }

  String _getFormattedDocumentStatus(String status) {
    switch (status.toLowerCase()) {
      case "your turn!":
        return "YOUR TURN!";
      case "in progress":
        return "IN PROGRESS";
      case "ready for release":
      case "ready_for_release":
        return "BEING PROCESSED";
      case "ready_for_pickup":
      case "ready for pickup":
        return "READY FOR PICKUP";
      case "completed":
        return "COMPLETED";
      case "released":
        return "RELEASED";
      case "processing":
        return "PROCESSING";
      case "accepted":
        return "ACCEPTED";
      case "pending":
        return "PENDING";
      case "registrar_approved":
        return "APPROVED";
      case "in queue":
      case "in_queue":
        return "IN QUEUE";
      case "waiting":
        return "WAITING";
      case "payment pending":
      case "pending payment":
        return "PAYMENT PENDING";
      case "submitted":
        return "SUBMITTED";
      case "under review":
        return "UNDER REVIEW";
      default:
        return status.toUpperCase();
    }
  }

  String _formatRequestDate(String dateString) {
    try {
      final date = DateTime.parse(dateString);
      return _formatDate(date);
    } catch (e) {
      return dateString;
    }
  }

  String _getExpectedReleaseText() {
    if (_expectedReleaseTime == null) {
      return 'Not Available';
    }

    final now = DateTime.now();
    final difference = _expectedReleaseTime!.difference(now);

    if (difference.isNegative || difference.inMinutes < 1) {
      switch (_status.toLowerCase()) {
        case 'ready for release':
        case 'completed':
          return 'Ready for Pickup';
        default:
          return 'Processing Complete';
      }
    }

    // If we have data from API/database, show more professional display
    if (_expectedReleaseDate != null && _expectedReleaseDate!.isNotEmpty) {
      if (difference.inHours >= 24) {
        final days = difference.inDays;
        return '${days} day${days > 1 ? 's' : ''} remaining';
      } else if (difference.inHours > 0) {
        final hours = difference.inHours;
        return '${hours} hour${hours > 1 ? 's' : ''} remaining';
      } else {
        final minutes = difference.inMinutes;
        return '${minutes} minute${minutes > 1 ? 's' : ''} remaining';
      }
    }

    // Fallback to calculated display
    if (difference.inHours >= 24) {
      final days = difference.inDays;
      final hours = difference.inHours % 24;
      if (hours > 0) {
        return '${days}d ${hours}h';
      } else {
        return '${days} day${days > 1 ? 's' : ''}';
      }
    } else if (difference.inHours > 0) {
      final hours = difference.inHours;
      final minutes = difference.inMinutes % 60;
      if (minutes > 0) {
        return '${hours}h ${minutes}m';
      } else {
        return '${hours} hour${hours > 1 ? 's' : ''}';
      }
    } else {
      final minutes = difference.inMinutes;
      return '${minutes} minute${minutes > 1 ? 's' : ''}';
    }
  }

  String _getExpectedReleaseDateTimeText() {
    if (_expectedReleaseTime == null) {
      return 'Expected release date not available';
    }

    final now = DateTime.now();
    final difference = _expectedReleaseTime!.difference(now);

    if (difference.isNegative || difference.inMinutes < 1) {
      switch (_status.toLowerCase()) {
        case 'ready for release':
        case 'completed':
          return 'Your document is ready for pickup';
        default:
          return 'Processing is complete - pickup available';
      }
    }

    // If we have official release date from database, show specific format
    if (_expectedReleaseDate != null && _expectedReleaseDate!.isNotEmpty) {
      final today = DateTime(now.year, now.month, now.day);
      final expectedDate = DateTime(_expectedReleaseTime!.year, _expectedReleaseTime!.month, _expectedReleaseTime!.day);

      if (expectedDate == today) {
        final timeStr = _formatTime12Hour(_expectedReleaseTime!);
        return 'Today at $timeStr';
      } else if (expectedDate == today.add(const Duration(days: 1))) {
        final timeStr = _formatTime12Hour(_expectedReleaseTime!);
        return 'Tomorrow at $timeStr';
      } else {
        return _formatDateWithDayInline(_expectedReleaseTime!);
      }
    }

    // Fallback for calculated times
    final today = DateTime(now.year, now.month, now.day);
    final expectedDate = DateTime(_expectedReleaseTime!.year, _expectedReleaseTime!.month, _expectedReleaseTime!.day);

    String datePrefix;
    if (expectedDate == today) {
      datePrefix = 'Today';
    } else if (expectedDate == today.add(const Duration(days: 1))) {
      datePrefix = 'Tomorrow';
    } else {
      return _formatDateWithDayInline(_expectedReleaseTime!);
    }

    final timeStr = _formatTime12Hour(_expectedReleaseTime!);
    return '$datePrefix at $timeStr (estimated)';
  }

  String _getExpectedReleaseDateOnly() {
    if (_expectedReleaseTime == null) {
      return 'Not Available';
    }

    final now = DateTime.now();
    final difference = _expectedReleaseTime!.difference(now);

    if (difference.isNegative || difference.inMinutes < 1) {
      return 'Available Now';
    }

    // If we have official data from registrar, display it prominently
    if (_expectedReleaseDate != null && _expectedReleaseDate!.isNotEmpty) {
      return _formatDateWithDay(_expectedReleaseTime!);
    }

    // For calculated dates, add "Estimated" indicator
    return '${_formatDateWithDay(_expectedReleaseTime!)}\n(Estimated)';
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: _backgroundColor,
      drawer: _buildNavigationDrawer(),
      appBar: AppBar(
        title: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            const Text(
              'Document Queue Status',
              style: TextStyle(
                color: Colors.white,
                fontWeight: FontWeight.w700,
                fontSize: 18,
                letterSpacing: 0.5,
              ),
            ),
            Text(
              'Reference: ${widget.referenceId}',
              style: TextStyle(
                color: Colors.white.withOpacity(0.9),
                fontWeight: FontWeight.w400,
                fontSize: 12,
                letterSpacing: 0.3,
              ),
            ),
          ],
        ),
        backgroundColor: nuBlue,
        elevation: 4,
        shadowColor: nuBlue.withOpacity(0.3),
        leading: Builder(
          builder: (context) => IconButton(
            icon: Container(
              padding: const EdgeInsets.all(6),
              decoration: BoxDecoration(
                color: Colors.white.withOpacity(0.1),
                borderRadius: BorderRadius.circular(8),
              ),
              child: const Icon(
                Icons.menu,
                color: Colors.white,
                size: 20,
              ),
            ),
            onPressed: () => Scaffold.of(context).openDrawer(),
            tooltip: 'Open Menu',
          ),
        ),
        actions: [
          // Debug TTS/Vibration Test Button
          Container(
            margin: const EdgeInsets.only(right: 8),
            child: IconButton(
              onPressed: () {
                print('DEBUG: Manual TTS/Vibration test triggered');
                _speakStatusUpdate('in queue');
                _vibrateDevice();
              },
              icon: Container(
                padding: const EdgeInsets.all(8),
                decoration: BoxDecoration(
                  color: Colors.white.withOpacity(0.1),
                  borderRadius: BorderRadius.circular(8),
                ),
                child: const Icon(
                  Icons.volume_up,
                  color: Colors.white,
                  size: 20,
                ),
              ),
              tooltip: 'Test TTS & Vibration',
            ),
          ),

          // Quick Refresh Action
          Container(
            margin: const EdgeInsets.only(right: 8),
            child: IconButton(
              onPressed: _handleRefresh,
              icon: Container(
                padding: const EdgeInsets.all(8),
                decoration: BoxDecoration(
                  color: Colors.white.withOpacity(0.1),
                  borderRadius: BorderRadius.circular(8),
                ),
                child: const Icon(
                  Icons.refresh,
                  color: Colors.white,
                  size: 20,
                ),
              ),
              tooltip: 'Update Queue',
            ),
          ),
          Container(
            padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
            decoration: BoxDecoration(
              color: Colors.white.withOpacity(0.1),
              borderRadius: BorderRadius.circular(8),
            ),
            child: Column(
              mainAxisAlignment: MainAxisAlignment.center,
              crossAxisAlignment: CrossAxisAlignment.end,
              children: [
                Row(
                  mainAxisSize: MainAxisSize.min,
                  children: [
                    Icon(
                      Icons.access_time,
                      color: Colors.white.withOpacity(0.9),
                      size: 14,
                    ),
                    const SizedBox(width: 4),
                    Text(
                      _formatTime12Hour(_currentTime),
                      style: const TextStyle(
                        color: Colors.white,
                        fontSize: 16,
                        fontWeight: FontWeight.w600,
                        letterSpacing: 0.5,
                      ),
                    ),
                  ],
                ),
                Text(
                  _formatDate(_currentTime),
                  style: TextStyle(
                    color: Colors.white.withOpacity(0.8),
                    fontSize: 11,
                    fontWeight: FontWeight.w400,
                    letterSpacing: 0.3,
                  ),
                ),
              ],
            ),
          ),
        ],
      ),
      body: SafeArea(
        child: Column(
          children: [
            Expanded(
              child: SingleChildScrollView(
                padding: const EdgeInsets.all(16.0),
                child: Column(
                  children: [
                    // Debug Status Information
                    Container(
                      width: double.infinity,
                      padding: const EdgeInsets.all(12),
                      margin: const EdgeInsets.only(bottom: 16),
                      decoration: BoxDecoration(
                        color: Colors.yellow.withOpacity(0.1),
                        borderRadius: BorderRadius.circular(8),
                        border: Border.all(color: Colors.yellow, width: 1),
                      ),
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          const Text(
                            'DEBUG STATUS INFO',
                            style: TextStyle(
                              fontWeight: FontWeight.bold,
                              fontSize: 12,
                              color: Colors.orange,
                            ),
                          ),
                          const SizedBox(height: 8),
                          Text('Reference: ${widget.referenceId}', style: const TextStyle(fontSize: 12)),
                          Text('Current Status: $_status', style: const TextStyle(fontSize: 12)),
                          Text('Queue Number: $_queueNumber', style: const TextStyle(fontSize: 12)),
                          Text('Position: $_position', style: const TextStyle(fontSize: 12)),
                          Text('Status Text: ${_getStatusText()}', style: const TextStyle(fontSize: 12)),
                          Text('Processing Label: ${_getCurrentProcessingLabel()}', style: const TextStyle(fontSize: 12)),
                          if (_debugInfo != null) ...[
                            const SizedBox(height: 8),
                            const Text('API Debug:', style: TextStyle(fontSize: 12, fontWeight: FontWeight.bold)),
                            Text('Raw Status: ${_debugInfo!['raw_status']}', style: const TextStyle(fontSize: 10)),
                            Text('Display Status: ${_debugInfo!['display_status']}', style: const TextStyle(fontSize: 10)),
                            Text('Registrar ID: ${_debugInfo!['assigned_registrar_id']}', style: const TextStyle(fontSize: 10)),
                            Text('Total Requests: ${_debugInfo!['total_requests_for_registrar']}', style: const TextStyle(fontSize: 10)),
                            Text('First Request ID: ${_debugInfo!['first_request_id']}', style: const TextStyle(fontSize: 10)),
                            Text('Current Request ID: ${_debugInfo!['current_request_id']}', style: const TextStyle(fontSize: 10)),
                            Text('Is First: ${_debugInfo!['is_first']}', style: const TextStyle(fontSize: 10)),
                          ],
                        ],
                      ),
                    ),

                    // Currently Processing Card
                    Container(
                      width: double.infinity,
                      padding: const EdgeInsets.all(20),
                      decoration: BoxDecoration(
                        gradient: LinearGradient(
                          colors: [nuBlue, nuBlue.withOpacity(0.8)],
                          begin: Alignment.topLeft,
                          end: Alignment.bottomRight,
                        ),
                        borderRadius: BorderRadius.circular(20),
                        boxShadow: [
                          BoxShadow(
                            color: nuBlue.withOpacity(0.3),
                            blurRadius: 15,
                            offset: const Offset(0, 8),
                          ),
                        ],
                      ),
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Row(
                            children: [
                              Container(
                                padding: const EdgeInsets.all(8),
                                decoration: BoxDecoration(
                                  color: Colors.white.withOpacity(0.2),
                                  borderRadius: BorderRadius.circular(8),
                                ),
                                child: const Icon(
                                  Icons.play_arrow,
                                  color: Colors.white,
                                  size: 20,
                                ),
                              ),
                              const SizedBox(width: 12),
                              Text(
                                _getCurrentProcessingLabel(),
                                style: const TextStyle(
                                  color: Colors.white70,
                                  fontSize: 14,
                                  fontWeight: FontWeight.w500,
                                ),
                              ),
                            ],
                          ),
                          const SizedBox(height: 12),
                          Text(
                            _currentlyProcessing,
                            style: const TextStyle(
                              color: Colors.white,
                              fontSize: 18,
                              fontWeight: FontWeight.bold,
                            ),
                          ),
                          const SizedBox(height: 8),
                          Container(
                            padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
                            decoration: BoxDecoration(
                              color: Colors.white.withOpacity(0.2),
                              borderRadius: BorderRadius.circular(20),
                            ),
                            child: Text(
                              'Number: $_currentNumber',
                              style: const TextStyle(
                                color: Colors.white,
                                fontSize: 12,
                                fontWeight: FontWeight.w500,
                              ),
                            ),
                          ),
                        ],
                      ),
                    ),
                    const SizedBox(height: 16),

                    // Your Queue Number Card - Enhanced Design
                    Container(
                      width: double.infinity,
                      padding: const EdgeInsets.all(28),
                      decoration: BoxDecoration(
                        color: Colors.white,
                        borderRadius: BorderRadius.circular(24),
                        boxShadow: [
                          BoxShadow(
                            color: Colors.black.withOpacity(0.08),
                            blurRadius: 25,
                            offset: const Offset(0, 10),
                          ),
                          BoxShadow(
                            color: Colors.black.withOpacity(0.05),
                            blurRadius: 10,
                            offset: const Offset(0, 4),
                          ),
                        ],
                      ),
                      child: Column(
                        children: [
                          Row(
                            mainAxisAlignment: MainAxisAlignment.center,
                            children: [
                              Container(
                                padding: const EdgeInsets.all(8),
                                decoration: BoxDecoration(
                                  color: nuBlue.withOpacity(0.1),
                                  borderRadius: BorderRadius.circular(8),
                                ),
                                child: Icon(
                                  Icons.confirmation_number,
                                  color: nuBlue,
                                  size: 20,
                                ),
                              ),
                              const SizedBox(width: 12),
                              Text(
                                (_queueNumber.isNotEmpty ? 'Your Queue Number' : (_status.toLowerCase() == 'waiting' ? 'Your Position in Queue' : 'Your Queue Number')),
                                style: Theme.of(context).textTheme.titleMedium?.copyWith(
                                  color: nuBlue,
                                  fontWeight: FontWeight.w600,
                                  letterSpacing: 0.5,
                                ),
                              ),
                            ],
                          ),
                          const SizedBox(height: 8),
                          Text(
                            'Reference ID: ${widget.referenceId}',
                            style: Theme.of(context).textTheme.bodyMedium?.copyWith(
                              color: nuGray,
                              fontWeight: FontWeight.w500,
                              fontSize: 13,
                              letterSpacing: 0.3,
                            ),
                          ),
                          const SizedBox(height: 20),
                          Center(
                            child: Container(
                              width: 120,
                              height: 120,
                              decoration: BoxDecoration(
                                gradient: LinearGradient(
                                  colors: [
                                    nuYellow,
                                    nuYellow.withOpacity(0.9),
                                    nuYellow.withOpacity(0.8)
                                  ],
                                  begin: Alignment.topLeft,
                                  end: Alignment.bottomRight,
                                ),
                                shape: BoxShape.circle,
                                boxShadow: [
                                  BoxShadow(
                                    color: nuYellow.withOpacity(0.4),
                                    blurRadius: 25,
                                    offset: const Offset(0, 10),
                                  ),
                                  BoxShadow(
                                    color: nuYellow.withOpacity(0.2),
                                    blurRadius: 15,
                                    offset: const Offset(0, 5),
                                  ),
                                ],
                              ),
                              child: Center(
                                child: Text(
                                  (_queueNumber.isNotEmpty ? _queueNumber : (_status.toLowerCase() == 'waiting' ? '$_position' : '$_yourQueueNumber')),
                                  style: const TextStyle(
                                    fontSize: 48,
                                    fontWeight: FontWeight.w900,
                                    color: nuBlue,
                                    letterSpacing: 0.5,
                                  ),
                                ),
                              ),
                            ),
                          ),
                          const SizedBox(height: 20),

                          // Show "in queue" text for waiting status
                          if (_status.toLowerCase() == 'waiting')
                            Center(
                              child: Text(
                                'in queue',
                                style: TextStyle(
                                  fontSize: 16,
                                  color: nuGray,
                                  fontWeight: FontWeight.w500,
                                  letterSpacing: 0.5,
                                ),
                              ),
                            ),
                          if (_status.toLowerCase() == 'waiting')
                            const SizedBox(height: 12),

                          // Estimated Wait Time - moved here from Account Information
                          Container(
                            padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 12),
                            decoration: BoxDecoration(
                              color: nuBlue.withOpacity(0.08),
                              borderRadius: BorderRadius.circular(16),
                              border: Border.all(
                                color: nuBlue.withOpacity(0.2),
                                width: 1,
                              ),
                            ),
                            child: Row(
                              children: [
                                Container(
                                  padding: const EdgeInsets.all(6),
                                  decoration: BoxDecoration(
                                    color: nuBlue.withOpacity(0.1),
                                    borderRadius: BorderRadius.circular(6),
                                  ),
                                  child: Icon(
                                    Icons.schedule,
                                    color: nuBlue,
                                    size: 16,
                                  ),
                                ),
                                const SizedBox(width: 10),
                                Expanded(
                                  child: Column(
                                    crossAxisAlignment: CrossAxisAlignment.start,
                                    mainAxisSize: MainAxisSize.min,
                                    children: [
                                      Text(
                                        'Estimated Wait Time',
                                        style: TextStyle(
                                          fontSize: 12,
                                          color: nuGray,
                                          fontWeight: FontWeight.w500,
                                        ),
                                        overflow: TextOverflow.ellipsis,
                                        maxLines: 1,
                                      ),
                                      const SizedBox(height: 2),
                                      Text(
                                        _getEstimatedWaitTimeText(),
                                        style: const TextStyle(
                                          fontSize: 14,
                                          fontWeight: FontWeight.bold,
                                          color: Colors.black87,
                                        ),
                                        overflow: TextOverflow.ellipsis,
                                        maxLines: 1,
                                      ),
                                    ],
                                  ),
                                ),
                              ],
                            ),
                          ),
                          const SizedBox(height: 16),
                          // Professional Status Display
                          Container(
                            width: double.infinity,
                            padding: const EdgeInsets.all(20),
                            decoration: BoxDecoration(
                              color: _getStatusColor().withOpacity(0.05),
                              borderRadius: BorderRadius.circular(16),
                              border: Border.all(
                                color: _getStatusColor().withOpacity(0.2),
                                width: 2,
                              ),
                              boxShadow: [
                                BoxShadow(
                                  color: _getStatusColor().withOpacity(0.1),
                                  blurRadius: 12,
                                  offset: const Offset(0, 4),
                                ),
                              ],
                            ),
                            child: Column(
                              children: [
                                Row(
                                  mainAxisAlignment: MainAxisAlignment.center,
                                  children: [
                                    Container(
                                      padding: const EdgeInsets.all(8),
                                      decoration: BoxDecoration(
                                        color: _getStatusColor().withOpacity(0.1),
                                        borderRadius: BorderRadius.circular(8),
                                      ),
                                      child: Icon(
                                        _getStatusIcon(),
                                        color: _getStatusColor(),
                                        size: 20,
                                      ),
                                    ),
                                    const SizedBox(width: 12),
                                    Text(
                                      'Current Status',
                                      style: TextStyle(
                                        color: _getStatusColor(),
                                        fontWeight: FontWeight.w600,
                                        fontSize: 15,
                                        letterSpacing: 0.3,
                                      ),
                                    ),
                                  ],
                                ),
                                const SizedBox(height: 16),
                                Container(
                                  padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 12),
                                  decoration: BoxDecoration(
                                    color: _getStatusColor(),
                                    borderRadius: BorderRadius.circular(25),
                                    boxShadow: [
                                      BoxShadow(
                                        color: _getStatusColor().withOpacity(0.3),
                                        blurRadius: 8,
                                        offset: const Offset(0, 3),
                                      ),
                                    ],
                                  ),
                                  child: Row(
                                    mainAxisSize: MainAxisSize.min,
                                    children: [
                                      Container(
                                        width: 8,
                                        height: 8,
                                        decoration: const BoxDecoration(
                                          color: Colors.white,
                                          shape: BoxShape.circle,
                                        ),
                                      ),
                                      const SizedBox(width: 10),
                                      Text(
                                        _getFormattedStatus(),
                                        style: const TextStyle(
                                          color: Colors.white,
                                          fontWeight: FontWeight.bold,
                                          fontSize: 16,
                                          letterSpacing: 0.5,
                                        ),
                                      ),
                                    ],
                                  ),
                                ),
                                const SizedBox(height: 12),
                                Text(
                                  _getStatusDescription(),
                                  style: TextStyle(
                                    color: _getStatusColor(),
                                    fontWeight: FontWeight.w500,
                                    fontSize: 13,
                                    fontStyle: FontStyle.italic,
                                  ),
                                  textAlign: TextAlign.center,
                                ),
                              ],
                            ),
                          ),

                          // Expected Release Time - Enhanced Professional Design
                          Container(
                            width: double.infinity,
                            margin: const EdgeInsets.only(top: 20),
                            decoration: BoxDecoration(
                              gradient: LinearGradient(
                                colors: [
                                  Color(0xFF1A4B8C),
                                  Color(0xFF2E5BA3),
                                  Color(0xFF3D6BB5)
                                ],
                                begin: Alignment.topLeft,
                                end: Alignment.bottomRight,
                              ),
                              borderRadius: BorderRadius.circular(20),
                              boxShadow: [
                                BoxShadow(
                                  color: Color(0xFF1A4B8C).withOpacity(0.4),
                                  blurRadius: 20,
                                  offset: const Offset(0, 8),
                                ),
                              ],
                            ),
                            child: Container(
                              padding: const EdgeInsets.all(24),
                              child: Column(
                                children: [
                                  // Header with Icon
                                  Row(
                                    children: [
                                      Container(
                                        padding: const EdgeInsets.all(10),
                                        decoration: BoxDecoration(
                                          color: Colors.white.withOpacity(0.15),
                                          borderRadius: BorderRadius.circular(12),
                                        ),
                                        child: Icon(
                                          _expectedReleaseDate != null && _expectedReleaseDate!.isNotEmpty
                                              ? Icons.verified
                                              : Icons.schedule,
                                          color: Colors.white,
                                          size: 24,
                                        ),
                                      ),
                                      const SizedBox(width: 12),
                                      Expanded(
                                        child: Column(
                                          crossAxisAlignment: CrossAxisAlignment.start,
                                          children: [
                                            Text(
                                              'Expected Document Release',
                                              style: const TextStyle(
                                                color: Colors.white,
                                                fontWeight: FontWeight.w700,
                                                fontSize: 17,
                                                letterSpacing: 0.4,
                                              ),
                                              overflow: TextOverflow.ellipsis,
                                              maxLines: 2,
                                            ),
                                            if (_expectedReleaseDate != null && _expectedReleaseDate!.isNotEmpty)
                                              Text(
                                                'Official Date',
                                                style: TextStyle(
                                                  color: Colors.white.withOpacity(0.8),
                                                  fontWeight: FontWeight.w500,
                                                  fontSize: 12,
                                                  letterSpacing: 0.3,
                                                ),
                                                overflow: TextOverflow.ellipsis,
                                              ),
                                          ],
                                        ),
                                      ),
                                    ],
                                  ),
                                  const SizedBox(height: 24),

                                  // Main Release Date Display
                                  Container(
                                    width: double.infinity,
                                    padding: const EdgeInsets.all(24),
                                    decoration: BoxDecoration(
                                      color: Colors.white,
                                      borderRadius: BorderRadius.circular(16),
                                      boxShadow: [
                                        BoxShadow(
                                          color: Colors.black.withOpacity(0.1),
                                          blurRadius: 12,
                                          offset: const Offset(0, 4),
                                        ),
                                      ],
                                    ),
                                    child: Column(
                                      children: [
                                        // Expected Release Date - Large Display
                                        Container(
                                          padding: const EdgeInsets.all(20),
                                          decoration: BoxDecoration(
                                            color: nuYellow.withOpacity(0.1),
                                            borderRadius: BorderRadius.circular(12),
                                            border: Border.all(
                                              color: nuYellow.withOpacity(0.3),
                                              width: 2,
                                            ),
                                          ),
                                          child: Column(
                                            children: [
                                              const SizedBox(height: 16),
                                              Text(
                                                _getExpectedReleaseDateOnly(),
                                                style: TextStyle(
                                                  color: nuBlue,
                                                  fontWeight: FontWeight.w900,
                                                  fontSize: 26,
                                                  letterSpacing: 0.4,
                                                  height: 1.2,
                                                ),
                                                textAlign: TextAlign.center,
                                                overflow: TextOverflow.visible,
                                                softWrap: true,
                                              ),
                                            ],
                                          ),
                                        ),
                                      ],
                                    ),
                                  ),

                                  // Disclaimer
                                  const SizedBox(height: 16),
                                  Row(
                                    children: [
                                      Icon(
                                        Icons.info_outline,
                                        color: Colors.white.withOpacity(0.7),
                                        size: 14,
                                      ),
                                      const SizedBox(width: 8),
                                      Expanded(
                                        child: Text(
                                          _expectedReleaseDate != null && _expectedReleaseDate!.isNotEmpty
                                              ? 'Official release date from the registrar\'s office'
                                              : 'Estimated time based on current queue status and document processing requirements',
                                          style: TextStyle(
                                            color: Colors.white.withOpacity(0.8),
                                            fontSize: 11,
                                            fontStyle: FontStyle.italic,
                                            letterSpacing: 0.2,
                                          ),
                                        ),
                                      ),
                                    ],
                                  ),
                                ],
                              ),
                            ),
                          ),
                        ],
                      ),
                    ),
                    const SizedBox(height: 16),

                    // Student Information Card
                    if (widget.referenceData != null) ...[
                      Container(
                        width: double.infinity,
                        padding: const EdgeInsets.all(16),
                        decoration: BoxDecoration(
                          color: Colors.white,
                          borderRadius: BorderRadius.circular(16),
                          boxShadow: [
                            BoxShadow(
                              color: Colors.black.withOpacity(0.05),
                              blurRadius: 10,
                              offset: const Offset(0, 2),
                            ),
                          ],
                        ),
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Row(
                              children: [
                                Icon(
                                  widget.referenceType == 'transaction'
                                      ? Icons.person
                                      : Icons.person_outline,
                                  color: nuBlue,
                                  size: 20,
                                ),
                                const SizedBox(width: 8),
                                Text(
                                  'Request Details',
                                  style: Theme.of(context).textTheme.titleMedium?.copyWith(
                                    color: nuBlue,
                                    fontWeight: FontWeight.w600,
                                  ),
                                ),
                              ],
                            ),
                            const SizedBox(height: 12),
                            _buildDetailRow('Student Name', _studentName),
                            const SizedBox(height: 8),
                            if (widget.referenceData!['student_id'] != null) ...[
                              _buildDetailRow('Student ID', widget.referenceData!['student_id'].toString()),
                              const SizedBox(height: 8),
                            ],
                            if (widget.referenceData!['course'] != null) ...[
                              _buildDetailRow('Course', widget.referenceData!['course']),
                              const SizedBox(height: 8),
                            ],
                            if (widget.referenceData!['requested_at'] != null) ...[
                              _buildDetailRow('Requested', _formatRequestDate(widget.referenceData!['requested_at'])),
                              const SizedBox(height: 12),
                            ],

                            // Documents Section
                            Row(
                              children: [
                                Icon(
                                  Icons.folder_open,
                                  color: nuBlue,
                                  size: 18,
                                ),
                                const SizedBox(width: 8),
                                Text(
                                  _documents.length == 1 ? 'Document' : 'Documents (${_documents.length})',
                                  style: TextStyle(
                                    color: nuBlue,
                                    fontWeight: FontWeight.w600,
                                    fontSize: 15,
                                  ),
                                ),
                              ],
                            ),
                            const SizedBox(height: 12),

                            // Display all documents
                            ...List.generate(_documents.length, (index) {
                              return _buildDocumentCard(_documents[index], index);
                            }),
                          ],
                        ),
                      ),
                      const SizedBox(height: 16),
                    ],
                  ],
                ),
              ),
            ),

            // Bottom padding for safe area
            const SizedBox(height: 16),
          ],
        ),
      ),
    );
  }

  Widget _buildAccountInfoRow(String label, String value, IconData icon) {
    return Row(
      children: [
        Container(
          padding: const EdgeInsets.all(4),
          decoration: BoxDecoration(
            color: nuBlue.withOpacity(0.1),
            borderRadius: BorderRadius.circular(4),
          ),
          child: Icon(
            icon,
            color: nuBlue,
            size: 16,
          ),
        ),
        const SizedBox(width: 8),
        Text(
          label,
          style: const TextStyle(
            fontSize: 14,
            fontWeight: FontWeight.w600,
            color: nuGray,
          ),
        ),
        const SizedBox(width: 8),
        Expanded(
          child: Text(
            value,
            style: const TextStyle(
              fontSize: 14,
              fontWeight: FontWeight.bold,
              color: Colors.black87,
            ),
          ),
        ),
      ],
    );
  }

  Widget _buildMenuOption({
    required IconData icon,
    required String title,
    required String subtitle,
    required Color color,
    required VoidCallback onTap,
    bool isDestructive = false,
  }) {
    return Material(
      color: Colors.transparent,
      child: InkWell(
        onTap: onTap,
        borderRadius: BorderRadius.circular(12),
        child: Container(
          padding: const EdgeInsets.all(16),
          decoration: BoxDecoration(
            color: color.withOpacity(0.05),
            borderRadius: BorderRadius.circular(12),
            border: Border.all(
              color: color.withOpacity(0.1),
              width: 1,
            ),
          ),
          child: Row(
            children: [
              Container(
                padding: const EdgeInsets.all(10),
                decoration: BoxDecoration(
                  color: color.withOpacity(0.1),
                  borderRadius: BorderRadius.circular(10),
                ),
                child: Icon(
                  icon,
                  color: color,
                  size: 20,
                ),
              ),
              const SizedBox(width: 16),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      title,
                      style: TextStyle(
                        fontSize: 16,
                        fontWeight: FontWeight.w600,
                        color: isDestructive ? color : Colors.black87,
                      ),
                    ),
                    const SizedBox(height: 2),
                    Text(
                      subtitle,
                      style: TextStyle(
                        fontSize: 13,
                        color: isDestructive ? color.withOpacity(0.8) : Colors.grey[600],
                        fontWeight: FontWeight.w400,
                      ),
                    ),
                  ],
                ),
              ),
              Icon(
                Icons.arrow_forward_ios,
                color: color.withOpacity(0.6),
                size: 16,
              ),
            ],
          ),
        ),
      ),
    );
  }

  // Navigation Drawer
  Widget _buildNavigationDrawer() {
    return Drawer(
      child: SafeArea(
        child: Container(
          color: _isDarkMode ? const Color(0xFF1E1E1E) : Colors.white,
          child: Column(
            children: [
              // Drawer Header with NU Branding
              Container(
                height: 140,
                width: double.infinity,
                decoration: BoxDecoration(
                  gradient: LinearGradient(
                    colors: [nuBlue, nuBlue.withOpacity(0.8)],
                    begin: Alignment.topLeft,
                    end: Alignment.bottomRight,
                  ),
                ),
                child: Padding(
                  padding: const EdgeInsets.all(12),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      // NU Logo placeholder
                      Container(
                        width: 45,
                        height: 45,
                        decoration: BoxDecoration(
                          color: Colors.white,
                          borderRadius: BorderRadius.circular(10),
                          boxShadow: [
                            BoxShadow(
                              color: Colors.black.withOpacity(0.1),
                              blurRadius: 6,
                              offset: const Offset(0, 3),
                            ),
                          ],
                        ),
                        child: Center(
                          child: Text(
                            'NU',
                            style: TextStyle(
                              color: nuBlue,
                              fontSize: 18,
                              fontWeight: FontWeight.w900,
                            ),
                          ),
                        ),
                      ),
                      const SizedBox(height: 8),
                      const Text(
                        'National University Lipa City',
                        style: TextStyle(
                          color: Colors.white,
                          fontSize: 14,
                          fontWeight: FontWeight.w700,
                        ),
                      ),
                      const Text(
                        'Registrar\'s Office',
                        style: TextStyle(
                          color: Colors.white70,
                          fontSize: 12,
                          fontWeight: FontWeight.w500,
                        ),
                      ),
                      const Spacer(),
                      Container(
                        padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 3),
                        decoration: BoxDecoration(
                          color: Colors.white.withOpacity(0.2),
                          borderRadius: BorderRadius.circular(12),
                        ),
                        child: Text(
                          'Queue System v1.0',
                          style: TextStyle(
                            color: Colors.white.withOpacity(0.9),
                            fontSize: 9,
                            fontWeight: FontWeight.w500,
                          ),
                        ),
                      ),
                    ],
                  ),
                ),
              ),

              // Navigation Menu Items
              Expanded(
                child: ListView(
                  padding: const EdgeInsets.symmetric(vertical: 4),
                  children: [
                    _buildDrawerItem(
                      icon: Icons.help_outline,
                      title: 'Support Center',
                      subtitle: 'Get help and assistance',
                      onTap: () {
                        Navigator.of(context).pop();
                        _showHelpDialog();
                      },
                    ),

                    _buildDrawerItem(
                      icon: Icons.settings_outlined,
                      title: 'App Settings',
                      subtitle: 'Preferences and configuration',
                      onTap: () {
                        Navigator.of(context).pop();
                        _showAppSettings();
                      },
                    ),

                    _buildDrawerItem(
                      icon: Icons.info_outline,
                      title: 'About',
                      subtitle: 'App information and version',
                      onTap: () {
                        Navigator.of(context).pop();
                        _showAboutDialog();
                      },
                    ),
                  ],
                ),
              ),

              // Logout Section
              Container(
                decoration: BoxDecoration(
                  border: Border(
                    top: BorderSide(color: Colors.grey[200]!, width: 1),
                  ),
                ),
                child: _buildDrawerItem(
                  icon: Icons.logout,
                  title: 'Logout',
                  subtitle: 'Sign out of your session',
                  isDestructive: true,
                  onTap: () {
                    Navigator.of(context).pop();
                    _showLogoutDialog();
                  },
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }

  // Drawer Item Builder
  Widget _buildDrawerItem({
    required IconData icon,
    required String title,
    required String subtitle,
    required VoidCallback onTap,
    bool isSelected = false,
    bool isDestructive = false,
  }) {
    final baseColor = _isDarkMode ? Colors.white : Colors.grey[700]!;
    final color = isDestructive ? Colors.red : (isSelected ? nuBlue : baseColor);
    final backgroundColor = isSelected ? nuBlue.withOpacity(0.08) : Colors.transparent;

    return Container(
      margin: const EdgeInsets.symmetric(horizontal: 8, vertical: 1),
      decoration: BoxDecoration(
        color: backgroundColor,
        borderRadius: BorderRadius.circular(12),
      ),
      child: ListTile(
        contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 2),
        leading: Container(
          width: 40,
          height: 40,
          decoration: BoxDecoration(
            color: color.withOpacity(0.1),
            borderRadius: BorderRadius.circular(10),
          ),
          child: Icon(
            icon,
            color: color,
            size: 22,
          ),
        ),
        title: Text(
          title,
          style: TextStyle(
            fontSize: 16,
            fontWeight: isSelected ? FontWeight.w600 : FontWeight.w500,
            color: color,
          ),
        ),
        subtitle: Text(
          subtitle,
          style: TextStyle(
            fontSize: 13,
            color: color.withOpacity(0.7),
            fontWeight: FontWeight.w400,
          ),
        ),
        trailing: isSelected
            ? Icon(Icons.check_circle, color: nuBlue, size: 20)
            : Icon(Icons.chevron_right, color: color.withOpacity(0.5), size: 20),
        onTap: onTap,
      ),
    );
  }

  // Bottom sheet for settings
  void _showSettingsBottomSheet() {
    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      backgroundColor: Colors.transparent,
      builder: (context) => Container(
        decoration: const BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.only(
            topLeft: Radius.circular(24),
            topRight: Radius.circular(24),
          ),
        ),
        child: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            // Handle bar
            Container(
              margin: const EdgeInsets.only(top: 12, bottom: 8),
              width: 40,
              height: 4,
              decoration: BoxDecoration(
                color: Colors.grey[300],
                borderRadius: BorderRadius.circular(2),
              ),
            ),

            // Header
            Padding(
              padding: const EdgeInsets.fromLTRB(24, 16, 24, 8),
              child: Row(
                children: [
                  Container(
                    padding: const EdgeInsets.all(8),
                    decoration: BoxDecoration(
                      color: nuBlue.withOpacity(0.1),
                      borderRadius: BorderRadius.circular(8),
                    ),
                    child: Icon(
                      Icons.settings,
                      color: nuBlue,
                      size: 20,
                    ),
                  ),
                  const SizedBox(width: 12),
                  const Text(
                    'Account & Settings',
                    style: TextStyle(
                      fontSize: 20,
                      fontWeight: FontWeight.w700,
                      color: Colors.black87,
                    ),
                  ),
                  const Spacer(),
                  IconButton(
                    onPressed: () => Navigator.of(context).pop(),
                    icon: const Icon(Icons.close, color: Colors.grey),
                  ),
                ],
              ),
            ),

            // Divider
            Container(
              height: 1,
              margin: const EdgeInsets.symmetric(horizontal: 24),
              color: Colors.grey[200],
            ),

            // Menu Options
            Padding(
              padding: const EdgeInsets.all(24),
              child: Column(
                children: [
                  _buildBottomSheetOption(
                    icon: Icons.refresh,
                    title: 'Refresh Status',
                    subtitle: 'Update queue information',
                    color: nuBlue,
                    onTap: () {
                      Navigator.of(context).pop();
                      _handleRefresh();
                    },
                  ),

                  const SizedBox(height: 16),

                  _buildBottomSheetOption(
                    icon: Icons.help_outline,
                    title: 'Help & Support',
                    subtitle: 'Get assistance with your request',
                    color: Colors.green,
                    onTap: () {
                      Navigator.of(context).pop();
                      _showHelpDialog();
                    },
                  ),

                  const SizedBox(height: 16),

                  _buildBottomSheetOption(
                    icon: Icons.info_outline,
                    title: 'About',
                    subtitle: 'App version and information',
                    color: Colors.purple,
                    onTap: () {
                      Navigator.of(context).pop();
                      _showAboutDialog();
                    },
                  ),

                  const SizedBox(height: 24),

                  // Logout Section - Separated with divider
                  Container(
                    width: double.infinity,
                    height: 1,
                    color: Colors.grey[200],
                  ),

                  const SizedBox(height: 24),

                  _buildBottomSheetOption(
                    icon: Icons.logout,
                    title: 'Logout',
                    subtitle: 'Sign out of your session',
                    color: Colors.red,
                    isDestructive: true,
                    onTap: () {
                      Navigator.of(context).pop();
                      _showLogoutDialog();
                    },
                  ),

                  // Add bottom padding for safe area
                  SizedBox(height: MediaQuery.of(context).padding.bottom + 16),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }

  // Bottom sheet option builder
  Widget _buildBottomSheetOption({
    required IconData icon,
    required String title,
    required String subtitle,
    required Color color,
    required VoidCallback onTap,
    bool isDestructive = false,
  }) {
    return Material(
      color: Colors.transparent,
      child: InkWell(
        onTap: onTap,
        borderRadius: BorderRadius.circular(16),
        child: Container(
          padding: const EdgeInsets.all(20),
          decoration: BoxDecoration(
            color: color.withOpacity(0.05),
            borderRadius: BorderRadius.circular(16),
            border: Border.all(
              color: color.withOpacity(0.1),
              width: 1.5,
            ),
          ),
          child: Row(
            children: [
              Container(
                padding: const EdgeInsets.all(12),
                decoration: BoxDecoration(
                  color: color.withOpacity(0.1),
                  borderRadius: BorderRadius.circular(12),
                ),
                child: Icon(
                  icon,
                  color: color,
                  size: 24,
                ),
              ),
              const SizedBox(width: 16),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      title,
                      style: TextStyle(
                        fontSize: 16,
                        fontWeight: FontWeight.w600,
                        color: isDestructive ? color : Colors.black87,
                      ),
                    ),
                    const SizedBox(height: 4),
                    Text(
                      subtitle,
                      style: TextStyle(
                        fontSize: 14,
                        color: isDestructive ? color.withOpacity(0.8) : Colors.grey[600],
                        fontWeight: FontWeight.w400,
                      ),
                    ),
                  ],
                ),
              ),
              Icon(
                Icons.arrow_forward_ios,
                color: color.withOpacity(0.6),
                size: 18,
              ),
            ],
          ),
        ),
      ),
    );
  }

  // Popup menu item builder (keeping for compatibility but not used)
  Widget _buildPopupMenuItem({
    required IconData icon,
    required String title,
    required String subtitle,
    required Color color,
    bool isDestructive = false,
  }) {
    return Container(
      padding: const EdgeInsets.symmetric(vertical: 8, horizontal: 4),
      child: Row(
        children: [
          Container(
            padding: const EdgeInsets.all(8),
            decoration: BoxDecoration(
              color: color.withOpacity(0.1),
              borderRadius: BorderRadius.circular(8),
            ),
            child: Icon(
              icon,
              color: color,
              size: 18,
            ),
          ),
          const SizedBox(width: 12),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              mainAxisSize: MainAxisSize.min,
              children: [
                Text(
                  title,
                  style: TextStyle(
                    fontSize: 14,
                    fontWeight: FontWeight.w600,
                    color: isDestructive ? color : Colors.black87,
                  ),
                ),
                Text(
                  subtitle,
                  style: TextStyle(
                    fontSize: 12,
                    color: isDestructive ? color.withOpacity(0.8) : Colors.grey[600],
                    fontWeight: FontWeight.w400,
                  ),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }

  // Action handlers for the popup menu
  void _handleRefresh() async {
    print('_handleRefresh called - manual refresh');
    await _refreshDataFromAPI();
    if (mounted) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('Queue status refreshed'),
          duration: Duration(seconds: 1),
        ),
      );
    }
  }

  // Supporting Navigation Methods
  void _showAccountDetails() {
    showDialog(
      context: context,
      builder: (context) => AlertDialog(
        backgroundColor: _cardColor,
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
        title: Row(
          children: [
            Container(
              padding: const EdgeInsets.all(8),
              decoration: BoxDecoration(
                color: nuBlue.withOpacity(0.1),
                borderRadius: BorderRadius.circular(8),
              ),
              child: Icon(Icons.person, color: nuBlue),
            ),
            const SizedBox(width: 12),
            Text('Account Details', style: TextStyle(color: _textColor)),
          ],
        ),
        content: Column(
          mainAxisSize: MainAxisSize.min,
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            _buildInfoRow('Reference Number', _referenceNumber),
            const SizedBox(height: 12),
            _buildInfoRow('Request Date', _formatDateTime(DateTime.now())),
            const SizedBox(height: 12),
            _buildInfoRow('Status', _getStatusText()),
            const SizedBox(height: 12),
            _buildInfoRow('Queue Position', _queueNumber.isNotEmpty ? _queueNumber : _position.toString()),
          ],
        ),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(context),
            child: Text('Close', style: TextStyle(color: nuBlue)),
          ),
        ],
      ),
    );
  }

  void _showNotifications() {
    showDialog(
      context: context,
      builder: (context) => AlertDialog(
        backgroundColor: _cardColor,
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
        title: Row(
          children: [
            Icon(Icons.notifications, color: nuBlue),
            const SizedBox(width: 8),
            Text('Notifications', style: TextStyle(color: _textColor)),
          ],
        ),
        content: Text(
          'No new notifications at this time. We\'ll notify you when your document status changes.',
          style: TextStyle(fontSize: 16, color: _textColor),
        ),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(context),
            child: Text('Close', style: TextStyle(color: nuBlue)),
          ),
        ],
      ),
    );
  }

  void _showAppSettings() {
    showDialog(
      context: context,
      builder: (context) => StatefulBuilder(
        builder: (context, setState) => AlertDialog(
          backgroundColor: _cardColor,
          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
          title: Row(
            children: [
              Container(
                padding: const EdgeInsets.all(8),
                decoration: BoxDecoration(
                  color: nuBlue.withOpacity(0.1),
                  borderRadius: BorderRadius.circular(8),
                ),
                child: Icon(Icons.settings, color: nuBlue),
              ),
              const SizedBox(width: 12),
              Text('App Settings', style: TextStyle(color: _textColor)),
            ],
          ),
          content: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              ListTile(
                leading: Icon(_isDarkMode ? Icons.dark_mode : Icons.light_mode, color: nuBlue),
                title: Text('Dark Mode', style: TextStyle(color: _textColor)),
                subtitle: Text(_isDarkMode ? 'Dark theme enabled' : 'Light theme enabled',
                    style: TextStyle(color: _subtitleColor)),
                trailing: Switch(
                  value: _isDarkMode,
                  onChanged: (value) {
                    setState(() {
                      _isDarkMode = value;
                    });
                    // Update the main widget state
                    this.setState(() {});
                    _saveDarkModePreference(value);
                  },
                  activeColor: nuBlue,
                ),
              ),
            ],
          ),
          actions: [
            TextButton(
              onPressed: () => Navigator.pop(context),
              child: Text('Close', style: TextStyle(color: nuBlue)),
            ),
          ],
        ),
      ),
    );
  }

  // Dark mode preference methods
  void _saveDarkModePreference(bool isDark) async {
    // In a real app, you would save this to SharedPreferences
    // For now, we'll just keep it in memory
    print('Dark mode preference saved: $isDark');
  }

  void _loadDarkModePreference() async {
    // In a real app, you would load this from SharedPreferences
    // For now, we'll default to light mode
    _isDarkMode = false;
  }

  // Get theme-aware colors
  Color get _backgroundColor => _isDarkMode ? const Color(0xFF121212) : nuLightGray;
  Color get _cardColor => _isDarkMode ? const Color(0xFF1E1E1E) : Colors.white;
  Color get _textColor => _isDarkMode ? Colors.white : Colors.black;
  Color get _subtitleColor => _isDarkMode ? Colors.white70 : Colors.grey[600]!;

  Widget _buildInfoRow(String label, String value) {
    return Row(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        SizedBox(
          width: 120,
          child: Text(
            label,
            style: TextStyle(
              fontWeight: FontWeight.w500,
              color: _subtitleColor,
            ),
          ),
        ),
        Expanded(
          child: Text(
            value,
            style: TextStyle(
              fontWeight: FontWeight.w600,
              fontSize: 16,
              color: _textColor,
            ),
          ),
        ),
      ],
    );
  }

  void _showHelpDialog() {
    showDialog(
      context: context,
      builder: (context) => AlertDialog(
        title: Row(
          children: [
            Icon(Icons.help_outline, color: Colors.green),
            const SizedBox(width: 8),
            const Text('Help & Support'),
          ],
        ),
        content: const Text(
            'For assistance with your queue status or document requests:\n\n'
                '📞 Call: (043) 723-0706\n'
                '📧 Email: registrar@nu-lipa.edu.ph\n'
                '🕒 Office Hours: 8:00 AM - 5:00 PM (Mon-Fri)\n\n'
                'Please have your reference number ready when contacting support.'
        ),
        actions: [
          TextButton(
            onPressed: () => Navigator.of(context).pop(),
            child: const Text('Close'),
          ),
        ],
      ),
    );
  }

  void _showAboutDialog() {
    showDialog(
      context: context,
      builder: (context) => AlertDialog(
        title: Row(
          children: [
            Icon(Icons.info_outline, color: Colors.purple),
            const SizedBox(width: 8),
            const Text('About NU Queue'),
          ],
        ),
        content: const Text(
            'NU Queue Status Monitor\n'
                'Version 1.0.0\n\n'
                'Developed for National University Lipa\n'
                'Registrar\'s Office Digital Queue System\n\n'
                '© 2024 National University Lipa City'
        ),
        actions: [
          TextButton(
            onPressed: () => Navigator.of(context).pop(),
            child: const Text('Close'),
          ),
        ],
      ),
    );
  }

  void _showLogoutDialog() {
    showDialog(
      context: context,
      builder: (context) => AlertDialog(
        title: Row(
          children: [
            Icon(Icons.logout, color: Colors.red),
            const SizedBox(width: 8),
            const Text('Logout'),
          ],
        ),
        content: const Text(
            'Are you sure you want to logout?\n\n'
                'You will need your reference number to check queue status again.'
        ),
        actions: [
          TextButton(
            onPressed: () => Navigator.of(context).pop(),
            child: const Text('Cancel'),
          ),
          ElevatedButton(
            onPressed: () {
              Navigator.of(context).pop(); // Close dialog
              Navigator.of(context).pushNamedAndRemoveUntil('/', (route) => false);
            },
            style: ElevatedButton.styleFrom(
              backgroundColor: Colors.red,
              foregroundColor: Colors.white,
            ),
            child: const Text('Logout'),
          ),
        ],
      ),
    );
  }

  void _showDebugDialog() {
    showDialog(
      context: context,
      builder: (context) => AlertDialog(
        title: Row(
          children: [
            Icon(Icons.bug_report, color: Colors.blue),
            const SizedBox(width: 8),
            const Text('Debug Information'),
          ],
        ),
        content: SingleChildScrollView(
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            mainAxisSize: MainAxisSize.min,
            children: [
              Text('OneSignal Player ID:', style: TextStyle(fontWeight: FontWeight.bold)),
              Text(_onesignalPlayerId ?? 'Not available'),
              const SizedBox(height: 8),
              Text('Queue Status:', style: TextStyle(fontWeight: FontWeight.bold)),
              Text(_status ?? 'Not loaded'),
              const SizedBox(height: 8),
              Text('Your Queue Number:', style: TextStyle(fontWeight: FontWeight.bold)),
              Text(_yourQueueNumber?.toString() ?? 'Not available'),
              const SizedBox(height: 8),
              Text('Current Number:', style: TextStyle(fontWeight: FontWeight.bold)),
              Text(_currentNumber?.toString() ?? 'Not available'),
              const SizedBox(height: 8),
              Text('Reference Number:', style: TextStyle(fontWeight: FontWeight.bold)),
              Text(_referenceNumber ?? 'Not available'),
              const SizedBox(height: 8),
              Text('Last Updated:', style: TextStyle(fontWeight: FontWeight.bold)),
              Text(_lastUpdated?.toString() ?? 'Never'),
              const SizedBox(height: 8),
              Text('Is Connected:', style: TextStyle(fontWeight: FontWeight.bold)),
              Text(_isConnected.toString()),
            ],
          ),
        ),
        actions: [
          TextButton(
            onPressed: () => Navigator.of(context).pop(),
            child: const Text('Close'),
          ),
        ],
      ),
    );
  }

  String _getEstimatedWaitTimeText() {
    print('_getEstimatedWaitTimeText called with status: $_status, queueNumber: $_yourQueueNumber, currentNumber: $_currentNumber');
    final status = _status?.toLowerCase() ?? '';
    print('DEBUG: _getEstimatedWaitTimeText called with status: "$status", _yourQueueNumber: $_yourQueueNumber, _currentNumber: $_currentNumber');

    // Handle completed/ready states
    if (['ready_for_pickup', 'ready for pickup', 'completed', 'released'].contains(status)) {
      print('Returning ready for pickup message');
      return '0 minutes (Ready for pickup)';
    }

    // Handle being processed (ready_for_release is onsite processing)
    if (['ready for release', 'ready_for_release'].contains(status)) {
      print('Returning being processed message');
      return 'Being processed by registrar';
    }

    // Handle current turn
    if (['your turn!', 'in progress', 'processing'].contains(status)) {
      print('Returning your turn message');
      return '0 minutes (Your turn!)';
    }

    // Special case for demo reference ID
    if (widget.referenceId == 'NU822694' && ['in queue', 'in_queue', 'waiting'].contains(status)) {
      print('Returning demo wait time');
      return '19 minutes (peak hours)';
    }

    // Calculate wait time based on queue position
    final position = _yourQueueNumber - _currentNumber;
    print('Calculated position: $position');
    if (position <= 0) {
      print('Position <= 0, returning your turn');
      return '0 minutes (Your turn!)';
    }

    // Estimate based on position and current hour
    final currentHour = DateTime.now().hour;
    int baseTimePerPerson = 15; // minutes per person

    // Adjust for peak hours (similar to API logic)
    bool isPeakHours = (currentHour >= 9 && currentHour <= 11) ||
        (currentHour >= 14 && currentHour <= 16) ||
        (currentHour >= 12 && currentHour <= 13);

    if (isPeakHours) {
      baseTimePerPerson = (baseTimePerPerson * 1.2).round(); // 20% longer during peak
    }

    final estimatedMinutes = position * baseTimePerPerson;

    String peakIndicator = isPeakHours ? ' (peak hours)' : '';

    if (estimatedMinutes >= 60) {
      final hours = estimatedMinutes ~/ 60;
      final minutes = estimatedMinutes % 60;
      if (minutes > 0) {
        return '${hours}h ${minutes}m$peakIndicator';
      } else {
        return '${hours} hour${hours > 1 ? 's' : ''}$peakIndicator';
      }
    }

    return '$estimatedMinutes minutes$peakIndicator';
  }
}
