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
 * #Include in template
 *
 * <code>
 *  <html data-namespace-typo3-fluid="true"
 *       xmlns:sj="http://typo3.org/ns/SvenJuergens/SjViewhelpers/ViewHelpers"
 *  >
 * </code>
 *
 * <code>
 * <sj:format.firstWordWrap wrap="<strong>|</strong>"> {headline} </sj:format.firstWordWrap>
 * </code>
 * <output>
 *
 * </output>
 */
class FirstWordWrapViewHelper extends AbstractViewHelper
{
    /**
     * Initialize arguments
     * @throws \TYPO3Fluid\Fluid\Core\ViewHelper\Exception
     */
    public function initializeArguments(): void
    {
        $this->registerArgument(
            'content',
            'string',
            'Content string',
            false
        );
        $this->registerArgument(
            'wrap',
            'string',
            'A wrap value - [part 1] | [part 2]',
            false
        );
    }
    /**
     * Wrap the First Word
     *
     * @return string
     */
    public function render(): string
    {
        if ($this->arguments['content'] === null) {
            $this->arguments['content'] = $this->renderChildren();
        }
        if ($this->arguments['wrap'] !== null) {
            $wrapArr = GeneralUtility::trimExplode('|', $this->arguments['wrap']);
            $tempContent = GeneralUtility::trimExplode(' ', $this->renderChildren());
            if (\count($wrapArr) > 0 &&
                \count($tempContent) > 0
            ) {
                $this->arguments['content'] = $wrapArr[0] . $tempContent[0] . $wrapArr[1];
                unset($tempContent[0]);
                $this->arguments['content'] .= ' ' . implode(' ', $tempContent);
            }
        }
        return $this->arguments['content'];
    }
}
