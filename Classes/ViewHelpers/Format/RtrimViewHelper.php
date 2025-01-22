<?php

namespace SvenJuergens\SjViewhelpers\ViewHelpers\Format;

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
 * <sj:format.rtrim content="foo" characters="foo">
 * <!-- tag content - may be ignored! -->
 * </sj:format.rtrim>
 *
 *    Inline usage example
 *    {sj:format.rtrim(content: 'foo', characters: 'foo')}
 */
class RtrimViewHelper extends AbstractViewHelper
{
    public function initializeArguments(): void
    {
        $this->registerArgument('content', 'string', 'String to trim');
        $this->registerArgument('characters', 'string', 'List of characters to trim, no separators, e.g. "abc123"');
    }

    /**
     * Trims content by stripping off $characters
     *
     * @return string
     */
    public function render(): string
    {
        $characters = $this->arguments['characters'];
        $content = $this->renderChildren();
        if (empty($characters) === false) {
            $content = rtrim($content, $characters);
        } else {
            $content = rtrim($content);
        }
        return $content;
    }
}
