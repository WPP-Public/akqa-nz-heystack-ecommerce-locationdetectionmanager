<?php

namespace Heystack\LocationDetection;

use Heystack\Core\Identifier\Identifier;
use Heystack\Core\Input\ProcessorInterface;
use Heystack\Ecommerce\Locale\Traits\HasLocaleServiceTrait;

class SetCountryInputProcessor implements ProcessorInterface
{
    use HasLocaleManagerTrait;
    use HasLocaleServiceTrait;

    /**
     * Returns the identifier of the processor
     * @return \Heystack\Core\Identifier\Identifier
     */
    public function getIdentifier()
    {
        return new Identifier('country');
    }

    /**
     * Executes the main functionality of the input processor
     * @param  \SS_HTTPRequest $request Request to process
     * @return mixed
     */
    public function process(\SS_HTTPRequest $request)
    {
        if (!$countyCode = $request->param('ID')) {
            return false;
        }
        
        if (!$this->localeManager->getAllowUserOverride()) {
            return false;
        }
        
        $success = $this->localeManager->configureEnvironmentFromCountry(new Identifier($countyCode));
        
        $this->localeManager->setAllowUserOverride(false);
        
        return $success;
    }
}