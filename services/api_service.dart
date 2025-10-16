import 'dart:convert';
import 'dart:io';
import 'package:http/http.dart' as http;

class ApiService {
  // Update this base URL to match your Laravel backend
  static const String baseUrl = 'http://localhost:8000/api';
  
  /// Validates a reference ID and returns the request data
  static Future<Map<String, dynamic>?> validateReference(String referenceId) async {
    try {
      // First, try to get it as a transaction (StudentRequest)
      final transactionResponse = await http.get(
        Uri.parse('$baseUrl/transactions/reference/$referenceId'),
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
        },
      );

      if (transactionResponse.statusCode == 200) {
        final transactionData = json.decode(transactionResponse.body);
        return {
          'type': 'transaction',
          'data': transactionData,
        };
      }

      // If not found as transaction, try as onsite request
      final onsiteResponse = await http.get(
        Uri.parse('$baseUrl/onsite-requests/reference/$referenceId'),
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
        },
      );

      if (onsiteResponse.statusCode == 200) {
        final onsiteData = json.decode(onsiteResponse.body);
        return {
          'type': 'onsite_request',
          'data': onsiteData,
        };
      }

      // Reference not found in either endpoint
      print('Reference $referenceId not found in any endpoint');
      return null;

    } catch (e) {
      print('Error validating reference $referenceId: $e');
      return null;
    }
  }

  /// Search for transactions by reference
  static Future<List<Map<String, dynamic>>> searchTransactions(String query) async {
    try {
      final response = await http.get(
        Uri.parse('$baseUrl/transactions/search?reference=$query'),
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
        },
      );

      if (response.statusCode == 200) {
        final List<dynamic> data = json.decode(response.body);
        return data.cast<Map<String, dynamic>>();
      }

      return [];
    } catch (e) {
      print('Error searching transactions: $e');
      return [];
    }
  }

  /// Search for onsite requests by reference
  static Future<List<Map<String, dynamic>>> searchOnsiteRequests(String query) async {
    try {
      final response = await http.get(
        Uri.parse('$baseUrl/onsite-requests/search?ref_code=$query'),
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
        },
      );

      if (response.statusCode == 200) {
        final List<dynamic> data = json.decode(response.body);
        return data.cast<Map<String, dynamic>>();
      }

      return [];
    } catch (e) {
      print('Error searching onsite requests: $e');
      return [];
    }
  }

  /// Debug endpoint to check what transactions are available
  static Future<Map<String, dynamic>?> debugTransactions() async {
    try {
      final response = await http.get(
        Uri.parse('$baseUrl/debug/transactions'),
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
        },
      );

      if (response.statusCode == 200) {
        return json.decode(response.body);
      }

      return null;
    } catch (e) {
      print('Error getting debug info: $e');
      return null;
    }
  }
}