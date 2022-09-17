<?php

namespace Google;

use Google\Ads\GoogleAds\Lib\OAuth2TokenBuilder;
use Google\Ads\GoogleAds\Lib\V11\GoogleAdsClient;
use Google\Ads\GoogleAds\Lib\V11\GoogleAdsClientBuilder;

/**
 * 
 */
class Ads extends Base
{
    // private $property = '';

    function __construct()
    {
        // parent::__construct('Ads');

        // $this->property || $this->property = $this->property();
    }

    public function get(): array
    {
        // $oAuth2Credential = (new OAuth2TokenBuilder())
        // ->withClientId('724605947131-58l1t8r05ogjjlhisu524ne3t252p0i2.apps.googleusercontent.com')
        // ->withClientSecret('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')
        // ->withRefreshToken('1//0eeBvjsyXh765CgYIARAAGA4SNwF-L9IrVrjKWtm7fcgLR6cJl1oBxF1z8aTnkoKCv7OJOsg5Ka7H0Jpv5-aob5t-6D1sDijostI')
        // ->build();

        // $googleAdsClient = (new GoogleAdsClientBuilder())
        // ->withDeveloperToken('C4kI2P2JyMu3BNWZnwHD1Q')
        // ->withLoginCustomerId('4785624135')
        // ->withOAuth2Credential($oAuth2Credential)
        // ->build();
        // cookie('name', 'value');

        $oAuth2Credential = (new OAuth2TokenBuilder())
        ->withClientId('617074691565-o817as5l50cm881nqskia0hqlvg52a5l.apps.googleusercontent.com')
        ->withClientSecret('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')
        ->withRefreshToken('1//0eHK7unF10ditCgYIARAAGA4SNwF-L9IrW0I4EzDkMt6IxhAjyZCGEMVlCZtAYq9avKc_ppCmmkiUVn-QmldeMdj6MNEkWaKR_2c')
        ->build();

        $googleAdsClient = (new GoogleAdsClientBuilder())
        ->withDeveloperToken('C4kI2P2JyMu3BNWZnwHD1Q')
        ->withLoginCustomerId('7052590237')
        ->withOAuth2Credential($oAuth2Credential)
        ->build();

        $response = $googleAdsClient->getGoogleAdsServiceClient()->search(
            '6360368676',
            'SELECT customer_client.client_customer, customer_client.level,'
            . ' customer_client.manager, customer_client.descriptive_name,'
            . ' customer_client.currency_code, customer_client.time_zone,'
            . ' customer_client.id FROM customer_client WHERE customer_client.level <= 1',
            [
                'pageSize' => 200,
                // Requests to return the total results count. This is necessary to
                // determine how many pages of results exist.
                'returnTotalResultsCount' => true,
                // There is no need to go over the pages we already know the page tokens for.
                // Fetches the last page we know the page token for so that we can retrieve the
                // token of the page that comes after it.
            ]
        )->iterateAllElements();

        exit(json_encode($response));

        var_dump($response);die;

        return [];
    }
}