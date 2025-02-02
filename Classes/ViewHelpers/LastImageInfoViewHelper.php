<?php

/*
 * This file is part of the package bk2k/bootstrap-package.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace SvenJuergens\SjViewhelpers\ViewHelpers;

use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithRenderStatic;

/**
 * ViewHelper to get the ImageInfos like width and hight
 *
 * # Example:
 * # Include in template
 *
 * Original from EXT:bootstrap_package
 *
 * <code>
 *  <html data-namespace-typo3-fluid="true"
 *       xmlns:sj="http://typo3.org/ns/SvenJuergens/SjViewhelpers/ViewHelpers"
 *  >
 * </code>
 *
 * <code>
 *  <a href="{f:uri.image(image: file, maxHeight: settings.lightbox.image.maxHeight, maxWidth: settings.lightbox.image.maxWidth)}"
 *   data-lightbox-width="{sj:lastImageInfo(property: 'width')}"
 *   data-lightbox-height="{sj:lastImageInfo(property: 'height')}"
 * >
 * </code>
 *
 * <code>
 * <img src="..." loading="lazy" width="{sj:lastImageInfo(property: 'width')}" height="{sj:lastImageInfo(property: 'height')}">
 * </code>
 *
 * @deprecated use ImageInfoViewHelper
 */
class LastImageInfoViewHelper extends AbstractViewHelper
{
    use CompileWithRenderStatic;

    /**
     * @var array
     */
    protected static $imageInfoMapping = [
        'width' => 0,
        'height' => 1,
        'type' => 2,
        'file' => 3,
        'origFile' => 'origFile',
        'origFile_mtime' => 'origFile_mtime',
        'originalFile' => 'originalFile',
        'processedFile' => 'processedFile',
        'fileCacheHash' => 'fileCacheHash',
    ];

    /**
     * Initialize arguments.
     *
     * @throws \TYPO3Fluid\Fluid\Core\ViewHelper\Exception
     */
    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerArgument('property', 'string', 'Possible values: width, height, type, file, origFile, origFile_mtime, originalFile, processedFile, fileCacheHash');
    }

    /**
     * @param array $arguments
     * @param \Closure $renderChildrenClosure
     * @param RenderingContextInterface $renderingContext
     * @return mixed
     */
    public static function renderStatic(
        array $arguments,
        \Closure $renderChildrenClosure,
        RenderingContextInterface $renderingContext
    ) {
        if (self::getTypoScriptFrontendController()->lastImageInfo) {
            $property = array_key_exists($arguments['property'], self::$imageInfoMapping)
                ? self::$imageInfoMapping[$arguments['property']]
                : self::$imageInfoMapping['file'];
            return self::getTypoScriptFrontendController()->lastImageInfo[$property];
        }
        return null;
    }

    /**
     * @return \TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController
     */
    protected static function getTypoScriptFrontendController()
    {
        return $GLOBALS['TSFE'];
    }
}
