<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class QueueManagementService
{
    private const API_BASE_URL = 'https://smart-queueing-waiting-time-ai-ylac.vercel.app';
    private const CACHE_TTL = 30; // 30 seconds cache

    /**
     * Add a customer to the smart queue
     */
    public function joinQueue(array $customerData): ?array
    {
        try {
            $response = Http::timeout(10)->post(self::API_BASE_URL . '/queue/join', [
                'customer_name' => $customerData['name'],
                'phone' => $customerData['phone'] ?? null,
                'email' => $customerData['email'] ?? null,
                'customer_type' => $this->mapCustomerType($customerData['type'] ?? 'walk_in'),
                'service_type' => $this->mapServiceType($customerData['service_type'] ?? 'general'),
                'notes' => $customerData['notes'] ?? null,
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            Log::warning('Failed to join queue', ['response' => $response->body()]);
            return null;
        } catch (\Exception $e) {
            Log::error('Queue service error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get current queue status
     */
    public function getQueueStatus(): array
    {
        $cacheKey = 'queue_status';
        
        return Cache::remember($cacheKey, self::CACHE_TTL, function () {
            try {
                $response = Http::timeout(5)->get(self::API_BASE_URL . '/queue/waiting');
                
                if ($response->successful()) {
                    return $response->json();
                }
                
                return [];
            } catch (\Exception $e) {
                Log::error('Failed to get queue status: ' . $e->getMessage());
                return [];
            }
        });
    }

    /**
     * Get customer's current position and wait time
     */
    public function getCustomerStatus(string $customerId): ?array
    {
        try {
            $response = Http::timeout(5)->get(self::API_BASE_URL . "/queue/{$customerId}");
            
            if ($response->successful()) {
                return $response->json();
            }
            
            return null;
        } catch (\Exception $e) {
            Log::error('Failed to get customer status: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Update customer status (start service, complete, etc.)
     */
    public function updateCustomerStatus(string $customerId, string $status, ?int $serviceDuration = null): bool
    {
        try {
            $data = ['status' => $status];
            if ($serviceDuration) {
                $data['actual_service_duration'] = $serviceDuration;
            }

            $response = Http::timeout(10)->put(self::API_BASE_URL . "/queue/{$customerId}/status", $data);
            
            return $response->successful();
        } catch (\Exception $e) {
            Log::error('Failed to update customer status: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Remove customer from queue
     */
    public function removeFromQueue(string $customerId): bool
    {
        try {
            $response = Http::timeout(10)->delete(self::API_BASE_URL . "/queue/{$customerId}");
            return $response->successful();
        } catch (\Exception $e) {
            Log::error('Failed to remove from queue: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get enhanced wait time estimation
     */
    public function getWaitTimeEstimate(int $queuePosition, string $serviceType = 'general', int $counters = 3): array
    {
        $cacheKey = "wait_time_{$queuePosition}_{$serviceType}_{$counters}";
        
        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($queuePosition, $serviceType, $counters) {
            try {
                $avgServiceTime = $this->getAverageServiceTime($serviceType);
                $queueLength = max(0, $queuePosition - 1);
                
                $response = Http::timeout(5)->get(self::API_BASE_URL . '/estimate', [
                    'queue_length' => $queueLength,
                    'avg_service_time' => $avgServiceTime,
                    'counters' => $counters
                ]);
                
                if ($response->successful()) {
                    return $response->json();
                }
                
                // Fallback calculation
                return [
                    'estimated_turnaround_time_minutes' => ($queueLength * $avgServiceTime) / $counters
                ];
            } catch (\Exception $e) {
                Log::error('Failed to get wait time estimate: ' . $e->getMessage());
                return ['estimated_turnaround_time_minutes' => 0];
            }
        });
    }

    /**
     * Get analytics summary
     */
    public function getAnalyticsSummary(): array
    {
        $cacheKey = 'analytics_summary';
        
        return Cache::remember($cacheKey, 300, function () { // 5 minutes cache
            try {
                $response = Http::timeout(10)->get(self::API_BASE_URL . '/analytics/summary');
                
                if ($response->successful()) {
                    return $response->json();
                }
                
                return [];
            } catch (\Exception $e) {
                Log::error('Failed to get analytics: ' . $e->getMessage());
                return [];
            }
        });
    }

    /**
     * Get next customer to be served
     */
    public function getNextCustomer(): ?array
    {
        try {
            $response = Http::timeout(5)->get(self::API_BASE_URL . '/next-customer');
            
            if ($response->successful()) {
                $data = $response->json();
                return isset($data['message']) ? null : $data;
            }
            
            return null;
        } catch (\Exception $e) {
            Log::error('Failed to get next customer: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Update service counter configuration
     */
    public function updateServiceCounters(int $counters): bool
    {
        try {
            $response = Http::timeout(10)->post(self::API_BASE_URL . '/settings/counters', [
                'counters' => $counters
            ]);
            
            return $response->successful();
        } catch (\Exception $e) {
            Log::error('Failed to update service counters: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Check API health
     */
    public function healthCheck(): bool
    {
        try {
            $response = Http::timeout(5)->get(self::API_BASE_URL . '/health');
            return $response->successful();
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Map Laravel customer type to FastAPI customer type
     */
    private function mapCustomerType(string $type): string
    {
        $mapping = [
            'student' => 'walk_in',
            'alumni' => 'returning',
            'staff' => 'vip',
            'walk_in' => 'walk_in',
            'appointment' => 'appointment',
        ];

        return $mapping[$type] ?? 'walk_in';
    }

    /**
     * Map service type to FastAPI service categories
     */
    private function mapServiceType(string $serviceType): string
    {
        $mapping = [
            'transcript' => 'technical',
            'certification' => 'general',
            'student_documents' => 'general',
            'alumni_documents' => 'consultation',
            'verification' => 'consultation',
        ];

        return $mapping[$serviceType] ?? 'general';
    }

    /**
     * Get average service time based on document type
     */
    private function getAverageServiceTime(string $serviceType): int
    {
        $serviceTimes = [
            'student_documents' => 8,
            'alumni_documents' => 12,
            'transcript' => 15,
            'certification' => 10,
            'verification' => 8,
            'general' => 10,
            'consultation' => 20,
            'technical' => 25,
            'premium' => 15,
        ];

        return $serviceTimes[$serviceType] ?? 10;
    }

    /**
     * Format wait time for display
     */
    public function formatWaitTime(float $minutes): string
    {
        if ($minutes < 1) {
            return 'Less than 1 minute';
        } elseif ($minutes < 60) {
            return round($minutes) . ' minutes';
        } else {
            $hours = floor($minutes / 60);
            $remainingMinutes = $minutes % 60;
            return $hours . 'h' . ($remainingMinutes > 0 ? ' ' . round($remainingMinutes) . 'm' : '');
        }
    }
}