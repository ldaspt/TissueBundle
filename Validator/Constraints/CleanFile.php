<?php

/*
 * This file is part of the CLTissueBundle.
 *
 * (c) Cas Leentfaar <info@casleentfaar.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CL\Bundle\TissueBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraints\File;

/**
 * @Annotation
 * @Target({"PROPERTY", "METHOD", "ANNOTATION"})
 */
class CleanFile extends File
{
    /**
     * @var string
     */
    public $virusDetectedMessage = 'This file contains a virus.';

    /**
     * @var string
     */
    public $invalidFilenameMessage = 'This file does not have a valid name.';

    /**
     * @var bool
     */
    public $restrictedFilename = false;

    /**
     * @var bool
     */
    public $autoRemove = false;

    /**
     * @var string
     */
    public $restrictedFilenameRegex = '/^[\p{L}\p{N}\p{P}\p{S} ]{2,250}\.\w{3,4}$/';

    /**
     * {@inheritdoc}
     */
    public function validatedBy(): string
    {
        return 'clean_file';
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultOption(): string
    {
        return 'autoRemove';
    }
}
