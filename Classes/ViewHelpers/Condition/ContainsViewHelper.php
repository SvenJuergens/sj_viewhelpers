<?php

namespace SvenJuergens\SjViewhelpers\ViewHelpers\Condition;

/*
 * This file is a copy from the famous FluidTYPO3/Vhs project.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractConditionViewHelper;

/**
 * ### Condition: String contains substring
 *
 * Condition ViewHelper which renders the `then` child if provided
 * string $haystack contains provided string $needle.
 *
 * <code>
 *  {namespace sj=SvenJuergens\SjViewhelpers\ViewHelpers}
 *  </code>
 *
 *  <sj:condition.contains haystack="{data.xyz}" needle="searchPhrase">
 *        <f:then>
 *          needle found
 *        </f:then>
 *        <f:else>
 *            needle not found
 *        </f:else>
 *    </sj:condition.contains>
 */
class ContainsViewHelper extends AbstractConditionViewHelper
{
    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerArgument('haystack', 'string', 'haystack', true);
        $this->registerArgument('needle', 'string', 'need', true);
    }

    /**
     * @param array $arguments
     * @return bool
     */
    protected static function evaluateCondition($arguments = null): bool
    {
        return is_array($arguments) &&   str_contains((string)$arguments['haystack'], (string)$arguments['needle']);
    }
}
