<?php

/*
 * This file is part of Sulu.
 *
 * (c) Sulu GmbH
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Sulu\Bundle\CategoryBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Sulu\Component\Security\Authentication\UserInterface;

/**
 * Category.
 */
class Category implements CategoryInterface
{
    /**
     * @var int
     */
    protected $lft;

    /**
     * @var int
     */
    protected $rgt;

    /**
     * @var int
     */
    protected $depth;

    /**
     * @var \DateTime
     */
    protected $created;

    /**
     * @var \DateTime
     */
    protected $changed;

    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $key;

    /**
     * @var string
     */
    protected $defaultLocale;

    /**
     * @var CategoryInterface
     */
    protected $parent;

    /**
     * @var UserInterface
     */
    protected $creator;

    /**
     * @var UserInterface
     */
    protected $changer;

    /**
     * @var Collection|CategoryMetaInterface[]
     */
    protected $meta;

    /**
     * @var Collection|CategoryTranslationInterface[]
     */
    protected $translations;

    /**
     * @var Collection|CategoryInterface[]
     */
    protected $children;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->meta = new ArrayCollection();
        $this->translations = new ArrayCollection();
        $this->children = new ArrayCollection();
    }

    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    public function setLft($lft)
    {
        $this->lft = $lft;

        return $this;
    }

    public function getLft()
    {
        return $this->lft;
    }

    public function setRgt($rgt)
    {
        $this->rgt = $rgt;

        return $this;
    }

    public function getRgt()
    {
        return $this->rgt;
    }

    public function setDepth($depth)
    {
        $this->depth = $depth;

        return $this;
    }

    public function getDepth()
    {
        return $this->depth;
    }

    public function getCreated()
    {
        return $this->created;
    }

    public function getKey()
    {
        return $this->key;
    }

    public function setKey($key)
    {
        $this->key = $key;

        return $this;
    }

    public function setDefaultLocale($defaultLocale)
    {
        $this->defaultLocale = $defaultLocale;

        return $this;
    }

    public function getDefaultLocale()
    {
        return $this->defaultLocale;
    }

    public function getChanged()
    {
        return $this->changed;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setParent(CategoryInterface $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    public function getParent()
    {
        return $this->parent;
    }

    public function setCreator(UserInterface $creator = null)
    {
        $this->creator = $creator;

        return $this;
    }

    public function getCreator()
    {
        return $this->creator;
    }

    public function setChanger(UserInterface $changer = null)
    {
        $this->changer = $changer;

        return $this;
    }

    public function setChanged(\DateTime $changed)
    {
        $this->changed = $changed;

        return $this;
    }

    public function getChanger()
    {
        return $this->changer;
    }

    public function addMeta(CategoryMetaInterface $meta)
    {
        $this->meta[] = $meta;

        return $this;
    }

    public function removeMeta(CategoryMetaInterface $meta)
    {
        $this->meta->removeElement($meta);
    }

    public function getMeta()
    {
        return $this->meta;
    }

    public function addTranslation(CategoryTranslationInterface $translations)
    {
        $this->translations[] = $translations;

        return $this;
    }

    public function removeTranslation(CategoryTranslationInterface $translations)
    {
        $this->translations->removeElement($translations);
    }

    public function getTranslations()
    {
        return $this->translations;
    }

    public function findTranslationByLocale($locale)
    {
        return $this->translations->filter(
            function (CategoryTranslationInterface $translation) use ($locale) {
                return $translation->getLocale() === $locale;
            }
        )->first();
    }

    public function addChildren(CategoryInterface $child)
    {
        @\trigger_error(__METHOD__ . '() is deprecated since version 1.4 and will be removed in 2.0. Use addChild() instead.', \E_USER_DEPRECATED);

        $this->addChild($child);
    }

    public function addChild(CategoryInterface $child)
    {
        $this->children[] = $child;

        return $this;
    }

    public function removeChildren(CategoryInterface $child)
    {
        @\trigger_error(__METHOD__ . '() is deprecated since version 1.4 and will be removed in 2.0. Use removeChild() instead.', \E_USER_DEPRECATED);

        $this->removeChild($child);
    }

    public function removeChild(CategoryInterface $child)
    {
        $this->children->removeElement($child);
    }

    public function getChildren()
    {
        return $this->children;
    }
}
