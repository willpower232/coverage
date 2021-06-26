<?php

namespace Tests\App\Providers;

use Tests\TestCase;

class AppServiceProviderTest extends TestCase
{
    public function testAuthRequired(): void
    {
        $this->post('/user/project/branch', []);

        $this->assertResponseStatus(401);
    }

    public function testAuthByQuery(): void
    {
        $this->post('/user/project/branch?auth_token=hello', []);

        $this->receiveJson();
        $this->assertResponseStatus(422);
        $this->seeJsonStructure([
            'file',
        ]);
    }

    public function testAuthByInput(): void
    {
        $this->post('/user/project/branch', [
            'auth_token' => 'hello',
        ]);

        $this->receiveJson();
        $this->assertResponseStatus(422);
        $this->seeJsonStructure([
            'file',
        ]);
    }

    public function testAuthByBearer(): void
    {
        $this->post('/user/project/branch', [], [
            'Authorization' => 'Bearer hello',
        ]);

        $this->receiveJson();
        $this->assertResponseStatus(422);
        $this->seeJsonStructure([
            'file',
        ]);
    }

    // public function testAuthByPassword(): void
    // {
        // ?
    // }
}
