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

use CodeIgniter\Shield\Authentication\AuthenticationException;
use CodeIgniter\Shield\Authentication\Passwords\CompositionValidator;
use CodeIgniter\Shield\Config\Auth;
use Tests\Support\TestCase;

/**
 * @internal
 */
final class CompositionValidatorTest extends TestCase
{
    private CompositionValidator $validator;
    private Auth $config;

    protected function setUp(): void
    {
        parent::setUp();

        $this->config                        = new Auth();
        $this->config->minimumPasswordLength = 8;

        $this->validator = new CompositionValidator($this->config);
    }

    public function testCheckThrowsException(): void
    {
        $this->expectException(AuthenticationException::class);
        $this->expectExceptionMessage(lang('Auth.unsetPasswordLength'));

        $password                            = '1234';
        $this->config->minimumPasswordLength = 0;

        $this->validator->check($password);
    }

    public function testCheckFalse(): void
    {
        $password = '1234';

        $result = $this->validator->check($password);

        $this->assertFalse($result->isOK());
        $this->assertSame(
            lang('Auth.errorPasswordLength', [$this->config->minimumPasswordLength]),
            $result->reason(),
        );
    }

    public function testCheckFalseMultibyte(): void
    {
        $password = '🍣😀';

        $result = $this->validator->check($password);

        $this->assertFalse($result->isOK());
        $this->assertSame(
            lang('Auth.errorPasswordLength', [$this->config->minimumPasswordLength]),
            $result->reason(),
        );
    }

    public function testCheckTrue(): void
    {
        $password = '1234567890';

        $result = $this->validator->check($password);

        $this->assertTrue($result->isOK());
    }
}
