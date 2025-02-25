<?php

/*
 * This file is part of Sulu.
 *
 * (c) Sulu GmbH
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Sulu\Bundle\AudienceTargetingBundle\Rule;

use DeviceDetector\DeviceDetector;
use DeviceDetector\Parser\Client\Browser;
use Sulu\Bundle\AudienceTargetingBundle\Rule\Type\SingleSelect;
use Symfony\Contracts\Translation\TranslatorInterface;

class BrowserRule implements RuleInterface
{
    const BROWSER = 'browser';

    private static $browsers = ['Chrome', 'Firefox', 'Internet Explorer', 'Opera', 'Safari'];

    /**
     * @var DeviceDetector
     */
    private $deviceDetector;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    public function __construct(DeviceDetector $deviceDetector, TranslatorInterface $translator)
    {
        $this->deviceDetector = $deviceDetector;
        $this->translator = $translator;
    }

    public function evaluate(array $options)
    {
        if (!\array_key_exists(static::BROWSER, $options)) {
            return false;
        }

        $clientShortName = $this->deviceDetector->getClient('short_name');
        if (!$clientShortName) {
            return false;
        }

        $browser = Browser::getBrowserFamily($clientShortName);

        return $browser == $options[static::BROWSER];
    }

    public function getName()
    {
        return $this->translator->trans('sulu_audience_targeting.browser', [], 'admin');
    }

    public function getType()
    {
        return new SingleSelect(static::BROWSER, \array_map(function ($browser) {
            return [
                'id' => $browser,
                'name' => $browser,
            ];
        }, static::$browsers));
    }
}
