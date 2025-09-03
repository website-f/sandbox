<?php

// app/Http/Controllers/ReferralController.php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Referral;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class ReferralController extends Controller
{
  // app/Http/Controllers/ReferralController.php
    public function tree(Request $request)
    {
        $user = $request->user();
        $rootReferral = $user->referral->root_id 
            ? Referral::where('user_id', $user->referral->root_id)->first() 
            : $user->referral;

        if (!$rootReferral) {
            return response()->json(['nodes' => []]);
        }

        $nodes = [];
        $queue = [$rootReferral];

        while (!empty($queue)) {
            $ref = array_shift($queue);

            $nodes[] = [
    'id' => $ref->user->id,
    'name' => $ref->user->name,
    'level' => $ref->level,
    'parent_id' => $ref->parent_id,  // <-- add this
];


            // Fetch children based on parent_id = user_id
            $children = Referral::where('parent_id', $ref->user_id)->get();

            foreach ($children as $child) {
                $queue[] = $child;
            }
        }

        return response()->json(['nodes' => $nodes]);
    }




  public function qr(Request $req){
    $code = optional($req->user()->referral)->ref_code;
    $link = route('register', ['ref'=>$code]);

    $svg = QrCode::format('svg')->size(250)->generate($link);
    return response($svg, 200)->header('Content-Type', 'image/svg+xml');
  }
}
