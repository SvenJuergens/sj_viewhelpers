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

use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithRenderStatic;

use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * ViewHelper to include inline CSS
 *
 * # Example:
 * Include in template
 *
 *  <html data-namespace-typo3-fluid="true"
 *       xmlns:sj="http://typo3.org/ns/SvenJuergens/SjViewhelpers/ViewHelpers"
 *  >
 *
 * # Example: Basic example
 * <code>
 * <sj:asset.cssInline > .test{display:block ...} </sj:asset.cssInline>
 * </code>
 *
 * <code>
 * <sj:asset.cssInline path="{settings.cssInlineFile}"> .test{ display:block ...} </sj:asset.cssInline>
 * </code>
 *
 * <output>
 *
 * </output>
 */
class CssInlineViewHelper extends AbstractViewHelper
{
    use CompileWithRenderStatic;

    /**
     * Initialize arguments
     * @throws \TYPO3Fluid\Fluid\Core\ViewHelper\Exception
     */
    public function initializeArguments()
    {
        $this->registerArgument(
            'compress',
            'bool',
            'Define if file should be compressed',
            false,
            true
        );
        $this->registerArgument(
            'path',
            'string',
            'Path to the CSS file which should be included',
            false
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
    ) {
        $content = $renderChildrenClosure();
        if ($arguments['path'] !== null && strtolower(substr($arguments['path'], -4)) === '.css') {
            $content .= GeneralUtility::getUrl(GeneralUtility::getFileAbsFileName($arguments['path']));
        }
        $name = sha1($content);
        $compress = (bool)$arguments['compress'];
        if (!empty($content)) {
            /** @var PageRenderer $pageRenderer */
            $pageRenderer = GeneralUtility::makeInstance(PageRenderer::class);
            $pageRenderer->addCssInlineBlock($name, $content, $compress);
        }
    }
}
