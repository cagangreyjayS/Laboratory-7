protected function schedule(Schedule $schedule)
{
    $schedule->command('inventory:check-reorder')->daily();
}