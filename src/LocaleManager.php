<?php

namespace Heystack\LocationDetection;

use Heystack\Core\Identifier\Identifier;
use Heystack\Core\Identifier\IdentifierInterface;
use Heystack\Core\State\State;
use Heystack\Core\Traits\HasStateServiceTrait;
use Heystack\Core\ViewableData\ViewableDataInterface;
use Heystack\Ecommerce\Currency\Interfaces\CurrencyInterface;
use Heystack\Ecommerce\Currency\Interfaces\CurrencyServiceInterface;
use Heystack\Ecommerce\Currency\Traits\HasCurrencyServiceTrait;
use Heystack\Ecommerce\Locale\Interfaces\CountryInterface;
use Heystack\Ecommerce\Locale\Interfaces\LocaleDetectionInterface;
use Heystack\Ecommerce\Locale\Interfaces\LocaleServiceInterface;
use Heystack\Ecommerce\Locale\Interfaces\ZoneServiceInterface;
use Heystack\Ecommerce\Locale\Traits\HasLocaleDetectorServiceTrait;
use Heystack\Ecommerce\Locale\Traits\HasLocaleServiceTrait;
use Heystack\Ecommerce\Locale\Traits\HasZoneServiceTrait;

/**
 * @package Heystack\LocationDetection
 */
class LocaleManager
{
    const STATE_PREFIX = 'locale_manager';
    const ALLOW_OVERRIDE_KEY = 'allow_override';

    use HasStateServiceTrait;
    use HasLocaleDetectorServiceTrait;
    use HasLocaleServiceTrait;
    use HasZoneServiceTrait;
    use HasCurrencyServiceTrait;

    /**
     * @var string
     */
    protected $cookieName = 'Location';

    /**
     * @var int
     */
    protected $cookieExpiry = 90;

    /**
     * @param State $state
     * @param LocaleServiceInterface $localeService
     * @param LocaleDetectionInterface $localeDetector
     * @param ZoneServiceInterface $zoneService
     * @param CurrencyServiceInterface $currencyService
     */
    public function __construct(
        State $state,
        LocaleServiceInterface $localeService,
        LocaleDetectionInterface $localeDetector,
        ZoneServiceInterface $zoneService,
        CurrencyServiceInterface $currencyService
    )
    {
        $this->stateService = $state;
        $this->localeService = $localeService;
        $this->localeDetector = $localeDetector;
        $this->zoneService = $zoneService;
        $this->currencyService = $currencyService;
    }

    /**
     * If a cookie is already present, use the cookie to configure the environment. If the cookie isn't present
     * try to automatically detect the location. If this was successful use it, else use the default country
     * in the case of automatic detection, allow the user to override the setting
     * 
     * @param \SS_HTTPRequest $request
     */
    public function configureEnvironmentFromRequest(\SS_HTTPRequest $request)
    {
        if ($this->hasLocaleCookie()) {
            $success = $this->configureEnvironmentFromCountry(
                new Identifier($this->getLocaleCookie())
            );
            
            if ($success) {
                return;
            }
        }
        
        $country = $this->localeDetector->getCountryForRequest($request);

        if (!$country instanceof CountryInterface) {
            // This will always be valid unless the heystack configuration is somehow wrong
            $country = $this->localeService->getDefaultCountry();
        }
        
        $this->setAllowUserOverride(true);
        
        $this->configureEnvironmentFromCountry($country->getIdentifier());
    }

    /**
     * @param \Heystack\Core\Identifier\IdentifierInterface $countryIdentifier
     * @return bool
     */
    public function configureEnvironmentFromCountry(IdentifierInterface $countryIdentifier)
    {
        if (!$this->localeService->hasCountry($countryIdentifier)) {
            return false;
        }

        $this->localeService->setActiveCountry($countryIdentifier);

        $zoneCurrency = $this->zoneService->getActiveZone()->getCurrency();

        if ($zoneCurrency instanceof CurrencyInterface) {
            $this->currencyService->setActiveCurrency(
                $zoneCurrency->getIdentifier()
            );
        }
        
        $this->setLocaleCookie($countryIdentifier->getFull());
        
        return true;
    }

    /**
     * @return bool
     */
    public function hasLocaleCookie()
    {
        return (bool) $this->getLocaleCookie();
    }

    /**
     * @return string|null
     */
    public function getLocaleCookie()
    {
        return \Cookie::get($this->cookieName);
    }

    /**
     * @param $value
     */
    public function setLocaleCookie($value)
    {
        \Cookie::set($this->cookieName, $value, $this->cookieExpiry);
    }

    /**
     * @return bool
     */
    public function getAllowUserOverride()
    {
        return (bool) $this->stateService->getByKey(
            $this->getKey(self::ALLOW_OVERRIDE_KEY)
        );
    }

    /**
     * @param bool $value
     */
    public function setAllowUserOverride($value)
    {
        $this->stateService->setByKey(
            $this->getKey(self::ALLOW_OVERRIDE_KEY),
            (bool) $value
        );
    }

    /**
     * @param $key
     * @return string
     */
    protected function getKey($key)
    {
        return sprintf(
            "%s.%s",
            self::STATE_PREFIX,
            $key
        );
    }

    /**
     * @return string
     */
    public function getCookieName()
    {
        return $this->cookieName;
    }

    /**
     * @param string $cookieName
     */
    public function setCookieName($cookieName)
    {
        $this->cookieName = $cookieName;
    }

    /**
     * @return int
     */
    public function getCookieExpiry()
    {
        return $this->cookieExpiry;
    }

    /**
     * @param int $cookieExpiry
     */
    public function setCookieExpiry($cookieExpiry)
    {
        $this->cookieExpiry = (int) $cookieExpiry;
    }
}