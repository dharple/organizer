<?php

namespace Tests\Unit\Utility;

use App\Utility\Separator;
use PHPUnit\Framework\TestCase;

class SeparatorTest extends TestCase
{
    public function testNoTitleProducesRepeatedDashes(): void
    {
        $result = Separator::generate();
        $this->assertSame(str_repeat('-', Separator::WIDTH), $result);
    }

    public function testEmptyTitleProducesRepeatedDashes(): void
    {
        $result = Separator::generate('');
        $this->assertSame(str_repeat('-', Separator::WIDTH), $result);
    }

    public function testCustomWidthNoTitle(): void
    {
        $result = Separator::generate(null, 20);
        $this->assertSame(str_repeat('-', 20), $result);
        $this->assertSame(20, strlen($result));
    }

    public function testTotalWidthEqualsRequestedWidth(): void
    {
        $result = Separator::generate('Hello', Separator::WIDTH);
        $this->assertSame(Separator::WIDTH, strlen((string) $result));
    }

    public function testCenterAlignDefault(): void
    {
        $result = Separator::generate('Hello', 20);
        $this->assertStringContainsString('[ Hello ]', $result);
        $this->assertSame(20, strlen((string) $result));

        // For center: left padding = ceil((20 - 9) / 2) = ceil(5.5) = 6, right = 5
        $this->assertStringStartsWith('------', $result);
        $this->assertStringEndsWith('-----', $result);
    }

    public function testCenterAlignExplicit(): void
    {
        $implicit = Separator::generate('Hello', 20);
        $explicit  = Separator::generate('Hello', 20, Separator::ALIGN_CENTER);
        $this->assertSame($implicit, $explicit);
    }

    public function testLeftAlign(): void
    {
        $result = Separator::generate('Hello', 20, Separator::ALIGN_LEFT);
        $this->assertStringContainsString('[ Hello ]', $result);
        $this->assertSame(20, strlen((string) $result));
        // Left-aligned: ALIGN_WIDTH (5) dashes before the title
        $this->assertStringStartsWith(str_repeat('-', Separator::ALIGN_WIDTH), $result);
    }

    public function testRightAlign(): void
    {
        $result = Separator::generate('Hello', 20, Separator::ALIGN_RIGHT);
        $this->assertStringContainsString('[ Hello ]', $result);
        $this->assertSame(20, strlen((string) $result));
        // Right-aligned: ALIGN_WIDTH (5) dashes after the title
        $this->assertStringEndsWith(str_repeat('-', Separator::ALIGN_WIDTH), $result);
    }

    public function testTitleLongerThanWidthReturnsWrappedTitleOnly(): void
    {
        // title "[ AB ]" = 6 chars; width = 4 → title exceeds width
        $result = Separator::generate('AB', 4);
        $this->assertSame('[ AB ]', $result);
    }

    public function testTitleExactlyFitsWidth(): void
    {
        // "[ AB ]" is 6 chars; with width=6 titleLength == width, so no padding branch
        $result = Separator::generate('AB', 6);
        $this->assertSame('[ AB ]', $result);
    }

    public function testConstantsHaveExpectedValues(): void
    {
        $this->assertSame('center', Separator::ALIGN_CENTER);
        $this->assertSame('left', Separator::ALIGN_LEFT);
        $this->assertSame('right', Separator::ALIGN_RIGHT);
        $this->assertSame(5, Separator::ALIGN_WIDTH);
        $this->assertSame(78, Separator::WIDTH);
    }
}
