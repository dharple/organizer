<?php

namespace Tests\Unit\Console\Commands;

use App\Console\Commands\VerbosityTrait;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Output\OutputInterface;

#[AllowMockObjectsWithoutExpectations]
class VerbosityTraitTest extends TestCase
{
    /**
     * Creates an anonymous consumer of VerbosityTrait with the given verbosity level.
     */
    private function makeConsumer(int $verbosity): object
    {
        $output = $this->createMock(OutputInterface::class);
        $output->method('isDebug')->willReturn($verbosity >= OutputInterface::VERBOSITY_DEBUG);
        $output->method('isVeryVerbose')->willReturn($verbosity >= OutputInterface::VERBOSITY_VERY_VERBOSE);
        $output->method('isVerbose')->willReturn($verbosity >= OutputInterface::VERBOSITY_VERBOSE);

        return new class ($output) {
            use VerbosityTrait;

            /**
             * Constructor.
             */
            public function __construct(private OutputInterface $output)
            {
            }

            /**
             * Returns the output interface.
             */
            public function getOutput(): OutputInterface
            {
                return $this->output;
            }

            /**
             * Calls getVerbosityFlag() and returns the result.
             */
            public function callGetVerbosityFlag(): ?string
            {
                return $this->getVerbosityFlag();
            }
        };
    }

    /**
     * Tests that normal verbosity returns null.
     */
    public function testNormalVerbosityReturnsNull(): void
    {
        $consumer = $this->makeConsumer(OutputInterface::VERBOSITY_NORMAL);
        $this->assertNull($consumer->callGetVerbosityFlag());
    }

    /**
     * Tests that verbose verbosity returns '-v'.
     */
    public function testVerboseReturnsV(): void
    {
        $consumer = $this->makeConsumer(OutputInterface::VERBOSITY_VERBOSE);
        $this->assertSame('-v', $consumer->callGetVerbosityFlag());
    }

    /**
     * Tests that very verbose verbosity returns '-vv'.
     */
    public function testVeryVerboseReturnsVV(): void
    {
        $consumer = $this->makeConsumer(OutputInterface::VERBOSITY_VERY_VERBOSE);
        $this->assertSame('-vv', $consumer->callGetVerbosityFlag());
    }

    /**
     * Tests that debug verbosity returns '-vvv'.
     */
    public function testDebugReturnsVVV(): void
    {
        $consumer = $this->makeConsumer(OutputInterface::VERBOSITY_DEBUG);
        $this->assertSame('-vvv', $consumer->callGetVerbosityFlag());
    }

    /**
     * Tests that quiet verbosity returns null.
     */
    public function testQuietVerbosityReturnsNull(): void
    {
        $consumer = $this->makeConsumer(OutputInterface::VERBOSITY_QUIET);
        $this->assertNull($consumer->callGetVerbosityFlag());
    }
}
