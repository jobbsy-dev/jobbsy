<?php

namespace App\Tests\Job\Bridge\OpenAI;

use App\Entity\Job;
use App\Job\Bridge\OpenAI\CreateJobPromptForClassification;
use App\Job\EmploymentType;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

final class CreateJobPromptForClassificationTest extends TestCase
{
    #[DataProvider('dataPrompt')]
    public function test_create(string $description, string $expected): void
    {
        // Arrange
        $job = new Job('', '', EmploymentType::FULLTIME, '', '');
        $job->setDescription($description);

        // Act
        $prompt = CreateJobPromptForClassification::create($job);

        // Assert
        self::assertSame($expected, $prompt);
    }

    public static function dataPrompt(): \Generator
    {
        yield ['A description', 'Extract maximum 5 tech keywords separated by comma from this text: A description'];

        $text = <<<TXT
Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum at sem et turpis iaculis mattis. Phasellus euismod, risus sed volutpat maximus, magna arcu pretium eros, in iaculis diam arcu quis arcu. Ut dolor urna, lacinia id nunc ac, iaculis vehicula augue. Curabitur mollis eleifend posuere. Sed non metus et mi mollis pulvinar placerat in mi. Sed felis velit, facilisis non tempor eget, pulvinar sed nibh. Sed et mi lorem. Duis faucibus, odio vel vehicula pretium, nisl ipsum convallis diam, eu imperdiet ipsum mauris eget lacus. Morbi nec ultrices sapien, quis porta magna. Donec non felis sagittis, gravida quam ac, sollicitudin lectus.

Proin varius nunc magna, id egestas enim elementum porta. Vestibulum posuere condimentum nunc, nec porttitor risus dignissim eget. Fusce non accumsan mauris, ac aliquet urna. Orci varius natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Curabitur accumsan, massa id sagittis rutrum, metus nunc feugiat tellus, sit amet faucibus arcu massa sed libero. Vestibulum ac sem id felis gravida pretium. Donec posuere enim turpis, in semper tortor pellentesque quis. Sed congue gravida vulputate. In hac habitasse platea dictumst. Mauris eleifend auctor pellentesque. Fusce faucibus tellus consectetur quam placerat ultricies. Phasellus consectetur, nisl et blandit vestibulum, eros magna dictum lacus, vel scelerisque magna odio a arcu. Curabitur lacinia tincidunt vulputate.
TXT;

        yield [$text, 'Extract maximum 5 tech keywords separated by comma from this text: '.$text];
    }
}
