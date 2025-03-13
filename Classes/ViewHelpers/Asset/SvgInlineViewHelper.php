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
use TYPO3\CMS\Core\Resource\Security\SvgSanitizer;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Service\ImageService;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3Fluid\Fluid\Core\ViewHelper\Exception;

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
 * <code>
 * <sj:asset.svgInline setAriaHidden="true" setRole="false" src="{encore:asset(pathToFile: 'build/Icons/decorative.svg')}"/>
 * </code>
 *
 * <output>
 *
 * </output>
 */
class SvgInlineViewHelper extends AbstractViewHelper
{
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
    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerArgument('image', 'object', 'a FAL object');
        $this->registerArgument('path', 'string', 'Path to the Folder with the svg File');
        $this->registerArgument('src', 'string', 'a path to a file, overwrites Path');
        $this->registerArgument('class', 'string', 'Css class for the svg', false, null);
        $this->registerArgument('width', 'string', 'Width of the svg.', false, null);
        $this->registerArgument('height', 'string', 'Height of the svg.', false, null);
        $this->registerArgument('title', 'string', 'Title tag of the svg.', false, null);
        $this->registerArgument('description', 'string', 'Description of the svg.', false, null);
        $this->registerArgument('setRole', 'bool', 'Add role="img" to the svg.', false, true);
        $this->registerArgument('useSvgSanitizer', 'bool', 'use advanced SvgSanitizer', false, true);
        $this->registerArgument('setAriaHidden', 'bool', 'Add aria-hidden="true" to the svg.', false, false);
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function render(): string
    {
        $src = '';
        if (!empty($this->arguments['path'])) {
            $src = $this->arguments['path'] . trim($this->renderChildren()) . '.svg';
        }
        if (!empty($this->arguments['src'])) {
            $src = (string)$this->arguments['src'];
        }
        $image = $this->arguments['image'];
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
            if ($this->arguments['useSvgSanitizer'] === true) {
                $svgContent = (new SvgSanitizer())->sanitizeContent($svgContent);
            } else {
                $svgContent = trim(preg_replace('/<script[\s\S]*?>[\s\S]*?<\/script>/i', '', $svgContent));
            }

            // Exit if file does not contain content
            if (empty($svgContent)) {
                return '';
            }

            $svgElement = simplexml_load_string($svgContent);
            if (!$svgElement instanceof \SimpleXMLElement) {
                return '';
            }

            // Override attributes
            if ($this->arguments['class'] !== null) {
                $class = $this->arguments['class'];
                $class = htmlspecialchars($class ?? '');
                $svgElement = self::setAttribute($svgElement, 'class', $class);
            }
            if ($this->arguments['width']) {
                $width = $this->arguments['width'];
                $width = ((int)$width) > 0 ? (string)((int)$width) : null;
                $svgElement = self::setAttribute($svgElement, 'width', $width);
            }

            if ($this->arguments['height'] !== null) {
                $height = $this->arguments['height'];
                $height = ((int)$height) > 0 ? (string)((int)$height) : null;
                $svgElement = self::setAttribute($svgElement, 'height', $height);
            }

            if ($this->arguments['setRole'] === true) {
                $svgElement = self::setAttribute($svgElement, 'role', 'img');
            }
            if ($this->arguments['setAriaHidden'] === true) {
                $svgElement = self::setAttribute($svgElement, 'aria-hidden', 'true');
            }

            if ($this->arguments['description'] !== null) {
                $description = $this->arguments['description'];
                $description = htmlspecialchars($description ?? '');
                $svgElement = self::setChild($svgElement, 'desc', $description);
            }

            if ($this->arguments['title'] !== null) {
                $title = $this->arguments['title'];
                $title = htmlspecialchars($title ?? '');
                $svgElement = self::setChild($svgElement, 'title', $title);
            }

            // remove xml version tag
            $domXml = dom_import_simplexml($svgElement);
            /** @phpstan-ignore-next-line */
            if (!$domXml instanceof \DOMElement || !$domXml->ownerDocument instanceof \DOMDocument) {
                return '';
            }
            return (string)$domXml->ownerDocument->saveXML($domXml->ownerDocument->documentElement);
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
     * @param string|null $value
     * @return \SimpleXMLElement
     */
    protected static function setAttribute(\SimpleXMLElement $element, string $attribute, ?string $value): \SimpleXMLElement
    {
        if ($value !== null) {
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
     * @param string|null $value
     * @return \SimpleXMLElement
     */
    protected static function setChild(\SimpleXMLElement $element, string $child, ?string $value): \SimpleXMLElement
    {
        if ($value !== null) {
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
        return GeneralUtility::makeInstance(ImageService::class);
    }
}
