<?php

use Illuminate\Support\Facades\Auth;

class SamlAuth
{
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



    protected function buildSAMLResponse($authnRequest, $request)
    {
        $destination = config('saml.sp.' . base64_encode($authnRequest->getAssertionConsumerServiceURL()) . '.destination');
        $issuer = config('saml.sp.' . base64_encode($authnRequest->getAssertionConsumerServiceURL()) . '.issuer');
        $cert = config('saml.sp.' . base64_encode($authnRequest->getAssertionConsumerServiceURL()) . '.cert');
        $key = config('saml.sp.' . base64_encode($authnRequest->getAssertionConsumerServiceURL()) . '.key');

        $certificate = \LightSaml\Credential\X509Certificate::fromFile($cert);
        $privateKey = \LightSaml\Credential\KeyHelper::createPrivateKey($key, '', true);


        $response = new \LightSaml\Model\Protocol\Response();
        $response
            ->addAssertion($assertion = new \LightSaml\Model\Assertion\Assertion())
            ->setID(\LightSaml\Helper::generateID())
            ->setIssueInstant(new \DateTime())
            ->setDestination($destination)
            ->setIssuer(new \LightSaml\Model\Assertion\Issuer($issuer))
            ->setStatus(new \LightSaml\Model\Protocol\Status(new \LightSaml\Model\Protocol\StatusCode('urn:oasis:names:tc:SAML:2.0:status:Success')))
            ->setSignature(new \LightSaml\Model\XmlDSig\SignatureWriter($certificate, $privateKey));

        if (Auth::check()) {
            $email = Auth::user()->email;
            $name = Auth::user()->name;
        } else {
            $email = $request['email'];
            $name = 'Place Holder';
        }
        $assertion
            ->setId(\LightSaml\Helper::generateID())
            ->setIssueInstant(new \DateTime())
            ->setIssuer(new \LightSaml\Model\Assertion\Issuer($issuer))
            ->setSubject(
                (new \LightSaml\Model\Assertion\Subject())
                    ->setNameID(new \LightSaml\Model\Assertion\NameID(
                        $email,
                        \LightSaml\SamlConstants::NAME_ID_FORMAT_EMAIL
                    ))
                    ->addSubjectConfirmation(
                        (new \LightSaml\Model\Assertion\SubjectConfirmation())
                            ->setMethod(\LightSaml\SamlConstants::CONFIRMATION_METHOD_BEARER)
                            ->setSubjectConfirmationData(
                                (new \LightSaml\Model\Assertion\SubjectConfirmationData())
                                    ->setInResponseTo($authnRequest->getId())
                                    ->setNotOnOrAfter(new \DateTime('+1 MINUTE'))
                                    ->setRecipient($authnRequest->getAssertionConsumerServiceURL())
                            )
                    )
            );
        $this->sendSAMLResponse($response);
    }
    function sendSAMLResponse($response)
    {
        $bindingFactory = new \LightSaml\Binding\BindingFactory();
        $postBinding = $bindingFactory->create(\LightSaml\SamlConstants::BINDING_SAML2_HTTP_POST);
        $messageContext = new \LightSaml\Context\Profile\MessageContext();
        $messageContext->setMessage($response)->asResponse();

        /** @var \Symfony\Component\HttpFoundation\Response $httpResponse */
        $httpResponse = $postBinding->send($messageContext);
        print $httpResponse->getContent() . "\n\n";
    }
}
