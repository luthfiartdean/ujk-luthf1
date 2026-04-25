<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\LaundryOrderController;
use App\Models\User;

/*
|--------------------------------------------------------------------------
| Redirect Root
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return redirect('/login');
});

/*
|--------------------------------------------------------------------------
| GUEST (belum login)
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {

    // Halaman
    Route::view('/login', 'auth.login')->name('login');
    Route::view('/register', 'auth.register')->name('register');

    // Login
    Route::post('/login', function (Request $request) {

        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->route('orders.index');
        }

        return back()->with('error', 'Email atau password salah.');
    });

    // Register
    Route::post('/register', function (Request $request) {

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('orders.index');
    });
});

/*
|--------------------------------------------------------------------------
| LOGOUT (harus login)
|--------------------------------------------------------------------------
*/
Route::post('/logout', function (Request $request) {
    Auth::logout();

    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect()->route('login');
})->middleware('auth')->name('logout');

/*
|--------------------------------------------------------------------------
| ORDERS (SESSION AUTH)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    Route::get('/orders', [LaundryOrderController::class, 'index'])->name('orders.index');
    Route::post('/orders', [LaundryOrderController::class, 'store'])->name('orders.store');
    Route::put('/orders/{id}', [LaundryOrderController::class, 'update'])->name('orders.update');
    Route::delete('/orders/{id}', [LaundryOrderController::class, 'destroy'])->name('orders.destroy');

});

/*
|--------------------------------------------------------------------------
| OPTIONAL PAGE (bebas)
|--------------------------------------------------------------------------
*/
Route::get('/services-page', function () {
    return view('services.index');
})->name('services.page');