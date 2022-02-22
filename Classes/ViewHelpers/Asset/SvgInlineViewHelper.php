<?php

namespace SvenJuergens\SjViewhelpers\ViewHelpers\Asset;

/**
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use TYPO3\CMS\Core\Resource\Exception\ResourceDoesNotExistException;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Service\ImageService;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3Fluid\Fluid\Core\ViewHelper\Exception;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithRenderStatic;

/**
 * ViewHelper to include inline SVG
 * Original form bootstrap_package
 *
 * # Example:
 * Include in template
 *
 * <code>
 *  <html data-namespace-typo3-fluid="true"
 *       xmlns:sj="http://typo3.org/ns/SvenJuergens/SjViewhelpers/ViewHelpers"
 *  >
 * </code>
 *
 * # Example: Basic example
 *
 * <code>
 * <sj:asset.svgInline path="{settings.svgInlinePathToFolder}">contentIconPin</sj:asset.svgInline>
 * </code>
 *
 * <output>
 *
 * </output>
 */
class SvgInlineViewHelper extends AbstractViewHelper
{
    use CompileWithRenderStatic;

    /**
     * View helper returns HTML, thus we need to disable output escaping
     *
     * @var bool
     */
    protected $escapeOutput = false;

    /**
     * Initialize arguments.
     *
     * @throws Exception
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('image', 'object', 'a FAL object');
        $this->registerArgument('path', 'string', 'Path to the Folder with the svg File');
        $this->registerArgument('src', 'string', 'a path to a file, overwrites Path');
        $this->registerArgument('class', 'string', 'Css class for the svg');
        $this->registerArgument('width', 'string', 'Width of the svg.', false);
        $this->registerArgument('height', 'string', 'Height of the svg.', false);
        $this->registerArgument('title', 'string', 'Title tag of the svg.', false);
        $this->registerArgument('description', 'string', 'Description of the svg.', false);
        $this->registerArgument('setRole', 'bool', 'Add role="img" to the svg.', false, true);
    }

    /**
     * @param array $arguments
     * @param \Closure $renderChildrenClosure
     * @param RenderingContextInterface $renderingContext
     * @return string
     * @throws \Exception
     */
    public static function renderStatic(
        array $arguments,
        \Closure $renderChildrenClosure,
        RenderingContextInterface $renderingContext
    ) {
        $src = '';
        if (!empty($arguments['path'])) {
            $src = $arguments['path'] . trim($renderChildrenClosure()) . '.svg';
        }
        if (!empty($arguments['src'])) {
            {
            $src = (string)$arguments['src'];
        }
        }

        $image = $arguments['image'];

        if (($src === '' && $image === null) || ($src !== '' && $image !== null)) {
            throw new Exception('You must either specify a string src or a File object.', 1530601100);
        }

        try {
            $imageService = self::getImageService();
            $image = $imageService->getImage($src, $image, false);
            if ($image->getProperty('extension') !== 'svg') {
                return '';
            }

            $svgContent = $image->getContents();
            $svgContent = trim(preg_replace('/<script[\s\S]*?>[\s\S]*?<\/script>/i', '', $svgContent));

            // Exit if file does not contain content
            if (empty($svgContent)) {
                return '';
            }

            // Disables the functionality to allow external entities to be loaded when parsing the XML, must be kept
            $previousValueOfEntityLoader = libxml_disable_entity_loader(true);
            $svgElement = simplexml_load_string($svgContent);
            libxml_disable_entity_loader($previousValueOfEntityLoader);

            // Override css class
            $svgElement = self::setAttribute($svgElement, 'class', filter_var(trim((string)$arguments['class']), FILTER_SANITIZE_STRING));
            $svgElement = self::setAttribute($svgElement, 'width', (int)$arguments['width']);
            $svgElement = self::setAttribute($svgElement, 'height', (int)$arguments['height']);

            if ($arguments['setRole'] === true) {
                $svgElement = self::setAttribute($svgElement, 'role', 'img');
            }

            $svgElement = self::setChild($svgElement, 'desc', filter_var(trim((string)$arguments['description']), FILTER_SANITIZE_STRING));
            $svgElement = self::setChild($svgElement, 'title', filter_var(trim((string)$arguments['title']), FILTER_SANITIZE_STRING));

            // remove xml version tag
            $domXml = dom_import_simplexml($svgElement);
            return $domXml->ownerDocument->saveXML($domXml->ownerDocument->documentElement);
        } catch (ResourceDoesNotExistException $e) {
            // thrown if file does not exist
            throw new \Exception($e->getMessage(), 1530601100, $e);
        } catch (\UnexpectedValueException $e) {
            // thrown if a file has been replaced with a folder
            throw new \Exception($e->getMessage(), 1530601101, $e);
        } catch (\RuntimeException $e) {
            // RuntimeException thrown if a file is outside of a storage
            throw new \Exception($e->getMessage(), 1530601102, $e);
        } catch (\InvalidArgumentException $e) {
            // thrown if file storage does not exist
            throw new \Exception($e->getMessage(), 1530601103, $e);
        }
    }

    /**
     * @param \SimpleXMLElement $element
     * @param string $attribute
     * @param mixed $value
     * @return \SimpleXMLElement
     */
    protected static function setAttribute(\SimpleXMLElement $element, $attribute, $value): \SimpleXMLElement
    {
        if ($value) {
            if (isset($element->attributes()->$attribute)) {
                $element->attributes()->$attribute = $value;
            } else {
                $element->addAttribute($attribute, $value);
            }
        }

        return $element;
    }

    /**
     * @param \SimpleXMLElement $element
     * @param string $child
     * @param mixed $value
     * @return \SimpleXMLElement
     */
    protected static function setChild(\SimpleXMLElement $element, $child, $value): \SimpleXMLElement
    {
        if ($value) {
            if (isset($element->children()->$child)) {
                $element->children()->$child = $value;
            } else {
                // Source https://stackoverflow.com/a/6200894
                $targetDom = dom_import_simplexml($element);
                $hasChildren = $targetDom->hasChildNodes();
                $newNode = $element->addChild($child, $value);
                if ($hasChildren) {
                    $newNodeDom = $targetDom->ownerDocument->importNode(dom_import_simplexml($newNode), true);
                    $targetDom->insertBefore($newNodeDom, $targetDom->firstChild);
                    $element = simplexml_import_dom($targetDom);
                }
            }
        }
        return $element;
    }

    /**
     * Return an instance of ImageService using object manager
     *
     * @return ImageService
     */
    protected static function getImageService(): ImageService
    {
        /** @var ImageService $objectManager */
        return GeneralUtility::makeInstance(ObjectManager::class)
            ->get(ImageService::class);
    }
}
