<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\Admin\ReportsController;
use App\Http\Controllers\Admin\PIAReportsController;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class GenerateScheduledReports extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'reports:generate 
                            {--type=all : Type of report to generate (all, daily, weekly, monthly, compliance)}
                            {--email=* : Email addresses to send reports to}
                            {--format=csv : Export format (csv, pdf)}';

    /**
     * The console command description.
     */
    protected $description = 'Generate and optionally email scheduled reports for stakeholders';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $type = $this->option('type');
        $emails = $this->option('email');
        $format = $this->option('format');

        $this->info("Generating {$type} reports in {$format} format...");

        try {
            switch ($type) {
                case 'daily':
                    $this->generateDailyReports($emails, $format);
                    break;
                case 'weekly':
                    $this->generateWeeklyReports($emails, $format);
                    break;
                case 'monthly':
                    $this->generateMonthlyReports($emails, $format);
                    break;
                case 'compliance':
                    $this->generateComplianceReports($emails, $format);
                    break;
                case 'all':
                default:
                    $this->generateAllReports($emails, $format);
                    break;
            }

            $this->info('Reports generated successfully!');
        } catch (\Exception $e) {
            $this->error('Error generating reports: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }

    private function generateDailyReports($emails, $format)
    {
        $this->info('Generating daily performance summary...');
        
        // Generate daily metrics
        $reportsController = new ReportsController();
        $response = $reportsController->export(request()->merge(['type' => 'queue_performance']));
        
        $filename = 'daily_performance_' . Carbon::now()->format('Y_m_d') . '.csv';
        $this->saveReport($response, $filename);
        
        if (!empty($emails)) {
            $this->emailReport($filename, $emails, 'Daily Performance Report');
        }
    }

    private function generateWeeklyReports($emails, $format)
    {
        $this->info('Generating weekly analytics summary...');
        
        $reportsController = new ReportsController();
        
        // Generate summary report
        $response = $reportsController->export(request()->merge(['type' => 'summary']));
        $filename = 'weekly_summary_' . Carbon::now()->format('Y_W') . '.csv';
        $this->saveReport($response, $filename);
        
        // Generate registrar performance
        $response = $reportsController->export(request()->merge(['type' => 'registrar_performance']));
        $filename2 = 'weekly_registrar_performance_' . Carbon::now()->format('Y_W') . '.csv';
        $this->saveReport($response, $filename2);
        
        if (!empty($emails)) {
            $this->emailReport($filename, $emails, 'Weekly Summary Report');
            $this->emailReport($filename2, $emails, 'Weekly Registrar Performance Report');
        }
    }

    private function generateMonthlyReports($emails, $format)
    {
        $this->info('Generating monthly comprehensive reports...');
        
        $reportsController = new ReportsController();
        
        // Generate all main reports
        $reportTypes = ['summary', 'document_types', 'processing_times', 'revenue'];
        $filenames = [];
        
        foreach ($reportTypes as $type) {
            $response = $reportsController->export(request()->merge(['type' => $type]));
            $filename = "monthly_{$type}_" . Carbon::now()->format('Y_m') . '.csv';
            $this->saveReport($response, $filename);
            $filenames[] = $filename;
        }
        
        if (!empty($emails)) {
            foreach ($filenames as $filename) {
                $reportName = ucwords(str_replace(['monthly_', '_', Carbon::now()->format('Y_m')], ['', ' ', ''], $filename));
                $this->emailReport($filename, $emails, "Monthly {$reportName} Report");
            }
        }
    }

    private function generateComplianceReports($emails, $format)
    {
        $this->info('Generating PIA compliance reports...');
        
        $piaController = new PIAReportsController();
        
        // Generate compliance report
        $response = $piaController->exportCompliance(request());
        $filename = 'pia_compliance_' . Carbon::now()->format('Y_m_d') . '.csv';
        $this->saveReport($response, $filename);
        
        // Generate operational efficiency report
        $response = $piaController->exportOperational(request());
        $filename2 = 'operational_efficiency_' . Carbon::now()->format('Y_m_d') . '.csv';
        $this->saveReport($response, $filename2);
        
        if (!empty($emails)) {
            $this->emailReport($filename, $emails, 'PIA Compliance Report');
            $this->emailReport($filename2, $emails, 'Operational Efficiency Report');
        }
    }

    private function generateAllReports($emails, $format)
    {
        $this->info('Generating all available reports...');
        
        $this->generateDailyReports($emails, $format);
        $this->generateWeeklyReports($emails, $format);
        $this->generateComplianceReports($emails, $format);
    }

    private function saveReport($response, $filename)
    {
        // Create reports directory if it doesn't exist
        $reportsPath = storage_path('app/reports');
        if (!file_exists($reportsPath)) {
            mkdir($reportsPath, 0755, true);
        }
        
        // Save the report content
        $content = $response->getContent();
        file_put_contents($reportsPath . '/' . $filename, $content);
        
        $this->line("Report saved: {$filename}");
    }

    private function emailReport($filename, $emails, $subject)
    {
        $this->info("Emailing {$filename} to stakeholders...");
        
        try {
            $filePath = storage_path('app/reports/' . $filename);
            
            foreach ($emails as $email) {
                // Note: This would require setting up a mail class
                // For now, just log the action
                $this->line("Would email {$subject} to {$email}");
            }
        } catch (\Exception $e) {
            $this->error("Failed to email report: " . $e->getMessage());
        }
    }
}