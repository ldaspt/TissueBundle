<?php

namespace CL\Bundle\TissueBundle;

/**
 * Events.
 *
 * @copyright Evozon Systems SRL (http://www.evozon.com/)
 * @author    Constantin Bejenaru <constantin.bejenaru@evozon.com>
 */
final class Events
{
    /**
     * Dispatched when a virus detection is found during an upload file validation.
     */
    const DETECTION_VIRUS = 'tissue.detection.virus';
}
