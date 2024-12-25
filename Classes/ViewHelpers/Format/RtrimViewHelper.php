<?php

namespace SvenJuergens\SjViewhelpers\ViewHelpers\Format;

use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithContentArgumentAndRenderStatic;

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
    use CompileWithContentArgumentAndRenderStatic;

    public function initializeArguments(): void
    {
        $this->registerArgument('content', 'string', 'String to trim');
        $this->registerArgument('characters', 'string', 'List of characters to trim, no separators, e.g. "abc123"');
    }

    /**
     * Trims content by stripping off $characters
     *
     * @param array $arguments
     * @param \Closure $renderChildrenClosure
     * @param RenderingContextInterface $renderingContext
     * @return mixed
     */
    public static function renderStatic(array $arguments, \Closure $renderChildrenClosure, RenderingContextInterface $renderingContext)
    {
        $characters = $arguments['characters'];
        $content = $renderChildrenClosure();
        if (empty($characters) === false) {
            $content = rtrim($content, $characters);
        } else {
            $content = rtrim($content);
        }
        return $content;
    }
}
