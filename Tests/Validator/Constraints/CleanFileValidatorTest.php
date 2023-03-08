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
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Tests\Constraints\AbstractConstraintValidatorTest;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;


class CleanFileValidatorTest extends ConstraintValidatorTestCase
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
        return new CleanFileValidator(
            $this->createMock(EventDispatcherInterface::class),
            new MockAdapter()
        );
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
        $this->validator->validate(new UploadedFile(__DIR__ .  '/fixtures/clean.txt', 'clean_file',null, null,null, true), new CleanFile());

        $this->assertNoViolation();
    }

    public function testInfectedFileIsInvalid()
    {
        $this->validator->validate(new UploadedFile(__DIR__ .  '/fixtures/infected.txt', 'infected_file', null, null,null, true), new CleanFile());

        $this->buildViolation('This file contains a virus.')->assertRaised();
    }
}
