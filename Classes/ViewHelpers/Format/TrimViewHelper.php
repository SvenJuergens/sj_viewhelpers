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
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Trims content by stripping off $characters
 * Original from EXT:vhs
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
 * <sj:format.trim content="foo" characters="foo">
 * <!-- tag content - may be ignored! -->
 * </sj:format.trim>
 *
 *    Inline usage example
 *    {sj:format.trim(content: 'foo', characters: 'foo')}
 */
class TrimViewHelper extends AbstractViewHelper
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
            'characters',
            'string',
            'characters to trim',
            false
        );
    }
    /**
     * @return string
     */
    public function render(): string
    {
        if ($this->arguments['content'] === null) {
            $this->arguments['content'] = $this->renderChildren();
        }
        if ($this->arguments['characters'] !== null) {
            $this->arguments['content'] = trim($this->arguments['content'], $this->arguments['characters']);
        } else {
            $this->arguments['content'] = trim($this->arguments['content']);
        }
        return $this->arguments['content'];
    }
}
