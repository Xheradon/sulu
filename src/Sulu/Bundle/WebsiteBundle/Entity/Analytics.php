<?php

/*
 * This file is part of Sulu.
 *
 * (c) Sulu GmbH
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Sulu\Bundle\WebsiteBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use JMS\Serializer\Annotation\Exclude;
use JMS\Serializer\Annotation\VirtualProperty;

/**
 * Analytics.
 */
class Analytics
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $title;

    /**
     * @var bool
     */
    private $allDomains;

    /**
     * @var string
     *
     * @Exclude
     */
    private $content;

    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $webspaceKey;

    /**
     * @var Collection|Domain[]
     *
     * @Exclude
     */
    private $domains;

    public function __construct()
    {
        $this->domains = new ArrayCollection();
    }

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set title.
     *
     * @param string $title
     *
     * @return self
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set allDomains.
     *
     * @param bool $allDomains
     *
     * @return self
     */
    public function setAllDomains($allDomains)
    {
        $this->allDomains = $allDomains;

        return $this;
    }

    /**
     * Get allDomains.
     *
     * @return bool
     */
    public function isAllDomains()
    {
        return $this->allDomains;
    }

    /**
     * Set content.
     *
     * @param string $content
     *
     * @return self
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content.
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set type.
     *
     * @param string $type
     *
     * @return self
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type.
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set webspace-key.
     *
     * @param string $webspaceKey
     *
     * @return self
     */
    public function setWebspaceKey($webspaceKey)
    {
        $this->webspaceKey = $webspaceKey;

        return $this;
    }

    /**
     * Get webspace-key.
     *
     * @return string
     */
    public function getWebspaceKey()
    {
        return $this->webspaceKey;
    }

    /**
     * Add domain.
     *
     * @return self
     */
    public function addDomain(Domain $domain)
    {
        if ($this->domains->contains($domain)) {
            return $this;
        }

        $this->domains[] = $domain;

        return $this;
    }

    /**
     * Remove domain.
     */
    public function removeDomain(Domain $domain)
    {
        $this->domains->removeElement($domain);
    }

    /**
     * Removes all domains.
     */
    public function clearDomains()
    {
        $this->domains->clear();
    }

    /**
     * Get domains.
     *
     * @return Collection|Domain[]|null
     *
     * @VirtualProperty
     */
    public function getDomains()
    {
        if (0 === \count($this->domains)) {
            return null;
        }

        return $this->domains->map(function (Domain $domain) {
            return $domain->getUrl();
        });
    }
}
