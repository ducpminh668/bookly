<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;

class RemindBookingCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'remindBooking:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $bookings = \App\Models\Booking::where('booking_date', '>', Carbon::now()->subHour(1000))->get();
        $basic  = new \Vonage\Client\Credentials\Basic("30756336", "J99lp3fW7VpJzEDn");
        $client = new \Vonage\Client($basic);

        // send sms
        foreach ($bookings as $item) {
            $response = $client->sms()->send(
                new \Vonage\SMS\Message\SMS($item->phone_number, 'BOOKLY', 'Chào Đức, Bookly nhắn tin để thông báo bạn có lịch hẹn với Bookly vào lúc 15h 30p. Rất hân hạnh được phục vụ bạn')
            );

            $message = $response->current();

            if ($message->getStatus() == 0) {
            } else {
                \Log::warning("The message failed with status: " . $message->getStatus() . "\n");
            }
        }

        return 0;
    }
}
