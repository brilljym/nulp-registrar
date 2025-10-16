import 'package:flutter/material.dart';
import 'dart:async';
import '../theme/nu_theme.dart';
import '../services/api_service.dart';

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

  @override
  void initState() {
    super.initState();
    _initializeData();
    _startTimer();
    _startQueueRefresh();
    // Initial refresh from API
    _refreshDataFromAPI();
  }

  void _initializeData() {
    if (widget.referenceData != null) {
      final data = widget.referenceData!;

      if (widget.referenceType == 'transaction') {
        _studentName = data['student_name'] ?? 'Unknown Student';
        _status = data['status'] ?? 'Processing';
        _expectedReleaseDate = data['expected_release_date']; // Get from API

        // Handle multiple documents
        if (data['documents'] != null && data['documents'] is List) {
          _documents = List<Map<String, dynamic>>.from(data['documents']);
        } else if (data['document_name'] != null) {
          // Single document fallback
          _documents = [{'name': data['document_name'], 'status': _status, 'quantity': data['quantity'] ?? 1}];
          _documentName = data['document_name'];
        } else {
          _documents = [{'name': 'Unknown Document', 'status': _status, 'quantity': 1}];
          _documentName = 'Unknown Document';
        }

        // Generate queue number based on reference ID (simple hash)
        if (widget.referenceId == 'NU822694') {
          // Special case for demo
          _yourQueueNumber = 2;
          _currentNumber = 1;
          _status = 'In Queue';
        } else {
          _yourQueueNumber = widget.referenceId.hashCode.abs() % 20 + 1;
          _currentNumber = (_yourQueueNumber > 5) ? _yourQueueNumber - 2 : 1;
        }

      } else if (widget.referenceType == 'onsite_request') {
        _studentName = data['full_name'] ?? 'Unknown Student';
        _status = data['status'] ?? 'Processing';
        _expectedReleaseDate = data['expected_release_date']; // Get from API

        // Handle multiple documents
        if (data['documents'] != null && data['documents'] is List) {
          _documents = List<Map<String, dynamic>>.from(data['documents']);
        } else if (data['document_name'] != null) {
          // Single document fallback
          _documents = [{'name': data['document_name'], 'status': _status, 'quantity': data['quantity'] ?? 1}];
          _documentName = data['document_name'];
        } else {
          _documents = [{'name': 'Form 137', 'status': _status, 'quantity': 1}];
          _documentName = 'Form 137';
        }

        // Generate queue number based on reference ID (simple hash)
        if (widget.referenceId == 'NU822694') {
          // Special case for demo
          _yourQueueNumber = 2;
          _currentNumber = 1;
          _status = 'In Queue';
        } else {
          _yourQueueNumber = widget.referenceId.hashCode.abs() % 20 + 1;
          _currentNumber = (_yourQueueNumber > 5) ? _yourQueueNumber - 2 : 1;
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
      _documents = [{'name': 'Unknown Document', 'status': 'Processing', 'quantity': 1}];
      _documentName = 'Unknown Document';
      _calculateExpectedReleaseTime();
    }
  }

  void _setExpectedReleaseTime() {
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
  }

  void _calculateExpectedReleaseTime() {
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
      setState(() {
        _currentTime = DateTime.now();
        // This will trigger a rebuild and update the expected time display
      });
    });
  }

  void _startQueueRefresh() {
    _queueTimer = Timer.periodic(const Duration(seconds: 10), (timer) {
      // Refresh data from API
      _refreshDataFromAPI();
    });
  }

  Future<void> _refreshDataFromAPI() async {
    try {
      final result = await ApiService.validateReference(widget.referenceId);
      if (result != null && mounted) {
        print('API Response: $result'); // Debug output
        setState(() {
          final data = result['data'];
          if (result['type'] == 'transaction') {
            _studentName = data['student_name'] ?? _studentName;
            _status = data['status'] ?? _status;
            _expectedReleaseDate = data['expected_release_date']; // Get from API

            // Handle multiple documents from API
            if (data['documents'] != null && data['documents'] is List) {
              _documents = List<Map<String, dynamic>>.from(data['documents']);
            } else if (data['document_name'] != null) {
              _documents = [{'name': data['document_name'], 'status': _status, 'quantity': data['quantity'] ?? 1}];
              _documentName = data['document_name'];
            }

            print('Expected release date from API (transaction): $_expectedReleaseDate'); // Debug
          } else if (result['type'] == 'onsite_request') {
            _studentName = data['full_name'] ?? _studentName;
            _status = data['status'] ?? _status;
            _expectedReleaseDate = data['expected_release_date']; // Get from API

            // Handle multiple documents from API
            if (data['documents'] != null && data['documents'] is List) {
              _documents = List<Map<String, dynamic>>.from(data['documents']);
            } else if (data['document_name'] != null) {
              _documents = [{'name': data['document_name'], 'status': _status, 'quantity': data['quantity'] ?? 1}];
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
    }
  }

  void _updateQueueBasedOnStatus() {
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
  }

  @override
  void dispose() {
    _timer?.cancel();
    _queueTimer?.cancel();
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

  Color _getStatusColor() {
    switch (_status.toLowerCase()) {
      case "your turn!":
      case "in progress":
        return const Color(0xFF4CAF50); // Green
      case "ready for release":
      case "ready_for_pickup":
      case "completed":
      case "released":
        return const Color(0xFF2E7D32); // Dark Green
      case "processing":
        return const Color(0xFFFF9800); // Orange
      case "accepted":
      case "pending":
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
      case "ready_for_pickup":
      case "completed":
      case "released":
        return Icons.check_circle;
      case "processing":
        return Icons.refresh;
      case "accepted":
      case "pending":
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
      case "ready_for_pickup":
        return "READY FOR PICKUP";
      case "completed":
        return "COMPLETED";
      case "processing":
        return "PROCESSING";
      case "accepted":
        return "ACCEPTED";
      case "pending":
        return "PENDING";
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
      case "ready_for_pickup":
      case "completed":
        return "Your document is ready for pickup at the registrar's office";
      case "released":
        return "Your document has been released and collected";
      case "processing":
        return "Your request is being processed by our team";
      case "accepted":
      case "pending":
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
    return ['accepted', 'pending', 'in queue', 'in_queue', 'waiting', 'processing', 'in progress', 'your turn!', 'ready for release', 'ready_for_pickup', 'completed', 'released'].contains(status);
  }

  bool _isInQueueCompleted() {
    final status = _status.toLowerCase();
    return ['processing', 'in progress', 'your turn!', 'ready for release', 'ready_for_pickup', 'completed', 'released'].contains(status);
  }

  bool _isProcessingOrBeyond() {
    final status = _status.toLowerCase();
    return ['processing', 'in progress', 'your turn!', 'ready for release', 'ready_for_pickup', 'completed', 'released'].contains(status);
  }

  bool _isProcessingCompleted() {
    final status = _status.toLowerCase();
    return ['ready for release', 'ready_for_pickup', 'completed', 'released'].contains(status);
  }

  bool _isCompleted() {
    final status = _status.toLowerCase();
    return ['ready for release', 'ready_for_pickup', 'completed', 'released'].contains(status);
  }

  bool _isAllStepsCompleted() {
    final status = _status.toLowerCase();
    return ['completed'].contains(status);
  }

  // New methods for the updated progress steps
  bool _isPaymentPendingOrBeyond() {
    final status = _status.toLowerCase();
    return ['payment pending', 'pending payment', 'accepted', 'pending', 'processing', 'in progress', 'your turn!', 'ready for release', 'completed'].contains(status);
  }

  bool _isPaymentPendingCompleted() {
    final status = _status.toLowerCase();
    return ['accepted', 'pending', 'processing', 'in progress', 'your turn!', 'ready for release', 'completed'].contains(status);
  }

  bool _isReleaseDateOrBeyond() {
    final status = _status.toLowerCase();
    return ['ready for release', 'completed'].contains(status);
  }

  bool _isReleaseDateCompleted() {
    final status = _status.toLowerCase();
    return ['completed'].contains(status);
  }

  String _getCurrentProcessingLabel() {
    final status = _status.toLowerCase();
    switch (status) {
      case 'ready for release':
      case 'ready_for_pickup':
      case 'completed':
      case 'released':
        return 'Ready for Pickup';
      case 'processing':
      case 'in progress':
      case 'your turn!':
        return 'Now Processing';
      case 'accepted':
      case 'pending':
      case 'in queue':
      case 'in_queue':
      case 'waiting':
        return 'In Queue';
      default:
        return 'Status';
    }
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
              '#${index + 1}',
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
      case "ready_for_pickup":
      case "completed":
      case "released":
        return const Color(0xFF2E7D32); // Dark Green
      case "processing":
        return const Color(0xFFFF9800); // Orange
      case "accepted":
      case "pending":
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
      case "ready_for_pickup":
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
        backgroundColor: nuLightGray,
        appBar: AppBar(
          title: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              const Text(
                'Queue Status Monitor',
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
          actions: [
            IconButton(
              onPressed: () {
                showDialog(
                  context: context,
                  builder: (context) => AlertDialog(
                    title: const Text('Logout'),
                    content: const Text('Are you sure you want to logout? You will need your reference number to check queue status again.'),
                    actions: [
                      TextButton(
                        onPressed: () => Navigator.of(context).pop(),
                        child: const Text('Cancel'),
                      ),
                      TextButton(
                        onPressed: () {
                          Navigator.of(context).pop(); // Close dialog
                          Navigator.of(context).pushNamedAndRemoveUntil('/', (route) => false);
                        },
                        child: const Text('Logout', style: TextStyle(color: Colors.red)),
                      ),
                    ],
                  ),
                );
              },
              icon: const Icon(Icons.logout, color: Colors.white),
              tooltip: 'Logout',
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

                      // Student Account Information Card
                      Container(
                        width: double.infinity,
                        padding: const EdgeInsets.all(20),
                        decoration: BoxDecoration(
                          color: Colors.white,
                          borderRadius: BorderRadius.circular(16),
                          boxShadow: [
                            BoxShadow(
                              color: Colors.black.withOpacity(0.1),
                              blurRadius: 10,
                              offset: const Offset(0, 4),
                            ),
                          ],
                        ),
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            // Header
                            Row(
                              children: [
                                Container(
                                  padding: const EdgeInsets.all(8),
                                  decoration: BoxDecoration(
                                    color: nuBlue.withOpacity(0.1),
                                    borderRadius: BorderRadius.circular(8),
                                  ),
                                  child: Icon(
                                    Icons.account_circle,
                                    color: nuBlue,
                                    size: 24,
                                  ),
                                ),
                                const SizedBox(width: 12),
                                const Text(
                                  'Student Account Information',
                                  style: TextStyle(
                                    fontSize: 18,
                                    fontWeight: FontWeight.bold,
                                    color: Colors.black87,
                                  ),
                                ),
                              ],
                            ),
                            const SizedBox(height: 20),
                            
                            // Reference Code
                            _buildAccountInfoRow(
                              'Reference Code:',
                              widget.referenceId,
                              Icons.qr_code,
                            ),
                            const SizedBox(height: 12),
                            
                            // Queue Number
                            _buildAccountInfoRow(
                              'Queue Number:',
                              'A${_yourQueueNumber.toString().padLeft(3, '0')}',
                              Icons.confirmation_number,
                            ),
                            const SizedBox(height: 12),
                            
                            // Status
                            Row(
                              children: [
                                Container(
                                  padding: const EdgeInsets.all(4),
                                  decoration: BoxDecoration(
                                    color: _getStatusColor().withOpacity(0.1),
                                    borderRadius: BorderRadius.circular(4),
                                  ),
                                  child: Icon(
                                    _getStatusIcon(),
                                    color: _getStatusColor(),
                                    size: 16,
                                  ),
                                ),
                                const SizedBox(width: 8),
                                const Text(
                                  'Status:',
                                  style: TextStyle(
                                    fontSize: 14,
                                    fontWeight: FontWeight.w600,
                                    color: nuGray,
                                  ),
                                ),
                                const SizedBox(width: 8),
                                Container(
                                  padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 4),
                                  decoration: BoxDecoration(
                                    color: _getStatusColor(),
                                    borderRadius: BorderRadius.circular(12),
                                  ),
                                  child: Text(
                                    _getFormattedStatus(),
                                    style: const TextStyle(
                                      fontSize: 12,
                                      fontWeight: FontWeight.bold,
                                      color: Colors.white,
                                    ),
                                  ),
                                ),
                              ],
                            ),
                            const SizedBox(height: 12),
                            
                            // Estimated Wait Time
                            _buildAccountInfoRow(
                              'Estimated Wait Time:',
                              _getEstimatedWaitTimeText(),
                              Icons.access_time,
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
                                  'Your Queue Number',
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
                                    '$_yourQueueNumber',
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
                                                Row(
                                                  mainAxisAlignment: MainAxisAlignment.center,
                                                  children: [
                                                    Icon(
                                                      Icons.calendar_today,
                                                      color: nuBlue,
                                                      size: 20,
                                                    ),
                                                    const SizedBox(width: 8),
                                                    Flexible(
                                                      child: Text(
                                                        'Expected Release Date',
                                                        style: TextStyle(
                                                          color: nuBlue,
                                                          fontWeight: FontWeight.w600,
                                                          fontSize: 13,
                                                          letterSpacing: 0.3,
                                                        ),
                                                        overflow: TextOverflow.ellipsis,
                                                      ),
                                                    ),
                                                  ],
                                                ),
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

                                    const SizedBox(height: 20),

                                    // Additional Information
                                    Container(
                                      width: double.infinity,
                                      padding: const EdgeInsets.all(16),
                                      decoration: BoxDecoration(
                                        color: Colors.white.withOpacity(0.1),
                                        borderRadius: BorderRadius.circular(12),
                                        border: Border.all(
                                          color: Colors.white.withOpacity(0.2),
                                          width: 1,
                                        ),
                                      ),
                                      child: Column(
                                        children: [
                                          Row(
                                            children: [
                                              Icon(
                                                Icons.access_time,
                                                color: Colors.white.withOpacity(0.8),
                                                size: 16,
                                              ),
                                              const SizedBox(width: 8),
                                              Text(
                                                'Pickup Information',
                                                style: TextStyle(
                                                  color: Colors.white.withOpacity(0.9),
                                                  fontWeight: FontWeight.w600,
                                                  fontSize: 13,
                                                  letterSpacing: 0.3,
                                                ),
                                              ),
                                            ],
                                          ),
                                          const SizedBox(height: 12),
                                          Text(
                                            _getExpectedReleaseDateTimeText(),
                                            style: const TextStyle(
                                              color: Colors.white,
                                              fontWeight: FontWeight.w600,
                                              fontSize: 15,
                                              letterSpacing: 0.4,
                                            ),
                                            textAlign: TextAlign.center,
                                          ),
                                          const SizedBox(height: 8),
                                          Text(
                                            'Office Hours: 8:00 AM - 5:00 PM (Monday to Friday)',
                                            style: TextStyle(
                                              color: Colors.white.withOpacity(0.8),
                                              fontSize: 11,
                                              fontWeight: FontWeight.w400,
                                              letterSpacing: 0.2,
                                            ),
                                            textAlign: TextAlign.center,
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

                      // Progress Indicator
                      Container(
                        padding: const EdgeInsets.all(16),
                        decoration: BoxDecoration(
                          color: Colors.white,
                          borderRadius: BorderRadius.circular(16),
                          boxShadow: [
                            BoxShadow(
                              color: Colors.black.withOpacity(0.05),
                              blurRadius: 10,
                              offset: const Offset(0, 4),
                            ),
                          ],
                        ),
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Text(
                              'Queue Progress',
                              style: Theme.of(context).textTheme.titleSmall?.copyWith(
                                fontWeight: FontWeight.w600,
                                color: nuBlue,
                              ),
                            ),
                            const SizedBox(height: 16),
                            SizedBox(
                              height: 80,
                              child: SingleChildScrollView(
                                scrollDirection: Axis.horizontal,
                                child: IntrinsicHeight(
                                  child: Row(
                                    mainAxisAlignment: MainAxisAlignment.center,
                                    children: [
                                      _buildProgressStep('Submitted', true, true),
                                      _buildProgressLine(_isPaymentPendingOrBeyond() || _isAllStepsCompleted()),
                                      _buildProgressStep('Payment\nPending', _isPaymentPendingOrBeyond(), _isPaymentPendingCompleted() || _isAllStepsCompleted()),
                                      _buildProgressLine(_isInQueueOrBeyond() || _isAllStepsCompleted()),
                                      _buildProgressStep('In Queue', _isInQueueOrBeyond(), _isInQueueCompleted() || _isAllStepsCompleted()),
                                      _buildProgressLine(_isProcessingOrBeyond() || _isAllStepsCompleted()),
                                      _buildProgressStep('Processing', _isProcessingOrBeyond(), _isProcessingCompleted() || _isAllStepsCompleted()),
                                      _buildProgressLine(_isReleaseDateOrBeyond() || _isAllStepsCompleted()),
                                      _buildProgressStep('Release\nDate', _isReleaseDateOrBeyond(), _isReleaseDateCompleted() || _isAllStepsCompleted()),
                                      _buildProgressLine(_isCompleted() || _isAllStepsCompleted()),
                                      _buildProgressStep('Completed', _isCompleted(), _isCompleted()),
                                    ],
                                  ),
                                ),
                              ),
                            ),
                          ],
                        ),
                      ),
                    ],
                  ),
                ),
              ),

              // Fixed Refresh Button at bottom
              Padding(
                padding: const EdgeInsets.all(16.0),
                child: Container(
                  width: double.infinity,
                  height: 44,
                  decoration: BoxDecoration(
                    borderRadius: BorderRadius.circular(12),
                    border: Border.all(color: nuBlue.withOpacity(0.3)),
                  ),
                  child: ElevatedButton.icon(
                    onPressed: () async {
                      await _refreshDataFromAPI();

                      if (mounted) {
                        ScaffoldMessenger.of(context).showSnackBar(
                          const SnackBar(
                            content: Text('Queue status refreshed'),
                            duration: Duration(seconds: 1),
                          ),
                        );
                      }
                    },
                    icon: const Icon(Icons.refresh, color: nuBlue),
                    label: const Text(
                      'Refresh Status',
                      style: TextStyle(color: nuBlue, fontWeight: FontWeight.w600),
                    ),
                    style: ElevatedButton.styleFrom(
                      backgroundColor: Colors.white,
                      shadowColor: Colors.transparent,
                      shape: RoundedRectangleBorder(
                        borderRadius: BorderRadius.circular(12),
                      ),
                    ),
                  ),
                ),
              ),
            ],
          ),
        )
    );
  }

  Widget _buildProgressStep(String label, bool isActive, bool isCompleted) {
    return SizedBox(
      width: 65,
      child: Column(
        mainAxisSize: MainAxisSize.min,
        children: [
          Container(
            width: 32,
            height: 32,
            decoration: BoxDecoration(
              color: isCompleted ? Colors.green : (isActive ? nuBlue : nuGray.withOpacity(0.3)),
              shape: BoxShape.circle,
            ),
            child: Icon(
              isCompleted ? Icons.check : Icons.circle,
              color: Colors.white,
              size: 16,
            ),
          ),
          const SizedBox(height: 8),
          Text(
            label,
            style: TextStyle(
              fontSize: 9,
              color: isActive || isCompleted ? nuBlue : nuGray,
              fontWeight: isActive || isCompleted ? FontWeight.w600 : FontWeight.normal,
            ),
            textAlign: TextAlign.center,
            overflow: TextOverflow.ellipsis,
            maxLines: 2,
          ),
        ],
      ),
    );
  }

  Widget _buildProgressLine(bool isActive) {
    return SizedBox(
      width: 40,
      child: Container(
        height: 2,
        margin: const EdgeInsets.symmetric(horizontal: 8),
        color: isActive ? nuBlue : nuGray.withOpacity(0.3),
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

  String _getEstimatedWaitTimeText() {
    final status = _status.toLowerCase();
    
    // Handle completed/ready states
    if (['ready for release', 'ready_for_pickup', 'completed', 'released'].contains(status)) {
      return '0 minutes (Ready for pickup)';
    }
    
    // Handle current turn
    if (['your turn!', 'in progress'].contains(status)) {
      return '0 minutes (Your turn!)';
    }
    
    // Special case for demo reference ID
    if (widget.referenceId == 'NU822694' && ['in queue', 'in_queue', 'waiting'].contains(status)) {
      return '19 minutes (peak hours)';
    }
    
    // Calculate wait time based on queue position
    final position = _yourQueueNumber - _currentNumber;
    if (position <= 0) {
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
