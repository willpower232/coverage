<?php

namespace Tests\App\Http\Controllers;

use Illuminate\Http\Testing\File;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ControllerTest extends TestCase
{
    public function testItHappens(): void
    {
        Storage::fake('local');

        $clover = <<<ENDCLOVER
        <?xml version="1.0" encoding="UTF-8"?>
        <coverage generated="1618905787">
            <project timestamp="1618905787">
                <metrics files="29" loc="1846" ncloc="1233" classes="20" methods="58"
                coveredmethods="50" conditionals="0" coveredconditionals="0" statements="414"
                coveredstatements="316" elements="472" coveredelements="366"/>
            </project>
        </coverage>
        ENDCLOVER;

        // reference Illuminate\Http\Testing\FileFactory, combination of create and createWithContent
        $tmpfile = tmpfile();

        if ($tmpfile === false) {
            throw new \RuntimeException('help');
        }

        fwrite($tmpfile, $clover);

        $file = tap(new File('coverage.clover', $tmpfile), function ($file) use ($tmpfile) {
            $fstat = fstat($tmpfile);

            if ($fstat === false) {
                throw new \RuntimeException('help');
            }

            $file->sizeToReport = $fstat['size'];
            $file->mimeTypeToReport = 'text/xml';
        });

        $headers = [
            'Authorization' => 'Bearer hello',
        ];

        $server = $this->transformHeadersToServerVars($headers);

        $files = [
            'file' => $file,
        ];

        $response = $this->call('POST', '/user/project/branch', [], [], $files, $server);

        $response->assertNoContent();

        Storage::disk('local')->assertExists('user/project/branch.svg');
        Storage::disk('local')->assertExists('user/project/branch.clover');
    }

    public function testItDoesNotHappen(): void
    {
        Storage::fake('local');

        $clover = <<<ENDNOTCLOVER
        I am a plain text file
        ENDNOTCLOVER;

        // reference Illuminate\Http\Testing\FileFactory, combination of create and createWithContent
        $tmpfile = tmpfile();

        if ($tmpfile === false) {
            throw new \RuntimeException('help');
        }

        fwrite($tmpfile, $clover);

        $file = tap(new File('coverage.clover', $tmpfile), function ($file) use ($tmpfile) {
            $fstat = fstat($tmpfile);

            if ($fstat === false) {
                throw new \RuntimeException('help');
            }

            $file->sizeToReport = $fstat['size'];
            $file->mimeTypeToReport = 'text/plain';
        });

        $headers = [
            'Authorization' => 'Bearer hello',
        ];

        $server = $this->transformHeadersToServerVars($headers);

        $files = [
            'file' => $file,
        ];

        $response = $this->call('POST', '/user/project/branch', [], [], $files, $server);

        $response->assertUnprocessable();
    }
}
