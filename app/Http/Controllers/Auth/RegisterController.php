<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Models\Client;
use App\Models\Invitation;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Manager;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function registerNewInvitedUser(Request $request, $token)
    {
        $validatedData = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone' => ['required', 'string', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        $decodedMetadata = json_decode($request->input('metadata'), true);

        $user = User::create([
            'first_name' => $validatedData['first_name'],
            'last_name' => $validatedData['last_name'],
            'company_id' => $decodedMetadata['inviter']['company_id'],
            'email' => $validatedData['email'],
            'phone' => $validatedData['phone'],
            'password' => Hash::make($validatedData['password']),
        ]);

        // make the user a client or manager based on the inviter's user type
        if ($decodedMetadata['inviter']['type'] == 1) {
            // make this user client
            Client::create([
                'user_id' => $user->id
            ]);
        } else if ($decodedMetadata['inviter']['type'] == 2) {
            // make this user as manager
            Manager::create([
                'user_id' => $user->id,
                'role' => $decodedMetadata['manager_type']
            ]);
        }

        // delete the token data from the invitations table
        Invitation::where('id', $token)->delete();

        // login and redirect to dashboard
        Auth::login($user);

        return redirect()->route('dashboard');
    }
}
