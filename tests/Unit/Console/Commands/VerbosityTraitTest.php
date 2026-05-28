<?php

namespace Tests\Unit\Console\Commands;

use App\Console\Commands\VerbosityTrait;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Output\OutputInterface;

#[AllowMockObjectsWithoutExpectations]
class VerbosityTraitTest extends TestCase
{
    private function makeConsumer(int $verbosity): object
    {
        $output = $this->createMock(OutputInterface::class);
        $output->method('isDebug')->willReturn($verbosity >= OutputInterface::VERBOSITY_DEBUG);
        $output->method('isVeryVerbose')->willReturn($verbosity >= OutputInterface::VERBOSITY_VERY_VERBOSE);
        $output->method('isVerbose')->willReturn($verbosity >= OutputInterface::VERBOSITY_VERBOSE);

        return new class ($output) {
            use VerbosityTrait;

            public function __construct(private OutputInterface $output)
            {
            }

            public function getOutput(): OutputInterface
            {
                return $this->output;
            }

            public function callGetVerbosityFlag(): ?string
            {
                return $this->getVerbosityFlag();
            }
        };
    }

    public function testNormalVerbosityReturnsNull(): void
    {
        $consumer = $this->makeConsumer(OutputInterface::VERBOSITY_NORMAL);
        $this->assertNull($consumer->callGetVerbosityFlag());
    }

    public function testVerboseReturnsV(): void
    {
        $consumer = $this->makeConsumer(OutputInterface::VERBOSITY_VERBOSE);
        $this->assertSame('-v', $consumer->callGetVerbosityFlag());
    }

    public function testVeryVerboseReturnsVV(): void
    {
        $consumer = $this->makeConsumer(OutputInterface::VERBOSITY_VERY_VERBOSE);
        $this->assertSame('-vv', $consumer->callGetVerbosityFlag());
    }

    public function testDebugReturnsVVV(): void
    {
        $consumer = $this->makeConsumer(OutputInterface::VERBOSITY_DEBUG);
        $this->assertSame('-vvv', $consumer->callGetVerbosityFlag());
    }

    public function testQuietVerbosityReturnsNull(): void
    {
        $consumer = $this->makeConsumer(OutputInterface::VERBOSITY_QUIET);
        $this->assertNull($consumer->callGetVerbosityFlag());
    }
}
