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
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * ViewHelper to get an Arraykey
 * Original Maximilian Kalus, http://www.auxnet.de/typo3-array-viewhelper-holt-variable-aus-array/
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
 *
 * <code>
 *  <sj:arrayElement array="{array}" key="{lang}" variableName="myValue">
 *   <p>{myValue}</p>
 *  </sj:arrayElement>
 * </code>
 * <output>
 *    "array[arrayKey]"
 * </output>
  */
class ArrayElementViewHelper extends AbstractViewHelper
{
    /**
     * Initialize arguments
     * @throws \TYPO3Fluid\Fluid\Core\ViewHelper\Exception
     */
    public function initializeArguments(): void
    {
        $this->registerArgument(
            'array',
            'array',
            'array to search in',
            true
        );
        $this->registerArgument(
            'key',
            'string',
            '$key to search for',
            true
        );
        $this->registerArgument(
            'variableName',
            'string',
            'variable name to set',
            false,
            'element'
        );
    }
    /**
     * @return string
     */
    public function render(): string
    {
        if (!\is_array($this->arguments['array'])
            || empty($this->arguments['array'])
            || !isset($this->arguments['array'][$this->arguments['key']])
        ) {
            $value = null;
        } else {
            $value = $this->arguments['array'][$this->arguments['key']];
        }

        $this->templateVariableContainer->add($this->arguments['variableName'], $value);
        $content = $this->renderChildren();
        $this->templateVariableContainer->remove($this->arguments['variableName']);
        return $content;
    }
}
