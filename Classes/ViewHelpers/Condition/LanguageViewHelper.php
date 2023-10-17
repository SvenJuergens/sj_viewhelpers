<?php

namespace SvenJuergens\SjViewhelpers\ViewHelpers\Condition;

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

use TYPO3\CMS\Core\Site\Entity\SiteLanguage;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractConditionViewHelper;
use TYPO3Fluid\Fluid\Core\ViewHelper\Exception;

/**
 * If ViewHelper for LanguageCode
 * Inspired by Maximilian Kalus, http://www.auxnet.de/typo3-sprachen-viewhelper-fuer-fluid/
 *
 * # Example: Basic example
 * # Include in template
 *
 * <code>
 * {namespace sj=SvenJuergens\SjViewhelpers\ViewHelpers}
 * </code>
 *
 * <sj:condition.language value="de">
 *       <f:then>
 *         <f:render partial="de" />
 *       </f:then>
 *       <f:else>
 *           <f:render partial="en" />
 *       </f:else>
 *   </sj:condition.language>
 */
class LanguageViewHelper extends AbstractConditionViewHelper
{
    /**
     * Initialize arguments
     * @throws Exception
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('value', 'string', 'Language Code');
    }

    /**
     * @param array $arguments
     * @param RenderingContextInterface $renderingContext
     * @return bool
     */
    public static function verdict(array $arguments, RenderingContextInterface $renderingContext): bool
    {
        $request = $GLOBALS['TYPO3_REQUEST'] ?? null;
        $siteLanguage = $request ? $request->getAttribute('language') : null;
        if ($siteLanguage instanceof SiteLanguage) {
            $currentLanguageCode = $siteLanguage->getTwoLetterIsoCode();
        } else {
            $currentLanguageCode = $GLOBALS['TSFE']->config['config']['language'] ?? null;
        }
        return $currentLanguageCode === $arguments['value'];
    }
}
