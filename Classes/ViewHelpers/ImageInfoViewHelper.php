<?php

namespace SvenJuergens\SjViewhelpers\ViewHelpers;

use TYPO3\CMS\Core\Page\AssetCollector;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithRenderStatic;

/**
 * ViewHelper to get the ImageInfos like width and height
 *
 * # Example:
 * # Include in template
 *
 * Original from EXT:bootstrap_package
 * Require: min TYPO3 v10
 *
 * <code>
 *  <html data-namespace-typo3-fluid="true"
 *       xmlns:sj="http://typo3.org/ns/SvenJuergens/SjViewhelpers/ViewHelpers"
 *  >
 * </code>
 *
 * <code>
 *  <a href="{f:uri.image(image: file, maxHeight: settings.lightbox.image.maxHeight, maxWidth: settings.lightbox.image.maxWidth)}"
 *   data-lightbox-width="{sj:imageInfo(src: src, property: 'width')}"
 *   data-lightbox-height="{sj:imageInfo(src: src, property: 'height')}"
 * >
 * </code>
 *
 * <code>
 * <img src="..." loading="lazy" width="{sj:imageInfo(src: src, property: 'width')}" height="{sj:imageInfo(src: src, property: 'height')}">
 * </code>
 */
class ImageInfoViewHelper extends AbstractViewHelper
{
    use CompileWithRenderStatic;

    /**
     * @var array<string, int|string>
     */
    protected static $supportedProperties = [
        'width' => 0,
        'height' => 1,
        'type' => 3,
        'origFile' => 'origFile',
        'origFile_mtime' => 'origFile_mtime',
    ];

    /**
     * Initialize arguments.
     *
     * @throws \TYPO3Fluid\Fluid\Core\ViewHelper\Exception
     */
    public function initializeArguments()
    {
        $this->registerArgument('src', 'string', 'Path to a file');
        $this->registerArgument('property', 'string', 'Possible values: width, height, type, origFile, origFile_mtime');
    }

    /**
     * @param array $arguments
     * @param \Closure $renderChildrenClosure
     * @param RenderingContextInterface $renderingContext
     * @return string
     */
    public static function renderStatic(array $arguments, \Closure $renderChildrenClosure, RenderingContextInterface $renderingContext)
    {
        $src = $arguments['src'];
        $property = $arguments['property'];

        if (!array_key_exists($property, self::$supportedProperties)) {
            throw new \InvalidArgumentException('The value of property is invalid. Valid properties are: width, height, type, origFile or origFile_mtime');
        }

        $assetCollector = self::getAssetCollector();
        $mediaOnPage = $assetCollector->getMedia();

        foreach ($mediaOnPage as $mediaName => $mediaData) {
            if (str_contains($src, $mediaName)) {
                return (string)$mediaData[self::$supportedProperties[$property]];
            }
        }

        return '';
    }

    /**
     * @return AssetCollector
     */
    protected static function getAssetCollector(): AssetCollector
    {
        return GeneralUtility::makeInstance(AssetCollector::class);
    }
}
