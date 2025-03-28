<?php

declare(strict_types=1);

/**
 * This file is part of CodeIgniter Shield.
 *
 * (c) CodeIgniter Foundation <admin@codeigniter.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Tests\Unit;

use CodeIgniter\Shield\Exceptions\ValidationException;
use CodeIgniter\Shield\Models\LoginModel;
use CodeIgniter\Test\DatabaseTestTrait;
use Tests\Support\TestCase;

/**
 * @internal
 */
final class LoginModelTest extends TestCase
{
    use DatabaseTestTrait;

    protected $namespace;
    protected $refresh = true;

    private function createLoginModel(): LoginModel
    {
        return new LoginModel();
    }

    public function testRecordLoginAttemptThrowsException(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage(
            'Validation error: [ip_address] The ip_address field is required.'
            . ' [id_type] The id_type field is required.',
        );

        $model = $this->createLoginModel();

        $model->recordLoginAttempt(
            '',
            '',
            true,
        );
    }
}
