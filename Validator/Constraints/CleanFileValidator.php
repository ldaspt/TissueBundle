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

use CL\Tissue\Adapter\AdapterInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\FileValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * Validates whether a given file does not contain any viruses.
 */
class CleanFileValidator extends FileValidator
{
    /**
     * @var AdapterInterface|null
     */
    private $adapter;

    /**
     * @param AdapterInterface|null $adapter
     */
    public function __construct(AdapterInterface $adapter = null)
    {
        $this->adapter = $adapter;
    }

    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof CleanFile) {
            throw new UnexpectedTypeException($constraint, __NAMESPACE__.'\CleanFile');
        }

        // no uploaded file or virus scanning is disabled
        if (null === $value || '' === $value || !$this->adapter->isEnabled()) {
            return;
        }

        $path = $value instanceof File ? $value->getPathname() : (string) $value;

        if ($this->adapter->scan([$path])->hasVirus()) {
            if ($constraint->autoRemove) {
                unlink($path);
            }
            $this->context->buildViolation($constraint->virusDetectedMessage)->addViolation();

            return;
        }

        // only do the regular file-validation AFTER scanning the file
        parent::validate($value, $constraint);
    }
}
