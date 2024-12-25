<?php

namespace SvenJuergens\SjViewhelpers\ViewHelpers\Link;

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
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * ### TypolinkViewhelper
 *
 * Renders a link with the TypoLink function.
 * Can be used with the LinkWizard
 * Original form ext:vhs
 *
 * # Example: Basic example
 * # Include in template
 *
 * <code>
 *  <html data-namespace-typo3-fluid="true"
 *       xmlns:sj="http://typo3.org/ns/SvenJuergens/SjViewhelpers/ViewHelpers"
 *  >
 * <code>
 * {namespace sj=SvenJuergens\SjViewhelpers\ViewHelpers}
 * </code>
 *
 *  <sj:link.typolink configuration="{typoLinkConfiguration}" />
 *  <sj:link.typolink configuration="{object}">My LinkText</sj:link.typolink>
*/
class TypolinkViewHelper extends AbstractViewHelper
{
    /**
     * @var bool
     */
    protected $escapeOutput = false;

    /**
     * Initializes the arguments for the ViewHelper
     * @throws \TYPO3Fluid\Fluid\Core\ViewHelper\Exception
     */
    public function initializeArguments(): void
    {
        $this->registerArgument(
            'configuration',
            'array',
            'The typoLink configuration',
            true
        );
    }

    /**
     * @return mixed
     */
    public function render()
    {
        return $GLOBALS['TSFE']->cObj->typoLink(trim($this->renderChildren()), $this->arguments['configuration']);
    }
}
