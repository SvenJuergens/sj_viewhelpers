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
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * ViewHelper to include a inline JS file
 *
 *
 * Include in template
 *
 * <code>
 *  <html data-namespace-typo3-fluid="true"
 *       xmlns:sj="http://typo3.org/ns/SvenJuergens/SjViewhelpers/ViewHelpers"
 *  >
 * </code>
 * <code>
 * <sj:asset.jsInline position="bottom" path="{settings.jsInlineFile}"> ... </sj:asset.jsInline>
 * </code>
 * <output>
 *
 * </output>
 */
class JsInlineViewHelper extends AbstractViewHelper
{
    /**
     * @throws \TYPO3Fluid\Fluid\Core\ViewHelper\Exception
     */
    public function initializeArguments()
    {
        $this->registerArgument(
            'position',
            'string',
            'Optional: if is "bottom", then js is inserted in Footer',
            false,
            null
        );
        $this->registerArgument(
            'compress',
            'boolean',
            'Define if file should be compressed',
            false,
            true
        );
        $this->registerArgument(
            'path',
            'string',
            'Path to the JS file which should be included',
            false,
            null
        );
    }

    /**
     * Include JS Content
     * @throws \InvalidArgumentException
     */
    public function render()
    {
        $content = trim($this->renderChildren());
        if ($this->arguments['path'] !== null
            && strtolower(substr($this->arguments['path'], -3)) === '.js'
        ) {
            $content .= GeneralUtility::getUrl(GeneralUtility::getFileAbsFileName($this->arguments['path']));
        }
        $name = md5(GeneralUtility::minifyJavaScript($content));

        /** @var PageRenderer $pageRenderer */
        $pageRenderer = GeneralUtility::makeInstance(PageRenderer::class);
        if ($this->arguments['position'] === 'bottom') {
            $pageRenderer->addJsFooterInlineCode($name, $content, $this->arguments['compress']);
        } else {
            $pageRenderer->addJsInlineCode($name, $content, $this->arguments['compress']);
        }
    }
}
