<?php

/*
 * This file is part of Sulu.
 *
 * (c) Sulu GmbH
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Sulu\Bundle\CategoryBundle\Twig;

use JMS\Serializer\SerializationContext;
use Sulu\Bundle\CategoryBundle\Category\CategoryManagerInterface;
use Sulu\Component\Cache\MemoizeInterface;
use Sulu\Component\Category\Request\CategoryRequestHandlerInterface;
use Sulu\Component\Serializer\ArraySerializerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Provides functionality to handle categories in twig templates.
 */
class CategoryTwigExtension extends AbstractExtension
{
    /**
     * @var CategoryManagerInterface
     */
    private $categoryManager;

    /**
     * @var CategoryRequestHandlerInterface
     */
    private $categoryRequestHandler;

    /**
     * @var ArraySerializerInterface
     */
    private $serializer;

    /**
     * @var MemoizeInterface
     */
    private $memoizeCache;

    public function __construct(
        CategoryManagerInterface $categoryManager,
        CategoryRequestHandlerInterface $categoryRequestHandler,
        ArraySerializerInterface $serializer,
        MemoizeInterface $memoizeCache
    ) {
        $this->categoryManager = $categoryManager;
        $this->categoryRequestHandler = $categoryRequestHandler;
        $this->serializer = $serializer;
        $this->memoizeCache = $memoizeCache;
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('sulu_categories', [$this, 'getCategoriesFunction']),
            new TwigFunction('sulu_category_url', [$this, 'setCategoryUrlFunction']),
            new TwigFunction('sulu_category_url_append', [$this, 'appendCategoryUrlFunction']),
            new TwigFunction('sulu_category_url_remove', [$this, 'removeCategoryUrlFunction']),
            new TwigFunction('sulu_category_url_toggle', [$this, 'toggleCategoryUrlFunction']),
            new TwigFunction('sulu_category_url_clear', [$this, 'clearCategoryUrlFunction']),
        ];
    }

    /**
     * Returns an array of serialized categories.
     * If parentKey is set, only the children of the category which is assigned to the given key are returned.
     *
     * @param string $locale
     * @param string $parentKey key of parent category
     *
     * @return array
     */
    public function getCategoriesFunction($locale, $parentKey = null)
    {
        return $this->memoizeCache->memoizeById(
            'sulu_categories',
            \func_get_args(),
            function ($locale, $parentKey = null) {
                $entities = $this->categoryManager->findChildrenByParentKey($parentKey);
                $categories = $this->categoryManager->getApiObjects($entities, $locale);
                $context = SerializationContext::create();
                $context->setSerializeNull(true);

                return $this->serializer->serialize($categories, $context);
            }
        );
    }

    /**
     * Extends current URL with given category.
     *
     * @param array $category will be included in the URL
     * @param string $categoriesParameter GET parameter name
     *
     * @return string
     */
    public function appendCategoryUrlFunction($category, $categoriesParameter = 'categories')
    {
        return $this->categoryRequestHandler->appendCategoryToUrl($category, $categoriesParameter);
    }

    /**
     * Removes given category from current url.
     *
     * @param array $category will be removed from the URL
     * @param string $categoriesParameter GET parameter name
     *
     * @return string
     */
    public function removeCategoryUrlFunction($category, $categoriesParameter = 'categories')
    {
        return $this->categoryRequestHandler->removeCategoryFromUrl($category, $categoriesParameter);
    }

    /**
     * Toggles given category in current URL.
     *
     * @param array $category will be toggled in the URL
     * @param string $categoriesParameter GET parameter name
     *
     * @return string
     */
    public function toggleCategoryUrlFunction($category, $categoriesParameter = 'categories')
    {
        return $this->categoryRequestHandler->toggleCategoryInUrl($category, $categoriesParameter);
    }

    /**
     * Set category to current URL.
     *
     * @param array $category will be included in the URL
     * @param string $categoriesParameter GET parameter name
     *
     * @return string
     */
    public function setCategoryUrlFunction($category, $categoriesParameter = 'categories')
    {
        return $this->categoryRequestHandler->setCategoryToUrl($category, $categoriesParameter);
    }

    /**
     * Remove categories from current URL.
     *
     * @param string $categoriesParameter GET parameter name
     *
     * @return string
     */
    public function clearCategoryUrlFunction($categoriesParameter = 'categories')
    {
        return $this->categoryRequestHandler->removeCategoriesFromUrl($categoriesParameter);
    }
}
