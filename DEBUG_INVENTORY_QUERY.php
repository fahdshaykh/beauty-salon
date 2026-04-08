// ==================== DEBUG VERSION ====================
// Use this to identify the issue
// Replace inventory_record() with this temporarily

public function inventory_record(Request $request)
{
    $request->validate([
        'start_date' => 'required|date',
        'end_date' => 'required|date|after_or_equal:start_date',
        'inventory_id' => 'nullable|integer|exists:inventories,id',
    ]);

    $start_date = $request->start_date;
    $end_date = $request->end_date;
    $inventory_id = $request->inventory_id;

    // DEBUG: Let's test without any filters first
    $testQuery = DB::table('inventory_books as ib')
        ->join('service_bookings as sb', 'ib.booking_id', '=', 'sb.id')
        ->select('ib.*', 'sb.due_date', 'sb.service_status', 'sb.company_id')
        ->limit(5);

    $testResults = $testQuery->get();
    dd('Test Results:', $testResults);

    // REAL QUERY WITH DEBUGGING
    $query = DB::table('inventory_books as ib')
        ->join('inventories as inv', 'ib.inventory_id', '=', 'inv.id')
        ->join('service_bookings as sb', 'ib.booking_id', '=', 'sb.id')
        ->join('customers as c', 'sb.customer_id', '=', 'c.id')
        ->join('users as u', 'sb.user_id', '=', 'u.id')
        ->where('sb.company_id', auth()->user()->company_id)
        ->where('sb.service_status', '!=', 5)
        ->select([
            'ib.id as inventory_book_id',
            'inv.name as inventory_name',
            'ib.quantity',
            'ib.booking_id',
            'c.first_name',
            'c.last_name',
            'u.name as employee_name',
            'sb.due_date as booking_date',
            'sb.due_time as booking_time',
            'sb.total_price',
            'sb.service_status',
            'sb.company_id as debug_company_id',
            'sb.created_at'
        ]);

    // Filter by date range
    if ($start_date && $end_date) {
        $query->whereDate('sb.due_date', '>=', $start_date)
               ->whereDate('sb.due_date', '<=', $end_date);
    }

    // Filter by specific inventory if selected
    if ($inventory_id) {
        $query->where('ib.inventory_id', $inventory_id);
    }

    // Order by booking date descending
    $query->orderBy('sb.due_date', 'desc');

    // Show SQL query for debugging
    $sqlQuery = $query->toSql();
    dd('SQL Query:', $sqlQuery);

    $inventory_records = $query->get();

    dd('Total Records Found: ' . count($inventory_records), 'Records:', $inventory_records);
}
