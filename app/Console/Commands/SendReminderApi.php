<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SendReminderApi extends Command
{
   /**
    * The name and signature of the console command.
    *
    * @var string
    */
   protected $signature = 'reminder:send';

   /**
    * The console command description.
    *
    * @var string
    */
   protected $description = 'Reminder for approval waiting, to all superiors';

   /**
    * Execute the console command.
    *
    * @return int
    */
   public function handle()
   {
      $notificationHour = setting('site.notification-hour', '09:00');
      $reminderFrequent = (int) setting('site.notification-reminder-frequent', 1);

      $now = now('Asia/Jakarta');
      $targetTime = $now->copy()->setTimeFromTimeString($notificationHour);
      $today = $now->toDateString();

      // Sudah pernah dikirim hari ini?
      if (DB::table('notification_runs')->where('date', $today)->exists()) {
         $this->info("Reminder sudah dikirim hari ini.");
         return;
      }

      // Cek apakah hari ini sesuai jadwal (tiap N hari)
      $startDate = now()->startOfYear();
      $diffInDays = $startDate->diffInDays($now);

      if ($diffInDays % $reminderFrequent !== 0) {
         $this->info("Hari ini bukan jadwal pengiriman reminder.");
         return;
      }

      // Toleransi waktu lebih lebar biar aman
      $minutesDiff = abs($now->diffInMinutes($targetTime));
      if ($minutesDiff > 30) {
         $this->info("Belum waktunya mengirim reminder. Sekarang: {$now->format('H:i')} Target: {$targetTime->format('H:i')}");
         return;
      }

      $wapprovals = \App\Models\WorkspaceApproval::where('status', '0')->get();
      $sapprovals = \App\Models\WorkspaceStageApproval::where('status', '0')->get();

      $allApprovals = $wapprovals->merge($sapprovals);
      $approvalsByApprover = $allApprovals->groupBy('approver_id');

      $tanurapi = new \App\Http\Controllers\Api\TanurController();

      $chunkSize = 100;
      $approverChunks = $approvalsByApprover->chunk($chunkSize);

      $successCount = 0;
      $failCount = 0;

      foreach ($approverChunks as $chunk) {
         foreach ($chunk as $approverId => $approvals) {
               $countapproval = $approvals->count();
               $subject = "{$countapproval} Approvals Pending! Tindak Lanjut Segera!";
               $message = "Terdapat {$countapproval} approval yang masih pending. Mohon segera ditindaklanjuti pada menu WPS -> Approvals.";

               try {
                  $result = $tanurapi->notify(
                     $id_agent = $approverId,
                     $email = 1,
                     $whatsapp = 1,
                     $pushnotification = 1,
                     $subject,
                     $message,
                     $forAgent = 1
                  );

                  if ($result['status'] ?? false) {
                     $successCount++;
                  } else {
                     $failCount++;
                  }
               } catch (\Throwable $e) {
                  $failCount++;
                  Log::error("Gagal kirim notifikasi ke agent {$approverId}: " . $e->getMessage());
               }
         }
         sleep(2);
      }

      // Catat sudah kirim
      DB::table('notification_runs')->insert([
         'id' => \Illuminate\Support\Str::uuid(),
         'date' => $today,
         'sent_at' => now(),
         'created_at' => now(),
         'updated_at' => now(),
      ]);

      $this->info("Notifikasi berhasil dikirim ke {$successCount} agent. Gagal: {$failCount} agent.");
   }

}
