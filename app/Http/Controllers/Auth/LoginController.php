<?php

namespace App\Http\Controllers\Auth;

use Exception;
use App\Models\User;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Events\Registered;
use Laravel\Socialite\Facades\Socialite;

class LoginController extends Controller
{
    /**
     * Redirect the user to the GitHub authentication page.
     *
     * @return \Illuminate\Http\Response
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Obtain the user information from GitHub.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Routing\Redirector
     */
    public function handleGoogleCallback()
    {
        try {
            // create a user using socialite driver google
            $user = Socialite::driver('google')->user();

            // if the user exists, use that user and login
            $findUser = User::where('google_id', $user->id)->first();

            if (!$findUser) {
                // check whether the email address available or not
                $findUser = User::where('email', $user->email)->first();

                if ($findUser) {
                    // update users table with the google id
                    $findUser->google_id = $user->id;
                    $findUser->save();
                } else {
                    // user has not been created yet, so create first
                    $findUser = User::create([
                        'first_name' => $user->user['given_name'],
                        'last_name' => $user->user['family_name'],
                        'email' => $user->email,
                        'google_id' => $user->id,
                        'password' => encrypt('')
                    ]);

                    // send email verification link
                    event(new Registered($findUser));

                    // if the project has team support (here doesn't)
                    // every user needs a team for dashboard/jetstream to work.
                    // create a personal team for the user
                    // $newTeam = Team::forceCreate([
                    //     'user_id' => $newUser->id,
                    //     'name' => explode(' ', $user->name, 2)[0] . "'s Team",
                    //     'personal_team' => true,
                    // ]);
                    // save the team and add the team to the user.
                    // $newTeam->save();
                    // $newUser->current_team_id = $newTeam->id;
                    // $newUser->save();

                    // login
                    Auth::login($findUser);

                    // redirect to take the company name
                    return redirect(URL::signedRoute('register.company-input', [
                        'user' => $findUser->id
                    ]));
                }
            }

            Auth::login($findUser);

            return redirect()->route('dashboard');
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function takeCompanyInput(Request $request, $userId)
    {
        if (!$request->hasValidSignature()) {
            abort(401);
        }

        return view('auth.company-input', [
            'user_id' => $userId
        ]);
    }

    public function createUserFromGoogleSignIn(Request $request, User $user)
    {
        $validatedData = $request->validate([
            'company_name' => ['required', 'string', 'max:255', 'unique:companies,name'],
            'phone' => ['required', 'string', 'max:255', 'unique:users'],
        ]);

        $company = Company::firstOrCreate([
            'name' => $validatedData['company_name']
        ]);

        $user->update([
            'company_id' => $company->id,
            'phone' => $validatedData['phone']
        ]);

        return redirect()->route('dashboard');
    }
}
