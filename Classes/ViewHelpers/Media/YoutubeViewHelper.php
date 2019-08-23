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
 * {namespace sj=SvenJuergens\SjViewhelpers\ViewHelpers}
 * </code>
 *
 * <code>
 * <sj:media.youtube width="600" height="400"> http://youtu.be/E3vwLoDSBLQ </sj:media.youtube>
 * </code>
 * <output>
 */
class YoutubeViewHelper extends AbstractViewHelper
{
    /**
     * View helper returns HTML, thus we need to disable output escaping
     *
     * @var bool
     */
    protected $escapeOutput = false;

    /**
     * Initialize arguments.
     *
     * @api
     * @throws \TYPO3Fluid\Fluid\Core\ViewHelper\Exception
     */
    public function initializeArguments()
    {
        $this->registerArgument(
            'width',
            'integer',
            'Width of the video in pixels. Defaults to 640 for 16:9 content.',
            false,
            640
        );
        $this->registerArgument(
            'height',
            'integer',
            'Height of the video in pixels. Defaults to 385 for 16:9 content.',
            false,
            385
        );
    }

    /**
     * Render method
     *
     * @return string
     */
    public function render(): string
    {
        $width = $this->arguments['width'];
        $height = $this->arguments['height'];
        $content = '';

        $url = $this->getYoutubeUrl($this->renderChildren());

        if ($url !== null) {
            $content = '<iframe width="' . $width . '" height="' . $height . '" src="' . htmlspecialchars($url) . '" frameborder="0"></iframe>';
        }
        return $content;
    }

    /**
     * @param string $element
     * @return null|string
     */
    public function getYoutubeUrl($element)
    {
        $videoId = null;
        $youtubeUrl = null;

        if (preg_match('/(v=|v\\/|.be\\/)([^(\\&|$)]*)/', $element, $matches)) {
            $videoId = $matches[2];
        }
        if ($videoId) {
            $youtubeUrl = 'https://www.youtube-nocookie.com/embed/' . $videoId;
        }

        return $youtubeUrl;
    }
}
