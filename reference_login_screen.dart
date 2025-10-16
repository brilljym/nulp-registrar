import 'package:flutter/material.dart';
import '../theme/nu_theme.dart';
import '../services/api_service.dart';
import 'queue_status_screen.dart';

class ReferenceLoginScreen extends StatefulWidget {
  const ReferenceLoginScreen({Key? key}) : super(key: key);

  @override
  State<ReferenceLoginScreen> createState() => _ReferenceLoginScreenState();
}

class _ReferenceLoginScreenState extends State<ReferenceLoginScreen> {
  final TextEditingController _referenceController = TextEditingController();
  final GlobalKey<FormState> _formKey = GlobalKey<FormState>();
  bool _isLoading = false;

  void _submitReference() async {
    if (_formKey.currentState?.validate() ?? false) {
      setState(() {
        _isLoading = true;
      });

      try {
        // Validate reference number using API (accepts transactions with trackable statuses)
        final result = await ApiService.validateReference(_referenceController.text.trim());
        
        if (mounted) {
          setState(() {
            _isLoading = false;
          });

          if (result != null) {
            // Reference number is valid, navigate to queue status screen
            Navigator.of(context).pushReplacement(
              MaterialPageRoute(
                builder: (context) => QueueStatusScreen(
                  referenceId: _referenceController.text.trim(),
                  referenceType: result['type'],
                  referenceData: result['data'],
                ),
              ),
            );
          } else {
            // Reference number not found or not accepted
            _showErrorDialog(
              'Reference Number Not Found',
              'The reference number you entered could not be found or is not yet ready for tracking. Please check and try again, or contact the registrar if your request has been submitted.',
            );
          }
        }
      } catch (e) {
        if (mounted) {
          setState(() {
            _isLoading = false;
          });
          
          _showErrorDialog(
            'Connection Error',
            'Unable to verify reference number. Please check your internet connection and try again.',
          );
        }
      }
    }
  }

  void _showErrorDialog(String title, String message) {
    showDialog(
      context: context,
      builder: (context) => AlertDialog(
        title: Text(title),
        content: Text(message),
        actions: [
          TextButton(
            onPressed: () => Navigator.of(context).pop(),
            child: const Text('OK'),
          ),
        ],
      ),
    );
  }

  @override
  void dispose() {
    _referenceController.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: nuLightGray,
      body: SafeArea(
        child: SingleChildScrollView(
          child: Padding(
            padding: const EdgeInsets.all(24.0),
            child: Form(
              key: _formKey,
              child: Column(
                mainAxisAlignment: MainAxisAlignment.center,
                crossAxisAlignment: CrossAxisAlignment.stretch,
                children: [
                  SizedBox(height: MediaQuery.of(context).size.height * 0.05),
                  // NU Logo
                  Image.asset(
                    'assets/images/NU-LIPA-REGIS.jpg',
                    width: MediaQuery.of(context).size.width * 0.4,
                    height: MediaQuery.of(context).size.width * 0.4,
                    fit: BoxFit.contain,
                  ),
                  const SizedBox(height: 16),
                  const Text(
                    'NU Lipa Queue Status',
                    textAlign: TextAlign.center,
                    style: TextStyle(
                      fontSize: 28,
                      fontWeight: FontWeight.bold,
                      color: nuBlue,
                    ),
                  ),
                  const SizedBox(height: 8),
                  const Text(
                    'Enter your reference number to check your queue status',
                    textAlign: TextAlign.center,
                    style: TextStyle(
                      fontSize: 16,
                      color: nuGray,
                    ),
                  ),
                  const SizedBox(height: 32),
                  
                  // Reference Number Input
                  TextFormField(
                    controller: _referenceController,
                    decoration: InputDecoration(
                      labelText: 'Reference Number',
                      hintText: 'Enter your reference number',
                      prefixIcon: const Icon(Icons.confirmation_number),
                      border: OutlineInputBorder(
                        borderRadius: BorderRadius.circular(12),
                      ),
                      focusedBorder: OutlineInputBorder(
                        borderRadius: BorderRadius.circular(12),
                        borderSide: const BorderSide(color: nuBlue, width: 2),
                      ),
                    ),
                    textCapitalization: TextCapitalization.characters,
                    validator: (value) {
                      if (value == null || value.isEmpty) {
                        return 'Please enter your reference number';
                      }
                      if (value.length < 3) {
                        return 'Reference number must be at least 3 characters';
                      }
                      return null;
                    },
                  ),
                  const SizedBox(height: 16),
                  
                  // Submit Button
                  ElevatedButton(
                    onPressed: _isLoading ? null : _submitReference,
                    style: ElevatedButton.styleFrom(
                      backgroundColor: nuBlue,
                      foregroundColor: Colors.white,
                      padding: const EdgeInsets.symmetric(vertical: 16),
                      shape: RoundedRectangleBorder(
                        borderRadius: BorderRadius.circular(12),
                      ),
                    ),
                    child: _isLoading
                        ? const SizedBox(
                            height: 20,
                            width: 20,
                            child: CircularProgressIndicator(
                              strokeWidth: 2,
                              valueColor: AlwaysStoppedAnimation<Color>(Colors.white),
                            ),
                          )
                        : const Text(
                            'Check Queue Status',
                            style: TextStyle(
                              fontSize: 16,
                              fontWeight: FontWeight.w600,
                            ),
                          ),
                  ),
                  const SizedBox(height: 16),
                  
                  // Help text
                  TextButton(
                    onPressed: () {
                      showDialog(
                        context: context,
                        builder: (context) => AlertDialog(
                          title: const Text('Need Help?'),
                          content: const Text(
                            'Your reference number can be found on your transaction receipt or confirmation email. Requests with accepted, pending, in queue, processing, ready for pickup, or completed status can be tracked.',
                          ),
                          actions: [
                            TextButton(
                              onPressed: () => Navigator.of(context).pop(),
                              child: const Text('OK'),
                            ),
                          ],
                        ),
                      );
                    },
                    child: const Text(
                      'Where can I find my reference number?',
                      style: TextStyle(color: nuBlue),
                    ),
                  ),
                  SizedBox(height: MediaQuery.of(context).size.height * 0.05),
                ],
              ),
            ),
          ),
        ),
      ),
    );
  }
}