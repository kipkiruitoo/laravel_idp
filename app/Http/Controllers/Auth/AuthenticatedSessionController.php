<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use SamlAuth;
use App\Models\User;
use Illuminate\Auth\Events\Login as LoginEvent;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     *
     * @param  \App\Http\Requests\Auth\LoginRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(LoginRequest $request)
    {

        $SAMLRequest = $request->query("SAMLRequest");

        $user = User::where('national_id', $request->national_id)->first();
        if (User::where('national_id', $request->national_id)->exists()) {
            $user = User::where('national_id', $request->national_id)->first();

            if ($user->password == hash("sha256", $request->password)) {



                Auth::login($user);
                $request->session()->regenerate();

                event(new LoginEvent("web", $user, true));
            } else {
                return redirect()->back()->withErrors(["national_id" => "Wrong Password or ID Number"])->withQuery(['SAMLRequest' =>  $SAMLRequest]);
            }
        } else {
            return redirect()->back()->withErrors(["national_id" => "Wrong Password or ID Number"])->withQuery(['SAMLRequest' =>  $SAMLRequest]);
        }
    }

    /**
     * Destroy an authenticated session.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }


    // handle the saml request
    protected function handleSAMLRequest($request)
    {
        $SAML = $request->SAMLRequest;
        $decoded = base64_decode($SAML);
        $xml = gzinflate($decoded);

        $deserializationContext = new \LightSaml\Model\Context\DeserializationContext();
        $deserializationContext->getDocument()->loadXML($xml);

        $authnRequest = new \LightSaml\Model\Protocol\AuthnRequest();

        $authnRequest->deserialize($deserializationContext->getDocument()->firstChild, $deserializationContext);

        $this->buildSAMLResponse($authnRequest, $request);
    }
}
