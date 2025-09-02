<?php

// app/Http/Controllers/ReferralController.php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Referral;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class ReferralController extends Controller
{
  public function tree(Request $req){
    $user = $req->user();
    $rootId = $user->referral?->root_id ?? $user->id; // use self as root if missing

    // BFS build
    $nodes = [];
    $edges = [];
    $queue = [$rootId];
    $seen = [];

    while ($queue) {
        $uid = array_shift($queue);
        if (isset($seen[$uid])) continue;
        $seen[$uid] = true;

        $ref = Referral::where('user_id', $uid)->first();
        if (!$ref) continue;

        $nodes[] = [
            'id' => $uid,
            'name' => $ref->user->name,
            'level' => $ref->level
        ];

        $children = Referral::where('parent_id', $uid)->get();
        foreach ($children as $c){
            $edges[] = ['from' => $uid, 'to' => $c->user_id];
            $queue[] = $c->user_id;
        }
    }

    return response()->json(['nodes' => $nodes, 'edges' => $edges]);
}


  public function qr(Request $req){
    $code = optional($req->user()->referral)->ref_code;
    $link = route('register', ['ref'=>$code]);

    $svg = QrCode::format('svg')->size(250)->generate($link);
    return response($svg, 200)->header('Content-Type', 'image/svg+xml');
  }
}
