<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ImportLegacyData extends Command
{
    protected $signature = 'app:import-legacy-data
        {--sqlite= : Path to the legacy SQLite database}
        {--orders= : Path to the legacy order-history directory}
        {--dry-run : Report what would be imported without writing}';

    protected $description = 'Safely import legacy SQLite users and JSON orders without deleting or overwriting records.';

    public function handle(): int
    {
        $sqlite = $this->option('sqlite') ?: database_path('database.sqlite');
        $ordersPath = $this->option('orders') ?: storage_path('app/private/order-history');
        $dryRun = (bool) $this->option('dry-run');

        if (! is_file($sqlite)) {
            $this->error("Legacy SQLite database not found: {$sqlite}");
            return self::FAILURE;
        }
        if (! is_dir($ordersPath)) {
            $this->error("Legacy order directory not found: {$ordersPath}");
            return self::FAILURE;
        }

        config(['database.connections.legacy_sqlite.database' => $sqlite]);
        $legacy = DB::connection('legacy_sqlite');
        $users = $legacy->table('users')->get();
        $orderFiles = glob(rtrim($ordersPath, DIRECTORY_SEPARATOR).'/*.json') ?: [];
        $orders = collect();
        foreach ($orderFiles as $file) {
            $decoded = json_decode((string) file_get_contents($file), true);
            if (is_array($decoded)) {
                $orders = $orders->merge(array_is_list($decoded) ? $decoded : [$decoded]);
            }
        }
        $orders = $orders->filter(fn ($order) => is_array($order) && filled($order['number'] ?? null))->unique('number')->values();

        $this->info(sprintf('%s mode: %d users and %d unique orders found.', $dryRun ? 'Dry-run' : 'Import', $users->count(), $orders->count()));
        if ($dryRun) {
            return self::SUCCESS;
        }

        $userCount = 0;
        foreach ($users as $user) {
            if (blank($user->email)) {
                continue;
            }
            User::updateOrCreate(
                ['email' => strtolower(trim($user->email))],
                [
                    'name' => $user->name ?: 'Franchisee account',
                    'password' => $user->password,
                    'approved' => (bool) ($user->approved ?? false),
                    'email_verified_at' => $user->email_verified_at,
                ],
            );
            $userCount++;
        }

        $orderCount = 0;
        foreach ($orders as $legacyOrder) {
            $number = (string) $legacyOrder['number'];
            Order::updateOrCreate(
                ['number' => $number],
                [
                    'email' => $legacyOrder['email'] ?? 'Franchisee account',
                    'status' => $legacyOrder['status'] ?? 'Pending',
                    'delivery_address' => $legacyOrder['delivery_address'] ?? null,
                    'special_instructions' => $legacyOrder['special_instructions'] ?? null,
                    'submitted_at' => $this->parseDate($legacyOrder['submitted_at'] ?? null),
                    'total' => $this->parseAmount($legacyOrder['total'] ?? 0),
                    'items' => $legacyOrder['items'] ?? [],
                ],
            );
            $orderCount++;
        }

        $this->info("Imported {$userCount} users and {$orderCount} orders using non-destructive upserts.");
        return self::SUCCESS;
    }

    private function parseAmount(mixed $amount): float
    {
        return (float) preg_replace('/[^0-9.\-]/', '', (string) $amount);
    }

    private function parseDate(mixed $date): ?Carbon
    {
        if (blank($date)) {
            return null;
        }
        try {
            return Carbon::parse($date);
        } catch (\Throwable) {
            return null;
        }
    }
}
