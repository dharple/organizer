<?php

namespace Tests\Unit\Utility;

use App\Utility\Separator;
use PHPUnit\Framework\TestCase;

class SeparatorTest extends TestCase
{
    /**
     * Tests that no title produces a string of repeated dashes.
     */
    public function testNoTitleProducesRepeatedDashes(): void
    {
        $result = Separator::generate();
        $this->assertSame(str_repeat('-', Separator::WIDTH), $result);
    }

    /**
     * Tests that an empty title produces a string of repeated dashes.
     */
    public function testEmptyTitleProducesRepeatedDashes(): void
    {
        $result = Separator::generate('');
        $this->assertSame(str_repeat('-', Separator::WIDTH), $result);
    }

    /**
     * Tests that a custom width with no title produces the correct number of dashes.
     */
    public function testCustomWidthNoTitle(): void
    {
        $result = Separator::generate(null, 20);
        $this->assertSame(str_repeat('-', 20), $result);
        $this->assertSame(20, strlen($result));
    }

    /**
     * Tests that the total width of the generated separator equals the requested width.
     */
    public function testTotalWidthEqualsRequestedWidth(): void
    {
        $result = Separator::generate('Hello', Separator::WIDTH);
        $this->assertSame(Separator::WIDTH, strlen((string) $result));
    }

    /**
     * Tests that center alignment is the default.
     */
    public function testCenterAlignDefault(): void
    {
        $result = Separator::generate('Hello', 20);
        $this->assertStringContainsString('[ Hello ]', $result);
        $this->assertSame(20, strlen((string) $result));

        // phpcs:ignore Squiz.PHP.CommentedOutCode.Found
        // For center: left padding = ceil((20 - 9) / 2) = ceil(5.5) = 6, right = 5
        $this->assertStringStartsWith('------', $result);
        $this->assertStringEndsWith('-----', $result);
    }

    /**
     * Tests that explicit center alignment matches the default.
     */
    public function testCenterAlignExplicit(): void
    {
        $implicit = Separator::generate('Hello', 20);
        $explicit  = Separator::generate('Hello', 20, Separator::ALIGN_CENTER);
        $this->assertSame($implicit, $explicit);
    }

    /**
     * Tests that left alignment places a fixed number of dashes before the title.
     */
    public function testLeftAlign(): void
    {
        $result = Separator::generate('Hello', 20, Separator::ALIGN_LEFT);
        $this->assertStringContainsString('[ Hello ]', $result);
        $this->assertSame(20, strlen((string) $result));
        // Left-aligned: ALIGN_WIDTH (5) dashes before the title
        $this->assertStringStartsWith(str_repeat('-', Separator::ALIGN_WIDTH), $result);
    }

    /**
     * Tests that right alignment places a fixed number of dashes after the title.
     */
    public function testRightAlign(): void
    {
        $result = Separator::generate('Hello', 20, Separator::ALIGN_RIGHT);
        $this->assertStringContainsString('[ Hello ]', $result);
        $this->assertSame(20, strlen((string) $result));
        // Right-aligned: ALIGN_WIDTH (5) dashes after the title
        $this->assertStringEndsWith(str_repeat('-', Separator::ALIGN_WIDTH), $result);
    }

    /**
     * Tests that a title longer than the width returns the wrapped title without padding.
     */
    public function testTitleLongerThanWidthReturnsWrappedTitleOnly(): void
    {
        // title "[ AB ]" = 6 chars; width = 4 → title exceeds width
        $result = Separator::generate('AB', 4);
        $this->assertSame('[ AB ]', $result);
    }

    /**
     * Tests that a title exactly fitting the width returns the wrapped title without padding.
     */
    public function testTitleExactlyFitsWidth(): void
    {
        // "[ AB ]" is 6 chars; with width=6 titleLength == width, so no padding branch
        $result = Separator::generate('AB', 6);
        $this->assertSame('[ AB ]', $result);
    }

    /**
     * Tests that the Separator constants have the expected values.
     */
    public function testConstantsHaveExpectedValues(): void
    {
        $this->assertSame('center', Separator::ALIGN_CENTER);
        $this->assertSame('left', Separator::ALIGN_LEFT);
        $this->assertSame('right', Separator::ALIGN_RIGHT);
        $this->assertSame(5, Separator::ALIGN_WIDTH);
        $this->assertSame(78, Separator::WIDTH);
    }
}
