<?php

declare(strict_types=1);

namespace Metricool\Services;

use GuzzleHttp\Exception\GuzzleException;
use Metricool\Http\Metricool\MetricoolApi;

/**
 * @phpstan-type MetricoolPlan object{
 *   id: string,
 *   name: string,
 *   billingCycles: int,
 *   price: float,
 *   profiles: int,
 *   tweets: int,
 *   maxPostsPerBrand: int,
 *   shareProfiles: bool,
 *   whiteLabel: bool,
 *   influencers: bool,
 *   instagramBioLink: bool,
 *   reportTemplate: bool,
 *   currency: string,
 *   reportDownload: bool,
 *   historyMaxDays: int|null,
 *   influencersRestrictions: object{
 *     twitter: int,
 *     facebook: int,
 *     youtube: int,
 *     instagram: int,
 *     twitch: int,
 *     bluesky: int,
 *     threads: int
 *   },
 *   stripePriceId: string,
 *   level: int,
 *   annualDiscountPercent: float|null,
 *   maxAICopiesByBrandPerMonth: int,
 *   maxAICreditsPerBrand: int,
 *   twitterEnabled: bool,
 *   twitterPricePerBrand: float,
 *   twitterStripePriceId: string,
 *   advancedOrHigher: bool,
 *   apiEnabled: bool,
 *   yearly: bool,
 *   datastudioConnectorGranted: bool,
 *   agenciesPlan: bool
 * }
 *
 * @phpstan-type MetricoolSubscription object{
 *   id: string,
 *   provider: string|null,
 *   providerUrl: string,
 *   userId: int,
 *   created: int,
 *   status: string,
 *   paymentMethodToken: string|null,
 *   planId: string,
 *   nextPaymentDate: int,
 *   nextPaymentAmount: float,
 *   vatRate: float,
 *   addOn: string,
 *   company: string|null,
 *   country: string|null,
 *   state: string|null,
 *   address: string|null,
 *   vatNumber: string,
 *   plan: MetricoolPlan,
 *   immediatelyCancelable: bool,
 *   downgradeSubscription: mixed,
 *   twitterAddonCount: int,
 *   isTrialInProgress: bool,
 *   trialInProgress: bool,
 *   enterpriseAtScale: bool,
 *   starterPlan: bool,
 *   apiEnabled: bool,
 *   braintreeProvided: bool,
 *   stripeProvided: bool,
 *   standardPriceSubscription: bool
 * }
 *
 * @phpstan-type MetricoolUser object{
 *   id: int,
 *   name: string,
 *   lastName: string,
 *   mail: string,
 *   language: string,
 *   timezone: string,
 *   company: string|null,
 *   country: string|null,
 *   state: string|null,
 *   address: string|null,
 *   vat: string,
 *   subscription: MetricoolSubscription,
 *   payment: mixed,
 *   isWhiteLabel: bool,
 *   whiteLabelSettings: mixed,
 *   isBeta: bool,
 *   sharedWithUser: bool,
 *   vendastaUser: bool,
 *   vendastaSettings: mixed,
 *   hashtagBalance: int,
 *   vaxRate: int,
 *   activeBrands: int,
 *   beta: bool,
 *   whiteLabel: bool
 * }
 */
class MetricoolUserService
{
    public const METRICOOL_USER_OPTION = 'metricool_user';

    /** @var MetricoolUser|null */
    private ?object $user;
    private MetricoolApi $api;

    public function __construct(MetricoolApi $api)
    {
        $this->api = $api;
        $this->user = get_option(self::METRICOOL_USER_OPTION, null);
    }

    public function updateUserFromApi(): void
    {
        if (!$this->api->hasAuthentication()) {
            return;
        }

        try {
            $user = $this->api->user()->get();
        } catch (GuzzleException $e) {
            // If the request fails, we don't want to update the user data, but we also don't want to break the plugin.
            return;
        }

        $this->storeUser($user);
    }

    /** @return MetricoolUser|null */
    public function getUser(): ?object
    {
        return $this->user;
    }

    public function storeUser(object $user): void
    {
        $this->user = $user;
        update_option(self::METRICOOL_USER_OPTION, $user);
    }

    /**
     * Returns if the user is paid
     */
    public function isPremium(): bool
    {
        return $this->user !== null
            && $this->user->subscription->plan->name !== 'FREE';
    }
}
