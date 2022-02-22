<?php

namespace SvenJuergens\SjViewhelpers\ViewHelpers\Media;

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
 * Implementation of youtube support
 * It's a Mix from Tx_Vhs_ViewHelpers_Media_YoutubeViewHelper
 * and Tx_News_MediaRenderer_Video_Youtube
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
 * <sj:media.youtubeVideoId> http://youtu.be/E3vwLoDSBLQ </sj:media.youtubeVideoId>
 * </code>
 * <output>
 */
class YoutubeVideoIdViewHelper extends AbstractViewHelper
{

    /**
     * Render method
     *
     * @return string
     */
    public function render(): string
    {
        return $this->getYoutubeVideoId($this->renderChildren());
    }

    /**
     * @param string $element
     * @return string|null
     */
    public function getYoutubeVideoId($element)
    {
        $videoId = null;
        if (preg_match('/(v=|v\\/|.be\\/)([^(\\&|$)]*)/', $element, $matches)) {
            $videoId = $matches[2];
        }
        return $videoId;
    }
}
