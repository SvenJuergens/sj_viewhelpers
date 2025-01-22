<?php

namespace SvenJuergens\SjViewhelpers\ViewHelpers\Format;

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
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * ViewHelper to wrap the FirstWord of a Text
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
 *  <sj:format.explode contentString="{teaser.bodytext}" by="PHP_EOL" isAConstant="true" returnAs="elements">
 *  <f:for each="{elements}" as="element">
 *    <p>{element}</p>
 *  </f:for>
 *  </sj:format.explode>
 * </code>
 * <output>
 *
 * </output>
 */
class ExplodeViewHelper extends AbstractViewHelper
{
    /**
     * Initialize arguments
     * @throws \TYPO3Fluid\Fluid\Core\ViewHelper\Exception
     */
    public function initializeArguments(): void
    {
        $this->registerArgument(
            'contentString',
            'string',
            'to change to array',
            false
        );
        $this->registerArgument(
            'by',
            'string',
            'Explode String by this',
            false
        );
        $this->registerArgument(
            'isAConstant',
            'bool',
            'is "$by" a Constant like PHP_EOL',
            false,
            false
        );
        $this->registerArgument(
            'returnAs',
            'string',
            'variable name to set',
            false,
            'elements'
        );
    }

    /**
     * Make an array out of String by Linebreak
     *
     * @return string
     */
    public function render(): string
    {
        if ($this->arguments['contentString'] === null) {
            return $this->renderChildren();
        }
        if ($this->arguments['by'] === null) {
            $this->arguments['by'] = 'PHP_EOL';
            $this->arguments['isAConstant'] = true;
        }
        $value = GeneralUtility::trimExplode(
            ($this->arguments['isAConstant'] ? \constant($this->arguments['by']) : $this->arguments['by']),
            $this->arguments['contentString']
        );

        $this->templateVariableContainer->add($this->arguments['returnAs'], $value);
        $content = $this->renderChildren();
        $this->templateVariableContainer->remove($this->arguments['returnAs']);
        return $content;
    }
}
