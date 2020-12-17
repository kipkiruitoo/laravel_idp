<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use SamlAuth;
use Illuminate\Support\Facades\Auth;

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

        // if (isset($request['SAMLRequest'])) {
        //     new SamlAuth($request);
        // } else {
            $request->authenticate();

            $request->session()->regenerate();

            return redirect(RouteServiceProvider::HOME);
        // }
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
