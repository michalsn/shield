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

namespace CodeIgniter\Shield\Models;

use CodeIgniter\I18n\Time;
use CodeIgniter\Shield\Authentication\Authenticators\Session;
use CodeIgniter\Shield\Entities\Login;
use CodeIgniter\Shield\Entities\User;
use Faker\Generator;

class LoginModel extends BaseModel
{
    protected $primaryKey     = 'id';
    protected $returnType     = Login::class;
    protected $useSoftDeletes = false;
    protected $allowedFields  = [
        'ip_address',
        'user_agent',
        'id_type',
        'identifier',
        'user_id',
        'date',
        'success',
    ];
    protected $useTimestamps   = false;
    protected $validationRules = [
        'ip_address' => 'required',
        'id_type'    => 'required',
        'identifier' => 'permit_empty|string',
        'user_agent' => 'permit_empty|string',
        'user_id'    => 'permit_empty',
        'date'       => 'required',
    ];
    protected $validationMessages = [];
    protected $skipValidation     = false;

    protected function initialize(): void
    {
        parent::initialize();

        $this->table = $this->tables['logins'];
    }

    /**
     * Records login attempt.
     *
     * @param string          $idType Identifier type. See const ID_YPE_* in Authenticator.
     *                                auth_logins: 'email_password'|'username'|'magic-link'
     *                                auth_token_logins: 'access-token'
     * @param int|string|null $userId
     */
    public function recordLoginAttempt(
        string $idType,
        string $identifier,
        bool $success,
        ?string $ipAddress = null,
        ?string $userAgent = null,
        $userId = null,
    ): void {
        $this->disableDBDebug();

        if ($this->db->getPlatform() === 'OCI8' && $identifier === '') {
            $identifier = ' ';
        }

        $return = $this->insert([
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
            'id_type'    => $idType,
            'identifier' => $identifier,
            'user_id'    => $userId,
            'date'       => Time::now(),
            'success'    => (int) $success,
        ]);

        $this->checkQueryReturn($return);
    }

    /**
     * Returns the previous login information for the user,
     * useful to display to the user the last time the account
     * was accessed.
     */
    public function previousLogin(User $user): ?Login
    {
        return $this->where('success', 1)
            ->where('user_id', $user->id)
            ->orderBy('id', 'desc')
            ->limit(1, 1)->first();
    }

    /**
     * Returns the last login information for the user
     */
    public function lastLogin(User $user): ?Login
    {
        return $this->where('success', 1)
            ->where('user_id', $user->id)
            ->orderBy('id', 'desc')
            ->first();
    }

    /**
     * Generate a fake login for testing
     */
    public function fake(Generator &$faker): Login
    {
        return new Login([
            'ip_address' => $faker->ipv4(),
            'id_type'    => Session::ID_TYPE_EMAIL_PASSWORD,
            'identifier' => $faker->email(),
            'user_id'    => null,
            'date'       => Time::parse('-1 day'),
            'success'    => true,
        ]);
    }
}
