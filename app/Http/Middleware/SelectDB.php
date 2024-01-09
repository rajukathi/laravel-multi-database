<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Artisan;
use App\Models\User;

class SelectDB
{

    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();


        if (!empty($user) && $user->role == "guest") {
            if ( empty($user->database_name) ) {

                //here in appservice provider one method injected for same connection with multiple db
                /*User::where('id', $user->id)->update(['database_name' => explode("@", $user->email)[0]]);
                DB::statement("CREATE DATABASE " . explode("@", $user->email)[0]);
                config(["database.connections.mysql.database" => explode("@", $user->email)[0]]);
                DB::useDatabase(explode("@", $user->email)[0]);*/

                User::where('id', $user->id)->update(['database_name' => explode("@", $user->email)[0]]);
                DB::statement("CREATE DATABASE " . explode("@", $user->email)[0]);
                config(["database.connections.guest_mysql.database" => explode("@", $user->email)[0]]);
                DB::setDefaultConnection('guest_mysql');
                Artisan::call('migrate', [
                   '--path' => 'database/migrations/2024_01_09_101108_products.php',
                   '--database' => $user->database_name,
                   '--force' => true
                ]);
            }

            config(["database.connections.guest_mysql.database" => explode("@", $user->email)[0]]);
            DB::setDefaultConnection('guest_mysql');
        }

        return $next($request);
    }
}