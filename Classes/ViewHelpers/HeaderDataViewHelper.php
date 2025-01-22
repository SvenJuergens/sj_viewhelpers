<?php

namespace SvenJuergens\SjViewhelpers\ViewHelpers;

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
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * \TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper
 *
 * ViewHelper to render data in <head> section of website
 *
 * # Example: Basic example
 * # Include in template
 *
 * Original from EXT:news
 *
 * <code>
 *  <html data-namespace-typo3-fluid="true"
 *       xmlns:sj="http://typo3.org/ns/SvenJuergens/SjViewhelpers/ViewHelpers"
 *  >
 * </code>
 *
 * <code>
 * <sj:headerData>
 *        <link rel="alternate"
 *            type="application/rss+xml"
 *            title="RSS 2.0"
 *            href="<f:uri.page additionalParams="{type:9818}"/>" />
 * </sj:headerData>
 * </code>
 * <output>
 * Added to the header: <link rel="alternate" type="application/rss+xml" title="RSS 2.0" href="uri to this page and type 9818" />
 * </output>
 */
class HeaderDataViewHelper extends AbstractViewHelper
{
    /**
     * Renders HeaderData
     * @throws \InvalidArgumentException
     */
    public function render(): void
    {
        $pageRenderer = GeneralUtility::makeInstance(PageRenderer::class);
        $pageRenderer->addHeaderData($this->renderChildren());
    }
}
