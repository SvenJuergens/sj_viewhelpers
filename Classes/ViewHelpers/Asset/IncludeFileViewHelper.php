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
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\Resource\FilePathSanitizer;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithRenderStatic;

/**
 * ViewHelper to include a css/js file
 * Original taken from EXT:news
 *
 * # Example: Basic example
 *
 * Include in template
 *
 * <code>
 *  <html data-namespace-typo3-fluid="true"
 *       xmlns:sj="http://typo3.org/ns/SvenJuergens/SjViewhelpers/ViewHelpers"
 *  >
 * </code>
 *
 * <code>
 * <sj:asset.includeFile path="{settings.cssFile}" />
 * <sj:asset.includeFile path="//maps.google.com/maps/api/js?sensor=false&libraries=geometry&v=3.7&hnear=Deutschland" isJsFile="true"/>
 * </code>
 * <output>
 * This will include the file provided by {settings} in the footer (js) or in the header (css)
 * </output>
 */
class IncludeFileViewHelper extends AbstractViewHelper
{
    use CompileWithRenderStatic;

    /**
     * @throws \TYPO3Fluid\Fluid\Core\ViewHelper\Exception
     */
    public function initializeArguments(): void
    {
        $this->registerArgument(
            'path',
            'string',
            ' Path to the CSS/JS file which should be included'
        );
        $this->registerArgument(
            'compress',
            'boolean',
            'Define if file should be compressed',
            false,
            false
        );
        $this->registerArgument(
            'isJsFile',
            'boolean',
            'Define that file is a JS File, useful for jsFiles withs Params
             exp: //maps.googleapis.com/maps/api/js?sensor=false',
            false,
            false
        );
        $this->registerArgument(
            'external',
            'boolean',
            'Define that file is an external File, in getFileName from TYPO3 6.2
             is a check for http(s) to identify the external File, so files with //maps. ... are not included',
            false,
            false
        );
        $this->registerArgument(
            'async',
            'boolean',
            '(Since TYPO3 7.1) Allows the file to be loaded asynchronously, JS only',
            false,
            false
        );
        $this->registerArgument(
            'integrity',
            'string',
            '(Since TYPO3 7.3) Adds the integrity attribute to the script element to let
            browsers ensure subresource integrity, JS only'
        );
    }

    /**
     * @param array $arguments
     * @param \Closure $renderChildrenClosure
     * @param RenderingContextInterface $renderingContext
     */
    public static function renderStatic(
        array $arguments,
        \Closure $renderChildrenClosure,
        RenderingContextInterface $renderingContext
    ): void {
        if ($arguments['external'] === false) {
            try {
                $arguments['path'] = GeneralUtility::makeInstance(FilePathSanitizer::class)
                    ->sanitize((string)$arguments['path'], true);
            } catch (\Exception $e) {
                $arguments['path'] = null;
            }
        }
        if (empty($arguments['path'])) {
            return;
        }
        /** @var PageRenderer $pageRenderer */
        $pageRenderer = GeneralUtility::makeInstance(PageRenderer::class);
        // JS
        if ($arguments['isJsFile'] === true
            || strtolower(substr($arguments['path'], -3)) === '.js'
        ) {
            $pageRenderer->addJsFooterFile(
                $arguments['path'],
                null,
                $arguments['compress'],
                null,
                null,
                null,
                null,
                $arguments['async'],
                $arguments['integrity']
            );
            // CSS
        } elseif (strtolower(substr($arguments['path'], -4)) === '.css') {
            $pageRenderer->addCssFile(
                $arguments['path'],
                'stylesheet',
                'all',
                null,
                $arguments['compress']
            );
        }
    }
}
