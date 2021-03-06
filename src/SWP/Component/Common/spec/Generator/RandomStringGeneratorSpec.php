<?php

/*
 * This file is part of the Superdesk Web Publisher Common Component.
 *
 * Copyright 2015 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace spec\SWP\Component\Common\Generator;

use PhpSpec\ObjectBehavior;
use SWP\Component\Common\Generator\GeneratorInterface;
use SWP\Component\Common\Generator\RandomStringGenerator;

/**
 * @mixin RandomStringGenerator
 */
class RandomStringGeneratorSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(RandomStringGenerator::class);
    }

    public function it_should_implement_interface()
    {
        $this->shouldImplement(GeneratorInterface::class);
    }

    public function it_should_generate_code()
    {
        $this->generate(4)->shouldMatch('/^[a-z0-9]+$/');
        $this->generate(4)->shouldHaveLength(4);
    }

    public function it_should_throw_exception_when_empty_length()
    {
        $this->shouldThrow(\TypeError::class)
            ->during('generate', [null]);

        $this->shouldThrow(\TypeError::class)
            ->during('generate', ['']);
    }

    /**
     * {@inheritdoc}
     */
    public function getMatchers(): array
    {
        return [
            'haveLength' => function ($subject, $length) {
                return $length === strlen($subject);
            },
        ];
    }
}
