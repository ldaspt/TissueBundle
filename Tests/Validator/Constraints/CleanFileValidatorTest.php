<?php

/*
 * This file is part of the CLTissueBundle.
 *
 * (c) Cas Leentfaar <info@casleentfaar.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CL\Bundle\TissueBundle\Tests\Validator\Constraints;

use CL\Bundle\TissueBundle\Validator\Constraints\CleanFile;
use CL\Bundle\TissueBundle\Validator\Constraints\CleanFileValidator;
use CL\Tissue\Adapter\MockAdapter;
use CL\Tissue\Tests\Adapter\AbstractAdapterTestCase;
use Symfony\Component\Validator\Tests\Constraints\AbstractConstraintValidatorTest;
use Symfony\Component\Validator\Validation;

class CleanFileValidatorTest extends AbstractConstraintValidatorTest
{
    /**
     * @var CleanFileValidator
     */
    protected $validator;

    /**
     * @var string
     */
    protected $cleanFile;

    /**
     * @var string
     */
    protected $infectedFile;

    protected function getApiVersion()
    {
        return Validation::API_VERSION_2_5;
    }

    protected function createValidator()
    {
        return new CleanFileValidator(new MockAdapter());
    }

    public function testNullIsValid()
    {
        $this->validator->validate(null, new CleanFile());

        $this->assertNoViolation();
    }

    public function testEmptyStringIsValid()
    {
        $this->validator->validate('', new CleanFile());

        $this->assertNoViolation();
    }

    public function testCleanFileIsValid()
    {
        $this->validator->validate(AbstractAdapterTestCase::getPathToCleanFile(), new CleanFile());

        $this->assertNoViolation();
    }

    public function testInfectedFileIsInvalid()
    {
        $this->validator->validate(AbstractAdapterTestCase::getPathToInfectedFile(), new CleanFile());

        $this->buildViolation('This file contains a virus.')->assertRaised();
    }
}
