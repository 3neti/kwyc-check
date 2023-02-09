<?php

namespace Tests\Unit;

use App\Enums\ChannelEnum;
use App\Enums\FormatEnum;
use Tests\TestCase;

class EnumTest extends TestCase
{
    /** @test */
    public function application_has_enumerated_channels()
    {
        $this->assertEquals('Storage', ChannelEnum::STORAGE->value);
        $this->assertEquals('Email', ChannelEnum::EMAIL->value);
        $this->assertEquals('SMS', ChannelEnum::SMS->value);
        $this->assertEquals('Web Hook', ChannelEnum::WEB_HOOK->value);
    }

    /** @test */
    public function application_has_enumerated_formats()
    {
        $this->assertEquals('Text', FormatEnum::TXT->value);
        $this->assertEquals('Comma-Separated Values', FormatEnum::CSV->value);
        $this->assertEquals('Excel Worksheet', FormatEnum::XLS->value);
        $this->assertEquals('Structured Query Language', FormatEnum::SQL->value);
    }
}
