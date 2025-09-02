<?php

// app/Http/Controllers/DashboardController.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller {
  public function index(Request $req){
    $user = $req->user();
    $accounts = $user->accounts->keyBy('type');
    return view('dashboard', compact('user','accounts'));
  }
}
