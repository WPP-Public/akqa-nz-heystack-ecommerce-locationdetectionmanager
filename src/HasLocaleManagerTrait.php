<?php

namespace Heystack\LocationDetection;

/**
 * @package Heystack\LocationDetection
 */
trait HasLocaleManagerTrait
{
    /**
     * @var \Heystack\LocationDetection\LocaleManager
     */
    protected $localeManager;

    /**
     * @return \Heystack\LocationDetection\LocaleManager|null
     */
    public function getLocaleManager()
    {
        return $this->localeManager;
    }

    /**
     * @param \Heystack\LocationDetection\LocaleManager $localeManager
     */
    public function setLocaleManager(LocaleManager $localeManager)
    {
        $this->localeManager = $localeManager;
    }
}