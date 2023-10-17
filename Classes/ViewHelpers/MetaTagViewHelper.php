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
use TYPO3Fluid\Fluid\Core\ViewHelper\Exception;

/**
 * ViewHelper to render meta tags
 * Original from EXT:news
 *
 *
 * # Example: Basic Example: News title as og:title meta tag
 *
 * # Example: Basic example
 * # Include in template
 *
 * <code>
 *  <html data-namespace-typo3-fluid="true"
 *       xmlns:sj="http://typo3.org/ns/SvenJuergens/SjViewhelpers/ViewHelpers"
 *  >
 * </code>
 *
 * <code>
 *  <sj:metaTag type="property" name="og:title" content="{object.title}" />
 * </code>
 * <output>
 * <meta property="og:title" content="TYPO3 is awesome" />
 * </output>
 *
 * # Example: Using the attribute "name"
 * <code>
 *  <sj:metaTag type="name" name="keywords" content="{newsItem.keywords}" />
 * </code>
 * <output>
 * <meta name="keywords" content="news 1, news 2" />
 * </output>
 */
class MetaTagViewHelper extends AbstractViewHelper
{

    /**
     * Arguments initialization
     * @throws Exception
     */
    public function initializeArguments(): void
    {
        $this->registerArgument('type', 'string', 'The type of the meta tag. Allowed values are property, name or http-equiv', true, 'name');
        $this->registerArgument('name', 'string', 'The name of the property to add', true);
        $this->registerArgument('content', 'string', 'The content of the meta tag', false);
        $this->registerArgument('subProperties', 'array', 'Subproperties of the meta tag (like e.g. og:image:width -> {width: 400, height:400})', false);
        $this->registerArgument('replace', 'bool', 'Replace earlier set meta tag', false, true);

        $this->registerArgument('useCurrentDomain', 'boolean', 'Use current domain', false, false);
        $this->registerArgument('forceAbsoluteUrl', 'boolean', 'Force absolut domain', false, false);
    }

    /**
     * Renders a meta tag
     */
    public function render()
    {
        $useCurrentDomain = $this->arguments['useCurrentDomain'];
        $forceAbsoluteUrl = $this->arguments['forceAbsoluteUrl'];

        // set current domain
        if ($useCurrentDomain) {
            $this->arguments['content'] = GeneralUtility::getIndpEnv('TYPO3_REQUEST_URL');
        }

        // prepend current domain
        if ($forceAbsoluteUrl && !str_starts_with((string)$this->arguments['content'], (string)GeneralUtility::getIndpEnv('TYPO3_SITE_URL'))
        ) {
            $this->arguments['content'] =
                rtrim(GeneralUtility::getIndpEnv('TYPO3_SITE_URL'), '/')
                . '/'
                . ltrim($this->arguments['content'])
            ;
        }

        if ($useCurrentDomain || (isset($this->arguments['content']) && !empty($this->arguments['content']))) {
            $pageRenderer = GeneralUtility::makeInstance(PageRenderer::class);
            $pageRenderer->setMetaTag(
                (string)$this->arguments['type'],
                (string)$this->arguments['name'],
                (string)$this->arguments['content'],
                (array)$this->arguments['subProperties'],
                (bool)$this->arguments['replace']
            );
        }
    }
}
