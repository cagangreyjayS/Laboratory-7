use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\InventoryApiController;

Route::get('/dashboard/stats', [InventoryApiController::class, 'stats']);
Route::get('/products/low-stock', [InventoryApiController::class, 'lowStock']);
Route::get('/transactions/recent', [InventoryApiController::class, 'recentTransactions']);