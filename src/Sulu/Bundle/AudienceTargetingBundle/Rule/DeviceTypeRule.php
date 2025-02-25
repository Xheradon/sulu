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
use Sulu\Bundle\AudienceTargetingBundle\Rule\Type\SingleSelect;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * This rule determines from which type of device the request have been sent.
 */
class DeviceTypeRule implements RuleInterface
{
    const DEVICE_TYPE = 'device_type';

    const SMARTPHONE = 'smartphone';

    const TABLET = 'tablet';

    const DESKTOP = 'desktop';

    private static $deviceTypes = [self::SMARTPHONE, self::TABLET, self::DESKTOP];

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
        if (!\array_key_exists(static::DEVICE_TYPE, $options)) {
            return false;
        }

        switch ($options[static::DEVICE_TYPE]) {
            case static::SMARTPHONE:
                return $this->deviceDetector->isSmartphone();
            case static::TABLET:
                return $this->deviceDetector->isTablet();
            case static::DESKTOP:
                return $this->deviceDetector->isDesktop();
        }

        return false;
    }

    public function getName()
    {
        return $this->translator->trans('sulu_audience_targeting.device_type', [], 'admin');
    }

    public function getType()
    {
        return new SingleSelect(static::DEVICE_TYPE, \array_map(function ($deviceTypes) {
            return [
                'id' => $deviceTypes,
                'name' => $this->translator->trans(
                    'sulu_audience_targeting.' . $deviceTypes,
                    [],
                    'admin'
                ),
            ];
        }, static::$deviceTypes));
    }
}
