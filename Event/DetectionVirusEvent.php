<?php

namespace CL\Bundle\TissueBundle\Event;

use CL\Bundle\TissueBundle\Validator\Constraints\CleanFile;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Virus Detection Event.
 *
 * @copyright Evozon Systems SRL (http://www.evozon.com/)
 * @author    Constantin Bejenaru <constantin.bejenaru@evozon.com>
 */
class DetectionVirusEvent extends Event
{
    /**
     * @var UploadedFile
     */
    private $uploadedFile;

    /**
     * @var CleanFile
     */
    private $constraint;

    /**
     * Constructor.
     *
     * @param UploadedFile $uploadedFile
     * @param CleanFile    $constraint
     */
    public function __construct(UploadedFile $uploadedFile, CleanFile $constraint)
    {
        $this->uploadedFile = $uploadedFile;
        $this->constraint = $constraint;
    }

    /**
     * Get UploadedFile.
     *
     * @return UploadedFile
     */
    public function getUploadedFile(): UploadedFile
    {
        return $this->uploadedFile;
    }

    /**
     * Get Constraint.
     *
     * @return CleanFile
     */
    public function getConstraint(): CleanFile
    {
        return $this->constraint;
    }
}
