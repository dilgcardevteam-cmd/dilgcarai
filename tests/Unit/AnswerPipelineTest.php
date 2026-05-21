<?php

namespace Tests\Unit;

use App\Services\GeminiService;
use Tests\TestCase;

class AnswerPipelineTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    public function test_fallback_answer_with_empty_context(): void
    {
        $gemini = app(GeminiService::class);
        $reflection = new \ReflectionClass($gemini);
        $method = $reflection->getMethod('fallbackAnswer');
        $method->setAccessible(true);

        $result = $method->invoke($gemini, 'Test prompt', '', 'qa');

        $this->assertEquals('Hello! I\'m NoteGov AI. How can I help you today?', $result);
        $this->assertStringNotContainsString('Answer based on indexed notebook content', $result);
        $this->assertStringNotContainsString('Test prompt', $result);
    }

    public function test_fallback_answer_with_non_empty_context(): void
    {
        $gemini = app(GeminiService::class);
        $reflection = new \ReflectionClass($gemini);
        $method = $reflection->getMethod('fallbackAnswer');
        $method->setAccessible(true);

        $result = $method->invoke($gemini, 'Test prompt', 'Some sample context text', 'qa');

        $this->assertStringStartsWith('NoteGov AI is currently unavailable', $result);
        $this->assertStringNotContainsString('Answer based on indexed notebook content', $result);
        $this->assertStringNotContainsString('Some sample context text', $result);
        $this->assertStringNotContainsString('Test prompt', $result);
    }

    public function test_gemini_service_handles_error_context(): void
    {
        $gemini = app(GeminiService::class);

        $result = $gemini->answer('Test question', 'PDF too large to parse (19.6 MB).', [], 'qa');

        $this->assertStringNotContainsString('The uploaded document could not be processed', $result['text']);
        $this->assertStringNotContainsString('Answer based on indexed notebook content', $result['text']);
        $this->assertStringNotContainsString('PHP extensions like ZipArchive', $result['text']);
    }
}
