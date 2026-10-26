<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class HomeController extends Controller
{
    /**
     * Display the application homepage.
     */
    public function index()
    {
        return view('home');
    }

    public function adminLogin(Request $request)
    {
        if ($request->session()->has('admin_email')) {
            return redirect()->route('admin.dashboard');
        }

        return view('admin-login');
    }

    public function adminAuthenticate(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $adminEmail = (string) config('app.admin_email');
        $adminPasswordHash = (string) config('app.admin_password_hash');

        if ($adminEmail === '' || $adminPasswordHash === '' || ! hash_equals(strtolower($adminEmail), strtolower($credentials['email'])) || ! Hash::check($credentials['password'], $adminPasswordHash)) {
            return back()->withErrors(['email' => 'Invalid admin credentials.']);
        }

        $request->session()->regenerate();
        $request->session()->put('admin_email', $adminEmail);

        return redirect()->route('admin.dashboard');
    }

    public function admin(Request $request)
    {
        if (! $request->session()->has('admin_email')) {
            return redirect()->route('admin.login');
        }

        $allOrders = collect($this->loadAllOrders());
        $search = trim((string) $request->query('search', ''));
        $statusFilter = (string) $request->query('status', '');
        $range = (string) $request->query('range', '30');
        $orders = $allOrders->filter(function (array $order) use ($search, $statusFilter, $range) {
            $matchesSearch = $search === '' || str_contains(strtolower(json_encode($order)), strtolower($search));
            $matchesStatus = $statusFilter === '' || ($order['status'] ?? 'Pending') === $statusFilter;
            $matchesRange = $range === 'all' || ! isset($order['submitted_at']) || Carbon::parse($order['submitted_at'])->greaterThanOrEqualTo(now()->subDays((int) $range));
            return $matchesSearch && $matchesStatus && $matchesRange;
        })->values();
        $salesOrders = $allOrders->reject(fn ($order) => ($order['status'] ?? 'Pending') === 'Cancelled');
        $totalSales = $salesOrders->sum(fn ($order) => $this->orderAmount($order));
        $productTotals = [];
        foreach ($salesOrders as $order) {
            foreach ($order['items'] ?? [] as $item) {
                $name = $item['name'] ?? 'Unknown product';
                $productTotals[$name] = ($productTotals[$name] ?? 0) + ((int) ($item['quantity'] ?? 0));
            }
        }
        arsort($productTotals);
        $topProducts = collect($productTotals)->take(5);
        $salesChart = collect(range(6, 0))->mapWithKeys(function ($days) use ($salesOrders) {
            $date = now()->subDays($days)->format('M j');
            $total = $salesOrders->filter(fn ($order) => isset($order['submitted_at']) && Carbon::parse($order['submitted_at'])->isSameDay(now()->subDays($days)))->sum(fn ($order) => $this->orderAmount($order));
            return [$date => round($total, 2)];
        });
        $productSource = resource_path('views/orders.blade.php');
        $productCount = is_file($productSource) ? preg_match_all('/<x-order-product\b/', file_get_contents($productSource)) : 0;

        return view('admin', [
            'orders' => $orders,
            'totalSales' => '₱'.number_format($totalSales, 2),
            'totalOrders' => $allOrders->count(),
            'pendingCount' => $allOrders->where('status', 'Pending')->count(),
            'acceptedCount' => $allOrders->where('status', 'Order Accepted')->count(),
            'paymentsToVerify' => 0,
            'netProfit' => null,
            'franchiseeCount' => User::count(),
            'activeBranches' => $allOrders->pluck('email')->filter()->unique()->count(),
            'franchisees' => User::query()->orderBy('name')->get(),
            'productCount' => $productCount,
            'topProducts' => $topProducts,
            'salesChart' => $salesChart,
            'search' => $search,
            'statusFilter' => $statusFilter,
            'range' => $range,
            'adminEmail' => $request->session()->get('admin_email'),
        ]);
    }

    public function adminUpdateOrderStatus(Request $request, string $orderNumber)
    {
        if (! $request->session()->has('admin_email')) {
            return redirect()->route('admin.login');
        }

        $status = $request->validate(['status' => ['required', 'in:Pending,Order Accepted,Preparing Order,Ready for Dispatch,In Transit,Delivered,Rejected,Cancelled,On Hold']])['status'];
        $orders = $this->loadAllOrders();
        foreach ($orders as &$order) {
            if (($order['number'] ?? '') === $orderNumber) {
                $order['status'] = $status;
                $this->saveOrderFile($order['_file'] ?? '', $order);
                break;
            }
        }

        return back()->with('message', "Order {$orderNumber} marked {$status}.");
    }

    public function adminStoreFranchisee(Request $request)
    {
        if (! $request->session()->has('admin_email')) {
            return redirect()->route('admin.login');
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        User::create($data);

        return redirect()->route('admin.dashboard', ['created' => 'franchisee'])->with('message', "Franchisee {$data['name']} was added successfully.");
    }

    public function adminDeleteFranchisee(Request $request, User $user)
    {
        if (! $request->session()->has('admin_email')) {
            return redirect()->route('admin.login');
        }

        $name = $user->name;
        $user->delete();

        return redirect()->route('admin.dashboard', ['created' => 'franchisee'])->with('message', "Franchisee {$name} was removed.");
    }

    public function adminApproveFranchisee(Request $request, User $user)
    {
        if (! $request->session()->has('admin_email')) {
            return redirect()->route('admin.login');
        }

        $user->update(['approved' => true]);
        return redirect()->route('admin.dashboard', ['created' => 'franchisee'])->with('message', "Franchisee {$user->name} was approved.");
    }

    public function adminLogout(Request $request)
    {
        $request->session()->forget('admin_email');
        return redirect()->route('admin.login');
    }

    public function adminReceipt(Request $request, string $orderNumber)
    {
        if (! $request->session()->has('admin_email')) {
            return redirect()->route('admin.login');
        }

        $order = collect($this->loadAllOrders())->firstWhere('number', $orderNumber);
        abort_if(! $order, 404);

        return view('receipt', [
            'email' => $order['email'] ?? 'Franchisee account',
            'order' => $order,
        ]);
    }

    /**
     * Handle the portal sign-in form until real authentication is connected.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('email', $credentials['email'])->first();
        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            return back()->withErrors(['email' => 'The email or password is incorrect.'])->onlyInput('email');
        }
        if (! $user->approved) {
            return back()->withErrors(['email' => 'Your account is waiting for admin approval.'])->onlyInput('email');
        }

        $request->session()->regenerate();
        $request->session()->put('franchisee_email', $credentials['email']);

        return redirect()->route('dashboard');
    }

    public function register()
    {
        return view('register');
    }

    public function storeRegistration(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $data['approved'] = false;
        User::create($data);

        return redirect()->route('home')->with('message', 'Registration submitted. An admin must approve your account before you can sign in.');
    }

    public function logout(Request $request)
    {
        $request->session()->forget(['franchisee_email', 'order_submitted', 'submitted_orders']);
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }

    /**
     * Display the franchisee operations dashboard.
     */
    public function dashboard(Request $request)
    {
        if (! $request->session()->has('franchisee_email')) {
            return redirect()->route('home');
        }

        $submittedOrders = $this->loadSubmittedOrders($request);
        if (empty($submittedOrders)) {
            $submittedOrders = $request->session()->get('submitted_orders', []);
            if (empty($submittedOrders) && $request->session()->has('order_submitted')) {
                $submittedOrders = [$request->session()->get('order_submitted')];
            }
        }
        $latestOrder = $submittedOrders[array_key_last($submittedOrders)] ?? null;
        $closedStatuses = ['Delivered', 'Rejected', 'Cancelled'];
        $activeOrders = collect($submittedOrders)->reject(fn ($order) => in_array($order['status'] ?? 'Pending', $closedStatuses, true))->values();
        $deliveredOrders = collect($submittedOrders)->where('status', 'Delivered')->values();
        $latestActiveOrder = $activeOrders->last();
        $latestDeliveredOrder = $deliveredOrders->last();

        $franchisee = User::where('email', $request->session()->get('franchisee_email'))->first();

        return view('dashboard', [
            'email' => $request->session()->get('franchisee_email'),
            'branchName' => $franchisee?->name ?? 'Franchisee Branch',
            'hasSubmittedOrder' => ! empty($submittedOrders),
            'submittedOrder' => $latestOrder,
            'submittedOrders' => $submittedOrders,
            'orderCount' => $activeOrders->count(),
            'committedTotal' => '₱'.number_format($activeOrders->sum(fn ($order) => (float) str_replace([',', '₱'], '', $order['total'] ?? '0')), 2),
            'deliveredCount' => $deliveredOrders->count(),
            'latestActiveOrder' => $latestActiveOrder,
            'latestDeliveredOrder' => $latestDeliveredOrder,
        ]);
    }

    /**
     * Display the commissary order catalog.
     */
    public function orders(Request $request)
    {
        if (! $request->session()->has('franchisee_email')) {
            return redirect()->route('home');
        }

        // Opening the catalog starts a new draft after a previous order was submitted.
        $request->session()->forget('order_submitted');

        return view('orders', [
            'email' => $request->session()->get('franchisee_email'),
        ]);
    }

    /**
     * Display the order review and checkout page.
     */
    public function cart(Request $request)
    {
        if (! $request->session()->has('franchisee_email')) {
            return redirect()->route('home');
        }

        return view('cart', [
            'email' => $request->session()->get('franchisee_email'),
            'hasSubmittedOrder' => $request->session()->has('order_submitted'),
        ]);
    }

    /**
     * Display delivery tracking for the franchisee.
     */
    public function tracking(Request $request)
    {
        if (! $request->session()->has('franchisee_email')) {
            return redirect()->route('home');
        }

        $orders = $this->loadSubmittedOrders($request);
        $latestOrder = $orders[array_key_last($orders)] ?? $request->session()->get('order_submitted');

        return view('tracking', [
            'email' => $request->session()->get('franchisee_email'),
            'trackingOrder' => $latestOrder,
        ]);
    }

    /**
     * Submit the current purchase order and send the franchisee to tracking.
     */
    public function submitOrder(Request $request)
    {
        if (! $request->session()->has('franchisee_email')) {
            return redirect()->route('home');
        }

        $total = max(0, (float) $request->input('order_total', 0));

        $newOrder = [
            'number' => $this->nextOrderNumber(),
            'email' => $request->session()->get('franchisee_email'),
            'status' => 'Pending',
            'delivery_address' => $request->input('delivery_address', ''),
            'special_instructions' => $request->input('special_instructions', ''),
            'submitted_at' => now()->format('M j, Y g:i A'),
            'total' => '₱'.number_format($total, 2),
            'items' => collect(json_decode($request->input('order_items', '[]'), true) ?: [])->map(fn ($item) => [
                'name' => $item['name'] ?? 'Commissary product',
                'quantity' => (int) ($item['quantity'] ?? 1),
                'unit' => $item['unit'] ?? 'piece',
                'price' => (float) ($item['price'] ?? 0),
            ])->values()->all(),
        ];
        $submittedOrders = $this->loadSubmittedOrders($request);
        if (empty($submittedOrders)) {
            $submittedOrders = $request->session()->get('submitted_orders', []);
            if (empty($submittedOrders) && $request->session()->has('order_submitted')) {
                $submittedOrders = [$request->session()->get('order_submitted')];
            }
        }
        $submittedOrders[] = $newOrder;
        $this->saveSubmittedOrders($request, $submittedOrders);
        $request->session()->put('submitted_orders', $submittedOrders);
        $request->session()->put('order_submitted', $newOrder);

        return redirect()->route('tracking');
    }

    /**
     * Display a print-friendly receipt that can be saved as a PDF.
     */
    public function receipt(Request $request)
    {
        if (! $request->session()->has('franchisee_email')) {
            return redirect()->route('home');
        }

        if (! $request->session()->has('order_submitted')) {
            return redirect()->route('tracking');
        }

        $order = $request->session()->get('order_submitted');
        $order['items'] = is_array($order['items'] ?? null) ? $order['items'] : [];

        return view('receipt', [
            'email' => $request->session()->get('franchisee_email'),
            'order' => $order,
        ]);
    }

    /**
     * Handle the future AI generation request.
     */
    public function generate(Request $request)
    {
        $request->validate([
            'business_type' => ['required', 'string', 'max:255'],
        ]);

        return response('Generated successfully');
    }

    private function submittedOrdersPath(Request $request): string
    {
        return 'order-history/'.sha1(strtolower((string) $request->session()->get('franchisee_email'))).'.json';
    }

    private function loadSubmittedOrders(Request $request): array
    {
        $disk = Storage::disk('local');
        $path = $this->submittedOrdersPath($request);

        if (! $disk->exists($path)) {
            return [];
        }

        $orders = json_decode($disk->get($path), true);

        return is_array($orders) ? $orders : [];
    }

    private function saveSubmittedOrders(Request $request, array $orders): void
    {
        Storage::disk('local')->put($this->submittedOrdersPath($request), json_encode(array_values($orders), JSON_PRETTY_PRINT));
    }

    private function loadAllOrders(): array
    {
        $orders = [];
        foreach (Storage::disk('local')->files('order-history') as $path) {
            $emailHash = pathinfo($path, PATHINFO_FILENAME);
            $items = json_decode(Storage::disk('local')->get($path), true);
            foreach (is_array($items) ? $items : [] as $order) {
                $order['email'] = $order['email'] ?? 'Franchisee account';
                $order['status'] = $order['status'] ?? 'Pending';
                $order['_file'] = $path;
                $orders[] = $order;
            }
        }
        return array_reverse($orders);
    }

    private function orderAmount(array $order): float
    {
        return (float) str_replace([',', '₱', '$'], '', (string) ($order['total'] ?? 0));
    }

    private function saveOrderFile(string $path, array $updatedOrder): void
    {
        if ($path === '') {
            return;
        }

        $existing = Storage::disk('local')->exists($path) ? json_decode(Storage::disk('local')->get($path), true) : [];
        $existing = is_array($existing) ? $existing : [];
        foreach ($existing as &$order) {
            if (($order['number'] ?? '') === ($updatedOrder['number'] ?? '')) {
                $order = $updatedOrder;
                unset($order['_file']);
            }
        }
        Storage::disk('local')->put($path, json_encode(array_values($existing), JSON_PRETTY_PRINT));
    }

    private function nextOrderNumber(): string
    {
        $highest = 20260000;

        foreach ($this->loadAllOrders() as $order) {
            if (preg_match('/^#PO-(\d+)$/', (string) ($order['number'] ?? ''), $matches)) {
                $highest = max($highest, (int) $matches[1]);
            }
        }

        return '#PO-'.($highest + 1);
    }
}
